<template>
  <Card class="space-y-4">
    <h2 class="text-lg font-semibold text-text-primary">
      {{ t("modules.title") }}
    </h2>

    <div class="grid grid-cols-2 gap-3">
      <label
        v-for="module in modules"
        :key="module.id"
        class="flex items-center gap-2 cursor-pointer"
      >
        <input type="checkbox" :value="module.id" v-model="selectedModules" />
        <span>{{ module.name }}</span>
      </label>
    </div>

    <div class="flex justify-end">
      <Button :loading="loadingModules" @click="saveModules">
        {{ t("modules.actions.update") }}
      </Button>
    </div>
  </Card>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import { moduleService } from "@core/api/services/moduleService";
import { tenantModuleService } from "@core/api/services/tenantModuleService";
import { Card, Button } from "@ui";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

const props = defineProps({
  tenantId: Number,
});

onMounted(fetch);

const modules = ref<Array<{ id: number; name: string }>>([]);
const selectedModules = ref<number[]>([]);
const loadingModules = ref(false);

const loadModules = async () => {
  const all = await moduleService.getAll();
  modules.value = all.data;

  const assigned = await tenantModuleService.get(props.tenantId);
  selectedModules.value = assigned.data.map((m: any) => m.id);
};

const saveModules = async () => {
  loadingModules.value = true;

  await tenantModuleService.sync(props.tenantId, selectedModules.value);

  loadingModules.value = false;
};

loadModules();
</script>
