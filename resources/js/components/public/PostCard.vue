<template>
  <article :class="[
    'bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300',
    'hover:shadow-md hover:border-gray-300 group',
    featured ? 'lg:flex lg:items-center' : '',
    compact ? 'flex-col' : ''
  ]">
    <!-- Featured Image -->
    <div :class="[
      'relative overflow-hidden',
      featured ? 'lg:w-1/2 lg:flex-shrink-0' : '',
      'bg-gradient-to-br from-gray-100 to-gray-200'
    ]">
      <div v-if="post.featured_image" class="aspect-video w-full">
        <img 
          :src="post.featured_image" 
          :alt="post.title"
          class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
          loading="lazy"
        />
      </div>
      <div v-else :class="[
        'aspect-video w-full flex items-center justify-center',
        featured ? 'lg:aspect-square' : ''
      ]">
        <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
      </div>

      <!-- Reading Time Badge -->
      <div class="absolute top-3 right-3">
        <span class="bg-black bg-opacity-70 text-white text-xs px-2 py-1 rounded-full">
          {{ readingTime }} min read
        </span>
      </div>

      <!-- Category Badge -->
      <div v-if="showCategory && post.category" class="absolute top-3 left-3">
        <router-link 
          :to="{ name: 'public.category', params: { slug: post.category.slug } }"
          class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1 rounded-full font-medium transition-colors"
        >
          {{ post.category.name }}
        </router-link>
      </div>
    </div>

    <!-- Content -->
    <div :class="[
      'p-6 flex flex-col justify-between flex-grow',
      featured ? 'lg:w-1/2' : ''
    ]">
      <div>
        <!-- Category (for non-featured cards) -->
        <div v-if="showCategory && post.category && !featured" class="mb-3">
          <router-link 
            :to="{ name: 'public.category', params: { slug: post.category.slug } }"
            class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm font-medium transition-colors"
          >
            <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
            {{ post.category.name }}
          </router-link>
        </div>

        <!-- Title -->
        <h3 :class="[
          'font-bold text-gray-900 mb-3 group-hover:text-blue-600 transition-colors line-clamp-2',
          featured ? 'text-2xl' : compact ? 'text-lg' : 'text-xl'
        ]">
          <router-link 
            :to="{ name: 'public.post', params: { slug: post.slug } }"
            class="hover:underline"
          >
            {{ post.title }}
          </router-link>
        </h3>

        <!-- Excerpt -->
        <p :class="[
          'text-gray-600 mb-4 leading-relaxed',
          featured ? 'text-base line-clamp-3' : 'text-sm line-clamp-2'
        ]">
          {{ post.excerpt || extractExcerpt(post.content) }}
        </p>
      </div>

      <!-- Meta Info -->
      <div class="flex items-center justify-between text-sm text-gray-500">
        <div class="flex items-center space-x-3">
          <!-- Author -->
          <div v-if="post.user" class="flex items-center space-x-2">
            <div class="h-6 w-6 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
              {{ post.user.name.charAt(0).toUpperCase() }}
            </div>
            <span class="hover:text-gray-700 transition-colors cursor-pointer">{{ post.user.name }}</span>
          </div>

          <!-- Date -->
          <div class="flex items-center">
            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <time :datetime="post.published_at || post.created_at">
              {{ formatDate(post.published_at || post.created_at) }}
            </time>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center space-x-2">
          <!-- Bookmark (placeholder) -->
          <button 
            class="p-1 rounded-full hover:bg-gray-100 transition-colors"
            title="Bookmark"
            @click.stop="toggleBookmark"
          >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
            </svg>
          </button>

          <!-- Share -->
          <button 
            class="p-1 rounded-full hover:bg-gray-100 transition-colors"
            title="Share"
            @click.stop="sharePost"
          >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Tags (for featured posts) -->
      <div v-if="featured && post.tags && post.tags.length > 0" class="flex flex-wrap gap-2 mt-4 pt-4 border-t border-gray-100">
        <span 
          v-for="tag in post.tags.slice(0, 3)" 
          :key="tag"
          class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full hover:bg-gray-200 transition-colors cursor-pointer"
        >
          #{{ tag }}
        </span>
        <span v-if="post.tags.length > 3" class="text-gray-500 text-xs">
          +{{ post.tags.length - 3 }} more
        </span>
      </div>
    </div>
  </article>
</template>

<script setup>
import { computed } from 'vue'
import { formatDate } from '@/utils/index.js'
import { useReadingTime } from '@/composables/useReadingTime.js'

const props = defineProps({
  post: {
    type: Object,
    required: true
  },
  featured: {
    type: Boolean,
    default: false
  },
  compact: {
    type: Boolean,
    default: false
  },
  showCategory: {
    type: Boolean,
    default: true
  }
})

const { estimateReadingTime } = useReadingTime()

const readingTime = computed(() => {
  if (!props.post.content) return 1
  return estimateReadingTime(props.post.content)
})

const extractExcerpt = (content) => {
  if (!content) return ''
  const div = document.createElement('div')
  div.innerHTML = content
  const text = div.textContent || div.innerText || ''
  return text.length > 150 ? text.substring(0, 150) + '...' : text
}

const toggleBookmark = () => {
  // Placeholder for bookmark functionality
  console.log('Bookmark toggled for:', props.post.title)
}

const sharePost = () => {
  if (navigator.share) {
    navigator.share({
      title: props.post.title,
      text: props.post.excerpt || extractExcerpt(props.post.content),
      url: window.location.origin + `/posts/${props.post.slug}`
    }).catch(err => console.log('Error sharing:', err))
  } else {
    // Fallback: copy to clipboard
    const url = window.location.origin + `/posts/${props.post.slug}`
    navigator.clipboard.writeText(url).then(() => {
      // Could show a toast notification here
      console.log('Link copied to clipboard')
    })
  }
}
</script>

<style scoped>
.line-clamp-2 {
  overflow: hidden;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 2;
}

.line-clamp-3 {
  overflow: hidden;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 3;
}

.aspect-video {
  aspect-ratio: 16 / 9;
}

@media (min-width: 1024px) {
  .lg\:aspect-square {
    aspect-ratio: 1 / 1;
  }
}
</style>