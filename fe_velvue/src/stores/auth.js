import { defineStore } from "pinia";
import api from "@/service/apiService";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: null,
    loading: false,
  }),
  getters: {
    isLoggedIn: (state) => !!state.user,
  },
  actions: {
    async fetchUser() {
      this.loading = true;
      try {
        const { data } = await api.get("/user");
        this.user = data.user;
        return data.user;
      } catch (error) {
        this.user = null;
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async login(payload) {
      this.loading = true;
      try {
        await api.post("/login", payload);
        await this.fetchUser();
        return true;
      } catch (error) {
        if (error.response && error.response.data.errors) {
          throw error.response.data.errors;
        }
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async logout() {
      this.loading = true;
      try {
        await api.post("/logout");
        this.user = null;
      } catch (error) {
        console.error(error);
      } finally {
        this.loading = false;
      }
    },
  },
});
