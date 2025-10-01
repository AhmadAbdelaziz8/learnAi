<script setup>
import { useRouter } from "vue-router";

defineProps({
  decks: {
    type: Array,
    required: true,
  },
});

const router = useRouter();

// Navigation functions
const goToStudy = (deckId) => {
  router.push(`/study/${deckId}`);
};

const goToAddDeck = () => {
  router.push('/add-deck');
};

// Helper functions for deck metadata

const formatDate = (dateString) => {
  const date = new Date(dateString);
  const now = new Date();
  const diffTime = Math.abs(now - date);
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

  if (diffDays === 1) return "1 day ago";
  if (diffDays < 7) return `${diffDays} days ago`;
  if (diffDays < 30) return `${Math.ceil(diffDays / 7)} weeks ago`;
  return `${Math.ceil(diffDays / 30)} months ago`;
};
</script>

<template>
  <div v-if="!decks.length" class="text-center py-16">
    <div
      class="w-24 h-24 bg-gray-100 rounded-full mx-auto mb-4 flex items-center justify-center"
    >
      <svg
        class="w-12 h-12 text-gray-400"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
        />
      </svg>
    </div>
    <h3 class="text-xl font-semibold text-gray-900 mb-2">No decks found</h3>
    <p class="text-gray-600 mb-6">Why not create your first AI-generated deck?</p>
    <button
      @click="goToAddDeck"
      class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 inline-flex items-center gap-2"
    >
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
      </svg>
      Create Your First Deck
    </button>
  </div>

  <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <div
      v-for="deck in decks"
      :key="deck.id"
      @click="goToStudy(deck.id)"
      class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg hover:border-green-200 transition-all duration-300 overflow-hidden group cursor-pointer transform hover:scale-105"
    >
      <!-- Card Header -->
      <div class="p-6 pb-4">
        <div class="flex items-start justify-between mb-4">
          <div class="flex items-center gap-3">
            <span
              class="bg-green-100 text-green-800 px-2 py-1 text-xs font-medium rounded-full"
            >
              {{ deck.flashcards.length }} Cards
            </span>
          </div>
          <div class="text-xs text-gray-500">
            {{ formatDate(deck.created_at) }}
          </div>
        </div>

        <h3
          class="text-lg font-bold text-gray-900 mb-2 group-hover:text-green-600 transition-colors"
        >
          {{ deck.topic }}
        </h3>

        <p class="text-sm text-gray-600 mb-4">
          Created by {{ deck.user.name }}
        </p>

        <!-- Stats -->
        <div class="flex items-center justify-between text-sm mb-4">
          <div class="flex items-center gap-4">
            <div class="text-2xl font-bold text-gray-900">
              {{ deck.flashcards.length }}
            </div>
            <div class="text-xs text-gray-500">Cards</div>
          </div>
          <div class="flex items-center gap-1 text-xs text-gray-500">
            <svg
              class="w-4 h-4"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
              />
            </svg>
            {{ deck.user.name }}
          </div>
        </div>
      </div>

      <!-- Action Button -->
      <div class="p-6 pt-4 bg-gray-50 border-t border-gray-100">
        <button
          @click.stop="goToStudy(deck.id)"
          :class="
            deck.flashcards.length > 0
              ? 'bg-green-500 hover:bg-green-600 text-white'
              : 'bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-300'
          "
          class="w-full font-semibold py-3 px-4 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
        >
          {{ deck.flashcards.length > 0 ? "Start Learning" : "View Deck" }}
        </button>
      </div>
    </div>
  </div>
</template>
