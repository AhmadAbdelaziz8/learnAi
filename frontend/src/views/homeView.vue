<script setup>
import { onMounted } from "vue";
import { useDeckStore } from "@/store/deckStore";
import deckList from "@/components/deckList.vue";

const deckStore = useDeckStore();

// when the component is mounted, fetch the decks
onMounted(async () => {
  await deckStore.fetchDecks();
});
</script>

<template>
  <main
    class="min-h-screen bg-gradient-to-br from-green-50 via-green-100 to-green-200"
  >
    <!-- Header Section -->
    <div class="bg-white/50 backdrop-blur-sm shadow-sm">
      <div class="container mx-auto px-6 py-8">
        <div class="flex items-center gap-4 mb-6">
          <div
            class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center"
          >
            <svg
              class="w-7 h-7 text-white"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"
              />
            </svg>
          </div>
          <div>
            <h1 class="text-3xl font-bold text-gray-900">
              Learning <span class="text-green-500"> companion </span>
            </h1>

            <p class="text-gray-600 mt-1">
              Personalized learning powered by artificial intelligence
            </p>
          </div>
        </div>

        <!-- Stats -->
        <div class="flex items-center gap-8 text-sm">
          <div class="flex items-center gap-2">
            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
            <span class="text-gray-700"
              >{{ deckStore.decks.length }} Decks Available</span
            >
          </div>
        </div>
      </div>
    </div>

    <!-- Content Section -->
    <div class="container mx-auto px-6 py-8">
      <deck-list :decks="deckStore.decks" />
    </div>
  </main>
</template>
