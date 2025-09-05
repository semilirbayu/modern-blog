<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-semibold text-gray-900">Categories Management</h2>
      <button 
        @click="showCreateForm = true" 
        class="btn btn-primary"
      >
        Create New Category
      </button>
    </div>

    <!-- Create Category Form -->
    <div v-if="showCreateForm" class="card mb-6">
      <div class="card-body">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-medium text-gray-900">Create New Category</h3>
          <button 
            @click="cancelCreate"
            class="text-gray-400 hover:text-gray-600"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        
        <CategoryForm
          :category="null"
          :loading="categoriesStore.loading"
          :errors="categoriesStore.error"
          @save="handleCreate"
          @cancel="cancelCreate"
        />
      </div>
    </div>

    <!-- Categories Table -->
    <div class="card">
      <div class="card-body">
        <LoadingSpinner v-if="categoriesStore.loading && categories.length === 0" class="py-8" />
        
        <div v-else-if="categories.length === 0" class="text-center py-8">
          <div class="text-gray-400 mb-4">
            <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a.997.997 0 01-1.414 0l-7-7A1.997 1.997 0 013 12V7a4 4 0 014-4z"></path>
            </svg>
          </div>
          <h3 class="text-lg font-medium text-gray-900 mb-2">No categories found</h3>
          <p class="text-gray-600 mb-4">Get started by creating your first category.</p>
          <button @click="showCreateForm = true" class="btn btn-primary">
            Create Your First Category
          </button>
        </div>
        
        <div v-else>
          <!-- Table Header -->
          <div class="mb-4 flex justify-between items-center">
            <div class="flex items-center gap-4">
              <h3 class="text-lg font-medium text-gray-900">
                {{ categories.length }} {{ categories.length === 1 ? 'Category' : 'Categories' }}
              </h3>
              
              <div v-if="selectedCategories.length > 0" class="flex items-center gap-2">
                <span class="text-sm text-gray-600">{{ selectedCategories.length }} selected</span>
                <button 
                  @click="bulkDelete" 
                  class="btn btn-danger btn-sm"
                  :disabled="categoriesStore.loading"
                >
                  Delete Selected
                </button>
              </div>
            </div>
            
            <!-- Search -->
            <div class="flex items-center gap-3">
              <div class="relative">
                <input
                  v-model="searchTerm"
                  type="text"
                  placeholder="Search categories..."
                  class="input-field pl-10 w-64"
                />
                <svg class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
              </div>
            </div>
          </div>

          <!-- Table -->
          <div class="overflow-x-auto">
            <table class="min-w-full table">
              <thead>
                <tr>
                  <th class="w-12">
                    <input
                      type="checkbox"
                      :checked="isAllSelected"
                      @change="toggleSelectAll"
                      class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    />
                  </th>
                  <th 
                    @click="handleSort('name')"
                    class="cursor-pointer hover:bg-gray-50"
                  >
                    <div class="flex items-center gap-2">
                      Name
                      <svg v-if="sortField === 'name'" class="w-4 h-4" :class="{ 'rotate-180': sortDirection === 'desc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                      </svg>
                    </div>
                  </th>
                  <th>Slug</th>
                  <th>Description</th>
                  <th 
                    @click="handleSort('posts_count')"
                    class="cursor-pointer hover:bg-gray-50"
                  >
                    <div class="flex items-center gap-2">
                      Posts
                      <svg v-if="sortField === 'posts_count'" class="w-4 h-4" :class="{ 'rotate-180': sortDirection === 'desc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                      </svg>
                    </div>
                  </th>
                  <th 
                    @click="handleSort('created_at')"
                    class="cursor-pointer hover:bg-gray-50"
                  >
                    <div class="flex items-center gap-2">
                      Created
                      <svg v-if="sortField === 'created_at'" class="w-4 h-4" :class="{ 'rotate-180': sortDirection === 'desc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                      </svg>
                    </div>
                  </th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr 
                  v-for="category in filteredCategories" 
                  :key="category.id"
                  :class="{ 'bg-blue-50': selectedCategories.includes(category.id) }"
                >
                  <td>
                    <input
                      type="checkbox"
                      :checked="selectedCategories.includes(category.id)"
                      @change="toggleCategorySelection(category.id)"
                      class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    />
                  </td>
                  <td>
                    <div class="font-medium text-gray-900">{{ category.name }}</div>
                  </td>
                  <td>
                    <code class="text-sm bg-gray-100 px-2 py-1 rounded">{{ category.slug }}</code>
                  </td>
                  <td>
                    <div class="text-gray-600 max-w-xs truncate">
                      {{ category.description || 'No description' }}
                    </div>
                  </td>
                  <td>
                    <div class="flex items-center gap-2">
                      <span class="text-gray-900">{{ category.posts_count || 0 }}</span>
                      <router-link 
                        v-if="category.posts_count > 0"
                        :to="{ name: 'admin.posts', query: { category: category.id } }"
                        class="text-blue-600 hover:text-blue-800 text-sm"
                      >
                        View →
                      </router-link>
                    </div>
                  </td>
                  <td>
                    <div class="text-sm text-gray-600">
                      {{ formatDate(category.created_at) }}
                    </div>
                  </td>
                  <td>
                    <div class="flex items-center gap-2">
                      <button
                        @click="editCategory(category)"
                        class="btn-icon btn-icon-primary"
                        title="Edit category"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                      </button>
                      <button
                        @click="deleteCategory(category)"
                        class="btn-icon btn-icon-danger"
                        title="Delete category"
                        :disabled="category.posts_count > 0"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Category Modal -->
    <div 
      v-if="showEditModal && editingCategory" 
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="flex justify-between items-center p-6 border-b">
          <h3 class="text-lg font-medium text-gray-900">Edit Category</h3>
          <button 
            @click="cancelEdit"
            class="text-gray-400 hover:text-gray-600"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        
        <div class="p-6">
          <CategoryForm
            :category="editingCategory"
            :loading="categoriesStore.loading"
            :errors="categoriesStore.error"
            @save="handleUpdate"
            @cancel="cancelEdit"
          />
        </div>
      </div>
    </div>

    <!-- Confirm Delete Dialog -->
    <ConfirmDialog
      v-if="showDeleteDialog"
      :title="deleteDialogTitle"
      :message="deleteDialogMessage"
      :loading="categoriesStore.loading"
      @confirm="confirmDelete"
      @cancel="cancelDelete"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useCategoriesStore } from '../../stores/categories.js'
