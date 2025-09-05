<template>
  <div class="card">
    <div class="card-body">
      <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center">
        <!-- Search Input -->
        <div class="flex-1 min-w-0">
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
            </div>
            <input
              :value="filters.search"
              @input="updateFilter('search', $event.target.value)"
              type="text"
              class="input-field pl-10"
              placeholder="Search posts by title..."
            />
            <button
              v-if="filters.search"
              @click="updateFilter('search', '')"
              class="absolute inset-y-0 right-0 pr-3 flex items-center"
            >
              <svg class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- Status Filter -->
        <div class="w-full lg:w-auto">
          <select
            :value="filters.status"
            @change="updateFilter('status', $event.target.value)"
            class="input-field"
          >
            <option value="all">All Status</option>
            <option value="draft">Draft</option>
            <option value="published">Published</option>
          </select>
        </div>

        <!-- Category Filter -->
        <div class="w-full lg:w-auto">
          <select
            :value="filters.category"
            @change="updateFilter('category', $event.target.value)"
            class="input-field"
          >
            <option value="all">All Categories</option>
            <option 
              v-for="category in categories" 
              :key="category.id" 
              :value="category.id"
            >
              {{ category.name }}
            </option>
          </select>
        </div>

        <!-- Date Range Filter -->
        <div class="flex gap-2 w-full lg:w-auto">
          <div class="flex-1 lg:w-auto">
            <input
              :value="filters.dateFrom"
              @change="updateFilter('dateFrom', $event.target.value)"
              type="date"
              class="input-field"
              title="From date"
            />
          </div>
          <div class="flex-1 lg:w-auto">
            <input
              :value="filters.dateTo"
              @change="updateFilter('dateTo', $event.target.value)"
              type="date"
              class="input-field"
              title="To date"
            />
          </div>
        </div>

        <!-- Clear Filters Button -->
        <div class="flex gap-2">
          <button
            v-if="hasActiveFilters"
            @click="clearAllFilters"
            class="btn btn-secondary whitespace-nowrap"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            Clear Filters
          </button>
          
          <!-- Toggle Advanced Filters -->
          <button
            @click="showAdvanced = !showAdvanced"
            class="btn btn-outline whitespace-nowrap"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
            </svg>
            {{ showAdvanced ? 'Hide' : 'Show' }} Advanced
          </button>
        </div>
      </div>

      <!-- Advanced Filters (collapsible) -->
      <div 
        v-if="showAdvanced"
        class="mt-4 pt-4 border-t border-gray-200"
      >
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <!-- Author Filter -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Author</label>
            <select
              :value="filters.author"
              @change="updateFilter('author', $event.target.value)"
              class="input-field"
            >
              <option value="all">All Authors</option>
              <option 
                v-for="author in authors" 
                :key="author.id" 
                :value="author.id"
              >
                {{ author.name }}
              </option>
            </select>
          </div>

          <!-- Sort Options -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
            <select
              :value="filters.sortBy"
              @change="updateFilter('sortBy', $event.target.value)"
              class="input-field"
            >
              <option value="created_at">Created Date</option>
              <option value="updated_at">Modified Date</option>
              <option value="published_at">Published Date</option>
              <option value="title">Title</option>
            </select>
          </div>

          <!-- Sort Direction -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Sort Direction</label>
            <select
              :value="filters.sortDirection"
              @change="updateFilter('sortDirection', $event.target.value)"
              class="input-field"
            >
              <option value="desc">Newest First</option>
              <option value="asc">Oldest First</option>
            </select>
          </div>

          <!-- Posts Per Page -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Posts Per Page</label>
            <select
              :value="filters.perPage"
              @change="updateFilter('perPage', parseInt($event.target.value))"
              class="input-field"
            >
              <option value="10">10</option>
              <option value="15">15</option>
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Active Filters Summary -->
      <div v-if="hasActiveFilters" class="mt-4 flex flex-wrap gap-2">
        <span class="text-sm text-gray-600">Active filters:</span>
        
        <span 
          v-if="filters.search"
          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
        >
          Search: "{{ filters.search }}"
          <button @click="updateFilter('search', '')" class="ml-1.5 hover:text-blue-600">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </span>

        <span 
          v-if="filters.status !== 'all'"
          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"
        >
          Status: {{ filters.status }}
          <button @click="updateFilter('status', 'all')" class="ml-1.5 hover:text-green-600">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </span>

        <span 
          v-if="filters.category !== 'all'"
          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800"
        >
          Category: {{ getCategoryName(filters.category) }}
          <button @click="updateFilter('category', 'all')" class="ml-1.5 hover:text-purple-600">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </span>

        <span 
          v-if="filters.author !== 'all'"
          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800"
        >
          Author: {{ getAuthorName(filters.author) }}
          <button @click="updateFilter('author', 'all')" class="ml-1.5 hover:text-yellow-600">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </span>

        <span 
          v-if="filters.dateFrom || filters.dateTo"
          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800"
        >
          Date: {{ formatDateRange() }}
          <button @click="clearDateFilters" class="ml-1.5 hover:text-red-600">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

const props = defineProps({
  filters: {
    type: Object,
    required: true
  },
  categories: {
    type: Array,
    default: () => []
  },
  authors: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['updateFilters', 'clearFilters'])

const showAdvanced = ref(false)

const hasActiveFilters = computed(() => {
  return props.filters.search !== '' ||
         props.filters.status !== 'all' ||
         props.filters.category !== 'all' ||
         props.filters.author !== 'all' ||
         props.filters.dateFrom ||
         props.filters.dateTo
})

const getCategoryName = (categoryId) => {
  const category = props.categories.find(c => c.id.toString() === categoryId.toString())
  return category ? category.name : 'Unknown'
}

const getAuthorName = (authorId) => {
  const author = props.authors.find(a => a.id.toString() === authorId.toString())
  return author ? author.name : 'Unknown'
}

const formatDateRange = () => {
  if (props.filters.dateFrom && props.filters.dateTo) {
    return `${formatDate(props.filters.dateFrom)} to ${formatDate(props.filters.dateTo)}`
  } else if (props.filters.dateFrom) {
    return `From ${formatDate(props.filters.dateFrom)}`
  } else if (props.filters.dateTo) {
    return `Until ${formatDate(props.filters.dateTo)}`
  }
  return ''
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString()
}

const updateFilter = (key, value) => {
  emit('updateFilters', {
    ...props.filters,
    [key]: value
  })
}

const clearAllFilters = () => {
  emit('clearFilters')
}

const clearDateFilters = () => {
  emit('updateFilters', {
    ...props.filters,
    dateFrom: '',
    dateTo: ''
  })
}

onMounted(() => {
  // Initialize default filters if they don't exist
  const defaultFilters = {
    search: '',
    status: 'all',
    category: 'all',
    author: 'all',
    dateFrom: '',
    dateTo: '',
    sortBy: 'created_at',
    sortDirection: 'desc',
    perPage: 15
  }

  // Merge with existing filters to ensure all properties exist
  const mergedFilters = { ...defaultFilters, ...props.filters }
  
  // Only emit if there are differences
  const hasChanges = Object.keys(defaultFilters).some(
    key => mergedFilters[key] !== props.filters[key]
  )
  
  if (hasChanges) {
    emit('updateFilters', mergedFilters)
  }
})
</script>