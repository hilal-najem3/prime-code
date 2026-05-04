<script setup lang="ts">
// path to: frontend/apps/tenant-admin/src/pages/patients/components/PatientFormModal.vue
import { reactive, ref, watch } from "vue";
import {
  Modal,
  Button,
  TextInput,
  PasswordInput,
  FormField,
  SelectInput,
  DatePicker,
  Card,
  Spinner,
  useToast,
} from "@ui";
import { patientService } from "@core/api/services/patientService";
import type {
  Patient,
  PatientAddress,
  PatientIdentityDraft,
} from "@core/api/services/patientService";
import { useAction } from "@core/composables/useAction";
import PatientIdentitiesEditor from "./PatientIdentitiesEditor.vue";
import PatientToggleField from "./PatientToggleField.vue";
import PatientAddressesEditor from "./PatientAddressesEditor.vue";

const props = defineProps<{
  modelValue: boolean;
  patient?: Patient | null;
}>();

const emit = defineEmits<{
  (e: "update:modelValue", value: boolean): void;
  (e: "saved"): void;
}>();

const { loading, execute } = useAction();
const { show } = useToast();

const fetching = ref(false);
const errors = ref<Record<string, string[]>>({});

const genderOptions = [
  { label: "Not specified", value: "__null__" },
  { label: "Male", value: "male" },
  { label: "Female", value: "female" },
];

const statusOptions = [
  { label: "Active", value: "active" },
  { label: "Inactive", value: "inactive" },
];

const bloodTypeOptions = [
  { label: "Not specified", value: "" },
  { label: "A+", value: "A+" },
  { label: "A-", value: "A-" },
  { label: "B+", value: "B+" },
  { label: "B-", value: "B-" },
  { label: "AB+", value: "AB+" },
  { label: "AB-", value: "AB-" },
  { label: "O+", value: "O+" },
  { label: "O-", value: "O-" },
];

const identities = ref<PatientIdentityDraft[]>([]);
const deletedIdentityIds = ref<number[]>([]);

function blankAddress(): PatientAddress {
  return {
    country: "",
    city: "",
    street: "",
    building: "",
    floor: "",
    notes: "",
  };
}

const addresses = ref<PatientAddress[]>([blankAddress()]);

const form = reactive({
  first_name: "",
  last_name: "",
  gender: "__null__" as string,
  date_of_birth: null as string | null,
  phone: "",
  phone_secondary: "",
  email: "",
  blood_type: "",
  allergies: "",
  status: "active",
  notes: "",
  create_user: false,
  update_user: false,
  user_email: "",
  user_password: "",
});

function close() {
  emit("update:modelValue", false);
}

function getFirst(field: string) {
  return errors.value[field]?.[0] || null;
}

function resetForCreate() {
  errors.value = {};
  deletedIdentityIds.value = [];
  identities.value = [];
  addresses.value = [blankAddress()];
  form.first_name = "";
  form.last_name = "";
  form.gender = "__null__";
  form.date_of_birth = null;
  form.phone = "";
  form.phone_secondary = "";
  form.email = "";
  form.blood_type = "";
  form.allergies = "";
  form.status = "active";
  form.notes = "";
  form.create_user = false;
  form.update_user = false;
  form.user_email = "";
  form.user_password = "";
}

function sliceDate(raw: unknown): string | null {
  if (raw == null || raw === "") return null;
  const s = String(raw);
  return s.length >= 10 ? s.slice(0, 10) : s;
}

function mapIdentities(source: Patient | null): PatientIdentityDraft[] {
  if (!source?.identities?.length) return [];
  return source.identities.map((i) => ({
    id: i.id,
    type: i.type,
    number: i.number ?? "",
    issued_at: sliceDate(i.issued_at),
    expires_at: sliceDate(i.expires_at),
    notes: i.notes ?? "",
    newFiles: [],
    existingMedia: i.media ?? [],
  }));
}

function applyPatient(payload: Patient) {
  resetForCreate();

  form.first_name = payload.first_name ?? "";
  form.last_name = payload.last_name ?? "";
  form.gender =
    payload.gender && payload.gender !== "" ? payload.gender : "__null__";
  form.date_of_birth = sliceDate(payload.date_of_birth);
  form.phone = payload.phone ?? "";
  form.phone_secondary = payload.phone_secondary ?? "";
  form.email = payload.email ?? "";
  form.blood_type = payload.blood_type ?? "";
  form.allergies = payload.allergies ?? "";
  form.status =
    (payload.status as string) === "inactive" ? "inactive" : "active";
  form.notes = payload.notes ?? "";

  const rawAdr = payload.address ?? null;
  const arr = Array.isArray(rawAdr) ? rawAdr : rawAdr ? [rawAdr] : [];
  addresses.value = arr.length
    ? arr.map((a) => ({ ...blankAddress(), ...a }))
    : [blankAddress()];

  if (payload.user) {
    form.user_email = payload.user.email ?? "";
  }

  identities.value = mapIdentities(payload);
}