import LoadingSpinner from '../../components/common/LoadingSpinner.vue'
import ConfirmDialog from '../../components/common/ConfirmDialog.vue'
import CategoryForm from '../../components/admin/CategoryForm.vue'

const router = useRouter()
const categoriesStore = useCategoriesStore()

const showCreateForm = ref(false)
const showEditModal = ref(false)
const editingCategory = ref(null)
const selectedCategories = ref([])
const showDeleteDialog = ref(false)
const deleteTarget = ref(null)
const searchTerm = ref('')
const sortField = ref('name')
const sortDirection = ref('asc')

const categories = computed(() => categoriesStore.categories)

const filteredCategories = computed(() => {
  let filtered = categories.value || []

  if (searchTerm.value && filtered.length > 0) {
    const search = searchTerm.value.toLowerCase()
    filtered = filtered.filter(category =>
      category.name.toLowerCase().includes(search) ||
      category.slug.toLowerCase().includes(search) ||
      (category.description && category.description.toLowerCase().includes(search))
    )
  }

  return filtered.sort((a, b) => {
    let aValue = a[sortField.value]
    let bValue = b[sortField.value]
    
    if (sortField.value === 'created_at') {
      aValue = new Date(aValue).getTime()
      bValue = new Date(bValue).getTime()
    } else if (typeof aValue === 'string') {
      aValue = aValue.toLowerCase()
      bValue = bValue.toLowerCase()
    }
    
    const comparison = aValue < bValue ? -1 : aValue > bValue ? 1 : 0
    return sortDirection.value === 'asc' ? comparison : -comparison
  })
})

