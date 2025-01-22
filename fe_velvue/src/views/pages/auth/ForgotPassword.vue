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
const serverErrors = ref(null);

async function submitForgotPassword() {
  loading.value = true;
  errors.value = null;
  serverErrors.value = null;
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
    console.log(err);
    serverErrors.value =
      err.response?.data?.errors || "An unexpected error occurred.";
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

    <!-- Server error -->
    <div v-if="serverErrors" class="mb-4">
      <Message
        severity="error"
        icon="pi pi-exclamation-circle"
        v-for="(error, key) in serverErrors"
        :key="key"
      >
        {{ error.message }}
      </Message>
    </div>

    <!-- Email field -->
    <ValidFormElement :label="'Email'" :error="errors?.email" name="emailInput">
      <InputText
        id="emailInput"
        type="text"
        placeholder="Email address"
        class="w-full mb-2"
        v-model="emailField"
        @input="errors.email = null"
        @keydown.enter="submitForgotPassword"
        :invalid="errors?.email ? true : false"
      />
    </ValidFormElement>

    <Button
      label="Send Password Reset Link"
      class="w-full mt-2"
      :loading="loading"
      :disabled="!emailField || loading"
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
