<template>
  <div>
    <!-- Hero Section -->
    <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-2xl p-8 mb-12">
      <div class="text-center">
        <h1 class="text-5xl font-bold text-gray-900 mb-6">
          Welcome to ModernBlog
        </h1>
        <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto leading-relaxed">
          A modern blogging platform built with Laravel and Vue.js. 
          Discover amazing content, connect with writers, and share your thoughts with the world.
        </p>
        <div class="flex justify-center space-x-4">
          <router-link 
            to="/about" 
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors"
          >
            Learn More
          </router-link>
          <router-link 
            to="/contact" 
            class="bg-white hover:bg-gray-50 text-gray-900 px-6 py-3 rounded-lg font-medium border border-gray-300 transition-colors"
          >
            Get in Touch
          </router-link>
        </div>
      </div>
    </div>

    <!-- Featured Posts Section -->
    <section v-if="featuredPosts.length > 0" class="mb-16">
      <div class="flex items-center justify-between mb-8">
        <h2 class="text-3xl font-bold text-gray-900">Featured Posts</h2>
        <div class="h-1 bg-blue-600 rounded flex-1 ml-6 max-w-32"></div>
      </div>
      
      <div class="grid gap-8 lg:grid-cols-2">
        <PostCard 
          v-for="post in featuredPosts" 
          :key="post.id"
          :post="post"
          :featured="true"
          class="transform hover:scale-105 transition-transform duration-300"
        />
      </div>
    </section>

    <!-- Category Filter Chips -->
    <section v-if="categoriesStore.categories.length > 0" class="mb-12">
      <div class="flex flex-wrap gap-3">
        <button
          @click="selectedCategory = null"
          :class="[
            'px-4 py-2 rounded-full text-sm font-medium transition-all duration-200',
            selectedCategory === null
              ? 'bg-blue-600 text-white shadow-lg'
              : 'bg-white text-gray-700 border border-gray-300 hover:border-blue-300 hover:shadow-md'
          ]"
        >
          All Categories
        </button>
        <button
          v-for="category in categoriesStore.categories"
          :key="category.id"
          @click="selectedCategory = category.id"
          :class="[
            'px-4 py-2 rounded-full text-sm font-medium transition-all duration-200',
            selectedCategory === category.id
              ? 'bg-blue-600 text-white shadow-lg'
              : 'bg-white text-gray-700 border border-gray-300 hover:border-blue-300 hover:shadow-md'
          ]"
        >
          {{ category.name }}
          <span v-if="category.posts_count" class="ml-1 opacity-75">({{ category.posts_count }})</span>
        </button>
      </div>
    </section>

    <!-- Main Content Area -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-12">
      <!-- Posts Content -->
      <div class="lg:col-span-3">
        <!-- Loading State -->
        <div v-if="loading" class="text-center py-12">
          <div class="spinner h-8 w-8 mx-auto mb-4"></div>
          <p class="text-gray-600">Loading posts...</p>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="text-center py-12">
          <div class="bg-red-50 border border-red-200 rounded-lg p-6 max-w-md mx-auto">
            <div class="flex items-center">
              <svg class="h-6 w-6 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <p class="text-red-800">{{ error }}</p>
            </div>
          </div>
        </div>

        <!-- Posts Section -->
        <section v-else-if="filteredPosts.length > 0">
          <div class="flex items-center justify-between mb-8">
            <h2 class="text-3xl font-bold text-gray-900">
              {{ selectedCategory ? getCategoryName(selectedCategory) : 'Latest Posts' }}
            </h2>
            <div class="text-sm text-gray-500">
              {{ filteredPosts.length }} {{ filteredPosts.length === 1 ? 'post' : 'posts' }}
            </div>
          </div>

          <!-- Posts Grid -->
          <div class="grid gap-8 md:grid-cols-2">
            <PostCard 
              v-for="post in displayedPosts" 
              :key="post.id"
              :post="post"
              class="transform hover:scale-105 transition-transform duration-300"
            />
          </div>

          <!-- Load More / Infinite Scroll -->
          <div v-if="hasMorePosts" class="text-center mt-12">
            <button 
              v-if="!useInfiniteScrollEnabled"
              @click="loadMore"
              :disabled="loadingMore"
              class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="loadingMore" class="spinner h-4 w-4 mr-2"></span>
              Load More Posts
            </button>
            
            <div 
              v-else-if="loadingMore" 
              class="flex justify-center items-center py-8"
            >
              <div class="spinner h-6 w-6 mr-2"></div>
              <span class="text-gray-600">Loading more posts...</span>
            </div>
          </div>
        </section>

        <!-- Empty State -->
        <div v-else class="text-center py-16">
          <div class="text-gray-400 mb-6">
            <svg class="h-20 w-20 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 01-2-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
            </svg>
          </div>
          <h3 class="text-xl font-medium text-gray-900 mb-2">
            {{ selectedCategory ? 'No posts in this category' : 'No posts yet' }}
          </h3>
          <p class="text-gray-600 mb-6">
            {{ selectedCategory ? 'Try selecting a different category.' : 'Check back soon for amazing content!' }}
          </p>
          <button 
            v-if="selectedCategory"
            @click="selectedCategory = null"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors"
          >
            View All Posts
          </button>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="lg:col-span-1">
        <Sidebar />
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref, computed, onUnmounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { storeToRefs } from 'pinia'
import { usePostsStore } from '@/stores/posts.js'
import { useCategoriesStore } from '@/stores/categories.js'
import { formatDate, extractExcerpt } from '@/utils/index.js'
import { useInfiniteScroll } from '@/composables/useInfiniteScroll.js'
import PostCard from '@/components/public/PostCard.vue'
import Sidebar from '@/components/public/Sidebar.vue'

