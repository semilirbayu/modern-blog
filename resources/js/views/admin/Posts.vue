<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-semibold text-gray-900">Posts Management</h2>
      <div class="flex items-center gap-3">
        <div v-if="selectedPosts.length > 0" class="flex items-center gap-2">
          <span class="text-sm text-gray-600">{{ selectedPosts.length }} selected</span>
          <button @click="bulkDelete" class="btn btn-danger btn-sm">
            Delete Selected
          </button>
          <button @click="bulkUpdateStatus('published')" class="btn btn-success btn-sm">
            Publish Selected
          </button>
          <button @click="bulkUpdateStatus('draft')" class="btn btn-warning btn-sm">
            Draft Selected
          </button>
        </div>
        <router-link :to="{ name: 'admin.posts.create' }" class="btn btn-primary">
          Create New Post
        </router-link>
      </div>
    </div>
    
    <!-- Filters Section -->
    <PostFilters 
      :filters="filters"
      :categories="categories"
      @update-filters="handleFiltersUpdate"
      @clear-filters="clearFilters"
      class="mb-6"
    />
    
    <!-- Main Content Card -->
    <div class="card">
      <div class="card-body">
        <LoadingSpinner v-if="postsStore.loading" class="py-8" />
        
        <div v-else-if="posts.length === 0" class="text-center py-8">
          <div class="text-gray-400 mb-4">
            <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
          </div>
          <h3 class="text-lg font-medium text-gray-900 mb-2">No posts found</h3>
          <p class="text-gray-600 mb-4">
            {{ hasActiveFilters ? 'No posts match your current filters.' : 'Get started by creating your first post.' }}
          </p>
          <router-link v-if="!hasActiveFilters" :to="{ name: 'admin.posts.create' }" class="btn btn-primary">
            Create Your First Post
          </router-link>
          <button v-else @click="clearFilters" class="btn btn-secondary">
            Clear Filters
          </button>
        </div>
        
        <div v-else>
          <!-- Posts Table -->
          <PostsTable 
            :posts="posts"
            :selected-posts="selectedPosts"
            :loading="postsStore.loading"
            @select-post="togglePostSelection"
            @select-all="toggleSelectAll"
            @edit-post="handleEditPost"
            @delete-post="handleDeletePost"
            @toggle-status="handleToggleStatus"
            @sort="handleSort"
            :sort-field="sortField"
            :sort-direction="sortDirection"
          />
          
          <!-- Pagination -->
          <div v-if="uiPagination.total > uiPagination.perPage" class="mt-6">
            <Pagination 
              :pagination="uiPagination"
              @page-change="handlePageChange"
              @per-page-change="handlePerPageChange"
            />
          </div>
        </div>
      </div>
    </div>
    
    <!-- Confirm Delete Dialog -->
    <ConfirmDialog
      v-if="showDeleteDialog"
      :title="deleteDialogTitle"
      :message="deleteDialogMessage"
      :loading="postsStore.loading"
      @confirm="confirmDelete"
      @cancel="cancelDelete"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { usePostsStore } from '../../stores/posts.js'
import { useCategoriesStore } from '../../stores/categories.js'
import LoadingSpinner from '../../components/common/LoadingSpinner.vue'
import ConfirmDialog from '../../components/common/ConfirmDialog.vue'
import PostsTable from '../../components/admin/PostsTable.vue'
import PostFilters from '../../components/admin/PostFilters.vue'
import Pagination from '../../components/admin/Pagination.vue'

const router = useRouter()
const route = useRoute()
const postsStore = usePostsStore()
const categoriesStore = useCategoriesStore()

const selectedPosts = ref([])
const showDeleteDialog = ref(false)
const deleteTarget = ref(null)
const sortField = ref('created_at')
const sortDirection = ref('desc')

const filters = ref({
  search: '',
  status: 'all',
  category: 'all'
})

const posts = computed(() => postsStore.posts)
const categories = computed(() => categoriesStore.categories)
const pagination = computed(() => postsStore.pagination)
const uiPagination = computed(() => ({
  currentPage: postsStore.pagination.current_page,
  lastPage: postsStore.pagination.last_page,
  perPage: postsStore.pagination.per_page,
  total: postsStore.pagination.total
}))

const hasActiveFilters = computed(() => {
  return filters.value.search !== '' || 
         filters.value.status !== 'all' || 
         filters.value.category !== 'all'
})

const deleteDialogTitle = computed(() => {
  if (deleteTarget.value === 'bulk') {
    return `Delete ${selectedPosts.value.length} Posts`
  }
  return 'Delete Post'
})