const isAllSelected = computed(() => {
  return filteredCategories.value.length > 0 && 
         selectedCategories.value.length === filteredCategories.value.length
})

const deleteDialogTitle = computed(() => {
  if (deleteTarget.value === 'bulk') {
    return `Delete ${selectedCategories.value.length} Categories`
  }
  const category = categories.value.find(c => c.id === deleteTarget.value)
  return `Delete "${category?.name}"`
})

const deleteDialogMessage = computed(() => {
  if (deleteTarget.value === 'bulk') {
    return `Are you sure you want to delete ${selectedCategories.value.length} selected categories? This action cannot be undone.`
  }
  const category = categories.value.find(c => c.id === deleteTarget.value)
  if (category?.posts_count > 0) {
    return `This category has ${category.posts_count} posts. You cannot delete a category that contains posts. Please move or delete the posts first.`
  }
  return `Are you sure you want to delete "${category?.name}"? This action cannot be undone.`
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}

const handleSort = (field) => {
  if (sortField.value === field) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortField.value = field
    sortDirection.value = 'asc'
  }
}

const toggleCategorySelection = (categoryId) => {
  const index = selectedCategories.value.indexOf(categoryId)
  if (index > -1) {
    selectedCategories.value.splice(index, 1)
  } else {
    selectedCategories.value.push(categoryId)
  }
}

const toggleSelectAll = () => {
  if (isAllSelected.value) {
    selectedCategories.value = []
  } else {
    selectedCategories.value = filteredCategories.value.map(category => category.id)
  }
}

const handleCreate = async (categoryData) => {
  try {
    await categoriesStore.createCategory(categoryData)
    showCreateForm.value = false
    await categoriesStore.fetchCategories()
  } catch (error) {
    console.error('Error creating category:', error)
  }
}

const cancelCreate = () => {
  showCreateForm.value = false
  categoriesStore.clearError()
}

const editCategory = (category) => {
  editingCategory.value = { ...category }
  showEditModal.value = true
}

const handleUpdate = async (categoryData) => {
  try {
    await categoriesStore.updateCategory(editingCategory.value.id, categoryData)
    showEditModal.value = false
    editingCategory.value = null
    await categoriesStore.fetchCategories()
  } catch (error) {
    console.error('Error updating category:', error)
  }
}

const cancelEdit = () => {
  showEditModal.value = false
  editingCategory.value = null
  categoriesStore.clearError()
}

const deleteCategory = (category) => {
  if (category.posts_count > 0) {
    alert(`Cannot delete "${category.name}" because it has ${category.posts_count} posts. Please move or delete the posts first.`)
    return
  }
  
  deleteTarget.value = category.id
  showDeleteDialog.value = true
}

const bulkDelete = () => {
  const categoriesWithPosts = selectedCategories.value
    .map(id => categories.value.find(c => c.id === id))
    .filter(c => c && c.posts_count > 0)
  
  if (categoriesWithPosts.length > 0) {
    alert(`Cannot delete categories that contain posts: ${categoriesWithPosts.map(c => c.name).join(', ')}`)
    return
  }
  
  deleteTarget.value = 'bulk'
  showDeleteDialog.value = true
}

const confirmDelete = async () => {
  try {
    if (deleteTarget.value === 'bulk') {
      await Promise.all(
        selectedCategories.value.map(categoryId => 
          categoriesStore.deleteCategory(categoryId)
        )
      )
      selectedCategories.value = []
    } else {
      await categoriesStore.deleteCategory(deleteTarget.value)
    }
    
    await categoriesStore.fetchCategories()
    cancelDelete()
  } catch (error) {
    console.error('Error deleting categories:', error)
  }
}

const cancelDelete = () => {
  showDeleteDialog.value = false
  deleteTarget.value = null
}

onMounted(async () => {
  document.title = 'Categories - ModernBlog Admin'
  await categoriesStore.fetchCategories()
})
</script>