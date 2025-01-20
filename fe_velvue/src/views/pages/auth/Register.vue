<!-- /fe_velvue/src/views/pages/auth/Register.vue -->
<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import api from "@/service/apiService";
import AuthContainer from "@/components/auth/AuthContainer.vue";
import AuthLogo from "@/components/auth/AuthLogo.vue";
import SocialLoginButton from "@/components/auth/SocialLoginButton.vue";
import DividerOr from "@/components/auth/DividerOr.vue";
import ValidFormElement from "@/components/forms/ValidFormElement.vue";
import { validateEmail } from "@/helpers";

const router = useRouter();
const nameField = ref("");
const emailField = ref("");
const passwordField = ref("");
const passwordConfirmField = ref("");
const loading = ref(false);
const errors = ref(null);
const successMessage = ref(null);

async function submitRegister() {
  loading.value = true;
  errors.value = null;

  // Client-side validation
  if (nameField.value.length < 3) {
    errors.value = { name: "Name must be at least 3 characters." };
    loading.value = false;
    return;
  } else if (!validateEmail(emailField.value)) {
    errors.value = { email: "Please enter a valid email address." };
    loading.value = false;
    return;
  } else if (passwordField.value.length < 8) {
    errors.value = { password: "Password must be at least 8 characters." };
    loading.value = false;
    return;
  } else if (passwordField.value !== passwordConfirmField.value) {
    errors.value = { password_confirmation: "Passwords do not match." };
    loading.value = false;
    return;
  }

  // Backend validation
  try {
    const response = await api.post("/register", {
      name: nameField.value,
      email: emailField.value,
      password: passwordField.value,
      password_confirmation: passwordConfirmField.value,
    });
    if (response.data.success) {
      successMessage.value = "Account created successfully! Redirecting...";
      setTimeout(() => {
        router.push({ name: "login" });
      }, 2000);
    }
  } catch (err) {
    // Capture and map field-specific errors from backend
    const backendErrors = err.response?.data?.errors || [];
    errors.value = backendErrors.reduce((acc, error) => {
      acc[error.path] = error.message;
      return acc;
    }, {});
    if (!Object.keys(errors.value).length) {
      errors.value.general = "An unexpected error occurred.";
    }
  } finally {
    loading.value = false;
  }
}

function googleLogin() {
  window.location.href =
    import.meta.env.VITE_API_URL + "/api/login/google/redirect";
}
</script>

<template>
  <AuthContainer>
    <AuthLogo text="Create your account" />

    <!-- Social Login -->
    <SocialLoginButton
      provider="google"
      label="Sign up with Google"
      iconUrl="/demo/images/google-icon.svg"
      @click="googleLogin"
    />

    <DividerOr />

    <!-- General Error -->
    <div v-if="errors?.general" class="mb-4">
      <Message severity="error" icon="pi pi-exclamation-circle">
        {{ errors.general }}
      </Message>
    </div>

    <!-- Success message -->
    <div v-if="successMessage" class="mb-4">
      <Message severity="success" icon="pi pi-check-circle">
        {{ successMessage }}
      </Message>
    </div>

    <!-- Name -->
    <ValidFormElement :label="'Name'" :error="errors?.name" name="nameInput">
      <InputText
        id="nameInput"
        type="text"
        placeholder="Your Name"
        class="w-full md:w-[30rem] mb-2"
        v-model="nameField"
      />
    </ValidFormElement>

    <!-- Email -->
    <ValidFormElement :label="'Email'" :error="errors?.email" name="emailInput">
      <InputText
        id="emailInput"
        type="text"
        placeholder="Email address"
        class="w-full md:w-[30rem] mb-2"
        v-model="emailField"
      />
    </ValidFormElement>

    <!-- Password -->
    <ValidFormElement
      :label="'Password'"
      :error="errors?.password"
      name="password1"
    >
      <Password
        id="password1"
        v-model="passwordField"
        placeholder="Password"
        :toggleMask="true"
        fluid
        :feedback="false"
        class="mb-2"
      />
    </ValidFormElement>

    <!-- Password Confirmation -->
    <ValidFormElement
      :label="'Confirm Password'"
      :error="errors?.password_confirmation"
      name="password2"
    >
      <Password
        id="password2"
        v-model="passwordConfirmField"
        placeholder="Confirm Password"
        :toggleMask="true"
        fluid
        :feedback="false"
        class="mb-4"
      />
    </ValidFormElement>

    <Button
      label="Sign Up"
      class="w-full mb-4"
      :loading="loading"
      @click="submitRegister"
    />

    <div class="text-center text-sm mt-6">
      Already have an account?
      <router-link
        to="/auth/login"
        class="text-primary font-semibold underline ml-1"
      >
        Login
      </router-link>
    </div>
  </AuthContainer>
</template>
