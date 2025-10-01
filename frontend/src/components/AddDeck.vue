<script setup>
import { ref, computed } from "vue";
import { useDeckStore } from "@/store/deckStore";
import { useRouter } from "vue-router";

const deckStore = useDeckStore();
const router = useRouter();

// Form data
const formData = ref({
  topic: "",
  pdfFile: null,
  userId: 1, // For now, we'll hardcode user ID to 1
});

// File input reference
const fileInput = ref(null);

// Form validation
const isValidForm = computed(() => {
  return (
    formData.value.topic.trim() &&
    formData.value.pdfFile &&
    formData.value.userId
  );
});

// File handling
const handleFileChange = (event) => {
  const file = event.target.files[0];
  if (file) {
    if (file.type !== "application/pdf") {
      alert("Please select a PDF file");
      event.target.value = "";
      return;
    }
    if (file.size > 10 * 1024 * 1024) {
      // 10MB limit
      alert("File size must be less than 10MB");
      event.target.value = "";
      return;
    }
    formData.value.pdfFile = file;
  }
};

const removeFile = () => {
  formData.value.pdfFile = null;
  if (fileInput.value) {
    fileInput.value.value = "";
  }
};

// Form submission
const submitForm = async () => {
  if (!isValidForm.value) return;

  try {
    const newDeck = await deckStore.createDeck({
      topic: formData.value.topic,
      pdfFile: formData.value.pdfFile,
      userId: formData.value.userId,
    });

    // Reset form
    formData.value.topic = "";
    formData.value.pdfFile = null;
    if (fileInput.value) {
      fileInput.value.value = "";
    }

    // Navigate to the newly created deck
    router.push(`/study/${newDeck.id}`);
  } catch (error) {
    console.error("Error creating deck:", error);
  }
};

// Helper function to format file size
const formatFileSize = (bytes) => {
  if (bytes === 0) return "0 Bytes";
  const k = 1024;
  const sizes = ["Bytes", "KB", "MB", "GB"];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
};
</script>

<template>
  <div class="max-w-2xl mx-auto p-6">
    <!-- Navigation Header -->
    <div class="flex items-center gap-4 mb-6">
      <button
        @click="router.push('/')"
        class="flex items-center gap-2 text-gray-600 hover:text-gray-900 transition-colors group"
      >
        <svg
          class="w-5 h-5 group-hover:translate-x-[-2px] transition-transform"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M15 19l-7-7 7-7"
          />
        </svg>
        <span class="text-sm font-medium">Back to Decks</span>
      </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
      <!-- Header -->
      <div class="text-center mb-8">
        <div
          class="w-16 h-16 bg-green-100 rounded-full mx-auto mb-4 flex items-center justify-center"
        >
          <svg
            class="w-8 h-8 text-green-600"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 6v6m0 0v6m0-6h6m-6 0H6"
            />
          </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Create New Deck</h1>
        <p class="text-gray-600">
          Upload a PDF and let AI generate flashcards for you
        </p>
      </div>

      <!-- Error Message -->
      <div
        v-if="deckStore.createError"
        class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg"
      >
        <div class="flex items-center">
          <svg
            class="w-5 h-5 text-red-500 mr-2"
            fill="currentColor"
            viewBox="0 0 20 20"
          >
            <path
              fill-rule="evenodd"
              d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
              clip-rule="evenodd"
            />
          </svg>
          <span class="text-red-700 text-sm">
            Failed to create deck. Please try again.
          </span>
        </div>
      </div>

      <form @submit.prevent="submitForm" class="space-y-6">
        <!-- Topic Input -->
        <div>
          <label
            for="topic"
            class="block text-sm font-medium text-gray-700 mb-2"
          >
            Deck Topic *
          </label>
          <input
            id="topic"
            v-model="formData.topic"
            type="text"
            required
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
            placeholder="e.g., Introduction to Biology, Math Fundamentals..."
            :disabled="deckStore.creating"
          />
        </div>

        <!-- PDF Upload -->
        <div>
          <label
            for="pdf-upload"
            class="block text-sm font-medium text-gray-700 mb-2"
          >
            PDF File *
          </label>

          <!-- Upload Area -->
          <div v-if="!formData.pdfFile" class="relative">
            <input
              id="pdf-upload"
              ref="fileInput"
              type="file"
              accept=".pdf"
              @change="handleFileChange"
              class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
              :disabled="deckStore.creating"
            />
            <div
              class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-green-400 transition-colors"
            >
              <svg
                class="w-12 h-12 text-gray-400 mx-auto mb-4"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
                />
              </svg>
              <p class="text-gray-600 mb-2">
                <span class="font-medium text-green-600">Click to upload</span>
                or drag and drop
              </p>
              <p class="text-sm text-gray-500">PDF files only (max 10MB)</p>
            </div>
          </div>

          <!-- Selected File Preview -->
          <div v-else class="border border-gray-300 rounded-lg p-4">
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <svg
                  class="w-8 h-8 text-red-500 mr-3"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path
                    fill-rule="evenodd"
                    d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                    clip-rule="evenodd"
                  />
                </svg>
                <div>
                  <p class="font-medium text-gray-900">
                    {{ formData.pdfFile.name }}
                  </p>
                  <p class="text-sm text-gray-500">
                    {{ formatFileSize(formData.pdfFile.size) }}
                  </p>
                </div>
              </div>
              <button
                type="button"
                @click="removeFile"
                class="text-gray-400 hover:text-red-500 transition-colors"
                :disabled="deckStore.creating"
              >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path
                    fill-rule="evenodd"
                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                    clip-rule="evenodd"
                  />
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="flex gap-4">
          <button
            type="button"
            @click="router.push('/')"
            class="flex-1 py-3 px-4 border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 flex items-center justify-center gap-2"
            :disabled="deckStore.creating"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Home
          </button>
          <button
            type="submit"
            :disabled="!isValidForm || deckStore.creating"
            class="flex-1 py-3 px-4 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
          >
            <div
              v-if="deckStore.creating"
              class="flex items-center justify-center"
            >
              <svg
                class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                fill="none"
                viewBox="0 0 24 24"
              >
                <circle
                  class="opacity-25"
                  cx="12"
                  cy="12"
                  r="10"
                  stroke="currentColor"
                  stroke-width="4"
                />
                <path
                  class="opacity-75"
                  fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                />
              </svg>
              Creating Deck...
            </div>
            <span v-else>Create Deck</span>
          </button>
        </div>
      </form>

      <!-- Info Section -->
      <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex">
          <svg
            class="w-5 h-5 text-blue-500 mt-0.5 mr-2"
            fill="currentColor"
            viewBox="0 0 20 20"
          >
            <path
              fill-rule="evenodd"
              d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
              clip-rule="evenodd"
            />
          </svg>
          <div class="text-sm text-blue-700">
            <p class="font-medium mb-1">How it works:</p>
            <ul class="list-disc list-inside space-y-1 text-blue-600">
              <li>Upload a PDF with educational content</li>
              <li>Our AI analyzes the text and creates flashcards</li>
              <li>Review and study your generated deck</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
