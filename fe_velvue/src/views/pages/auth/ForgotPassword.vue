<!-- /fe_velvue/src/views/pages/auth/ForgotPassword.vue -->
<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import api from "@/service/apiService";
import { validateEmail } from "@/helpers";

// DRY components
import AuthContainer from "@/components/auth/AuthContainer.vue";
import AuthLogo from "@/components/auth/AuthLogo.vue";
import ValidFormElement from "@/components/forms/ValidFormElement.vue";

const router = useRouter();
const emailField = ref("");
const loading = ref(false);
const serverMessage = ref(null);
const errors = ref(null);

async function submitForgotPassword() {
  loading.value = true;
  errors.value = null;
  serverMessage.value = null;

  if (!validateEmail(emailField.value)) {
    errors.value = { email: "Please enter a valid email address." };
    loading.value = false;
    return;
  }

  try {
    const response = await api.post("/forgot-password", {
      email: emailField.value,
    });
    if (response.data.success) {
      serverMessage.value = "Password reset link sent! Check your email inbox.";
    }
  } catch (err) {
    errors.value = err.response?.data?.errors || {
      general: "Something went wrong.",
    };
  } finally {
    loading.value = false;
  }
}
</script>

<template>
  <AuthContainer>
    <AuthLogo text="Forgot your password?" />

    <!-- Success message -->
    <div v-if="serverMessage" class="mb-4">
      <Message severity="success" icon="pi pi-check-circle">
        {{ serverMessage }}
      </Message>
    </div>

    <!-- General error -->
    <div v-if="errors?.general" class="mb-4">
      <Message severity="error" icon="pi pi-exclamation-circle">
        {{ errors.general }}
      </Message>
    </div>

    <!-- Email field -->
    <ValidFormElement
      :label="'Email'"
      :error="errors?.email"
      name="emailInput"
      messageClass="mb-4 w-96"
    >
      <InputText
        id="emailInput"
        type="text"
        placeholder="Email address"
        class="w-full mb-4"
        v-model="emailField"
      />
    </ValidFormElement>

    <Button
      label="Send Password Reset Link"
      class="w-full"
      :loading="loading"
      :disabled="!emailField"
      @click="submitForgotPassword"
    />

    <!-- Link back -->
    <div class="text-center text-sm mt-6">
      Remembered your password?
      <router-link
        to="/auth/login"
        class="text-primary font-semibold underline ml-1"
      >
        Login
      </router-link>
    </div>
  </AuthContainer>
</template>
