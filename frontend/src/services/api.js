import axios from "axios";

export const apiClient = axios.create({
  baseURL: "http://localhost:80/api", // ✅ Fixed: was :8000
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },
});
