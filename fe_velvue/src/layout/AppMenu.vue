<script setup>
import { ref } from "vue";
import { useAuthStore } from "@/stores/auth.js";

import AppMenuItem from "./AppMenuItem.vue";

const model = ref([
  {
    icon: "pi pi-fw pi-briefcase",
    to: "/",
    items: [
      { label: "Dashboard", icon: "pi pi-fw pi-objects-column", to: "/" },
      {
        label: "Crud",
        icon: "pi pi-fw pi-pencil",
        to: "/pages/crud",
      },
      {
        label: "Help Center",
        icon: "pi pi-fw pi-question-circle",
        to: "/landing",
      },
      {
        label: "Auth",
        icon: "pi pi-fw pi-user",
        items: [
          {
            label: "Login",
            icon: "pi pi-fw pi-sign-in",
            to: "/auth/login",
          },
          {
            label: "Error",
            icon: "pi pi-fw pi-times-circle",
            to: "/auth/error",
          },
          {
            label: "Access Denied",
            icon: "pi pi-fw pi-lock",
            to: "/auth/access",
          },
        ],
      },

      {
        label: "Not Found",
        icon: "pi pi-fw pi-exclamation-circle",
        to: "/pages/notfound",
      },
      {
        label: "Empty",
        icon: "pi pi-fw pi-circle-off",
        to: "/pages/empty",
      },
    ],
  },
  {
    label: "Hierarchy",
    items: [
      {
        label: "Submenu 1",
        icon: "pi pi-fw pi-bookmark",
        items: [
          {
            label: "Submenu 1.1",
            icon: "pi pi-fw pi-bookmark",
            items: [
              { label: "Submenu 1.1.1", icon: "pi pi-fw pi-bookmark" },
              { label: "Submenu 1.1.2", icon: "pi pi-fw pi-bookmark" },
              { label: "Submenu 1.1.3", icon: "pi pi-fw pi-bookmark" },
            ],
          },
          {
            label: "Submenu 1.2",
            icon: "pi pi-fw pi-bookmark",
            items: [{ label: "Submenu 1.2.1", icon: "pi pi-fw pi-bookmark" }],
          },
        ],
      },
      {
        label: "Submenu 2",
        icon: "pi pi-fw pi-bookmark",
        items: [
          {
            label: "Submenu 2.1",
            icon: "pi pi-fw pi-bookmark",
            items: [
              { label: "Submenu 2.1.1", icon: "pi pi-fw pi-bookmark" },
              { label: "Submenu 2.1.2", icon: "pi pi-fw pi-bookmark" },
            ],
          },
          {
            label: "Submenu 2.2",
            icon: "pi pi-fw pi-bookmark",
            items: [{ label: "Submenu 2.2.1", icon: "pi pi-fw pi-bookmark" }],
          },
        ],
      },
    ],
  },
  {
    label: "Get Started",
    items: [
      {
        label: "Documentation",
        icon: "pi pi-fw pi-book",
        to: "/documentation",
      },
      {
        label: "View Source",
        icon: "pi pi-fw pi-github",
        url: "https://github.com/DreamUpstream/Velvue-Laravel-Vue-SaaS-Starter",
        target: "_blank",
      },
    ],
  },
]);
</script>

<template>
  <ul class="layout-menu">
    <template v-for="(item, i) in model" :key="item">
      <app-menu-item
        v-if="!item.separator"
        :item="item"
        :index="i"
        class="font-sans font-medium"
      ></app-menu-item>
      <li v-if="item.separator" class="menu-separator"></li>
    </template>
  </ul>
</template>

<style lang="scss" scoped></style>
