<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
          <div class="flex items-center">
            <router-link to="/" class="text-2xl font-bold text-gray-900 hover:text-blue-600 transition-colors">
              ModernBlog
            </router-link>
          </div>

          <!-- Desktop Navigation -->
          <div class="hidden lg:flex items-center space-x-8">
            <nav class="flex space-x-6">
              <router-link 
                to="/" 
                class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors"
                active-class="text-blue-600"
              >
                Home
              </router-link>
              
              <!-- Categories Dropdown -->
              <!-- <CategoriesMenu /> -->
              
              <router-link 
                to="/about" 
                class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors"
                active-class="text-blue-600"
              >
                About
              </router-link>
              <router-link 
                to="/contact" 
                class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors"
                active-class="text-blue-600"
              >
                Contact
              </router-link>
            </nav>

            <!-- Search Bar -->
            <SearchBar />
          </div>

          <!-- Mobile menu button -->
          <div class="lg:hidden flex items-center space-x-2">
            <SearchBar :mobile="true" />
            <button
              @click="mobileMenuOpen = !mobileMenuOpen"
              type="button"
              class="text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 p-2"
              aria-controls="mobile-menu"
              :aria-expanded="mobileMenuOpen"
            >
              <span class="sr-only">Open main menu</span>
              <svg v-if="!mobileMenuOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
              <svg v-else class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Mobile menu -->
        <div 
          v-show="mobileMenuOpen" 
          class="lg:hidden border-t border-gray-200 pt-4 pb-4"
          id="mobile-menu"
        >
          <div class="space-y-1">
            <router-link 
              to="/" 
              @click="mobileMenuOpen = false"
              class="block text-gray-600 hover:text-gray-900 hover:bg-gray-50 px-3 py-2 rounded-md text-base font-medium transition-colors"
              active-class="text-blue-600 bg-blue-50"
            >
              Home
            </router-link>
            
            <!-- Mobile Categories -->
            <!-- <div class="px-3 py-2">
              <div class="text-gray-900 font-medium mb-2">Categories</div>
              <div v-if="categoriesStore.loading" class="text-gray-500 text-sm">Loading...</div>
              <div v-else-if="categoriesStore.categories.length > 0" class="space-y-1 ml-2">
                <router-link
                  v-for="category in categoriesStore.categories"
                  :key="category.id"
                  :to="`/categories/${category.slug}`"
                  @click="mobileMenuOpen = false"
                  class="block text-gray-600 hover:text-gray-900 hover:bg-gray-50 px-3 py-1 rounded text-sm transition-colors"
                >
                  {{ category.name }}
                  <span v-if="category.posts_count" class="text-gray-400 text-xs ml-1">
                    ({{ category.posts_count }})
                  </span>
                </router-link>
              </div>
            </div> -->
            
            <router-link 
              to="/about" 
              @click="mobileMenuOpen = false"
              class="block text-gray-600 hover:text-gray-900 hover:bg-gray-50 px-3 py-2 rounded-md text-base font-medium transition-colors"
              active-class="text-blue-600 bg-blue-50"
            >
              About
            </router-link>
            <router-link 
              to="/contact" 
              @click="mobileMenuOpen = false"
              class="block text-gray-600 hover:text-gray-900 hover:bg-gray-50 px-3 py-2 rounded-md text-base font-medium transition-colors"
              active-class="text-blue-600 bg-blue-50"
            >
              Contact
            </router-link>
          </div>
        </div>
      </div>
    </header>

    <!-- Breadcrumb -->
    <!-- <Breadcrumb v-if="showBreadcrumb" /> -->

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <router-view />
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
          <div class="col-span-1 md:col-span-2">
            <router-link to="/" class="text-xl font-bold text-gray-900 hover:text-blue-600 transition-colors">
              ModernBlog
            </router-link>
            <p class="mt-2 text-gray-600 text-sm">
              A modern blogging platform built with Laravel & Vue.js. 
              Share your thoughts and connect with readers worldwide.
            </p>
          </div>
          
          <div>
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Navigation</h3>
            <ul class="mt-4 space-y-2">
              <li>
                <router-link to="/" class="text-gray-600 hover:text-gray-900 text-sm transition-colors">Home</router-link>
              </li>
              <li>
                <router-link to="/about" class="text-gray-600 hover:text-gray-900 text-sm transition-colors">About</router-link>
              </li>
              <li>
                <router-link to="/contact" class="text-gray-600 hover:text-gray-900 text-sm transition-colors">Contact</router-link>
              </li>
            </ul>
          </div>
          
          <div>
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Connect</h3>
            <div class="mt-4 flex space-x-3">
              <a href="#" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
                </svg>
              </a>
              <a href="#" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                  <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd"/>
                </svg>
              </a>
            </div>
          </div>
        </div>
        
        <div class="mt-8 pt-8 border-t border-gray-200">
          <div class="text-center">
            <p class="text-gray-500 text-sm">
              © {{ currentYear }} ModernBlog. Built with Laravel & Vue.js
            </p>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
// import { useCategoriesStore } from '@/stores/categories'
// import CategoriesMenu from '@/components/public/CategoriesMenu.vue'
import SearchBar from '@/components/public/SearchBar.vue'
// import Breadcrumb from '@/components/public/Breadcrumb.vue'

const route = useRoute()
// const categoriesStore = useCategoriesStore()
const mobileMenuOpen = ref(false)

const currentYear = computed(() => new Date().getFullYear())

const showBreadcrumb = computed(() => {
  return route.name !== 'public.home' && route.path !== '/'
})

// onMounted(async () => {
//   try {
//     await categoriesStore.fetchCategories()
//   } catch (error) {
//     console.error('Failed to load categories:', error)
//   }
// })
</script>