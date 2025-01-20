<!-- File: /fe_velvue/src/layout/UserMenu.vue -->
<script setup>
import { ref } from "vue";
import { useAuthStore } from "@/stores/auth.js";

const isOpen = ref(false);

function toggleMenu() {
  isOpen.value = !isOpen.value;
}

const authStore = useAuthStore();

function logout() {
  authStore.logout();
}
</script>

<template>
  <div class="relative">
    <!-- Clickable Avatar/Name -->
    <button
      @click="toggleMenu"
      class="flex items-center gap-2 px-4 py-2 rounded hover:bg-gray-50"
    >
      <Avatar icon="pi pi-user" :image="authStore.user?.avatar" size="large" />
      <span>{{ authStore.user?.name || "Guest" }}</span>
      <i class="pi pi-chevron-down"></i>
    </button>

    <!-- Dropdown Menu -->
    <div
      v-if="isOpen"
      class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded shadow-md z-50"
    >
      <router-link
        to="/account"
        class="block px-4 py-2 text-gray-700 hover:bg-gray-100"
      >
        Account Settings
      </router-link>
      <router-link
        to="/billing"
        class="block px-4 py-2 text-gray-700 hover:bg-gray-100"
      >
        Billing / Upgrade
      </router-link>
      <hr class="my-1" />
      <button
        @click="logout"
        class="block w-full text-left px-4 py-2 text-red-600 hover:bg-red-50"
      >
        Logout
      </button>
    </div>
  </div>
</template>

<style scoped>
/* Adjust menu styling as necessary */
</style>