const deleteDialogMessage = computed(() => {
  if (deleteTarget.value === 'bulk') {
    return `Are you sure you want to delete ${selectedPosts.value.length} selected posts? This action cannot be undone.`
  }
  const post = posts.value.find(p => p.id === deleteTarget.value)
  return `Are you sure you want to delete "${post?.title}"? This action cannot be undone.`
})

const fetchPosts = async (page = 1) => {
  const params = {
    page,
    per_page: postsStore.pagination.per_page || 15,
    sort: sortField.value,
    direction: sortDirection.value,
    ...filters.value
  }
  
  if (params.status === 'all') delete params.status
  if (params.category === 'all') delete params.category
  if (!params.search) delete params.search
  
  await postsStore.fetchPosts(params)
}

const handleFiltersUpdate = (newFilters) => {
  filters.value = { ...newFilters }
  selectedPosts.value = []
  fetchPosts(1)
}

const clearFilters = () => {
  filters.value = {
    search: '',
    status: 'all',
    category: 'all'
  }
  selectedPosts.value = []
  fetchPosts(1)
}

const handleSort = ({ field, direction }) => {
  sortField.value = field
  sortDirection.value = direction
  fetchPosts(uiPagination.value.currentPage)
}

const handlePageChange = (page) => {
  fetchPosts(page)
}

const handlePerPageChange = (perPage) => {
  postsStore.pagination.per_page = perPage
  fetchPosts(1)
}

const togglePostSelection = (postId) => {
  const index = selectedPosts.value.indexOf(postId)
  if (index > -1) {
    selectedPosts.value.splice(index, 1)
  } else {
    selectedPosts.value.push(postId)
  }
}

const toggleSelectAll = (selected) => {
  if (selected) {
    selectedPosts.value = posts.value.map(post => post.id)
  } else {
    selectedPosts.value = []
  }
}

const handleEditPost = (postId) => {
  router.push({ name: 'admin.posts.edit', params: { id: postId } })
}

const handleDeletePost = (postId) => {
  deleteTarget.value = postId
  showDeleteDialog.value = true
}

const handleToggleStatus = async (postId) => {
  const post = posts.value.find(p => p.id === postId)
  if (!post) return
  
  const newStatus = post.status === 'published' ? 'draft' : 'published'
  const updateData = { status: newStatus }
  
  if (newStatus === 'published' && !post.published_at) {
    updateData.published_at = new Date().toISOString()
  }
  
  try {
    await postsStore.updatePost(postId, updateData)
    fetchPosts(uiPagination.value.currentPage)
  } catch (error) {
    console.error('Error updating post status:', error)
  }
}

const bulkDelete = () => {
  deleteTarget.value = 'bulk'
  showDeleteDialog.value = true
}

const bulkUpdateStatus = async (status) => {
  try {
    const updates = selectedPosts.value.map(postId => {
      const updateData = { status }
      if (status === 'published') {
        const post = posts.value.find(p => p.id === postId)
        if (!post.published_at) {
          updateData.published_at = new Date().toISOString()
        }
      }
      return postsStore.updatePost(postId, updateData)
    })
    
    await Promise.all(updates)
    selectedPosts.value = []
    fetchPosts(uiPagination.value.currentPage)
  } catch (error) {
    console.error('Error updating posts status:', error)
  }
}

const confirmDelete = async () => {
  try {
    if (deleteTarget.value === 'bulk') {
      await Promise.all(
        selectedPosts.value.map(postId => postsStore.deletePost(postId))
      )
      selectedPosts.value = []
    } else {
      await postsStore.deletePost(deleteTarget.value)
    }
    
    fetchPosts(uiPagination.value.currentPage)
    cancelDelete()
  } catch (error) {
    console.error('Error deleting posts:', error)
  }
}

const cancelDelete = () => {
  showDeleteDialog.value = false
  deleteTarget.value = null
}

onMounted(async () => {
  document.title = 'Posts - ModernBlog Admin'
  
  // Initialize filters from route query parameters
  const q = route.query
  filters.value = { 
    ...filters.value, 
    category: q.category ?? 'all',
    status: q.status ?? 'all',
    search: q.search ?? ''
  }
  
  await Promise.all([
    fetchPosts(1),
    categoriesStore.fetchCategories()
  ])
})

watch(() => uiPagination.value.currentPage, (newPage) => {
  if (newPage && newPage !== uiPagination.value.currentPage) {
    fetchPosts(newPage)
  }
})
</script>