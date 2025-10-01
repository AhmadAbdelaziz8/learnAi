import { defineStore } from "pinia";
import { apiClient } from "@/services/api";
import { deckService } from "@/services/deckService";

export const useDeckStore = defineStore("deck", {
  state: () => ({
    decks: [],
    currentDeck: null,
    loading: false,
    error: null,
    creating: false,
    createError: null,
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

    async fetchDeck(id) {
      this.loading = true;
      this.error = null;
      this.currentDeck = null;
      try {
        const response = await apiClient.get(`/decks/${id}`);
        this.currentDeck = response.data;
      } catch (error) {
        this.error = error;
        console.error("Error fetching deck:", error);
      } finally {
        this.loading = false;
      }
    },

    async createDeck(deckData) {
      this.creating = true;
      this.createError = null;
      try {
        const response = await deckService.createDeck(deckData);
        // Add the new deck to the existing decks array
        this.decks.push(response.deck);
        return response.deck;
      } catch (error) {
        this.createError = error;
        console.error("Error creating deck:", error);
        throw error;
      } finally {
        this.creating = false;
      }
    },

    clearCurrentDeck() {
      this.currentDeck = null;
    },

    clearErrors() {
      this.error = null;
      this.createError = null;
    },
  },
});
