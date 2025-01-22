<!-- /fe_velvue/src/views/pages/auth/Login.vue -->
<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { validateEmail } from "@/helpers";

// DRY components
import AuthContainer from "@/components/auth/AuthContainer.vue";
import AuthLogo from "@/components/auth/AuthLogo.vue";
import SocialLoginButton from "@/components/auth/SocialLoginButton.vue";
import DividerOr from "@/components/auth/DividerOr.vue";
import ValidFormElement from "@/components/forms/ValidFormElement.vue";

const email = ref("");
const password = ref("");
const remember = ref(false);
const errors = ref(null);
const authStore = useAuthStore();
const router = useRouter();
const loading = ref(false);

async function submitForm() {
  errors.value = null;
  loading.value = true;

  if (!validateEmail(email.value)) {
    errors.value = { email: "Please enter a valid email address." };
    loading.value = false;
    return;
  } else if (password.value.length < 6) {
    errors.value = { password: "Password must be at least 6 characters." };
    loading.value = false;
    return;
  }

  try {
    await authStore.login({
      email: email.value,
      password: password.value,
      remember: remember.value,
    });
    router.push({ name: "dashboard" });
  } catch (err) {
    if (err[0]?.message) {
      errors.value = { general: err[0].message };
    } else if (err.response?.status === 429) {
      errors.value = { general: "Too many login attempts. Try again later." };
    } else {
      errors.value = {
        general: "An unexpected error occurred. Please try again.",
        ...(err.response?.data?.errors || {}),
      };
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
    <AuthLogo />

    <!-- Social Login -->
    <SocialLoginButton
      provider="google"
      label="Sign in with Google"
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

    <!-- Email -->
    <ValidFormElement :label="'Email'" :error="errors?.email" name="email">
      <InputText
        id="email"
        type="text"
        placeholder="Email address"
        class="w-full mb-2"
        v-model="email"
        :invalid="errors?.email"
        @keydown.enter="submitForm"
        @input="errors.email = null"
      />
    </ValidFormElement>

    <!-- Password -->
    <ValidFormElement
      :label="'Password'"
      :error="errors?.password"
      name="password"
    >
      <Password
        id="password"
        v-model="password"
        placeholder="Password"
        :toggleMask="true"
        class="mb-2"
        fluid
        :feedback="false"
        @keydown.enter="submitForm"
        :invalid="errors?.password"
        @input="errors.password = null"
      />
    </ValidFormElement>

    <!-- Remember & Forgot -->
    <div class="flex items-center justify-between mt-2 mb-8 gap-8">
      <div class="flex items-center">
        <Checkbox v-model="remember" id="rememberme" binary class="mr-2" />
        <label for="rememberme">Remember me</label>
      </div>
      <router-link to="/auth/forgot-password" class="font-medium text-primary"
        >Forgot password?</router-link
      >
    </div>

    <!-- Submit -->
    <Button
      label="Sign In"
      class="w-full mb-4"
      :loading="loading"
      @click="submitForm"
      :disabled="loading || !email || !password"
    />

    <!-- Register link -->
    <div class="text-center text-sm mt-6">
      Don’t have an account?
      <router-link
        to="/auth/register"
        class="text-primary font-semibold underline ml-1"
      >
        Sign Up
      </router-link>
    </div>
  </AuthContainer>
</template>

<style scoped>
.pi-eye,
.pi-eye-slash {
  transform: scale(1.6);
  margin-right: 1rem;
}
</style>
