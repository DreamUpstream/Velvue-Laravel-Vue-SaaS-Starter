<!-- File: /fe_velvue/src/layout/AppSidebar.vue -->
<script setup>
import AppMenu from "./AppMenu.vue";
import { useAuthStore } from "@/stores/auth.js";
import { useRouter } from "vue-router";

const authStore = useAuthStore();
const router = useRouter();

function logout() {
  authStore.logout().then(() => router.push({ name: "login" }));
}
</script>

<template>
  <!-- Full-height sidebar pinned left -->
  <div
    class="fixed top-0 left-0 w-64 h-screen bg-white border-r border-surface-border flex flex-col justify-between"
  >
    <!-- Top: logo -->
    <div class="p-4">
      <img
        src="/demo/images/logo.png"
        alt="Velvue"
        class="mx-auto mb-4"
        style="max-width: 120px"
      />
      <app-menu class="border-t border-surface-border" />
    </div>

    <!-- Bottom: logout button -->
    <div class="p-4 border-t border-surface-border">
      <button
        @click="logout"
        class="w-full flex items-center gap-2 p-2 rounded hover:bg-surface-hover"
      >
        <i class="pi pi-sign-out"></i>
        <span>Logout</span>
      </button>
    </div>
  </div>
</template>

<style scoped>
/* If you want custom scss or just rely on Tailwind classes */
</style>
