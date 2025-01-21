<script setup>
import { ref, onMounted, watch } from "vue";
import { useAuthStore } from "@/stores/auth.js";
import { useRoute } from "vue-router";

const isOpen = ref(false);

function toggleMenu() {
  isOpen.value = !isOpen.value;
}

function closeMenu() {
  isOpen.value = false;
}

const authStore = useAuthStore();
const route = useRoute();

function logout() {
  authStore.logout();
}

onMounted(() => {
  // Close menu on clicking outside
  function handleClickOutside(event) {
    const menu = document.querySelector(".user-menu-container");
    if (menu && !menu.contains(event.target)) {
      closeMenu();
    }
  }
  document.addEventListener("click", handleClickOutside);

  return () => {
    document.removeEventListener("click", handleClickOutside);
  };
});

// Close menu when navigating
watch(
  () => route.path,
  () => {
    closeMenu();
  }
);
</script>

<template>
  <div class="relative user-menu-container">
    <!-- Clickable Avatar/Name -->
    <button
      @click="toggleMenu"
      :class="
        isOpen
          ? `flex items-center gap-2 px-4 py-2 rounded-full hover:bg-gray-100 border border-gray-200 shadow-lg`
          : `flex items-center gap-2 px-4 py-2 rounded-full hover:bg-gray-100 border border-gray-200`
      "
    >
      <i class="pi pi-bars"></i>
      <Avatar
        icon="pi pi-user rounded"
        :image="authStore.user?.avatar"
        shape="circle"
      />
      <span>{{ authStore.user?.name || "Guest" }}</span>
    </button>

    <!-- Dropdown Menu -->
    <div
      v-if="isOpen"
      class="absolute right-0 mt-2 w-48 bg-white card-container shadow-lg z-50 border border-gray-200 rounded-lg"
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
        Help Center
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
