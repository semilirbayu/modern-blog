<template>
  <aside class="space-y-8">
    <!-- Categories Widget -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
      <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
        <svg class="h-5 w-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
        </svg>
        Categories
      </h3>
      
      <!-- Loading State -->
      <div v-if="categoriesStore.loading" class="text-center py-4">
        <div class="spinner h-5 w-5 mx-auto mb-2"></div>
        <p class="text-gray-600 text-sm">Loading...</p>
      </div>
      
      <!-- Categories List -->
      <div v-else-if="topCategories.length > 0" class="space-y-2">
        <router-link
          v-for="category in topCategories"
          :key="category.id"
          :to="{ name: 'public.category', params: { slug: category.slug } }"
          class="flex items-center justify-between p-3 rounded-lg border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-all duration-200 group"
        >
          <div class="flex items-center space-x-3 flex-1 min-w-0">
            <div class="h-8 w-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
              </svg>
            </div>
            <span class="text-gray-900 group-hover:text-blue-600 font-medium text-sm truncate">
              {{ category.name }}
            </span>
          </div>
          <span class="text-gray-500 text-xs bg-gray-100 group-hover:bg-blue-100 px-2 py-1 rounded-full">
            {{ category.posts_count || 0 }}
          </span>
        </router-link>
        
        <!-- View All Categories -->
        <router-link
          to="/"
          class="block text-center text-blue-600 hover:text-blue-800 text-sm font-medium py-2 transition-colors"
        >
          View All Categories →
        </router-link>
      </div>
      
      <!-- Empty State -->
      <div v-else class="text-center py-6">
        <svg class="h-8 w-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
        </svg>
        <p class="text-gray-600 text-sm">No categories found</p>
      </div>
    </div>

    <!-- Recent Posts Widget -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
      <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
        <svg class="h-5 w-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        Recent Posts
      </h3>
      
      <!-- Loading State -->
      <div v-if="postsStore.loading" class="text-center py-4">
        <div class="spinner h-5 w-5 mx-auto mb-2"></div>
        <p class="text-gray-600 text-sm">Loading...</p>
      </div>
      
      <!-- Recent Posts List -->
      <div v-else-if="recentPosts.length > 0" class="space-y-4">
        <article 
          v-for="post in recentPosts" 
          :key="post.id"
          class="group"
        >
          <router-link 
            :to="{ name: 'public.post', params: { slug: post.slug } }"
            class="block hover:bg-gray-50 p-2 rounded-lg transition-colors -mx-2"
          >
            <h4 class="text-sm font-medium text-gray-900 group-hover:text-blue-600 line-clamp-2 mb-2">
              {{ post.title }}
            </h4>
            
            <div class="flex items-center text-xs text-gray-500 space-x-3">
              <span v-if="post.category" class="flex items-center">
                <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                {{ post.category.name }}
              </span>
              <span class="flex items-center">
                <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ formatDate(post.published_at || post.created_at) }}
              </span>
            </div>
          </router-link>
        </article>
        
        <!-- View All Posts -->
        <router-link
          to="/"
          class="block text-center text-green-600 hover:text-green-800 text-sm font-medium py-2 transition-colors"
        >
          View All Posts →
        </router-link>
      </div>
      
      <!-- Empty State -->
      <div v-else class="text-center py-6">
        <svg class="h-8 w-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <p class="text-gray-600 text-sm">No posts found</p>
      </div>
    </div>

    <!-- Popular Posts Widget -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
      <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
        <svg class="h-5 w-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
        </svg>
        Popular Posts
      </h3>
      
      <!-- Popular Posts List -->
      <div v-if="popularPosts.length > 0" class="space-y-4">
        <article 
          v-for="(post, index) in popularPosts" 
          :key="post.id"
          class="group"
        >
          <router-link 
            :to="{ name: 'public.post', params: { slug: post.slug } }"
            class="flex items-start space-x-3 hover:bg-gray-50 p-2 rounded-lg transition-colors -mx-2"
          >
            <div class="flex-shrink-0 mt-1">
              <span class="inline-flex items-center justify-center h-6 w-6 bg-orange-100 text-orange-800 text-xs font-bold rounded-full">
                {{ index + 1 }}
              </span>
            </div>
            
            <div class="flex-1 min-w-0">
              <h4 class="text-sm font-medium text-gray-900 group-hover:text-blue-600 line-clamp-2 mb-1">
                {{ post.title }}
              </h4>
              
              <div class="flex items-center text-xs text-gray-500 space-x-2">
                <span>{{ formatDate(post.published_at || post.created_at) }}</span>
                <!-- Placeholder for view count -->
                <span class="flex items-center">
                  <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  {{ Math.floor(Math.random() * 500) + 100 }} views
                </span>
              </div>
            </div>
          </router-link>
        </article>
      </div>
      
      <!-- Fallback to Recent Posts -->
      <div v-else class="text-center py-6">
        <svg class="h-8 w-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
        </svg>
        <p class="text-gray-600 text-sm">Popular posts will appear here</p>
      </div>
    </div>

    <!-- Newsletter Signup Widget -->
    <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-lg p-6">
      <div class="text-center mb-4">
        <svg class="h-12 w-12 text-blue-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
        </svg>
        <h3 class="text-lg font-semibold text-gray-900 mb-2">Stay Updated</h3>
        <p class="text-gray-600 text-sm">Get the latest posts delivered right to your inbox.</p>
      </div>

      <form @submit.prevent="subscribeNewsletter" class="space-y-3">
        <div>
          <label for="newsletter-email" class="sr-only">Email address</label>
          <input 
            id="newsletter-email"
            v-model="newsletterEmail"
            type="email" 
            placeholder="Enter your email"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
            :disabled="newsletterLoading"
          />
        </div>
        
        <button 
          type="submit"
          :disabled="newsletterLoading || !newsletterEmail"
          class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white py-2 px-4 rounded-lg text-sm font-medium transition-colors flex items-center justify-center"
        >
          <span v-if="newsletterLoading" class="spinner h-4 w-4 mr-2"></span>
          <svg v-else class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
          </svg>
          {{ newsletterLoading ? 'Subscribing...' : 'Subscribe' }}
        </button>
      </form>

      <p class="text-xs text-gray-500 text-center mt-3">
        No spam, unsubscribe at any time.
      </p>
    </div>

    <!-- Tag Cloud Widget -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
      <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
        <svg class="h-5 w-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
        </svg>
        Popular Tags
      </h3>
      
      <div v-if="popularTags.length > 0" class="flex flex-wrap gap-2">
        <button
          v-for="tag in popularTags"
          :key="tag.name"
          @click="searchByTag(tag.name)"
          class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs px-3 py-1 rounded-full transition-colors cursor-pointer"
          :style="{ fontSize: tag.size + 'px' }"
        >
          #{{ tag.name }}
        </button>
      </div>
      
      <div v-else class="text-center py-6">
        <svg class="h-8 w-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
        </svg>
        <p class="text-gray-600 text-sm">No tags found</p>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { storeToRefs } from 'pinia'
