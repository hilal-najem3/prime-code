import http from "../http";

export type PatientIdentityType = "id_card" | "passport" | "driver_license";

export interface PatientAddress {
  country?: string;
  city?: string;
  street?: string;
  building?: string;
  floor?: string;
  notes?: string;
}

export interface PatientMediaRow {
  id: number;
  filename: string;
  mime_type?: string;
  url?: string | null;
  collection?: string | null;
}

export interface PatientIdentity {
  id: number;
  type: PatientIdentityType;
  number?: string | null;
  issued_at?: string | null;
  expires_at?: string | null;
  notes?: string | null;
  media?: PatientMediaRow[];
}

export interface Patient {
  id: number;
  user_id?: number | null;
  first_name: string;
  last_name: string;
  full_name?: string;
  gender?: string | null;
  date_of_birth?: string | null;
  phone?: string | null;
  phone_secondary?: string | null;
  email?: string | null;
  // Backward compatible: API may return a single object or an array.
  address?: PatientAddress | PatientAddress[] | null;
  blood_type?: string | null;
  allergies?: string | null;
  status?: string;
  notes?: string | null;
  identities?: PatientIdentity[];
  user?: { id: number; email: string };
}

export interface PatientsQuery {
  page?: number;
  per_page?: number;
  search?: string;
  sort?: string;
  direction?: "asc" | "desc";
}

export interface PatientIdentityDraft {
  id?: number;
  type: PatientIdentityType;
  number: string;
  issued_at: string | null;
  expires_at: string | null;
  notes: string;
  newFiles: File[];
  existingMedia: PatientMediaRow[];
}

type PaginatedPatients = {
  data: Patient[];
  meta: {
    current_page: number;
    per_page: number;
    total: number;
    last_page: number;
  } | null;
};

function normalizeAddressArray(
  address: PatientAddress | PatientAddress[] | null | undefined,
): PatientAddress[] {
  if (!address) return [];
  return Array.isArray(address) ? address : [address];
}

function appendAddresses(
  formData: FormData,
  address: PatientAddress | PatientAddress[] | null | undefined,
) {
  const rows = normalizeAddressArray(address);
  const keys = ["country", "city", "street", "building", "floor", "notes"] as const;

  rows.forEach((row, idx) => {
    const a = row || {};
    keys.forEach((k) => {
      const v = a[k];
      if (v != null && v !== "") {
        formData.append(`address[${idx}][${k}]`, String(v));
      }
    });
  });
}

function appendScalar(formData: FormData, key: string, val: unknown) {
  if (val === undefined || val === null || val === "") return;
  if (typeof val === "boolean") {
    formData.append(key, val ? "1" : "0");
    return;
  }
  formData.append(key, String(val));
}

function appendIdentity(
  formData: FormData,
  idx: number,
  row: PatientIdentityDraft,
) {
  const p = `identities[${idx}]`;

  if (row.id != null) {
    formData.append(`${p}[id]`, String(row.id));
  }

  formData.append(`${p}[type]`, row.type);
  appendScalar(formData, `${p}[number]`, row.number);
  appendScalar(formData, `${p}[issued_at]`, row.issued_at);
  appendScalar(formData, `${p}[expires_at]`, row.expires_at);
  appendScalar(formData, `${p}[notes]`, row.notes);

  row.newFiles.forEach((file, mi) => {
    formData.append(`${p}[media][${mi}]`, file);
  });
}

export const patientService = {
  async getAll(params: PatientsQuery = {}): Promise<PaginatedPatients> {
    const res = (await http.get("/patients", { params })) as unknown as {
      data: Patient[];
      meta: PaginatedPatients["meta"];
    };

    return {
      data: Array.isArray(res.data) ? res.data : [],
      meta: res.meta ?? null,
    };
  },

  async getById(id: number | string): Promise<Patient> {
    const res = (await http.get(`/patients/${id}`)) as unknown as {
      data: Patient;
    };
    return res.data;
  },

  buildCreateFormData(fields: Record<string, unknown>, identities: PatientIdentityDraft[]): FormData {
    const formData = new FormData();

    const scalarKeys = [
      "first_name",
      "last_name",
      "gender",
      "date_of_birth",
      "phone",
      "phone_secondary",
      "email",
      "blood_type",
      "allergies",
      "status",
      "notes",
      "create_user",
    ];

    scalarKeys.forEach((k) => appendScalar(formData, k, fields[k]));

    if (fields.user && typeof fields.user === "object") {
      const u = fields.user as Record<string, string>;
      appendScalar(formData, "user[email]", u.email);
      appendScalar(formData, "user[password]", u.password);
    }

    appendAddresses(
      formData,
      fields.address as PatientAddress | PatientAddress[] | null | undefined,
    );

    identities.forEach((identity, idx) => appendIdentity(formData, idx, identity));

    return formData;
  },

  async create(formData: FormData): Promise<Patient> {
    const res = (await http.post("/patients", formData, {
      headers: { "Content-Type": "multipart/form-data" },
    })) as unknown as { data: Patient };

    return res.data;
  },

  buildUpdateFormData(
    fields: Record<string, unknown>,
    identities: PatientIdentityDraft[],
    deletedIdentityIds: number[],
  ): FormData {
    const formData = new FormData();
    formData.append("_method", "PUT");

    const scalarKeys = [
      "first_name",
      "last_name",
      "gender",
      "date_of_birth",
      "phone",
      "phone_secondary",
      "email",
      "blood_type",
      "allergies",
      "status",
      "notes",
      "update_user",
    ];

    scalarKeys.forEach((k) => appendScalar(formData, k, fields[k]));

    if (fields.user && typeof fields.user === "object") {
      const u = fields.user as Record<string, string>;
      appendScalar(formData, "user[email]", u.email);
      appendScalar(formData, "user[password]", u.password);
    }

    appendAddresses(
      formData,
      fields.address as PatientAddress | PatientAddress[] | null | undefined,
    );

    identities.forEach((identity, idx) => appendIdentity(formData, idx, identity));

    deletedIdentityIds.forEach((did, i) =>
      formData.append(`deleted_identity_ids[${i}]`, String(did)),
    );

    return formData;
  },

  async update(id: number | string, formData: FormData): Promise<Patient> {
    const res = (await http.post(`/patients/${id}`, formData, {
      headers: { "Content-Type": "multipart/form-data" },
    })) as unknown as { data: Patient };

    return res.data;
  },

  async delete(id: number | string): Promise<void> {
    await http.delete(`/patients/${id}`);
  },
};
