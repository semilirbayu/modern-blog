<template>
  <div>
    <!-- Category Header -->
    <div v-if="currentCategory" class="bg-gradient-to-br from-gray-50 to-blue-50 rounded-2xl p-8 mb-12">
      <div class="max-w-4xl mx-auto text-center">
        <div class="inline-flex items-center bg-blue-100 text-blue-800 px-4 py-2 rounded-full text-sm font-medium mb-4">
          <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
          </svg>
          Category
        </div>
        
        <h1 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-4">
          {{ currentCategory.name }}
        </h1>
        
        <div v-if="currentCategory.description" class="text-xl text-gray-600 mb-6 leading-relaxed max-w-2xl mx-auto">
          {{ currentCategory.description }}
        </div>

        <div class="flex justify-center items-center space-x-8 text-sm text-gray-600">
          <div class="flex items-center">
            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            {{ categoryStats.totalPosts }} {{ categoryStats.totalPosts === 1 ? 'post' : 'posts' }}
          </div>
          
          <div v-if="categoryStats.latestPost" class="flex items-center">
            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Latest: {{ formatDate(categoryStats.latestPost) }}
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content Area -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-12">
      <!-- Posts Content -->
      <div class="lg:col-span-3">
        <!-- Filters and Sorting -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 space-y-4 sm:space-y-0">
          <div class="flex items-center space-x-4">
            <span class="text-gray-700 font-medium">Sort by:</span>
            <select 
              v-model="sortBy" 
              @change="applySorting"
              class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="newest">Newest First</option>
              <option value="oldest">Oldest First</option>
              <option value="title">Title (A-Z)</option>
              <option value="title-desc">Title (Z-A)</option>
            </select>
          </div>

          <div v-if="!loading && categoryPosts.length > 0" class="text-sm text-gray-600">
            Showing {{ ((pagination.current_page - 1) * pagination.per_page) + 1 }} - 
            {{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }} 
            of {{ pagination.total }} posts
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="text-center py-12">
          <div class="spinner h-8 w-8 mx-auto mb-4"></div>
          <p class="text-gray-600">Loading posts...</p>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="text-center py-12">
          <div class="bg-red-50 border border-red-200 rounded-lg p-8 max-w-md mx-auto">
            <div class="flex items-center justify-center mb-4">
              <svg class="h-12 w-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <p class="text-red-800 text-center mb-6">{{ error }}</p>
            <div class="text-center">
              <router-link 
                to="/" 
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors inline-block"
              >
                ← Back to Home
              </router-link>
            </div>
          </div>
        </div>

        <!-- Category Posts -->
        <div v-else-if="categoryPosts.length > 0">
          <div class="grid gap-8 md:grid-cols-2">
            <PostCard
              v-for="post in categoryPosts"
              :key="post.id"
              :post="post"
              :show-category="false"
              class="transform hover:scale-105 transition-transform duration-300"
            />
          </div>

          <!-- Enhanced Pagination -->
          <div v-if="pagination.last_page > 1" class="mt-16">
            <Pagination
              :current-page="pagination.current_page"
              :last-page="pagination.last_page"
              :total="pagination.total"
              :per-page="pagination.per_page"
              @page-changed="loadPage"
              :loading="loading"
            />
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-16">
          <div class="text-gray-400 mb-6">
            <svg class="h-20 w-20 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 01-2-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
            </svg>
          </div>
          <h3 class="text-xl font-medium text-gray-900 mb-2">No posts in this category</h3>
          <p class="text-gray-600 mb-6">This category doesn't have any posts yet. Check back soon for new content!</p>
          <div class="flex justify-center space-x-4">
            <router-link 
              to="/" 
              class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors"
            >
              Browse All Posts
            </router-link>
            <button 
              @click="reloadCategory" 
              class="bg-gray-200 hover:bg-gray-300 text-gray-900 px-6 py-3 rounded-lg font-medium transition-colors"
            >
              Refresh
            </button>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <aside class="lg:col-span-1">
        <div class="sticky top-24 space-y-8">
          <!-- Category Info -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Category Info</h3>
            <div class="space-y-3 text-sm">
              <div class="flex justify-between">
                <span class="text-gray-600">Total posts</span>
                <span class="text-gray-900 font-medium">{{ categoryStats.totalPosts }}</span>
              </div>
              <div v-if="categoryStats.latestPost" class="flex justify-between">
                <span class="text-gray-600">Latest post</span>
                <span class="text-gray-900">{{ formatDate(categoryStats.latestPost) }}</span>
              </div>
              <div v-if="categoryStats.oldestPost" class="flex justify-between">
                <span class="text-gray-600">First post</span>
                <span class="text-gray-900">{{ formatDate(categoryStats.oldestPost) }}</span>
              </div>
            </div>
          </div>

          <!-- Related Categories -->
          <div v-if="relatedCategories.length > 0" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Related Categories</h3>
            <div class="space-y-2">
              <router-link
                v-for="category in relatedCategories"
                :key="category.id"
                :to="{ name: 'public.category', params: { slug: category.slug } }"
                class="block p-3 rounded-lg border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-all duration-200 group"
              >
                <div class="flex items-center justify-between">
                  <span class="text-gray-900 group-hover:text-blue-600 font-medium">{{ category.name }}</span>
                  <span class="text-gray-500 text-sm">{{ category.posts_count || 0 }}</span>
                </div>
                <p v-if="category.description" class="text-gray-600 text-xs mt-1 line-clamp-2">
                  {{ category.description }}
                </p>
              </router-link>
            </div>
          </div>

          <!-- Back to Categories -->
          <div class="bg-gradient-to-br from-gray-50 to-blue-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Explore More</h3>
            <p class="text-gray-600 text-sm mb-4">Discover content from other categories and topics.</p>
            <router-link 
              to="/" 
              class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg text-sm font-medium transition-colors inline-block text-center"
            >
              Browse All Posts
            </router-link>
          </div>
        </div>
      </aside>
    </div>
  </div>
