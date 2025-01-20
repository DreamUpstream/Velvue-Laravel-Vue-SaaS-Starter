<!-- File: /fe_velvue/src/layout/AppLayout.vue -->
<script setup>
import AppSidebar from "./AppSidebar.vue";
import UserMenu from "./UserMenu.vue";
import { useLayout } from "@/layout/composables/layout";

const { activeTitle } = useLayout();
</script>

<template>
  <div class="flex min-h-screen relative">
    <!-- Sidebar -->
    <app-sidebar class="shadow-lg" />

    <!-- Main Content -->
    <div class="flex-1 flex flex-col ml-64">
      <!-- Header -->
      <div class="flex items-center justify-between px-6 py-4 bg-main-content">
        <h1 class="text-xl font-semibold">{{ activeTitle }}</h1>
        <user-menu />
      </div>

      <!-- Page Content -->
      <div class="p-6 flex-1 bg-main-content">
        <router-view />
      </div>
    </div>
  </div>
</template>

<style scoped>
.bg-main-content {
  background-color: var(--main-background-color);
}
</style>
