// /fe_velvue/src/service/apiService.js

import axios from "axios";
import Cookies from "js-cookie";
import router from "@/router";

const API_URL = import.meta.env.VITE_API_URL; // or your server URL

export const api = axios.create({
  baseURL: API_URL + "/api",
  withCredentials: true,
  withXSRFToken: true,
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },
});

const setCSRFToken = () => {
  return api.get(`/sanctum/csrf-cookie`);
};

const onRequest = async (config) => {
  if (
    ["post", "put", "patch", "delete"].includes(config.method) &&
    !Cookies.get("XSRF-TOKEN")
  ) {
    return setCSRFToken().then(() => config);
  }
  return config;
};

api.interceptors.request.use(onRequest, (error) => {
  return Promise.reject(error);
});

// Response interceptor for 401s or 500s
api.interceptors.response.use(
  (response) => response,
  async (error) => {
    // Network error or server unreachable
    if (!error.response) {
      return Promise.reject(error);
    }

    // If expired CSRF token (419)
    if (error.response.status === 419) {
      await setCSRFToken();
      return api.request(error.config);
    }

    // If 500 -> show error page
    if (error.response.status === 500) {
      router.push({ name: "error" }); // or "500"
    }

    // If 401 -> go to login (unless already on login or error pages)
    if (
      error.response.status === 401 &&
      !["login", "404", "error", "500"].includes(router.currentRoute.value.name)
    ) {
      router.push({ name: "login" });
    }

    return Promise.reject(error);
  }
);

export default api;
