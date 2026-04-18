<template>
  <ProfileModal v-model="showProfile" />
  <ChangePasswordModal v-model="showPassword" />

  <header
    class="h-16 bg-white border-b flex items-center justify-between px-6 shadow-sm"
  >
    <button
      @click="ui.toggleSidebar()"
      class="p-2 rounded-lg hover:bg-gray-200"
    >
      <Menu class="w-5 h-5" />
    </button>

    <!-- Left -->
    <div class="text-lg font-semibold text-gray-800">
      {{ t("topbar.dashboard") }}
    </div>

    <!-- Right -->
    <div class="flex items-center gap-4">
      <!-- User -->
      <div class="relative">
        <button
          @click="toggle"
          class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 transition"
        >
          <div
            class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 text-white flex items-center justify-center text-sm font-semibold shadow"
          >
            {{ initials }}
          </div>

          <div class="text-left hidden sm:block">
            <div class="text-sm font-medium text-gray-800">
              {{ auth.userName }}
            </div>
            <div class="text-xs text-gray-500">
              {{ auth.role }}
            </div>
          </div>
        </button>

        <!-- Dropdown -->
        <transition name="fade">
          <div
            v-if="open"
            class="absolute right-0 mt-2 w-56 bg-white border rounded-xl shadow-xl overflow-hidden"
          >
            <div class="px-4 py-3 border-b">
              <div class="text-sm font-medium">
                {{ auth.userName }}
              </div>
              <div class="text-xs text-gray-500">
                {{ auth.userEmail }}
              </div>
            </div>

            <button
              @click="showProfile = true"
              class="w-full text-left px-4 py-2 hover:bg-gray-100 text-sm"
            >
              {{ t("topbar.profile") }}
            </button>

            <button
              @click="showPassword = true"
              class="w-full text-left px-4 py-2 hover:bg-gray-100 text-sm"
            >
              {{ t("topbar.changePassword") }}
            </button>

            <button
              class="w-full text-left px-4 py-2 hover:bg-gray-100 text-sm"
            >
              {{ t("topbar.settings") }}
            </button>

            <button
              @click="auth.logout"
              class="w-full text-left px-4 py-2 hover:bg-red-50 text-red-500 text-sm"
            >
              {{ t("topbar.logout") }}
            </button>
          </div>
        </transition>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { ref, computed } from "vue";
import { useI18n } from "vue-i18n";
import { useAuthStore } from "@/core/store/authStore";
import { useUIStore } from "@/core/store/uiStore";
import { Menu } from "lucide-vue-next";

import ProfileModal from "../ProfileModal.vue";
import ChangePasswordModal from "../ChangePasswordModal.vue";

const { t } = useI18n();

const showProfile = ref(false);
const showPassword = ref(false);

const ui = useUIStore();
const auth = useAuthStore();

const open = ref(false);

const toggle = () => (open.value = !open.value);

const initials = computed(() => {
  if (!auth.userName) return "?";
  return auth.userName
    .split(" ")
    .map((n) => n[0])
    .join("")
    .toUpperCase();
});
</script>

<style>
.fade-enter-active,
.fade-leave-active {
  transition: all 0.15s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(-5px);
}
</style>
