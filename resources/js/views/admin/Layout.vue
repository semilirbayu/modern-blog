<template>
  <div class="min-h-screen bg-gray-50 flex">
    <!-- Sidebar -->
    <aside class="sidebar w-64 min-h-screen">
      <div class="p-6">
        <h1 class="text-2xl font-bold text-gray-900">ModernBlog</h1>
        <p class="text-sm text-gray-500">Admin Dashboard</p>
      </div>
      
      <nav class="sidebar-nav">
        <router-link 
          :to="{ name: 'admin.dashboard' }" 
          class="sidebar-nav-item"
          active-class="active"
        >
          <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2H3a2 2 0 00-2 2z" />
          </svg>
          Dashboard
        </router-link>
        
        <router-link 
          :to="{ name: 'admin.posts' }" 
          class="sidebar-nav-item"
          active-class="active"
        >
          <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
          </svg>
          Posts
        </router-link>
        
        <router-link 
          :to="{ name: 'admin.categories' }" 
          class="sidebar-nav-item"
          active-class="active"
        >
          <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
          </svg>
          Categories
        </router-link>
        
        <router-link 
          :to="{ name: 'admin.profile' }" 
          class="sidebar-nav-item"
          active-class="active"
        >
          <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
          Profile
        </router-link>
        
        <button 
          @click="showLogoutConfirm = true"
          class="sidebar-nav-item w-full text-left text-red-600 hover:text-red-800 hover:bg-red-50 disabled:opacity-50 disabled:cursor-not-allowed"
          :disabled="loggingOut || loading"
        >
          <LoadingSpinner 
            v-if="loggingOut" 
            variant="small" 
            color="currentColor" 
            class="mr-3"
          />
          <svg v-else class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
          {{ loggingOut ? 'Logging out...' : 'Logout' }}
        </button>
      </nav>
    </aside>

    <!-- Main Content -->
    <div class="main-content flex-1">
      <!-- Top Navigation -->
      <header class="page-header">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="page-title">{{ pageTitle }}</h1>
            <p v-if="pageSubtitle" class="page-subtitle">{{ pageSubtitle }}</p>
          </div>
          
          <div class="flex items-center space-x-4">
            <a href="/" target="_blank" class="btn btn-outline btn-sm">
              View Site
            </a>
            
            <div v-if="user" class="flex items-center space-x-2">
              <div class="h-8 w-8 bg-blue-600 rounded-full flex items-center justify-center">
                <span class="text-white text-sm font-medium">
                  {{ user.name ? user.name.charAt(0).toUpperCase() : 'U' }}
                </span>
              </div>
              <span class="text-sm text-gray-700">{{ user.name || 'User' }}</span>
            </div>
            <div v-else class="flex items-center space-x-2">
              <LoadingSpinner variant="small" />
              <span class="text-sm text-gray-500">Loading...</span>
            </div>
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="p-6">
        <router-view />
      </main>
    </div>

    <!-- Logout Confirmation Dialog -->
    <ConfirmDialog
      :show="showLogoutConfirm"
      :loading="loggingOut"
      title="Confirm Logout"
      message="Are you sure you want to logout? You will be redirected to the login page."
      confirm-text="Logout"
      cancel-text="Cancel"
      type="warning"
      @confirm="handleLogout"
      @cancel="showLogoutConfirm = false"
    />
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { storeToRefs } from 'pinia'
import { useAuth } from '../../composables/useAuth.js'
import ConfirmDialog from '../../components/common/ConfirmDialog.vue'
import LoadingSpinner from '../../components/common/LoadingSpinner.vue'

const route = useRoute()
const router = useRouter()
const { user, logout, loading } = useAuth()

const showLogoutConfirm = ref(false)
const loggingOut = ref(false)

const pageTitle = computed(() => {
  const titleMap = {
    'admin.dashboard': 'Dashboard',
    'admin.posts': 'Posts',
    'admin.posts.create': 'Create Post',
    'admin.posts.edit': 'Edit Post',
    'admin.categories': 'Categories',
    'admin.profile': 'Profile'
  }
  
  return titleMap[route.name] || 'Admin'
})

const pageSubtitle = computed(() => {
  const subtitleMap = {
    'admin.dashboard': 'Welcome to your admin dashboard',
    'admin.posts': 'Manage your blog posts',
    'admin.posts.create': 'Create a new blog post',
    'admin.posts.edit': 'Edit your blog post',
    'admin.categories': 'Organize your content',
    'admin.profile': 'Manage your account settings'
  }
  
  return subtitleMap[route.name] || ''
})

const handleLogout = async () => {
  loggingOut.value = true
  
  try {
    const result = await logout()
    if (result.success) {
      showLogoutConfirm.value = false
      router.push({ name: 'admin.login' })
    } else {
      console.error('Logout failed:', result.error)
    }
  } catch (error) {
    console.error('Logout error:', error)
  } finally {
    loggingOut.value = false
  }
}
</script>