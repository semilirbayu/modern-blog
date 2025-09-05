<template>
  <div>
    <!-- Welcome Message -->
    <div class="mb-8">
      <h2 class="text-2xl font-semibold text-gray-900 mb-2">
        Welcome back, {{ user?.name || 'Admin' }}!
      </h2>
      <p class="text-gray-600">Here's what's happening with your blog today.</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div class="card">
        <div class="card-body">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="h-8 w-8 bg-blue-500 rounded-md flex items-center justify-center">
                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                </svg>
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Total Posts</p>
              <p class="text-2xl font-semibold text-gray-900">{{ stats.totalPosts }}</p>
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="h-8 w-8 bg-green-500 rounded-md flex items-center justify-center">
                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Published</p>
              <p class="text-2xl font-semibold text-gray-900">{{ stats.publishedPosts }}</p>
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="h-8 w-8 bg-yellow-500 rounded-md flex items-center justify-center">
                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Drafts</p>
              <p class="text-2xl font-semibold text-gray-900">{{ stats.draftPosts }}</p>
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="h-8 w-8 bg-purple-500 rounded-md flex items-center justify-center">
                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Categories</p>
              <p class="text-2xl font-semibold text-gray-900">{{ stats.totalCategories }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
      <div class="card">
        <div class="card-header">
          <h3 class="text-lg font-semibold">Quick Actions</h3>
        </div>
        <div class="card-body">
          <div class="space-y-3">
            <router-link 
              :to="{ name: 'admin.posts.create' }" 
              class="btn btn-primary w-full"
            >
              <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
              </svg>
              Create New Post
            </router-link>
            
            <router-link 
              :to="{ name: 'admin.categories' }" 
              class="btn btn-secondary w-full"
            >
              <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
              </svg>
              Manage Categories
            </router-link>
            
            <a href="/" target="_blank" class="btn btn-outline w-full">
              <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
              </svg>
              View Site
            </a>
          </div>
        </div>
      </div>

      <!-- Recent Posts -->
      <div class="card">
        <div class="card-header flex justify-between items-center">
          <h3 class="text-lg font-semibold">Recent Posts</h3>
          <router-link 
            :to="{ name: 'admin.posts' }" 
            class="text-blue-600 hover:text-blue-800 text-sm"
          >
            View all
          </router-link>
        </div>
        <div class="card-body">
          <div v-if="recentPostsLoading" class="text-center py-4">
            <div class="spinner h-6 w-6 mx-auto"></div>
          </div>
          
          <div v-else-if="recentPosts.length === 0" class="text-center py-8 text-gray-500">
            <p>No posts yet. Create your first post!</p>
          </div>
          
          <div v-else class="space-y-3">
            <div 
              v-for="post in recentPosts.slice(0, 5)" 
              :key="post.id"
              class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0"
            >
              <div class="flex-1">
                <router-link 
                  :to="{ name: 'admin.posts.edit', params: { id: post.id } }"
                  class="text-sm font-medium text-gray-900 hover:text-blue-600"
                >
                  {{ post.title }}
                </router-link>
                <p class="text-xs text-gray-500 mt-1">
                  {{ formatDate(post.updated_at) }}
                </p>
              </div>
              <span 
                :class="post.status === 'published' ? 'badge-success' : 'badge-warning'"
                class="badge text-xs"
              >
                {{ post.status }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue'
import { storeToRefs } from 'pinia'
import { useAuth } from '../../composables/useAuth.js'
import { usePostsStore } from '../../stores/posts.js'
import { useCategoriesStore } from '../../stores/categories.js'
import { formatDate } from '../../utils/index.js'

const { user } = useAuth()
const postsStore = usePostsStore()
const categoriesStore = useCategoriesStore()

const { posts } = storeToRefs(postsStore)
const { categories } = storeToRefs(categoriesStore)

const stats = reactive({
  totalPosts: 0,
  publishedPosts: 0,
  draftPosts: 0,
  totalCategories: 0
})

const recentPosts = ref([])
const recentPostsLoading = ref(true)

const loadDashboardData = async () => {
  try {
    // Load posts and categories
    await Promise.all([
      postsStore.fetchPosts({ per_page: 10 }),
      categoriesStore.fetchCategories()
    ])

    // Calculate stats
    stats.totalPosts = posts.value.length
    stats.publishedPosts = posts.value.filter(post => post.status === 'published').length
    stats.draftPosts = posts.value.filter(post => post.status === 'draft').length
    stats.totalCategories = categories.value.length

    // Set recent posts
    recentPosts.value = [...posts.value].sort((a, b) => 
      new Date(b.updated_at) - new Date(a.updated_at)
    )
  } catch (error) {
    console.error('Failed to load dashboard data:', error)
  } finally {
    recentPostsLoading.value = false
  }
}

onMounted(() => {
  document.title = 'Dashboard - ModernBlog Admin'
  loadDashboardData()
})
</script>