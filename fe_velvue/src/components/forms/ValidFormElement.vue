<!-- /fe_velvue/src/components/forms/FormGroup.vue -->
<script setup>
const props = defineProps({
  label: { type: String, required: true },
  error: { type: String, default: "" },
  name: { type: String, default: "" },
});
</script>

<template>
  <label
    :for="name"
    class="block text-surface-900 dark:text-surface-0 font-medium text-xl mb-2"
  >
    {{ label }}
  </label>

  <slot />

  <!-- Error message -->
  <Message
    v-if="error"
    severity="error"
    variant="simple"
    size="small"
    icon="pi pi-times-circle"
    class="w-full mb-4"
  >
    {{ error }}
  </Message>
</template>
