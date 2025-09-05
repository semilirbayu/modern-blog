<template>
  <nav v-if="breadcrumbs.length > 1" class="bg-gray-50 border-b border-gray-200 py-3" aria-label="Breadcrumb">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <ol class="flex items-center space-x-2 text-sm" itemscope itemtype="https://schema.org/BreadcrumbList">
        <li 
          v-for="(crumb, index) in breadcrumbs" 
          :key="crumb.path"
          class="flex items-center"
          itemprop="itemListElement" 
          itemscope 
          itemtype="https://schema.org/ListItem"
        >
          <!-- Separator -->
          <svg 
            v-if="index > 0" 
            class="h-4 w-4 text-gray-400 mr-2" 
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
          
          <!-- Breadcrumb Item -->
          <div class="flex items-center">
            <!-- Home Icon for first item -->
            <svg 
              v-if="index === 0" 
              class="h-4 w-4 text-gray-500 mr-1" 
              fill="none" 
              stroke="currentColor" 
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            
            <!-- Category Icon for category pages -->
            <svg 
              v-else-if="crumb.type === 'category'" 
              class="h-4 w-4 text-gray-500 mr-1" 
              fill="none" 
              stroke="currentColor" 
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
            
            <!-- Post Icon for post pages -->
            <svg 
              v-else-if="crumb.type === 'post'" 
              class="h-4 w-4 text-gray-500 mr-1" 
              fill="none" 
              stroke="currentColor" 
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            
            <!-- Page Icon for other pages -->
            <svg 
              v-else-if="crumb.type === 'page'" 
              class="h-4 w-4 text-gray-500 mr-1" 
              fill="none" 
              stroke="currentColor" 
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>

            <!-- Link or Text -->
            <router-link
              v-if="index < breadcrumbs.length - 1"
              :to="crumb.path"
              class="text-gray-600 hover:text-gray-900 transition-colors font-medium"
              :class="{ 'text-blue-600 hover:text-blue-800': index === 0 }"
              itemprop="item"
            >
              <span itemprop="name">{{ crumb.name }}</span>
            </router-link>
            
            <span 
              v-else 
              class="text-gray-900 font-medium line-clamp-1 max-w-xs"
              itemprop="name"
              :title="crumb.name"
            >
              {{ crumb.name }}
            </span>
            
            <!-- Schema.org position -->
            <meta itemprop="position" :content="index + 1" />
          </div>
        </li>
      </ol>
    </div>
  </nav>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { storeToRefs } from 'pinia'
import { usePostsStore } from '@/stores/posts.js'
import { useCategoriesStore } from '@/stores/categories.js'

const route = useRoute()
const postsStore = usePostsStore()
const categoriesStore = useCategoriesStore()

const { currentPost } = storeToRefs(postsStore)
const { currentCategory } = storeToRefs(categoriesStore)

const breadcrumbs = computed(() => {
  const crumbs = []
  
  // Always start with Home
  crumbs.push({
    name: 'Home',
    path: '/',
    type: 'home'
  })
  
  // Handle different route types
  switch (route.name) {
    case 'public.post':
      // For posts: Home > [Category] > Post Title
      if (currentPost.value) {
        // Add category if available
        if (currentPost.value.category) {
          crumbs.push({
            name: currentPost.value.category.name,
            path: `/categories/${currentPost.value.category.slug}`,
            type: 'category'
          })
        }
        
        // Add post title
        crumbs.push({
          name: currentPost.value.title,
          path: route.path,
          type: 'post'
        })
      }
      break
      
    case 'public.category':
      // For categories: Home > Category Name
      if (currentCategory.value) {
        crumbs.push({
          name: currentCategory.value.name,
          path: route.path,
          type: 'category'
        })
      } else if (route.params.slug) {
        // Fallback if category data isn't loaded yet
        crumbs.push({
          name: capitalizeSlug(route.params.slug),
          path: route.path,
          type: 'category'
        })
      }
      break
      
    case 'about':
      // For about page: Home > About
      crumbs.push({
        name: 'About',
        path: '/about',
        type: 'page'
      })
      break
      
    case 'contact':
      // For contact page: Home > Contact
      crumbs.push({
        name: 'Contact',
        path: '/contact',
        type: 'page'
      })
      break
      
    case 'search':
      // For search results: Home > Search Results
      const query = route.query.q || route.query.search
      if (query) {
        crumbs.push({
          name: `Search: "${query}"`,
          path: route.path,
          type: 'search'
        })
      } else {
        crumbs.push({
          name: 'Search',
          path: route.path,
          type: 'search'
        })
      }
      break
      
    default:
      // For other pages, try to infer from route path
      if (route.path !== '/') {
        const pathSegments = route.path.split('/').filter(segment => segment)
        
        pathSegments.forEach((segment, index) => {
          const name = capitalizeSlug(segment)
          const path = '/' + pathSegments.slice(0, index + 1).join('/')
          
          crumbs.push({
            name,
            path,
            type: 'page'
          })
        })
      }
      break
  }
  
  return crumbs
})

const capitalizeSlug = (slug) => {
  return slug
    .split('-')
    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ')
}
</script>

<style scoped>
.line-clamp-1 {
  overflow: hidden;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 1;
}
</style>