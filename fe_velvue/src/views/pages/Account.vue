<!-- /fe_velvue/src/views/pages/Account.vue -->
<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import api from "@/service/apiService";

// PrimeVue imports
// (Make sure you have installed them and configured in main.js / components auto-import)
import { useToast } from "primevue/usetoast";
import { FilterMatchMode } from "@primevue/core/api";

// Local references
// If you have a component for "ValidFormElement" usage, you can import it.
// Otherwise you can just inline the label + error UI.
import ValidFormElement from "@/components/forms/ValidFormElement.vue";

const toast = useToast();
const router = useRouter();
const authStore = useAuthStore();

// Basic user form fields
const nameField = ref("");
const emailField = ref("");

// We assume the user is loaded from the store or from an API
// onMounted, you can load the user data from the store or an API.
onMounted(() => {
  if (authStore.user) {
    nameField.value = authStore.user.name;
    emailField.value = authStore.user.email;
  }
});

// If you have an "activity log" data, populate it here.
// For demonstration, we use static data. Replace with API calls if needed.
const activityLog = ref([
  { date: "2025-01-02 14:03", ip: "192.168.0.1", location: "Chicago, IL" },
  { date: "2025-01-03 09:15", ip: "192.168.0.2", location: "Denver, CO" },
  { date: "2025-01-04 22:45", ip: "192.168.0.3", location: "New York, NY" },
]);

// Payment methods placeholder data
const paymentMethods = ref([
  { type: "Visa ****1234", expires: "09/28", default: true },
  { type: "Mastercard ****9999", expires: "01/26", default: false },
]);

// Billing history placeholder data
const billingHistory = ref([
  { id: "INV-001", amount: 49.99, date: "2025-01-01", status: "Paid" },
  { id: "INV-002", amount: 29.99, date: "2025-02-01", status: "Paid" },
  { id: "INV-003", amount: 29.99, date: "2025-03-01", status: "Pending" },
]);

// For uploading the profile image
const profileImage = ref(null);
function onUploadProfileImage(event) {
  // event.files => array of uploaded files
  // you can call your own API endpoint here
  const file = event.files?.[0];
  if (!file) return;

  const formData = new FormData();
  formData.append("image", file);

  // Example - call your upload endpoint
  api
    .post("/upload", formData, {
      headers: { "Content-Type": "multipart/form-data" },
    })
    .then((response) => {
      toast.add({
        severity: "success",
        summary: "Profile Image Uploaded",
        detail: "Your new profile image was uploaded successfully.",
        life: 3000,
      });
      // Possibly reload user or store response
    })
    .catch((err) => {
      toast.add({
        severity: "error",
        summary: "Upload failed",
        detail: "Something went wrong while uploading your image.",
        life: 3000,
      });
    });
}

// Save profile changes
const errors = ref(null);
const loading = ref(false);
async function saveProfile() {
  errors.value = null;
  loading.value = true;
  try {
    // Adjust as needed:
    const resp = await api.post("/account/update", {
      name: nameField.value,
      email: emailField.value,
    });

    toast.add({
      severity: "success",
      summary: "Profile Updated",
      detail: "Your account information has been updated.",
      life: 3000,
    });

    // Optionally refresh auth user in store
    await authStore.fetchUser();
  } catch (error) {
    errors.value = "Error updating profile. Please try again.";
  } finally {
    loading.value = false;
  }
}

// Delete account
async function deleteAccount() {
  const confirmDelete = confirm(
    "Are you sure you want to delete your account? This action cannot be undone."
  );
  if (!confirmDelete) return;

  try {
    await api.post("/account/delete");
    // or: await api.delete('/account')
    // Then log out
    await authStore.logout();
    router.push({ name: "login" });
  } catch (error) {
    toast.add({
      severity: "error",
      summary: "Error",
      detail: "Could not delete account. Please try again later.",
      life: 3000,
    });
  }
}

// Logout
function logout() {
  authStore.logout().then(() => {
    router.push({ name: "login" });
  });
}

/* DataTable filters for placeholders (Activity Log, Payment, Billing).
   Replace or remove if you don't need table filtering here.
*/
const filtersActivity = ref({
  global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});
const filtersPayment = ref({
  global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});
const filtersBilling = ref({
  global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});
</script>