function normalizeAddressesPayload(): PatientAddress[] {
  const rows = (addresses.value || []).map((a) => ({
    country: a.country?.trim() || undefined,
    city: a.city?.trim() || undefined,
    street: a.street?.trim() || undefined,
    building: a.building?.trim() || undefined,
    floor: a.floor?.trim() || undefined,
    notes: a.notes?.trim() || undefined,
  }));

  // Drop fully empty rows to avoid sending noisy payloads
  const nonEmpty = rows.filter((a) =>
    Object.values(a).some((v) => v != null && String(v).trim() !== ""),
  );

  return nonEmpty;
}

function buildScalars(): Record<string, unknown> {
  const base: Record<string, unknown> = {
    first_name: form.first_name,
    last_name: form.last_name,
    gender: form.gender === "__null__" ? null : form.gender,
    date_of_birth: form.date_of_birth,
    phone: form.phone || null,
    phone_secondary: form.phone_secondary || null,
    email: form.email || null,
    blood_type: form.blood_type || null,
    allergies: form.allergies || null,
    status: form.status,
    notes: form.notes || null,
    address: normalizeAddressesPayload(),
  };

  if (props.patient) {
    // Only send the flag when enabled; don't send update_user=0.
    base.update_user = form.update_user ? true : undefined;
    if (form.update_user && form.user_email) {
      base.user = {
        email: form.user_email,
        password: form.user_password || undefined,
      };
    }
  } else {
    base.create_user = form.create_user;
    if (form.create_user && form.user_email && form.user_password) {
      base.user = {
        email: form.user_email,
        password: form.user_password,
      };
    }
  }

  return base;
}

watch(
  () => [props.modelValue, props.patient] as const,
  async ([open, patient]) => {
    if (!open) return;

    errors.value = {};
    deletedIdentityIds.value = [];

    if (patient?.id) {
      fetching.value = true;
      try {
        const full = await patientService.getById(patient.id);
        applyPatient(full);
      } finally {
        fetching.value = false;
      }
      return;
    }

    resetForCreate();
    fetching.value = false;
  },
);

function onRemovedIdentity(id: number) {
  deletedIdentityIds.value.push(id);
}

async function submit() {
  errors.value = {};

  await execute(async () => {
    try {
      if (props.patient?.id) {
        const fd = patientService.buildUpdateFormData(
          buildScalars(),
          identities.value,
          deletedIdentityIds.value,
        );
        await patientService.update(props.patient.id, fd);
        show("Patient updated", "success");
      } else {
        const fd = patientService.buildCreateFormData(
          buildScalars(),
          identities.value,
        );
        await patientService.create(fd);
        show("Patient created", "success");
      }
      emit("saved");
      close();
    } catch (e: any) {
      if (e.errors) errors.value = e.errors;
      else throw e;
    }
  });
}
</script>

