<template>
  <div class="overflow-x-auto">
    <table class="min-w-full table">
      <thead>
        <tr>
          <th class="w-12">
            <input
              type="checkbox"
              :checked="isAllSelected"
              @change="$emit('selectAll', $event.target.checked)"
              class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
            />
          </th>
          <th 
            @click="handleSort('title')"
            class="cursor-pointer hover:bg-gray-50"
          >
            <div class="flex items-center gap-2">
              Title
              <svg 
                v-if="sortField === 'title'" 
                class="w-4 h-4" 
                :class="{ 'rotate-180': sortDirection === 'desc' }" 
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
              </svg>
            </div>
          </th>
          <th>Category</th>
          <th>Status</th>
          <th>Author</th>
          <th 
            @click="handleSort('created_at')"
            class="cursor-pointer hover:bg-gray-50"
          >
            <div class="flex items-center gap-2">
              Created
              <svg 
                v-if="sortField === 'created_at'" 
                class="w-4 h-4" 
                :class="{ 'rotate-180': sortDirection === 'desc' }" 
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
              </svg>
            </div>
          </th>
          <th 
            @click="handleSort('published_at')"
            class="cursor-pointer hover:bg-gray-50"
          >
            <div class="flex items-center gap-2">
              Published
              <svg 
                v-if="sortField === 'published_at'" 
                class="w-4 h-4" 
                :class="{ 'rotate-180': sortDirection === 'desc' }" 
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
              </svg>
            </div>
          </th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr 
          v-for="post in posts" 
          :key="post.id"
          :class="{ 'bg-blue-50': selectedPosts.includes(post.id) }"
          class="hover:bg-gray-50"
        >
          <td>
            <input
              type="checkbox"
              :checked="selectedPosts.includes(post.id)"
              @change="$emit('selectPost', post.id)"
              class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
            />
          </td>
          <td>
            <div class="max-w-xs">
              <div class="font-medium text-gray-900 truncate">{{ post.title }}</div>
              <div class="text-sm text-gray-500 truncate mt-1" v-if="post.excerpt">
                {{ post.excerpt }}
              </div>
            </div>
          </td>
          <td>
            <span 
              v-if="post.category"
              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
            >
              {{ post.category.name }}
            </span>
            <span v-else class="text-gray-400 text-sm">No category</span>
          </td>
          <td>
            <div class="flex items-center gap-2">
              <StatusBadge :status="post.status" />
              <button
                v-if="!loading"
                @click="$emit('toggleStatus', post.id)"
                class="text-xs text-blue-600 hover:text-blue-800"
                :title="`Change to ${post.status === 'published' ? 'draft' : 'published'}`"
              >
                {{ post.status === 'published' ? 'Draft' : 'Publish' }}
              </button>
            </div>
          </td>
          <td>
            <div class="text-sm text-gray-900">{{ post.author?.name || 'Unknown' }}</div>
          </td>
          <td>
            <div class="text-sm text-gray-600">
              {{ formatDate(post.created_at) }}
            </div>
          </td>
          <td>
            <div class="text-sm text-gray-600">
              {{ post.published_at ? formatDate(post.published_at) : '-' }}
            </div>
          </td>
          <td>
            <div class="flex items-center gap-2">
              <button
                @click="$emit('editPost', post.id)"
                class="btn-icon btn-icon-primary"
                title="Edit post"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
              </button>
              
              <button
                @click="$emit('deletePost', post.id)"
                class="btn-icon btn-icon-danger"
                title="Delete post"
                :disabled="loading"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
              </button>
              
              <!-- Quick actions dropdown -->
              <div class="relative">
                <button
                  @click="toggleDropdown(post.id)"
                  class="btn-icon btn-icon-secondary"
                  title="More actions"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                  </svg>
                </button>
                
                <div 
                  v-if="activeDropdown === post.id"
                  class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-10"
                >
                  <div class="py-1">
                    <a 
                      :href="getPublicUrl(post.slug)" 
                      target="_blank"
                      class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    >
                      <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                        View Post
                      </div>
                    </a>
                    <button
                      @click="copyPostUrl(post.slug)"
                      class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    >
                      <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        Copy URL
                      </div>
                    </button>
                    <button
                      @click="duplicatePost(post)"
                      class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    >
                      <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        Duplicate
                      </div>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
    
    <!-- Loading overlay -->
    <div v-if="loading" class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center">
      <LoadingSpinner />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import StatusBadge from './StatusBadge.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'

const props = defineProps({
  posts: {
    type: Array,
    required: true
  },
  selectedPosts: {
    type: Array,
    default: () => []
  },
  loading: {
    type: Boolean,
    default: false
  },
  sortField: {
    type: String,
    default: 'created_at'
  },
  sortDirection: {
    type: String,
    default: 'desc'
  }
})

const emit = defineEmits([
  'selectPost',
  'selectAll', 
  'editPost',
  'deletePost',
  'toggleStatus',
  'sort'
])

const activeDropdown = ref(null)

const isAllSelected = computed(() => {
  return props.posts.length > 0 && props.selectedPosts.length === props.posts.length
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}

const handleSort = (field) => {
  emit('sort', {
    field,
    direction: props.sortField === field && props.sortDirection === 'asc' ? 'desc' : 'asc'
  })
}

const toggleDropdown = (postId) => {
  activeDropdown.value = activeDropdown.value === postId ? null : postId
}

const getPublicUrl = (slug) => {
  return `/posts/${slug}`
}

const copyPostUrl = async (slug) => {
  const url = `${window.location.origin}/posts/${slug}`
  try {
    await navigator.clipboard.writeText(url)
    // Could emit an event here to show a success message
  } catch (error) {
    console.error('Failed to copy URL:', error)
  }
  activeDropdown.value = null
}

const duplicatePost = (post) => {
  // This would typically emit an event to the parent component
  // which would handle the duplication logic
  emit('duplicatePost', post)
  activeDropdown.value = null
}

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    activeDropdown.value = null
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>