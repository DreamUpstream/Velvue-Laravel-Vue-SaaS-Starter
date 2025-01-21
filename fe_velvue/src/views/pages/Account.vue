<!-- /fe_velvue/src/views/pages/Account.vue -->
<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import api from "@/service/apiService";
import { FilterMatchMode } from "@primevue/core/api";

const router = useRouter();
const authStore = useAuthStore();

// Basic user form fields
const nameField = ref("");
const emailField = ref("");
const profileImage = ref(null);
const uploadingImage = ref(false);
const successMessage = ref("");
const errorMessage = ref("");

// We assume the user is loaded from the store or from an API
// onMounted, you can load the user data from the store or an API.
onMounted(() => {
  if (authStore.user) {
    nameField.value = authStore.user.name;
    emailField.value = authStore.user.email;
    profileImage.value = authStore.user.avatar;
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

async function uploadProfileImage(event) {
  console.log("Upload event has been triggered", event);
  const file = event.files?.[0];
  if (!file) {
    errorMessage.value = "No file selected. Please select an image to upload.";
  }

  const formData = new FormData();
  formData.append("image", file);
  formData.append("entity", "avatars");

  try {
    uploadingImage.value = true;
    const response = await api.post("/upload", formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });

    if (response.data.success) {
      profileImage.value = `${import.meta.env.VITE_API_URL}/storage/${response.data.path}`; // Update image preview
      successMessage.value =
        "Your profile picture has been updated successfully.";

      authStore.user.avatar = profileImage.value;
    }
  } catch (error) {
    errorMessage.value =
      "Failed to upload your profile image. Please try again.";
    console.error(error);
  } finally {
    uploadingImage.value = false;
  }
}

const loading = ref(false);
async function saveProfile() {
  successMessage.value = "";
  errorMessage.value = "";
  try {
    const response = await api.post("/account/update", {
      name: nameField.value,
      email: emailField.value,
      avatar: profileImage.value, // Save updated avatar
    });

    successMessage.value =
      "Your account details have been updated successfully.";

    // Refresh the user details
    await authStore.fetchUser();
  } catch (error) {
    errorMessage.value = "Failed to update your profile. Please try again.";
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
    await authStore.logout();
    router.push({ name: "login" });
  } catch (error) {
    errorMessage.value = "Could not delete account. Please try again later.";
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

    <Message
      v-if="successMessage"
      severity="success"
      icon="pi pi-check"
      class="mb-4"
    >
      {{ successMessage }}
    </Message>
    <Message
      v-if="errorMessage"
      severity="error"
      icon="pi pi-times-circle"
      class="mb-4"
    >
      {{ errorMessage }}
    </Message>
    <Tabs>
      <!-- PROFILE TAB -->
      <TabPanel header="Profile">
        <div class="grid grid-cols-12 gap-6">
          <!-- Profile Image -->
          <div
            class="col-span-12 md:col-span-4 flex flex-col items-center gap-4"
          >
            <Avatar
              v-if="profileImage"
              :image="profileImage"
              class="w-32 h-32 rounded-full border border-gray-300 shadow-lg"
            />

            <Avatar
              v-else
              icon="pi pi-user"
              class="mr-2"
              size="xlarge"
              :image="authStore.user?.avatar"
            />
            <FileUpload
              mode="basic"
              auto
              name="profileImage"
              accept="image/*"
              chooseLabel="Change Picture"
              customUpload
              @uploader="uploadProfileImage"
              :disabled="uploadingImage"
            />
          </div>

          <!-- Profile Details -->
          <div class="col-span-12 md:col-span-8">
            <div class="mb-4">
              <label class="block text-sm font-medium mb-1">Name</label>
              <InputText v-model="nameField" class="w-full" />
            </div>
            <div class="mb-4">
              <label class="block text-sm font-medium mb-1">Email</label>
              <InputText v-model="emailField" type="email" class="w-full" />
            </div>
            <Button
              label="Save Changes"
              icon="pi pi-check"
              @click="saveProfile"
            />
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
