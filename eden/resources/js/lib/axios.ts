import axios from "axios";

const api = axios.create({
  baseURL: "/api",
  headers: {
    "X-Requested-With": "XMLHttpRequest",
    "Content-Type": "application/json",
  },
  withCredentials: true, // important if using Laravel auth/sanctum
});

export default api;