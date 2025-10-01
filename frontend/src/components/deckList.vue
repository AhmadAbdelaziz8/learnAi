<script setup>
defineProps({
  decks: {
    type: Array,
    required: true,
  },
});

// Helper functions for deck metadata
const getDeckCategory = (topic) => {
  if (
    topic.toLowerCase().includes("javascript") ||
    topic.toLowerCase().includes("programming")
  )
    return "Programming";
  if (
    topic.toLowerCase().includes("spanish") ||
    topic.toLowerCase().includes("language")
  )
    return "Language";
  if (
    topic.toLowerCase().includes("geography") ||
    topic.toLowerCase().includes("world")
  )
    return "Geography";
  if (
    topic.toLowerCase().includes("biology") ||
    topic.toLowerCase().includes("medical")
  )
    return "Medicine";
  if (topic.toLowerCase().includes("art")) return "Art";
  return "General";
};

const getDifficultyLevel = (cardCount) => {
  if (cardCount <= 10)
    return { level: "Beginner", color: "bg-green-100 text-green-800" };
  if (cardCount <= 20)
    return { level: "Intermediate", color: "bg-yellow-100 text-yellow-800" };
  return { level: "Advanced", color: "bg-red-100 text-red-800" };
};

const getRandomProgress = () => Math.floor(Math.random() * 100);
const getRandomTimeAgo = () => {
  const times = ["2 hours ago", "1 day ago", "3 days ago", "1 week ago"];
  return times[Math.floor(Math.random() * times.length)];
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
    <p class="text-gray-600">Why not create your first AI-generated deck?</p>
  </div>

  <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <div
      v-for="deck in decks"
      :key="deck.id"
      class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg hover:border-green-200 transition-all duration-300 overflow-hidden group"
    >
      <!-- Card Header -->
      <div class="p-6 pb-4">
        <div class="flex items-start justify-between mb-4">
          <div class="flex items-center gap-3">
            <span
              :class="getDifficultyLevel(deck.flashcards.length).color"
              class="px-2 py-1 text-xs font-medium rounded-full"
            >
              {{ getDeckCategory(deck.topic) }}
            </span>
            <span
              :class="getDifficultyLevel(deck.flashcards.length).color"
              class="px-2 py-1 text-xs font-medium rounded-full"
            >
              {{ getDifficultyLevel(deck.flashcards.length).level }}
            </span>
          </div>
          <div class="text-xs text-gray-500">
            {{ getRandomTimeAgo() }}
          </div>
        </div>

        <h3
          class="text-lg font-bold text-gray-900 mb-2 group-hover:text-green-600 transition-colors"
        >
          {{ deck.topic }}
        </h3>

        <p class="text-sm text-gray-600 mb-4">
          {{
            deck.flashcards.length > 0
              ? `Core concepts every learner should know`
              : `Essential knowledge for beginners`
          }}
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
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
            {{ getRandomTimeAgo() }}
          </div>
        </div>
      </div>

      <!-- Progress Bar (if has cards) -->
      <div v-if="deck.flashcards.length > 0" class="px-6 pb-4">
        <div
          class="flex items-center justify-between text-xs text-gray-600 mb-2"
        >
          <span>Progress</span>
          <span>{{ getRandomProgress() }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
          <div
            class="bg-green-500 h-2 rounded-full transition-all duration-300"
            :style="{ width: getRandomProgress() + '%' }"
          ></div>
        </div>
      </div>

      <!-- Action Button -->
      <div class="p-6 pt-4 bg-gray-50 border-t border-gray-100">
        <button
          class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
        >
          {{ deck.flashcards.length > 0 ? "Start Learning" : "Create Cards" }}
        </button>
      </div>
    </div>
  </div>
</template>
