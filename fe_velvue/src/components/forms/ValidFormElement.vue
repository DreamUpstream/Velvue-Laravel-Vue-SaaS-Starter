<!-- /fe_velvue/src/components/forms/FormGroup.vue -->
<script setup>
const props = defineProps({
  label: { type: String, required: true },
  error: { type: String, default: "" },
  name: { type: String, default: "" },
  messageClass: { type: String, default: "mb-4" },
});

// We might also handle "id" attribute or slot the input inside
</script>

<template>
  <label
    :for="name"
    class="block text-surface-900 dark:text-surface-0 font-medium text-xl mb-2"
  >
    {{ label }}
  </label>

  <!-- We allow consumer to pass the input via <slot> -->
  <slot />

  <!-- Error message -->
  <Message
    v-if="error"
    severity="error"
    variant="simple"
    size="small"
    icon="pi pi-times-circle"
    :class="messageClass"
  >
    {{ error }}
  </Message>
</template>