const route = useRoute()
const postsStore = usePostsStore()
const categoriesStore = useCategoriesStore()
const loadingMore = ref(false)
const selectedCategory = ref(null)
const useInfiniteScrollEnabled = ref(true)
const isSearchMode = ref(false)

const { publicPosts, loading, error, publicPagination } = storeToRefs(postsStore)

const filteredPosts = computed(() => {
  if (selectedCategory.value === null) {
    return publicPosts.value
  }
  return publicPosts.value.filter(post => 
    post.category && post.category.id === selectedCategory.value
  )
})

const featuredPosts = computed(() => {
  return publicPosts.value.slice(0, 2)
})

const featuredIds = computed(() => new Set(featuredPosts.value.map(p => p.id)))

const displayedPosts = computed(() => {
  if (selectedCategory.value !== null) return filteredPosts.value
  return filteredPosts.value.filter(p => !featuredIds.value.has(p.id))
})

const hasMorePosts = computed(() => {
  return publicPagination.value.current_page < publicPagination.value.last_page
})

const getCategoryName = (categoryId) => {
  const category = categoriesStore.categories.find(cat => cat.id === categoryId)
  return category ? category.name : 'Category'
}

const loadPosts = async () => {
  try {
    if (route.query.search) {
      isSearchMode.value = true
      await postsStore.fetchPublicPosts({ search: route.query.search, per_page: 12 })
    } else {
      isSearchMode.value = false
      await postsStore.fetchPublicPosts({ per_page: 12 })
    }
  } catch (err) {
    console.error('Failed to load posts:', err)
  }
}

const loadMore = async () => {
  if (loadingMore.value || !hasMorePosts.value) return
  
  loadingMore.value = true
  try {
    const nextPage = publicPagination.value.current_page + 1
    await postsStore.fetchPublicPosts({ 
      page: nextPage, 
      per_page: 12 
    }, { append: true })
  } catch (err) {
    console.error('Failed to load more posts:', err)
  } finally {
    loadingMore.value = false
  }
}

const { startObserving, stopObserving } = useInfiniteScroll(loadMore)

// Watch for search query changes
watch(() => route.query.search, async (newSearch, oldSearch) => {
  if (newSearch !== oldSearch) {
    selectedCategory.value = null // Clear category filter when searching
    await loadPosts()
  }
})

onMounted(async () => {
  await Promise.all([
    loadPosts(),
    categoriesStore.fetchCategories()
  ])
  
  if (useInfiniteScrollEnabled.value) {
    startObserving()
  }
})

onUnmounted(() => {
  stopObserving()
})
</script>