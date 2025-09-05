<template>
  <div class="relative">
    <!-- Categories Dropdown Trigger -->
    <button
      @click="toggleDropdown"
      @keydown.enter="toggleDropdown"
      @keydown.space.prevent="toggleDropdown"
      @keydown.escape="closeDropdown"
      :aria-expanded="isOpen"
      aria-haspopup="true"
      class="flex items-center text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
      :class="{ 'text-blue-600': isOpen }"
    >
      <span>Categories</span>
      <svg 
        class="ml-1 h-4 w-4 transition-transform duration-200" 
        :class="{ 'rotate-180': isOpen }"
        fill="none" 
        stroke="currentColor" 
        viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>

    <!-- Dropdown Menu -->
    <transition
      enter-active-class="transition ease-out duration-100"
      enter-from-class="transform opacity-0 scale-95"
      enter-to-class="transform opacity-100 scale-100"
      leave-active-class="transition ease-in duration-75"
      leave-from-class="transform opacity-100 scale-100"
      leave-to-class="transform opacity-0 scale-95"
    >
      <div
        v-if="isOpen"
        class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50"
        role="menu"
        aria-orientation="vertical"
        @keydown.escape="closeDropdown"
      >
        <!-- Loading State -->
        <div v-if="categoriesStore.loading" class="px-4 py-6 text-center">
          <div class="spinner h-6 w-6 mx-auto mb-2"></div>
          <p class="text-gray-600 text-sm">Loading categories...</p>
        </div>

        <!-- Error State -->
        <div v-else-if="categoriesStore.error" class="px-4 py-6 text-center">
          <svg class="h-8 w-8 text-red-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <p class="text-red-800 text-sm">Failed to load categories</p>
        </div>

        <!-- Categories List -->
        <div v-else-if="sortedCategories.length > 0">
          <!-- Header -->
          <div class="px-4 py-2 border-b border-gray-100">
            <h3 class="text-sm font-medium text-gray-900">Browse by Category</h3>
            <p class="text-xs text-gray-600 mt-1">{{ sortedCategories.length }} categories available</p>
          </div>

          <!-- Categories Grid -->
          <div class="max-h-96 overflow-y-auto">
            <div class="grid grid-cols-2 gap-1 p-2">
              <router-link
                v-for="category in sortedCategories"
                :key="category.id"
                :to="{ name: 'public.category', params: { slug: category.slug } }"
                @click="selectCategory(category)"
                class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors group"
                role="menuitem"
              >
                <div class="flex-shrink-0 mr-3">
                  <div class="h-8 w-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                    <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                  </div>
                </div>
                
                <div class="flex-1 min-w-0">
                  <div class="flex items-center justify-between">
                    <h4 class="text-sm font-medium text-gray-900 group-hover:text-blue-600 transition-colors line-clamp-1">
                      {{ category.name }}
                    </h4>
                    <span class="ml-2 text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
                      {{ category.posts_count || 0 }}
                    </span>
                  </div>
                  
                  <p v-if="category.description" class="text-xs text-gray-600 mt-1 line-clamp-1">
                    {{ category.description }}
                  </p>
                </div>
              </router-link>
            </div>
          </div>

          <!-- View All Categories -->
          <div class="border-t border-gray-100 p-2">
            <router-link
              to="/"
              @click="closeDropdown"
              class="flex items-center justify-center w-full p-2 text-sm text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors"
            >
              <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
              </svg>
              View All Posts
            </router-link>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="px-4 py-6 text-center">
          <svg class="h-12 w-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
          </svg>
          <h4 class="text-sm font-medium text-gray-900 mb-1">No categories found</h4>
          <p class="text-xs text-gray-600">Categories will appear here when they're created.</p>
        </div>
      </div>
    </transition>
  </div>

  <!-- Overlay to close dropdown -->
  <div
    v-if="isOpen"
    class="fixed inset-0 z-40"
    @click="closeDropdown"
  ></div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useCategoriesStore } from '@/stores/categories.js'

const categoriesStore = useCategoriesStore()
const isOpen = ref(false)

const sortedCategories = computed(() => {
  return [...categoriesStore.categories].sort((a, b) => {
    // Sort by post count (descending), then by name (ascending)
    const countDiff = (b.posts_count || 0) - (a.posts_count || 0)
    if (countDiff !== 0) return countDiff
    return a.name.localeCompare(b.name)
  })
})

const toggleDropdown = () => {
  isOpen.value = !isOpen.value
  
  // Load categories if not already loaded and dropdown is opening
  if (isOpen.value && categoriesStore.categories.length === 0 && !categoriesStore.loading) {
    categoriesStore.fetchCategories()
  }
}

const closeDropdown = () => {
  isOpen.value = false
}

const selectCategory = (category) => {
  closeDropdown()
  // The router-link will handle navigation
}

// Close dropdown on escape key
const handleKeydown = (event) => {
  if (event.key === 'Escape' && isOpen.value) {
    closeDropdown()
  }
}

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
  const dropdown = event.target.closest('[role="menu"]')
  const trigger = event.target.closest('button[aria-haspopup="true"]')
  
  if (!dropdown && !trigger && isOpen.value) {
    closeDropdown()
  }
}

onMounted(() => {
  document.addEventListener('keydown', handleKeydown)
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleKeydown)
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
.spinner {
  border: 2px solid #f3f3f3;
  border-top: 2px solid #3498db;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.line-clamp-1 {
  overflow: hidden;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 1;
}

.rotate-180 {
  transform: rotate(180deg);
}
</style>