import { usePostsStore } from '@/stores/posts.js'
import { useCategoriesStore } from '@/stores/categories.js'
import { formatDate } from '@/utils/index.js'

const router = useRouter()
const postsStore = usePostsStore()
const categoriesStore = useCategoriesStore()

const { publicPosts, loading } = storeToRefs(postsStore)
const { categories } = storeToRefs(categoriesStore)

const newsletterEmail = ref('')
const newsletterLoading = ref(false)

// Sample popular tags (in a real app, these would come from the API)
const popularTags = ref([
  { name: 'JavaScript', size: 14, count: 25 },
  { name: 'Vue.js', size: 12, count: 18 },
  { name: 'Laravel', size: 13, count: 22 },
  { name: 'PHP', size: 11, count: 15 },
  { name: 'CSS', size: 10, count: 12 },
  { name: 'HTML', size: 9, count: 10 },
  { name: 'React', size: 11, count: 14 },
  { name: 'Node.js', size: 10, count: 11 },
])

const topCategories = computed(() => {
  return categories.value
    .filter(cat => cat.posts_count > 0)
    .sort((a, b) => (b.posts_count || 0) - (a.posts_count || 0))
    .slice(0, 6)
})

const recentPosts = computed(() => {
  return [...publicPosts.value]
    .sort((a, b) => new Date(b.published_at || b.created_at) - new Date(a.published_at || a.created_at))
    .slice(0, 5)
})

const popularPosts = computed(() => {
  // In a real app, this would be based on view counts, likes, etc.
  // For now, we'll just use the most recent posts
  return recentPosts.value.slice(0, 4)
})

const subscribeNewsletter = async () => {
  if (!newsletterEmail.value || newsletterLoading.value) return
  
  newsletterLoading.value = true
  
  try {
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1500))
    
    // Success feedback
    alert('Thank you for subscribing! You\'ll receive our latest posts in your inbox.')
    newsletterEmail.value = ''
  } catch (error) {
    console.error('Newsletter subscription failed:', error)
    alert('Subscription failed. Please try again.')
  } finally {
    newsletterLoading.value = false
  }
}

const searchByTag = (tagName) => {
  router.push({ name: 'home', query: { search: tagName } })
}

onMounted(async () => {
  // Load data if not already loaded
  try {
    if (publicPosts.value.length === 0) {
      await postsStore.fetchPublicPosts({ per_page: 10 })
    }
    if (categories.value.length === 0) {
      await categoriesStore.fetchCategories()
    }
  } catch (error) {
    console.error('Failed to load sidebar data:', error)
  }
})
</script>

<style scoped>
.spinner {
  border: 2px solid transparent;
  border-top: 2px solid currentColor;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.line-clamp-2 {
  overflow: hidden;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 2;
}
</style>