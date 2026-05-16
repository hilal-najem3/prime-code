// path to: frontend/packages/core/api/services/patientService.ts

import http from "../http";
// import type { Media } from "@media";
import type { Media } from "../../../media/types/media";

/*
|--------------------------------------------------------------------------
| Types
|--------------------------------------------------------------------------
*/

export type PatientIdentityType = "id_card" | "passport" | "driver_license";

export interface PatientAddress {
  country?: string;
  city?: string;
  street?: string;
  building?: string;
  floor?: string;
  notes?: string;
}

export interface PatientIdentity {
  id: number;
  type: PatientIdentityType;
  number?: string | null;
  issued_at?: string | null;
  expires_at?: string | null;
  notes?: string | null;

  media?: Media[];
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

  address?: PatientAddress | PatientAddress[] | null;

  blood_type?: string | null;
  allergies?: string | null;

  status?: string;
  notes?: string | null;

  identities?: PatientIdentity[];

  user?: {
    id: number;
    email: string;
  };
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

  // ✅ unified media system
  media: Media[];
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

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

function normalizeAddressArray(
  address: PatientAddress | PatientAddress[] | null | undefined,
): PatientAddress[] {
  if (!address) return [];

  return Array.isArray(address) ? address : [address];
}

function appendScalar(formData: FormData, key: string, val: unknown) {
  if (val === undefined || val === null || val === "") {
    return;
  }

  if (typeof val === "boolean") {
    formData.append(key, val ? "1" : "0");
    return;
  }

  formData.append(key, String(val));
}

function appendAddresses(
  formData: FormData,
  address: PatientAddress | PatientAddress[] | null | undefined,
) {
  const rows = normalizeAddressArray(address);

  const keys = [
    "country",
    "city",
    "street",
    "building",
    "floor",
    "notes",
  ] as const;

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

function appendIdentity(formData: FormData, idx: number, row: any) {
  const p = `identities[${idx}]`;

  /*
  |--------------------------------------------------------------------------
  | Existing Identity ID
  |--------------------------------------------------------------------------
  */

  if (row.id != null) {
    formData.append(`${p}[id]`, String(row.id));
  }

  /*
  |--------------------------------------------------------------------------
  | Identity Data
  |--------------------------------------------------------------------------
  */

  formData.append(`${p}[type]`, row.type);

  appendScalar(formData, `${p}[number]`, row.number);

  appendScalar(formData, `${p}[issued_at]`, row.issued_at);

  appendScalar(formData, `${p}[expires_at]`, row.expires_at);

  appendScalar(formData, `${p}[notes]`, row.notes);

  /*
  |--------------------------------------------------------------------------
  | Media IDs
  |--------------------------------------------------------------------------
  */

  row.media.forEach((media: any, mediaIndex: number) => {
    console.log("MEDIA OBJECT", media);

    formData.append(`${p}[media_ids][${mediaIndex}]`, String(media.data.id));
  });

  console.log("FORM DATA", formData);
  // Log all the appended form data for debugging
  for (const pair of formData.entries()) {
    console.log(pair[0] + ": " + pair[1]);
  }
}

/*
|--------------------------------------------------------------------------
| Service
|--------------------------------------------------------------------------
*/

export const patientService = {
  /*
  |--------------------------------------------------------------------------
  | List Patients
  |--------------------------------------------------------------------------
  */

  async getAll(params: PatientsQuery = {}): Promise<PaginatedPatients> {
    const res = await http.get("/patients", {
      params,
    });

    return {
      data: res.data.data ?? [],
      meta: res.data.meta ?? null,
    };
  },

  /*
  |--------------------------------------------------------------------------
  | Get Patient
  |--------------------------------------------------------------------------
  */

  async getById(id: number | string): Promise<Patient> {
    const res = (await http.get(`/patients/${id}`)) as unknown as {
      data: { data: Patient };
    };

    return res.data.data;
  },

  /*
  |--------------------------------------------------------------------------
  | Build Create Payload
  |--------------------------------------------------------------------------
  */

  buildCreateFormData(
    fields: Record<string, unknown>,
    identities: PatientIdentityDraft[],
  ): FormData {
    const formData = new FormData();

    /*
    |--------------------------------------------------------------------------
    | Scalars
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | User
    |--------------------------------------------------------------------------
    */

    if (fields.user && typeof fields.user === "object") {
      const u = fields.user as Record<string, string>;

      appendScalar(formData, "user[email]", u.email);

      appendScalar(formData, "user[password]", u.password);
    }

    /*
    |--------------------------------------------------------------------------
    | Addresses
    |--------------------------------------------------------------------------
    */

    appendAddresses(
      formData,
      fields.address as PatientAddress | PatientAddress[] | null | undefined,
    );

    /*
    |--------------------------------------------------------------------------
    | Identities
    |--------------------------------------------------------------------------
    */

    identities.forEach((identity, idx) => {
      appendIdentity(formData, idx, identity);
    });

    return formData;
  },

  /*
  |--------------------------------------------------------------------------
  | Create Patient
  |--------------------------------------------------------------------------
  */

  async create(formData: FormData): Promise<Patient> {
    const res = (await http.post("/patients", formData, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    })) as unknown as {
      data: { data: Patient };
    };

    return res.data.data;
  },

  /*
  |--------------------------------------------------------------------------
  | Build Update Payload
  |--------------------------------------------------------------------------
  */

  buildUpdateFormData(
    fields: Record<string, unknown>,
    identities: PatientIdentityDraft[],
    deletedIdentityIds: number[],
  ): FormData {
    const formData = new FormData();

    formData.append("_method", "PUT");

    /*
    |--------------------------------------------------------------------------
    | Scalars
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | User
    |--------------------------------------------------------------------------
    */

    if (fields.user && typeof fields.user === "object") {
      const u = fields.user as Record<string, string>;

      appendScalar(formData, "user[email]", u.email);

      appendScalar(formData, "user[password]", u.password);
    }

    /*
    |--------------------------------------------------------------------------
    | Addresses
    |--------------------------------------------------------------------------
    */

    appendAddresses(
      formData,
      fields.address as PatientAddress | PatientAddress[] | null | undefined,
    );

    /*
    |--------------------------------------------------------------------------
    | Identities
    |--------------------------------------------------------------------------
    */

    identities.forEach((identity, idx) => {
      appendIdentity(formData, idx, identity);
    });

    /*
    |--------------------------------------------------------------------------
    | Deleted Identities
    |--------------------------------------------------------------------------
    */

    deletedIdentityIds.forEach((did, i) => {
      formData.append(`deleted_identity_ids[${i}]`, String(did));
    });

    return formData;
  },

  /*
  |--------------------------------------------------------------------------
  | Update Patient
  |--------------------------------------------------------------------------
  */

  async update(id: number | string, formData: FormData): Promise<Patient> {
    const res = (await http.post(`/patients/${id}`, formData, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    })) as unknown as {
      data: { data: Patient };
    };

    return res.data.data;
  },

  /*
  |--------------------------------------------------------------------------
  | Delete Patient
  |--------------------------------------------------------------------------
  */

  async delete(id: number | string): Promise<void> {
    await http.delete(`/patients/${id}`);
  },
};
