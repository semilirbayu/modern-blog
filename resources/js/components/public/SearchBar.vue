<template>
  <div class="relative" :class="mobile ? 'w-full' : 'w-80'">
    <!-- Search Input -->
    <div class="relative">
      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
      </div>
      
      <input
        v-model="query"
        type="text"
        :placeholder="mobile ? 'Search...' : 'Search posts...'"
        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors"
        :class="mobile ? 'text-sm' : ''"
        @input="handleInput"
        @focus="showDropdown = true"
        @keydown="handleKeydown"
        ref="searchInput"
      />
      
      <!-- Clear Button -->
      <button
        v-if="query"
        @click="clearSearch"
        class="absolute inset-y-0 right-0 pr-3 flex items-center"
      >
        <svg class="h-4 w-4 text-gray-400 hover:text-gray-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <!-- Search Dropdown -->
    <div
      v-if="showDropdown && (searchResults.length > 0 || loading || query)"
      class="absolute top-full left-0 right-0 mt-1 bg-white rounded-lg shadow-lg border border-gray-200 z-50 max-h-96 overflow-y-auto"
    >
      <!-- Loading State -->
      <div v-if="loading" class="p-4 text-center">
        <div class="spinner h-5 w-5 mx-auto mb-2"></div>
        <p class="text-gray-600 text-sm">Searching...</p>
      </div>

      <!-- Search Results -->
      <div v-else-if="searchResults.length > 0">
        <div class="p-3 border-b border-gray-100 bg-gray-50">
          <p class="text-xs text-gray-600 uppercase tracking-wider font-medium">
            {{ searchResults.length }} result{{ searchResults.length !== 1 ? 's' : '' }} found
          </p>
        </div>
        
        <div class="max-h-80 overflow-y-auto">
          <router-link
            v-for="(post, index) in searchResults"
            :key="post.id"
            :to="{ name: 'public.post', params: { slug: post.slug } }"
            @click="selectResult(post, index)"
            :class="[
              'block p-4 hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-b-0',
              highlightedIndex === index ? 'bg-blue-50 border-blue-100' : ''
            ]"
          >
            <div class="flex items-start space-x-3">
              <div class="flex-shrink-0 mt-1">
                <div class="h-8 w-8 bg-gradient-to-br from-gray-200 to-gray-300 rounded flex items-center justify-center">
                  <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                </div>
              </div>
              
              <div class="flex-1 min-w-0">
                <h4 class="text-sm font-medium text-gray-900 line-clamp-1" v-html="highlightMatch(post.title)"></h4>
                <p class="text-xs text-gray-600 line-clamp-2 mt-1" v-html="highlightMatch(post.excerpt || extractExcerpt(post.content))"></p>
                
                <div class="flex items-center mt-2 space-x-3 text-xs text-gray-500">
                  <span v-if="post.category" class="flex items-center">
                    <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    {{ post.category.name }}
                  </span>
                  <span>{{ formatDate(post.published_at || post.created_at) }}</span>
                </div>
              </div>
            </div>
          </router-link>
        </div>

        <!-- View All Results -->
        <div v-if="searchResults.length >= 5" class="p-3 border-t border-gray-100 bg-gray-50">
          <button
            @click="viewAllResults"
            class="w-full text-center text-blue-600 hover:text-blue-800 text-sm font-medium transition-colors"
          >
            View all results for "{{ query }}"
          </button>
        </div>
      </div>

      <!-- No Results -->
      <div v-else-if="query && !loading" class="p-6 text-center">
        <svg class="h-12 w-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <h4 class="text-sm font-medium text-gray-900 mb-1">No posts found</h4>
        <p class="text-xs text-gray-600">Try searching with different keywords.</p>
      </div>

      <!-- Recent Searches -->
      <div v-else-if="!query && recentSearches.length > 0" class="p-3">
        <div class="mb-2">
          <p class="text-xs text-gray-600 uppercase tracking-wider font-medium">Recent Searches</p>
        </div>
        <div class="space-y-1">
          <button
            v-for="search in recentSearches.slice(0, 5)"
            :key="search"
            @click="selectRecentSearch(search)"
            class="flex items-center w-full text-left p-2 hover:bg-gray-50 rounded text-sm text-gray-700 transition-colors"
          >
            <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ search }}
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Overlay to close dropdown -->
  <div
    v-if="showDropdown"
    class="fixed inset-0 z-40"
    @click="closeDropdown"
  ></div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { usePostsStore } from '@/stores/posts.js'