</template>

<script setup>
import { onMounted, watch, ref, computed } from 'vue'
import { useRoute } from 'vue-router'
import { storeToRefs } from 'pinia'
import { useCategoriesStore } from '@/stores/categories.js'
import { formatDate, extractExcerpt } from '@/utils/index.js'
import { useSEO } from '@/composables/useSEO.js'
import PostCard from '@/components/public/PostCard.vue'
import Pagination from '@/components/public/Pagination.vue'

const route = useRoute()
const categoriesStore = useCategoriesStore()

const { currentCategory, categoryPosts, loading, error, pagination, categories } = storeToRefs(categoriesStore)

const sortBy = ref('newest')
const { updateTitle, updateMeta } = useSEO()

const categoryStats = computed(() => {
  if (!categoryPosts.value.length) {
    return { totalPosts: 0, latestPost: null, oldestPost: null }
  }

  const posts = [...categoryPosts.value]
  const sortedByDate = posts.sort((a, b) => new Date(b.published_at || b.created_at) - new Date(a.published_at || a.created_at))
  
  return {
    totalPosts: pagination.value.total || categoryPosts.value.length,
    latestPost: sortedByDate[0]?.published_at || sortedByDate[0]?.created_at,
    oldestPost: sortedByDate[sortedByDate.length - 1]?.published_at || sortedByDate[sortedByDate.length - 1]?.created_at
  }
})

const relatedCategories = computed(() => {
  if (!categories.value.length || !currentCategory.value) return []
  
  return categories.value
    .filter(cat => cat.id !== currentCategory.value.id)
    .slice(0, 4)
})

const loadCategory = async () => {
  try {
    await categoriesStore.fetchCategoryBySlug(route.params.slug)
    await loadCategoryPosts(1)
    
    if (currentCategory.value) {
      updateTitle(`${currentCategory.value.name} - ModernBlog`)
      updateMeta('description', 
        currentCategory.value.description || 
        `Browse all posts in the ${currentCategory.value.name} category on ModernBlog.`
      )
    }
  } catch (err) {
    console.error('Failed to load category:', err)
  }
}

const loadCategoryPosts = async (page = 1) => {
  try {
    const params = { 
      page, 
      per_page: 12,
      sort: sortBy.value 
    }
    
    await categoriesStore.fetchCategoryPosts(route.params.slug, params)
  } catch (err) {
    console.error('Failed to load category posts:', err)
  }
}

const loadPage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    loadCategoryPosts(page)
    
    // Scroll to top of posts section
    const postsSection = document.querySelector('.lg\\:col-span-3')
    if (postsSection) {
      postsSection.scrollIntoView({ behavior: 'smooth', block: 'start' })
    }
  }
}

const applySorting = () => {
  loadCategoryPosts(1)
}

const reloadCategory = () => {
  loadCategory()
}

// Watch for route changes
watch(() => route.params.slug, () => {
  if (route.name === 'public.category') {
    sortBy.value = 'newest' // Reset sorting when changing categories
    loadCategory()
  }
})

onMounted(async () => {
  await Promise.all([
    loadCategory(),
    categoriesStore.fetchPublicCategories() // Load all categories for related categories
  ])
})
</script>