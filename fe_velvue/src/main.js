import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";

import Aura from "@primevue/themes/aura";
import PrimeVue from "primevue/config";
import ConfirmationService from "primevue/confirmationservice";
import ToastService from "primevue/toastservice";
import { createPinia } from "pinia";
const pinia = createPinia();

import "@/assets/styles.scss";
import "@/assets/tailwind.css";

const app = createApp(App);

app.use(router);
app.use(pinia);
app.use(PrimeVue, {
  theme: {
    preset: Aura,
    options: {
      darkModeSelector: ".app-dark",
    },
  },
});
app.use(ToastService);
app.use(ConfirmationService);

app.mount("#app");