import { formatDate } from '@/utils/index.js'
import { refDebounced } from '@vueuse/core'

const props = defineProps({
  mobile: {
    type: Boolean,
    default: false
  }
})

const router = useRouter()
const postsStore = usePostsStore()

const query = ref('')
const debouncedQuery = refDebounced(query, 300)
const showDropdown = ref(false)
const searchResults = ref([])
const loading = ref(false)
const highlightedIndex = ref(-1)
const searchInput = ref(null)
const recentSearches = ref([])

// Load recent searches from localStorage
onMounted(() => {
  const stored = localStorage.getItem('modernblog_recent_searches')
  if (stored) {
    try {
      recentSearches.value = JSON.parse(stored)
    } catch (e) {
      console.warn('Failed to parse recent searches from localStorage')
    }
  }
})

// Save recent searches to localStorage
const saveRecentSearches = () => {
  localStorage.setItem('modernblog_recent_searches', JSON.stringify(recentSearches.value))
}

// Add to recent searches
const addToRecentSearches = (searchTerm) => {
  if (!searchTerm.trim()) return
  
  const searches = recentSearches.value.filter(s => s !== searchTerm)
  searches.unshift(searchTerm)
  recentSearches.value = searches.slice(0, 10) // Keep only last 10 searches
  saveRecentSearches()
}

const handleInput = async () => {
  if (debouncedQuery.value.length < 2) {
    searchResults.value = []
    return
  }

  loading.value = true
  try {
    const response = await postsStore.searchPosts(debouncedQuery.value, { per_page: 8 })
    searchResults.value = response.data || []
  } catch (error) {
    console.error('Search failed:', error)
    searchResults.value = []
  } finally {
    loading.value = false
  }
}

const handleKeydown = (event) => {
  const resultsCount = searchResults.value.length

  switch (event.key) {
    case 'ArrowDown':
      event.preventDefault()
      highlightedIndex.value = Math.min(highlightedIndex.value + 1, resultsCount - 1)
      break
    
    case 'ArrowUp':
      event.preventDefault()
      highlightedIndex.value = Math.max(highlightedIndex.value - 1, -1)
      break
    
    case 'Enter':
      event.preventDefault()
      if (highlightedIndex.value >= 0 && searchResults.value[highlightedIndex.value]) {
        selectResult(searchResults.value[highlightedIndex.value], highlightedIndex.value)
      } else if (query.value.trim()) {
        viewAllResults()
      }
      break
    
    case 'Escape':
      closeDropdown()
      break
  }
}

const selectResult = (post, index) => {
  addToRecentSearches(query.value)
  closeDropdown()
  router.push({ name: 'public.post', params: { slug: post.slug } })
}

const selectRecentSearch = (search) => {
  query.value = search
  showDropdown.value = true
  handleInput()
}

const clearSearch = () => {
  query.value = ''
  searchResults.value = []
  highlightedIndex.value = -1
  searchInput.value?.focus()
}

const closeDropdown = () => {
  showDropdown.value = false
  highlightedIndex.value = -1
}

const viewAllResults = () => {
  if (query.value.trim()) {
    addToRecentSearches(query.value)
    closeDropdown()
    router.push({ name: 'public.home', query: { search: query.value } })
  }
}

const extractExcerpt = (content) => {
  if (!content) return ''
  const div = document.createElement('div')
  div.innerHTML = content
  const text = div.textContent || div.innerText || ''
  return text.length > 100 ? text.substring(0, 100) + '...' : text
}

const highlightMatch = (text) => {
  if (!query.value || !text) return text
  
  const regex = new RegExp(`(${query.value})`, 'gi')
  return text.replace(regex, '<mark class="bg-yellow-200 px-1 rounded">$1</mark>')
}

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    closeDropdown()
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
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

.line-clamp-2 {
  overflow: hidden;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 2;
}

mark {
  background-color: #fef3c7;
  padding: 0.125rem 0.25rem;
  border-radius: 0.25rem;
}
</style>