<template>
  <div class="flex items-center justify-between">
    <!-- Results Info -->
    <div class="flex-1 flex justify-between sm:hidden">
      <!-- Mobile pagination -->
      <button
        @click="goToPrevious"
        :disabled="!hasPrevious"
        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        Previous
      </button>
      <button
        @click="goToNext"
        :disabled="!hasNext"
        class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        Next
      </button>
    </div>
    
    <!-- Desktop pagination -->
    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
      <div class="flex items-center gap-4">
        <!-- Results summary -->
        <p class="text-sm text-gray-700">
          Showing
          <span class="font-medium">{{ startItem }}</span>
          to
          <span class="font-medium">{{ endItem }}</span>
          of
          <span class="font-medium">{{ pagination.total }}</span>
          results
        </p>
        
        <!-- Per page selector -->
        <div class="flex items-center gap-2">
          <label for="perPage" class="text-sm text-gray-700">Show:</label>
          <select
            id="perPage"
            :value="pagination.perPage"
            @change="handlePerPageChange($event.target.value)"
            class="border border-gray-300 rounded-md text-sm px-2 py-1 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          >
            <option v-for="option in perPageOptions" :key="option" :value="option">
              {{ option }}
            </option>
          </select>
          <span class="text-sm text-gray-700">per page</span>
        </div>
      </div>
      
      <!-- Page navigation -->
      <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
        <!-- Previous button -->
        <button
          @click="goToPrevious"
          :disabled="!hasPrevious"
          class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <span class="sr-only">Previous</span>
          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </button>

        <!-- Page numbers -->
        <template v-for="page in visiblePages" :key="page">
          <!-- Page number -->
          <button
            v-if="typeof page === 'number'"
            @click="goToPage(page)"
            :class="[
              'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
              page === pagination.currentPage
                ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
            ]"
          >
            {{ page }}
          </button>
          
          <!-- Ellipsis -->
          <span
            v-else
            class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700"
          >
            ...
          </span>
        </template>

        <!-- Next button -->
        <button
          @click="goToNext"
          :disabled="!hasNext"
          class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <span class="sr-only">Next</span>
          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </button>
      </nav>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  pagination: {
    type: Object,
    required: true,
    validator: (pagination) => {
      return pagination &&
        typeof pagination.currentPage === 'number' &&
        typeof pagination.lastPage === 'number' &&
        typeof pagination.total === 'number' &&
        typeof pagination.perPage === 'number'
    }
  },
  maxVisiblePages: {
    type: Number,
    default: 7
  },
  perPageOptions: {
    type: Array,
    default: () => [10, 15, 25, 50, 100]
  }
})

const emit = defineEmits(['pageChange', 'perPageChange'])

// Computed properties
const hasPrevious = computed(() => props.pagination.currentPage > 1)
const hasNext = computed(() => props.pagination.currentPage < props.pagination.lastPage)

const startItem = computed(() => {
  if (props.pagination.total === 0) return 0
  return (props.pagination.currentPage - 1) * props.pagination.perPage + 1
})

const endItem = computed(() => {
  const end = props.pagination.currentPage * props.pagination.perPage
  return Math.min(end, props.pagination.total)
})

const visiblePages = computed(() => {
  const current = props.pagination.currentPage
  const last = props.pagination.lastPage
  const maxVisible = props.maxVisiblePages
  
  if (last <= maxVisible) {
    // Show all pages if total pages is less than or equal to maxVisible
    return Array.from({ length: last }, (_, i) => i + 1)
  }
  
  const pages = []
  const halfVisible = Math.floor(maxVisible / 2)
  
  // Always show first page
  pages.push(1)
  
  let start = Math.max(2, current - halfVisible)
  let end = Math.min(last - 1, current + halfVisible)
  
  // Adjust range if we're near the beginning or end
  if (current <= halfVisible + 1) {
    end = maxVisible - 2
  } else if (current >= last - halfVisible) {
    start = last - maxVisible + 3
  }
  
  // Add ellipsis after first page if needed
  if (start > 2) {
    pages.push('...')
  }
  
  // Add middle pages
  for (let i = start; i <= end; i++) {
    if (i > 1 && i < last) {
      pages.push(i)
    }
  }
  
  // Add ellipsis before last page if needed
  if (end < last - 1) {
    pages.push('...')
  }
  
  // Always show last page (if more than 1 page)
  if (last > 1) {
    pages.push(last)
  }
  
  return pages
})

// Navigation methods
const goToPage = (page) => {
  if (page !== props.pagination.currentPage && page >= 1 && page <= props.pagination.lastPage) {
    emit('pageChange', page)
  }
}

const goToPrevious = () => {
  if (hasPrevious.value) {
    goToPage(props.pagination.currentPage - 1)
  }
}

const goToNext = () => {
  if (hasNext.value) {
    goToPage(props.pagination.currentPage + 1)
  }
}

const handlePerPageChange = (newPerPage) => {
  const perPage = parseInt(newPerPage)
  if (perPage !== props.pagination.perPage && props.perPageOptions.includes(perPage)) {
    emit('perPageChange', perPage)
  }
}
</script>

<style scoped>
/* Additional styles for better visual consistency */
.pagination-button {
  @apply relative inline-flex items-center px-4 py-2 border text-sm font-medium;
  @apply bg-white border-gray-300 text-gray-500 hover:bg-gray-50;
  @apply disabled:opacity-50 disabled:cursor-not-allowed;
}

.pagination-button.active {
  @apply z-10 bg-blue-50 border-blue-500 text-blue-600;
}

.pagination-button:focus {
  @apply outline-none ring-2 ring-offset-2 ring-blue-500;
}

/* Ensure proper spacing and alignment */
nav[aria-label="Pagination"] {
  @apply inline-flex rounded-md shadow-sm -space-x-px;
}

nav[aria-label="Pagination"] > * {
  @apply relative;
}

nav[aria-label="Pagination"] > *:first-child {
  @apply rounded-l-md;
}

nav[aria-label="Pagination"] > *:last-child {
  @apply rounded-r-md;
}
</style>