<script setup>
import { onMounted, ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useDeckStore } from '@/store/deckStore'

const route = useRoute()
const router = useRouter()
const deckStore = useDeckStore()

const currentCardIndex = ref(0)
const isFlipped = ref(false)

const deckId = route.params.id
const deck = computed(() => deckStore.currentDeck)
const currentCard = computed(() => {
  if (deck.value && deck.value.flashcards && deck.value.flashcards.length > 0) {
    return deck.value.flashcards[currentCardIndex.value]
  }
  return null
})

const progress = computed(() => {
  if (!deck.value?.flashcards?.length) return 0
  return Math.round(((currentCardIndex.value + 1) / deck.value.flashcards.length) * 100)
})

onMounted(async () => {
  await deckStore.fetchDeck(deckId)
})

const flipCard = () => {
  isFlipped.value = !isFlipped.value
}

const nextCard = () => {
  if (deck.value && currentCardIndex.value < deck.value.flashcards.length - 1) {
    currentCardIndex.value++
    isFlipped.value = false
  }
}

const prevCard = () => {
  if (currentCardIndex.value > 0) {
    currentCardIndex.value--
    isFlipped.value = false
  }
}

const goHome = () => {
  router.push('/')
}
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-green-50 via-green-100 to-green-200">
    <!-- Header -->
    <div class="bg-white/80 backdrop-blur-sm shadow-sm">
      <div class="container mx-auto px-6 py-4">
        <div class="flex items-center justify-between">
          <button 
            @click="goHome"
            class="flex items-center gap-2 text-gray-600 hover:text-green-600 transition-colors"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Decks
          </button>
          
          <div v-if="deck" class="text-center">
            <h1 class="text-xl font-bold text-gray-900">{{ deck.topic }}</h1>
            <p class="text-sm text-gray-600">{{ deck.flashcards?.length || 0 }} Cards</p>
          </div>
          
          <div class="w-20"></div> <!-- Spacer -->
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="deckStore.loading" class="flex items-center justify-center min-h-96">
      <div class="text-center">
        <div class="w-12 h-12 border-4 border-green-200 border-t-green-500 rounded-full animate-spin mx-auto mb-4"></div>
        <p class="text-gray-600">Loading flashcards...</p>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="deckStore.error" class="flex items-center justify-center min-h-96">
      <div class="text-center">
        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 mb-2">Failed to load deck</h3>
        <p class="text-gray-600 mb-4">{{ deckStore.error.message || 'Something went wrong' }}</p>
        <button 
          @click="goHome"
          class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors"
        >
          Go Back
        </button>
      </div>
    </div>

    <!-- No Cards State -->
    <div v-else-if="!deck?.flashcards?.length" class="flex items-center justify-center min-h-96">
      <div class="text-center max-w-md">
        <div class="w-20 h-20 bg-gradient-to-br from-green-100 to-green-200 rounded-full flex items-center justify-center mx-auto mb-6">
          <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-3">No flashcards yet</h3>
        <p class="text-gray-600 mb-6">This deck "{{ deck?.topic }}" doesn't have any flashcards to study. Flashcards can be generated from documents or created manually.</p>
        <div class="space-y-3">
          <button 
            @click="goHome"
            class="w-full bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-medium transition-colors"
          >
            Browse Other Decks
          </button>
          <button 
            @click="goHome"
            class="w-full bg-white hover:bg-gray-50 text-gray-700 px-6 py-3 rounded-lg font-medium border border-gray-300 transition-colors"
          >
            Create Flashcards
          </button>
        </div>
      </div>
    </div>

    <!-- Study Interface -->
    <div v-else class="container mx-auto px-6 py-8">
      <!-- Progress Bar -->
      <div class="mb-8">
        <div class="flex items-center justify-between text-sm text-gray-600 mb-2">
          <span>Progress</span>
          <span>{{ currentCardIndex + 1 }} of {{ deck.flashcards.length }}</span>
        </div>
        <div class="w-full bg-white/50 rounded-full h-3">
          <div 
            class="bg-gradient-to-r from-green-500 to-green-600 h-3 rounded-full transition-all duration-500"
            :style="{ width: progress + '%' }"
          ></div>
        </div>
        <div class="text-center mt-2 text-sm font-medium text-gray-700">{{ progress }}% Complete</div>
      </div>

      <!-- Flashcard -->
      <div class="max-w-2xl mx-auto">
        <div 
          class="relative h-96 cursor-pointer"
          @click="flipCard"
        >
          <!-- Card Container -->
          <div 
            class="absolute inset-0 w-full h-full transition-transform duration-500 preserve-3d"
            :class="{ 'rotate-y-180': isFlipped }"
          >
            <!-- Front (Question) -->
            <div class="absolute inset-0 w-full h-full backface-hidden">
              <div class="bg-white rounded-2xl shadow-lg p-8 h-full flex flex-col justify-center items-center text-center border-2 border-green-200">
                <div class="mb-4">
                  <span class="bg-green-100 text-green-800 text-sm font-medium px-3 py-1 rounded-full">
                    Question
                  </span>
                </div>
                <h2 class="text-xl md:text-2xl font-semibold text-gray-900 leading-relaxed">
                  {{ currentCard?.question }}
                </h2>
                <p class="text-sm text-gray-500 mt-6">Click to reveal answer</p>
              </div>
            </div>

            <!-- Back (Answer) -->
            <div class="absolute inset-0 w-full h-full backface-hidden rotate-y-180">
              <div class="bg-green-50 rounded-2xl shadow-lg p-8 h-full flex flex-col justify-center items-center text-center border-2 border-green-300">
                <div class="mb-4">
                  <span class="bg-green-200 text-green-800 text-sm font-medium px-3 py-1 rounded-full">
                    Answer
                  </span>
                </div>
                <h2 class="text-xl md:text-2xl font-semibold text-gray-900 leading-relaxed">
                  {{ currentCard?.answer }}
                </h2>
                <p class="text-sm text-gray-600 mt-6">Click to see question again</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Navigation Controls -->
        <div class="flex items-center justify-between mt-8">
          <button
            @click="prevCard"
            :disabled="currentCardIndex === 0"
            class="flex items-center gap-2 px-6 py-3 bg-white hover:bg-gray-50 border border-gray-300 rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Previous
          </button>

          <button
            @click="flipCard"
            class="px-6 py-3 bg-green-500 hover:bg-green-600 text-white rounded-lg font-medium transition-colors"
          >
            {{ isFlipped ? 'Show Question' : 'Show Answer' }}
          </button>

          <button
            @click="nextCard"
            :disabled="currentCardIndex === deck.flashcards.length - 1"
            class="flex items-center gap-2 px-6 py-3 bg-white hover:bg-gray-50 border border-gray-300 rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Next
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </button>
        </div>

        <!-- Completion Message -->
        <div v-if="currentCardIndex === deck.flashcards.length - 1 && isFlipped" class="text-center mt-8">
          <div class="bg-green-100 border border-green-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-green-800 mb-2">🎉 Deck Complete!</h3>
            <p class="text-green-700 mb-4">You've reviewed all {{ deck.flashcards.length }} flashcards in this deck.</p>
            <button 
              @click="goHome"
              class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg font-medium transition-colors"
            >
              Back to Decks
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.preserve-3d {
  transform-style: preserve-3d;
}

.backface-hidden {
  backface-visibility: hidden;
}

.rotate-y-180 {
  transform: rotateY(180deg);
}
</style>