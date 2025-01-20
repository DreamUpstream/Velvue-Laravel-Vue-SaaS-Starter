<script setup>
const props = defineProps({
  text: {
    type: String,
    default: "",
  },
});
</script>

<template>
  <div class="text-center mb-8 flex flex-col items-center">
    <img src="/demo/images/logo.png" alt="velvue" class="w-40" />
    <span class="text-muted-color font-medium">{{ text }}</span>
  </div>
</template>