<template>
  <Modal
    :model-value="modelValue"
    @update:model-value="emit('update:modelValue', $event)"
  >
    <div
      class="w-[min(960px,calc(100vw-48px))] max-h-[min(840px,calc(100vh-96px))] overflow-y-auto pr-2"
    >
      <div v-if="fetching" class="py-10">
        <Spinner />
      </div>

      <div v-else class="space-y-6">
        <h2 class="text-lg font-semibold text-text-primary text-center">
          {{ props.patient ? "Edit patient" : "Create patient" }}
        </h2>

        <form class="space-y-6" @submit.prevent="submit">
          <Card class="space-y-4">
            <h3 class="text-sm font-semibold text-text-primary">
              Demographics
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <FormField :error="getFirst('first_name')">
                <p class="text-xs font-medium text-text-muted uppercase mb-1">
                  First name
                </p>
                <TextInput v-model="form.first_name" />
              </FormField>
              <FormField :error="getFirst('last_name')">
                <p class="text-xs font-medium text-text-muted uppercase mb-1">
                  Last name
                </p>
                <TextInput v-model="form.last_name" />
              </FormField>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <FormField :error="getFirst('gender')">
                <p class="text-xs font-medium text-text-muted uppercase mb-1">
                  Gender
                </p>
                <SelectInput
                  :model-value="form.gender"
                  :options="genderOptions"
                  @update:modelValue="(v) => (form.gender = String(v))"
                />
              </FormField>
              <FormField :error="getFirst('date_of_birth')">
                <p class="text-xs font-medium text-text-muted uppercase mb-1">
                  Date of birth
                </p>
                <DatePicker
                  v-model="form.date_of_birth"
                  input-class="color-text-primary"
                />
              </FormField>
            </div>
          </Card>

          <Card class="space-y-4">
            <h3 class="text-sm font-semibold text-text-primary">Contact</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <FormField :error="getFirst('email')">
                <p class="text-xs font-medium text-text-muted uppercase mb-1">
                  Email
                </p>
                <TextInput v-model="form.email" type="email" />
              </FormField>
              <FormField :error="getFirst('phone')">
                <p class="text-xs font-medium text-text-muted uppercase mb-1">
                  Phone
                </p>
                <TextInput v-model="form.phone" />
              </FormField>
            </div>
            <FormField :error="getFirst('phone_secondary')">
              <p class="text-xs font-medium text-text-muted uppercase mb-1">
                Secondary phone
              </p>
              <TextInput v-model="form.phone_secondary" />
            </FormField>
          </Card>

          <Card class="space-y-4">
            <h3 class="text-sm font-semibold text-text-primary">
              Clinical notes
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <FormField :error="getFirst('blood_type')">
                <p class="text-xs font-medium text-text-muted uppercase mb-1">
                  Blood type
                </p>
                <SelectInput
                  v-model="form.blood_type"
                  :options="bloodTypeOptions"
                />
              </FormField>
              <FormField :error="getFirst('status')">
                <p class="text-xs font-medium text-text-muted uppercase mb-1">
                  Status
                </p>
                <SelectInput v-model="form.status" :options="statusOptions" />
              </FormField>
            </div>
            <FormField :error="getFirst('allergies')">
              <p class="text-xs font-medium text-text-muted uppercase mb-1">
                Allergies
              </p>
              <TextInput v-model="form.allergies" />
            </FormField>
            <FormField :error="getFirst('notes')">
              <p class="text-xs font-medium text-text-muted uppercase mb-1">
                Notes
              </p>
              <TextInput v-model="form.notes" />
            </FormField>
          </Card>

          <Card class="space-y-3">
            <PatientAddressesEditor v-model="addresses" />
          </Card>

          <Card class="space-y-4">
            <h3 class="text-sm font-semibold text-text-primary">
              Login (optional)
            </h3>
            <template v-if="!props.patient">
              <PatientToggleField
                v-model="form.create_user"
                label="Create portal user alongside patient profile"
              />
              <div
                v-if="form.create_user"
                class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2"
              >
                <FormField :error="getFirst('user.email')">
                  <p class="text-xs font-medium text-text-muted uppercase mb-1">
                    User email
                  </p>
                  <TextInput v-model="form.user_email" type="email" />
                </FormField>
                <FormField :error="getFirst('user.password')">
                  <p class="text-xs font-medium text-text-muted uppercase mb-1">
                    Password
                  </p>
                  <PasswordInput v-model="form.user_password" />
                </FormField>
              </div>
            </template>
            <template v-else>
              <PatientToggleField
                v-model="form.update_user"
                label="Update linked portal user credentials"
              />
              <div
                v-if="form.update_user || props.patient?.user"
                class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2"
              >
                <FormField :error="getFirst('user.email')">
                  <p class="text-xs font-medium text-text-muted uppercase mb-1">
                    User email
                  </p>
                  <TextInput v-model="form.user_email" type="email" />
                </FormField>
                <FormField :error="getFirst('user.password')">
                  <p class="text-xs font-medium text-text-muted uppercase mb-1">
                    New password
                  </p>
                  <PasswordInput
                    v-model="form.user_password"
                    autofocus="current_password"
                  />
                </FormField>
              </div>
            </template>
          </Card>

          <Card class="space-y-3">
            <PatientIdentitiesEditor
              v-model="identities"
              @removed-identity="onRemovedIdentity"
            />
          </Card>

          <div class="flex justify-end gap-3 pt-2">
            <Button type="button" variant="outline" @click="close">
              Cancel
            </Button>
            <Button type="submit" :loading="loading">
              {{ props.patient ? "Save changes" : "Create patient" }}
            </Button>
          </div>
        </form>
      </div>
    </div>
  </Modal>
</template>
