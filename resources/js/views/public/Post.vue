<template>
  <div>
    <!-- Reading Progress -->
    <ReadingProgress v-if="currentPost && !loading" />
    
    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="spinner h-8 w-8 mx-auto mb-4"></div>
      <p class="text-gray-600">Loading post...</p>
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

    <!-- Post Content -->
    <div v-else-if="currentPost" class="grid grid-cols-1 lg:grid-cols-4 gap-12">
      <!-- Main Content -->
      <article class="lg:col-span-3">
        <!-- Post Header -->
        <header class="mb-12">
          <div class="flex items-center space-x-3 mb-6">
            <span v-if="currentPost.category" class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
              <router-link 
                :to="{ name: 'public.category', params: { slug: currentPost.category.slug } }"
                class="text-current hover:text-blue-900 transition-colors"
              >
                {{ currentPost.category.name }}
              </router-link>
            </span>
            
            <div class="text-gray-500 text-sm flex items-center space-x-4">
              <span class="flex items-center">
                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ readingTime }} min read
              </span>
              <span>{{ formatDate(currentPost.published_at || currentPost.created_at) }}</span>
            </div>
          </div>
          
          <h1 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-6 leading-tight">
            {{ currentPost.title }}
          </h1>
          
          <div v-if="currentPost.excerpt" class="text-xl text-gray-600 mb-8 leading-relaxed">
            {{ currentPost.excerpt }}
          </div>

          <div v-if="currentPost.user" class="flex items-center space-x-4 py-6 border-y border-gray-200">
            <div class="flex-shrink-0">
              <div class="h-12 w-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                {{ currentPost.user.name.charAt(0).toUpperCase() }}
              </div>
            </div>
            <div>
              <p class="text-gray-900 font-medium">{{ currentPost.user.name }}</p>
              <p class="text-gray-600 text-sm">Author</p>
            </div>
            <div class="flex-grow"></div>
            <SocialShare :post="currentPost" />
          </div>
        </header>

        <!-- Featured Image Placeholder -->
        <div v-if="currentPost.featured_image" class="mb-12">
          <img 
            :src="currentPost.featured_image" 
            :alt="currentPost.title"
            class="w-full h-64 lg:h-96 object-cover rounded-lg shadow-lg"
          />
        </div>
        <div v-else class="mb-12 h-64 lg:h-96 bg-gradient-to-br from-gray-100 to-gray-200 rounded-lg shadow-lg flex items-center justify-center">
          <svg class="h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
        </div>

        <!-- Post Content -->
        <div class="prose prose-lg max-w-none mb-16">
          <div v-html="currentPost.content" ref="contentRef"></div>
        </div>

        <!-- Tags -->
        <div v-if="currentPost.tags && currentPost.tags.length > 0" class="flex flex-wrap gap-2 mb-12">
          <span 
            v-for="tag in currentPost.tags" 
            :key="tag"
            class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm hover:bg-gray-200 transition-colors cursor-pointer"
          >
            #{{ tag }}
          </span>
        </div>

        <!-- Navigation -->
        <div class="flex items-center justify-between py-8 border-t border-gray-200 mb-12">
          <router-link 
            to="/" 
            class="flex items-center text-gray-600 hover:text-gray-900 transition-colors group"
          >
            <svg class="h-5 w-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Blog
          </router-link>
          
          <div class="flex items-center space-x-4">
            <button 
              @click="scrollToTop"
              class="text-gray-600 hover:text-gray-900 transition-colors p-2 rounded-lg hover:bg-gray-100"
              title="Scroll to top"
            >
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Related Posts -->
        <section v-if="relatedPosts.length > 0" class="border-t border-gray-200 pt-12">
          <h2 class="text-2xl font-bold text-gray-900 mb-8">Related Posts</h2>
          <div class="grid gap-8 md:grid-cols-2">
            <PostCard
              v-for="post in relatedPosts"
              :key="post.id"
              :post="post"
              :compact="true"
            />
          </div>
        </section>

        <!-- Comments Placeholder -->
        <section class="border-t border-gray-200 pt-12 mt-12">
          <h2 class="text-2xl font-bold text-gray-900 mb-6">Comments</h2>
          <div class="bg-gray-50 rounded-lg p-8 text-center">
            <svg class="h-16 w-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Comments Coming Soon</h3>
            <p class="text-gray-600">We're working on adding a comment system. Check back soon!</p>
          </div>
        </section>
      </article>

      <!-- Sidebar -->
      <aside class="lg:col-span-1">
        <div class="sticky top-24 space-y-8">
          <!-- Table of Contents -->
          <div v-if="tableOfContents.length > 0" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Table of Contents</h3>
            <nav>
              <ul class="space-y-2 text-sm">
                <li 
                  v-for="heading in tableOfContents" 
                  :key="heading.id"
                  :class="[
                    'border-l-2 transition-colors duration-200',
                    heading.level === 1 ? 'pl-3' :
                    heading.level === 2 ? 'pl-5' : 'pl-7',
                    activeHeading === heading.id 
                      ? 'border-blue-600 text-blue-600' 
                      : 'border-gray-200 text-gray-600 hover:text-gray-900 hover:border-gray-300'
                  ]"
                >
                  <a 
                    :href="`#${heading.id}`"
                    @click.prevent="scrollToHeading(heading.id)"
                    class="block py-1 transition-colors duration-200"
                  >
                    {{ heading.text }}
                  </a>
                </li>
              </ul>
            </nav>
          </div>

          <!-- Post Info -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Post Info</h3>
            <div class="space-y-3 text-sm">
              <div class="flex justify-between">
                <span class="text-gray-600">Published</span>
                <span class="text-gray-900">{{ formatDate(currentPost.published_at || currentPost.created_at) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Reading time</span>
                <span class="text-gray-900">{{ readingTime }} minutes</span>
              </div>
              <div v-if="currentPost.category" class="flex justify-between">
                <span class="text-gray-600">Category</span>
                <router-link 
                  :to="{ name: 'public.category', params: { slug: currentPost.category.slug } }"
                  class="text-blue-600 hover:text-blue-800 transition-colors"
                >
                  {{ currentPost.category.name }}
                </router-link>
              </div>
            </div>
          </div>

          <!-- Newsletter Signup -->
          <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Stay Updated</h3>
            <p class="text-gray-600 text-sm mb-4">Subscribe to get notified about new posts and updates.</p>
            <div class="space-y-3">
              <input 
                type="email" 
                placeholder="Enter your email"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
              />
              <button class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg text-sm font-medium transition-colors">
                Subscribe
              </button>
            </div>
          </div>
        </div>
      </aside>
    </div>
  </div>
</template>

<script setup>
import { onMounted, onUnmounted, ref, computed, nextTick, watch } from 'vue'
import { useRoute } from 'vue-router'
import { storeToRefs } from 'pinia'
import { usePostsStore } from '@/stores/posts.js'
import { formatDate } from '@/utils/index.js'
import { useReadingTime } from '@/composables/useReadingTime.js'
import { useSEO } from '@/composables/useSEO.js'
import ReadingProgress from '@/components/public/ReadingProgress.vue'
import SocialShare from '@/components/public/SocialShare.vue'
import PostCard from '@/components/public/PostCard.vue'

const route = useRoute()
const postsStore = usePostsStore()

const { currentPost, loading, error, publicPosts } = storeToRefs(postsStore)

const contentRef = ref(null)
const tableOfContents = ref([])
const activeHeading = ref('')
const relatedPosts = ref([])

const { updateTitle, updateMeta, updateOpenGraph, updateTwitterCard } = useSEO()

const readingTime = computed(() => {
  if (!currentPost.value?.content) return 0
  const { estimateReadingTime } = useReadingTime()
  return estimateReadingTime(currentPost.value.content)
})

const loadPost = async () => {
  try {
    await postsStore.fetchPublicPost(route.params.slug)
    
    if (currentPost.value) {
      // Update SEO meta tags
      updateTitle(`${currentPost.value.title} - ModernBlog`)
      updateMeta('description', currentPost.value.excerpt || extractExcerpt(currentPost.value.content))
      
      // Update Open Graph meta tags
      updateOpenGraph({
        title: currentPost.value.title,
        description: currentPost.value.excerpt || extractExcerpt(currentPost.value.content),
        type: 'article',
        url: window.location.href,
        image: currentPost.value.featured_image || '/images/default-og.jpg'
      })
      
      // Update Twitter Card meta tags
      updateTwitterCard({
        card: 'summary_large_image',
        title: currentPost.value.title,
        description: currentPost.value.excerpt || extractExcerpt(currentPost.value.content),
        image: currentPost.value.featured_image || '/images/default-og.jpg'
      })

      // Generate table of contents and related posts
      nextTick(() => {
        generateTableOfContents()
        loadRelatedPosts()
      })
    }
  } catch (err) {
    console.error('Failed to load post:', err)
  }
}

const generateTableOfContents = () => {
  if (!contentRef.value) return
  
  const headings = contentRef.value.querySelectorAll('h1, h2, h3, h4, h5, h6')
  tableOfContents.value = Array.from(headings).map((heading, index) => {
    const id = heading.id || `heading-${index}`
    if (!heading.id) {
      heading.id = id
    }
    
    return {
      id,
      text: heading.textContent,
      level: parseInt(heading.tagName.charAt(1))
    }
  })
}

const loadRelatedPosts = async () => {
  if (!currentPost.value) return
  
  try {
    relatedPosts.value = await postsStore.fetchRelatedPosts({ 
      category: currentPost.value.category?.id,
      per_page: 4,
      exclude: currentPost.value.id 
    })
  } catch (err) {
    console.error('Failed to load related posts:', err)
  }
}

const scrollToHeading = (headingId) => {
  const element = document.getElementById(headingId)
  if (element) {
    const offset = 100
    const bodyRect = document.body.getBoundingClientRect().top
    const elementRect = element.getBoundingClientRect().top
    const elementPosition = elementRect - bodyRect
    const offsetPosition = elementPosition - offset

    window.scrollTo({
      top: offsetPosition,
      behavior: 'smooth'
    })
  }
}

const scrollToTop = () => {
  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  })
}

const extractExcerpt = (content) => {
  const div = document.createElement('div')
  div.innerHTML = content
  return div.textContent.substring(0, 160) + '...'
}

const updateActiveHeading = () => {
  const headings = tableOfContents.value
  if (headings.length === 0) return

  const scrollPosition = window.scrollY + 120
  
  for (let i = headings.length - 1; i >= 0; i--) {
    const element = document.getElementById(headings[i].id)
    if (element && element.offsetTop <= scrollPosition) {
      activeHeading.value = headings[i].id
      break
    }
  }
}

// Watch for slug changes
watch(() => route.params.slug, async () => {
  activeHeading.value = ''
  tableOfContents.value = []
  postsStore.clearCurrentPost()
  await loadPost()
  scrollToTop()
})

onMounted(() => {
  loadPost()
  window.addEventListener('scroll', updateActiveHeading)
})

onUnmounted(() => {
  window.removeEventListener('scroll', updateActiveHeading)
})
</script>