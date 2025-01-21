import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "@/stores/auth.js";
import AppLayout from "@/layout/AppLayout.vue";

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: "/",
      component: AppLayout,
      meta: { requiresAuth: true },
      children: [
        {
          path: "/",
          name: "dashboard",
          component: () => import("@/views/Dashboard.vue"),
          meta: { requiresVerified: true, title: "Overview" },
        },
        {
          path: "/account",
          name: "account",
          component: () => import("@/views/pages/Account.vue"),
        },
        {
          path: "/uikit/formlayout",
          name: "formlayout",
          component: () => import("@/views/uikit/FormLayout.vue"),
          meta: { requiresVerified: true },
        },
        {
          path: "/uikit/input",
          name: "input",
          component: () => import("@/views/uikit/InputDoc.vue"),
          meta: { requiresVerified: true },
        },
        {
          path: "/uikit/button",
          name: "button",
          component: () => import("@/views/uikit/ButtonDoc.vue"),
          meta: { requiresVerified: true },
        },
        {
          path: "/uikit/table",
          name: "table",
          component: () => import("@/views/uikit/TableDoc.vue"),
          meta: { requiresVerified: true },
        },
        {
          path: "/uikit/list",
          name: "list",
          component: () => import("@/views/uikit/ListDoc.vue"),
          meta: { requiresVerified: true },
        },
        {
          path: "/uikit/tree",
          name: "tree",
          component: () => import("@/views/uikit/TreeDoc.vue"),
          meta: { requiresVerified: true },
        },
        {
          path: "/uikit/panel",
          name: "panel",
          component: () => import("@/views/uikit/PanelsDoc.vue"),
          meta: { requiresVerified: true },
        },

        {
          path: "/uikit/overlay",
          name: "overlay",
          component: () => import("@/views/uikit/OverlayDoc.vue"),
          meta: { requiresVerified: true },
        },
        {
          path: "/uikit/media",
          name: "media",
          component: () => import("@/views/uikit/MediaDoc.vue"),
          meta: { requiresVerified: true },
        },
        {
          path: "/uikit/message",
          name: "message",
          component: () => import("@/views/uikit/MessagesDoc.vue"),
          meta: { requiresVerified: true },
        },
        {
          path: "/uikit/file",
          name: "file",
          component: () => import("@/views/uikit/FileDoc.vue"),
        },
        {
          path: "/uikit/menu",
          name: "menu",
          component: () => import("@/views/uikit/MenuDoc.vue"),
          meta: { requiresVerified: true },
        },
        {
          path: "/uikit/charts",
          name: "charts",
          component: () => import("@/views/uikit/ChartDoc.vue"),
          meta: { requiresVerified: true },
        },
        {
          path: "/uikit/misc",
          name: "misc",
          component: () => import("@/views/uikit/MiscDoc.vue"),
          meta: { requiresVerified: true },
        },
        {
          path: "/uikit/timeline",
          name: "timeline",
          component: () => import("@/views/uikit/TimelineDoc.vue"),
          meta: { requiresVerified: true },
        },
        {
          path: "/pages/empty",
          name: "empty",
          component: () => import("@/views/pages/Empty.vue"),
          meta: { requiresVerified: true },
        },
        {
          path: "/pages/crud",
          name: "crud",
          component: () => import("@/views/pages/uikit/Crud.vue"),
          meta: { requiresVerified: true },
        },
        {
          path: "/documentation",
          name: "documentation",
          component: () => import("@/views/pages/uikit/Documentation.vue"),
          meta: { requiresVerified: true },
        },
      ],
    },

    {
      path: "/auth/login",
      name: "login",
      component: () => import("@/views/pages/auth/Login.vue"),
    },

    {
      path: "/404",
      name: "notfound",
      component: () => import("@/views/pages/NotFound.vue"),
    },
    {
      path: "/auth/access",
      name: "accessDenied",
      component: () => import("@/views/pages/auth/Access.vue"),
    },
    {
      path: "/auth/error",
      name: "error",
      component: () => import("@/views/pages/auth/Error.vue"),
    },
    {
      path: "/auth/register",
      name: "register",
      component: () => import("@/views/pages/auth/Register.vue"),
      meta: { isGuest: true },
    },
    {
      path: "/auth/forgot-password",
      name: "forgotPassword",
      component: () => import("@/views/pages/auth/ForgotPassword.vue"),
      meta: { isGuest: true },
    },

    {
      path: "/:pathMatch(.*)*",
      redirect: "/404",
    },
  ],
});

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();

  // 1) Fetch the user if not loaded yet.
  if (authStore.user === null && !authStore.loading) {
    try {
      await authStore.fetchUser();
    } catch (err) {
      // user stays null if error
    }
  }

  // 2) If route is protected but user is not logged in -> login
  if (to.meta.requiresAuth && !authStore.isLoggedIn) {
    return next({ name: "login" });
  }

  // 3) If route is guest-only but user *is* logged in -> dashboard
  if (to.meta.isGuest && authStore.isLoggedIn) {
    return next({ name: "dashboard" });
  }

  // 4) Check for email verification if route requires it
  //    - If user is logged in but not verified -> redirect to an "unverified" page
  if (to.meta.requiresVerified && authStore.isLoggedIn) {
    if (authStore.user.must_verify_email) {
      // user is unverified, so handle it
      return next({ name: "account" });
    }
  }

  return next();
});

export default router;
