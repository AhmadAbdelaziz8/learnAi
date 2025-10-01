import { apiClient } from "./api";

export const deckService = {
  // Create a new deck with PDF upload
  async createDeck(deckData) {
    const formData = new FormData();
    formData.append('topic', deckData.topic);
    formData.append('pdf_file', deckData.pdfFile);
    formData.append('user_id', deckData.userId);

    try {
      const response = await apiClient.post('/decks', formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      });
      return response.data;
    } catch (error) {
      throw error;
    }
  },

  // Get all decks
  async getDecks() {
    try {
      const response = await apiClient.get('/decks');
      return response.data;
    } catch (error) {
      throw error;
    }
  },

  // Get specific deck by ID
  async getDeck(id) {
    try {
      const response = await apiClient.get(`/decks/${id}`);
      return response.data;
    } catch (error) {
      throw error;
    }
  }
};