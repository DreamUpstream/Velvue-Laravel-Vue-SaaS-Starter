<!-- /fe_velvue/src/components/auth/SocialLoginButton.vue -->
<script setup>
/**
 * PROPS:
 *   - provider: 'google' | 'github' | 'facebook', etc.
 *   - label: string (e.g. "Sign in with Google")
 *   - iconUrl: string (e.g. "/demo/images/google-icon.svg")
 *   - onClick: function (emits or your own login callback)
 */

const props = defineProps({
  provider: {
    type: String,
    default: "google",
  },
  label: {
    type: String,
    default: "Sign in with Google",
  },
  iconUrl: {
    type: String,
    default: "/demo/images/google-icon.svg",
  },
});

const emits = defineEmits(["click"]);

function handleClick() {
  emits("click");
}
</script>

<template>
  <button
    @click="handleClick"
    class="flex items-center justify-center gap-2 w-full py-3 px-4 mb-8 border border-gray-300 hover:shadow-sm rounded-md text-gray-700 font-medium bg-white transition-shadow dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300"
  >
    <img :src="iconUrl" :alt="provider" class="w-6 h-6" />
    <span class="tracking-wide">{{ label }}</span>
  </button>
</template>