<template>
  <div class="card p-6 card-container">
    <h2 class="text-2xl font-semibold mb-4">Account Settings</h2>

    <Tabs>
      <!-- PROFILE TAB -->
      <TabPanel header="Profile">
        <!-- Error Alert -->
        <Message
          v-if="errors"
          severity="error"
          icon="pi pi-exclamation-circle"
          class="mb-4"
        >
          {{ errors }}
        </Message>

        <div class="grid grid-cols-12 gap-4 items-start">
          <!-- Left side: Profile Image upload -->
          <div
            class="col-span-12 md:col-span-4 flex flex-col items-center gap-4"
          >
            <div class="flex flex-col items-center">
              <Avatar
                icon="pi pi-user"
                class="mr-2"
                size="xlarge"
                :image="authStore.user?.avatar"
              />
              <span class="mt-2 font-semibold">
                {{ authStore.user?.name || "Your Name" }}
              </span>
            </div>

            <!-- Upload: using PrimeVue FileUpload with advanced mode -->
            <FileUpload
              name="profileImage"
              mode="basic"
              auto="true"
              accept="image/*"
              chooseLabel="Upload Profile Image"
              customUpload
              @upload="onUploadProfileImage"
            />
          </div>

          <!-- Right side: Profile Info -->
          <div class="col-span-6 md:col-span-6 flex flex-col gap-4 items-start">
            <ValidFormElement label="Name">
              <InputText v-model="nameField" type="text" class="w-full" />
            </ValidFormElement>

            <ValidFormElement label="Email">
              <InputText v-model="emailField" type="email" class="w-full" />
            </ValidFormElement>

            <div class="flex flex-row gap-2 justify-end mt-2">
              <Button
                label="Save"
                icon="pi pi-check"
                :loading="loading"
                @click="saveProfile"
              />
            </div>
          </div>
        </div>
      </TabPanel>

      <!-- ACTIVITY LOG TAB -->
      <TabPanel header="Activity Log">
        <DataTable
          :value="activityLog"
          :filters="filtersActivity"
          dataKey="date"
          :paginator="true"
          :rows="5"
          tableStyle="min-width: 20rem"
        >
          <template #header>
            <div class="flex justify-between gap-2 mt-6">
              <h5 class="m-0 text-lg font-semibold">Recent Logins</h5>
            </div>
          </template>

          <Column field="date" header="Date/Time" sortable />
          <Column field="ip" header="IP Address" sortable />
          <Column field="location" header="Location" sortable />
        </DataTable>
      </TabPanel>

      <!-- PAYMENT METHODS TAB -->
      <TabPanel header="Payment Methods">
        <DataTable
          :value="paymentMethods"
          dataKey="type"
          :filters="filtersPayment"
          :paginator="true"
          :rows="5"
          tableStyle="min-width: 20rem"
        >
          <template #header>
            <div class="flex justify-between gap-2 mt-6">
              <h5 class="m-0 text-lg font-semibold">Your Saved Cards</h5>
            </div>
          </template>

          <Column field="type" header="Card" />
          <Column field="expires" header="Expiry" />
          <Column header="Default">
            <template #body="slotProps">
              <i
                v-if="slotProps.data.default"
                class="pi pi-check text-green-500"
              ></i>
            </template>
          </Column>
        </DataTable>
        <div class="mt-6 flex justify-end">
          <Button icon="pi pi-plus" label="Add New Card" severity="secondary" />
        </div>
      </TabPanel>

      <!-- BILLING HISTORY TAB -->
      <TabPanel header="Billing History">
        <DataTable
          :value="billingHistory"
          dataKey="id"
          :filters="filtersBilling"
          :paginator="true"
          :rows="5"
          tableStyle="min-width: 20rem"
        >
          <template #header>
            <div class="flex justify-between gap-2 mt-6">
              <h5 class="m-0 text-lg font-semibold">Invoices</h5>
            </div>
          </template>

          <Column field="id" header="Invoice ID" />
          <Column field="amount" header="Amount" />
          <Column field="date" header="Date" sortable />
          <Column field="status" header="Status" />
        </DataTable>
      </TabPanel>

      <!-- DANGER ZONE TAB -->
      <TabPanel header="Danger Zone">
        <div class="p-4 border border-red-300 rounded-md mb-4 mt-8">
          <h5 class="text-red-600 font-semibold text-lg mb-2">
            Delete Account
          </h5>
          <p class="text-sm mb-4">
            Deleting your account is permanent and will remove all of your data.
            This cannot be undone.
          </p>
          <Button
            label="Delete My Account"
            icon="pi pi-trash"
            severity="danger"
            @click="deleteAccount"
          />
        </div>
      </TabPanel>
    </Tabs>
  </div>
</template>

<style scoped>
/* Example Tailwind classes are used for spacing, typography, etc.
   Adjust them to match your project's style.
*/
</style>
