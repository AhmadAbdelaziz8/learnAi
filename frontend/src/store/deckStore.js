import { defineStore } from "pinia";
import { apiClient } from "@/services/api";

export const useDeckStore = defineStore("deck", {
  state: () => ({
    decks: [],
    loading: false,
    error: null,
  }),
  actions: {
    async fetchDecks() {
      this.loading = true;
      this.error = null;
      try {
        const response = await apiClient.get("/decks");
        this.decks = response.data;
      } catch (error) {
        this.error = error;
        console.error("Error fetching decks:", error);
      } finally {
        this.loading = false;
      }
    },
  },
});
