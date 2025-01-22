<script setup>
import AppSidebar from "./AppSidebar.vue";
import UserMenu from "./UserMenu.vue";
import { useLayout } from "@/layout/composables/layout";
import { ref, computed, onMounted } from "vue";

// Track window width dynamically
const windowWidth = ref(window.innerWidth);

onMounted(() => {
  const handleResize = () => {
    windowWidth.value = window.innerWidth;
  };
  window.addEventListener("resize", handleResize);
  return () => window.removeEventListener("resize", handleResize);
});

// Determine if the hamburger button is visible
const isSidebarVisible = computed(() => {
  if (windowWidth.value <= 991) return true; // Always show on mobile
  return layoutConfig.menuMode !== "static"; // Show based on menu mode for desktops
});

const { activeTitle, layoutConfig, isSidebarActive, toggleMenu } = useLayout();
</script>

<template>
  <div
    :class="[
      'layout-wrapper',
      layoutConfig.menuMode === 'static' ? 'layout-static' : 'layout-overlay',
      isSidebarActive ? 'layout-mobile-active blocked-scroll' : '',
    ]"
  >
    <!-- Sidebar -->
    <app-sidebar class="card-container" />

    <!-- Main Content -->
    <div class="layout-main-container">
      <!-- Header -->

      <!-- Page Content -->
      <div class="layout-main p-6 bg-main-content">
        <div
          class="flex items-center justify-between py-4 bg-main-content mt-4"
        >
          <!-- Hamburger button -->
          <button
            class="p-link mr-4 p-2 px-3 inline-flex items-center justify-center border border-gray-200 rounded-lg"
            @click="toggleMenu"
            v-if="isSidebarVisible"
          >
            <i class="pi pi-bars text-xl"></i>
          </button>

          <h1 class="text-xl font-semibold">{{ activeTitle }}</h1>
          <user-menu />
        </div>
        <router-view />
      </div>
    </div>

    <!-- Overlay Mask (click anywhere outside to close) -->
    <div class="layout-mask" @click="toggleMenu" v-if="isSidebarActive"></div>
  </div>
</template>

<style scoped>
.bg-main-content {
  background-color: var(--main-background-color);
}
</style>
