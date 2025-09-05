<template>
  <div>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <div>
        <h2 class="text-2xl font-semibold text-gray-900">
          {{ isEditing ? 'Edit Post' : 'Create New Post' }}
        </h2>
        <p class="text-sm text-gray-600 mt-1" v-if="isEditing && post">
          Last updated: {{ formatDate(post.updated_at) }}
        </p>
      </div>
      
      <div class="flex items-center gap-3">
        <button 
          v-if="isDraft && !isEditing"
          @click="saveAsDraft"
          :disabled="postsStore.loading || !canSave"
          class="btn btn-secondary"
        >
          <LoadingSpinner v-if="postsStore.loading && !isPublishing" class="w-4 h-4 mr-2" />
          Save Draft
        </button>
        
        <button 
          @click="publish"
          :disabled="postsStore.loading || !canSave"
          class="btn btn-primary"
        >
          <LoadingSpinner v-if="postsStore.loading && isPublishing" class="w-4 h-4 mr-2" />
          {{ isEditing ? 'Update' : 'Publish' }}
        </button>
        
        <router-link :to="{ name: 'admin.posts' }" class="btn btn-outline">
          Cancel
        </router-link>
      </div>
    </div>

    <form @submit.prevent="handleSubmit" class="space-y-6">
      <!-- Title and Slug -->
      <div class="card">
        <div class="card-body space-y-4">
          <div>
            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
              Title *
            </label>
            <input
              id="title"
              v-model="form.title"
              type="text"
              class="input-field"
              :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': errors.title }"
              placeholder="Enter post title..."
              required
              @input="handleTitleChange"
            />
            <div v-if="errors.title" class="mt-1 text-sm text-red-600">
              {{ errors.title[0] }}
            </div>
          </div>

          <div>
            <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
              Slug *
            </label>
            <div class="flex">
              <input
                id="slug"
                v-model="form.slug"
                type="text"
                class="input-field flex-1"
                :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': errors.slug }"
                placeholder="post-slug"
                required
                @input="customSlugEdited = true"
              />
              <button
                type="button"
                @click="regenerateSlug"
                class="ml-2 px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-md border border-gray-300"
                title="Regenerate slug from title"
              >
                🔄
              </button>
            </div>
            <div v-if="errors.slug" class="mt-1 text-sm text-red-600">
              {{ errors.slug[0] }}
            </div>
          </div>

          <div>
            <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-2">
              Excerpt
            </label>
            <textarea
              id="excerpt"
              v-model="form.excerpt"
              rows="3"
              class="input-field"
              :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': errors.excerpt }"
              placeholder="Brief description of the post..."
            ></textarea>
            <div v-if="errors.excerpt" class="mt-1 text-sm text-red-600">
              {{ errors.excerpt[0] }}
            </div>
          </div>
        </div>
      </div>

      <!-- Content Editor -->
      <div class="card">
        <div class="card-body">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Content *
          </label>
          <MarkdownEditor
            v-model="form.content"
            :error="errors.content?.[0]"
            @change="handleContentChange"
          />
        </div>
      </div>

      <!-- Post Settings -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Category and Status -->
        <div class="card">
          <div class="card-body space-y-4">
            <h3 class="text-lg font-medium text-gray-900">Post Settings</h3>
            
            <div>
              <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                Category *
              </label>
              <select
                id="category"
                v-model="form.category_id"
                class="input-field"
                :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': errors.category_id }"
                required
              >
                <option value="">Select a category</option>
                <option 
                  v-for="category in categories" 
                  :key="category.id" 
                  :value="category.id"
                >
                  {{ category.name }}
                </option>
              </select>
              <div v-if="errors.category_id" class="mt-1 text-sm text-red-600">
                {{ errors.category_id[0] }}
              </div>
              
              <router-link 
                :to="{ name: 'admin.categories' }"
                class="text-sm text-blue-600 hover:text-blue-800 mt-1 inline-block"
              >
                Manage categories →
              </router-link>
            </div>

            <div>
              <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                Status
              </label>
              <select
                id="status"
                v-model="form.status"
                class="input-field"
              >
                <option value="draft">Draft</option>
                <option value="published">Published</option>
              </select>
            </div>

            <div v-if="form.status === 'published'">
              <label for="published_at" class="block text-sm font-medium text-gray-700 mb-2">
                Publish Date
              </label>
              <input
                id="published_at"
                v-model="publishedAtLocal"
                type="datetime-local"
                class="input-field"
              />
              <div class="mt-1 text-xs text-gray-500">
                Leave empty to use current date/time
              </div>
            </div>
          </div>
        </div>

        <!-- Featured Image -->
        <div class="card">
          <div class="card-body space-y-4">
            <h3 class="text-lg font-medium text-gray-900">Featured Image</h3>
            
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
              <div v-if="featuredImagePreview" class="mb-4">
                <img 
                  :src="featuredImagePreview" 
                  :alt="form.title || 'Featured image'"
                  class="max-w-full h-48 object-cover rounded-lg mx-auto"
                />
                <button
                  type="button"
                  @click="removeFeaturedImage"
                  class="mt-2 text-sm text-red-600 hover:text-red-800"
                >
                  Remove image
                </button>
              </div>
              
              <div v-else>
                <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                
                <input
                  ref="fileInput"
                  type="file"
                  accept="image/*"
                  @change="handleFileUpload"
                  class="hidden"
                />
                
                <button
                  type="button"
                  @click="$refs.fileInput.click()"
                  class="btn btn-secondary"
                >
                  Upload Featured Image
                </button>
                
                <p class="mt-2 text-xs text-gray-500">
                  PNG, JPG, GIF up to 10MB
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Auto-save Status -->
      <div v-if="showAutoSaveStatus" class="fixed bottom-4 right-4 bg-white shadow-lg rounded-lg p-3 border">
        <div class="flex items-center text-sm">
          <div class="w-2 h-2 rounded-full mr-2" :class="{
            'bg-green-500': autoSaveStatus === 'saved',
            'bg-yellow-500': autoSaveStatus === 'saving',
            'bg-red-500': autoSaveStatus === 'error'
          }"></div>
          <span class="text-gray-700">
            {{ autoSaveStatusText }}
          </span>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch, nextTick } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { usePostsStore } from '@/stores/posts'
import { useCategoriesStore } from '@/stores/categories'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import MarkdownEditor from '@/components/admin/MarkdownEditor.vue'
import { useSlug } from '@/composables/useSlug'

const route = useRoute()
const router = useRouter()
const postsStore = usePostsStore()
const categoriesStore = useCategoriesStore()
const { generateSlug } = useSlug()

const isEditing = computed(() => !!route.params.id)
const post = computed(() => postsStore.currentPost)
const categories = computed(() => categoriesStore.categories)
const errors = computed(() => postsStore.error || {})

const form = ref({
  title: '',
  slug: '',
  content: '',
  excerpt: '',
  category_id: '',
  status: 'draft',
  published_at: null,
  featured_image: null
})

const customSlugEdited = ref(false)
const isPublishing = ref(false)
const isDraft = computed(() => form.value.status === 'draft')
const featuredImagePreview = ref(null)
const publishedAtLocal = ref('')

const autoSaveStatus = ref(null)
const showAutoSaveStatus = ref(false)
const autoSaveTimer = ref(null)

const canSave = computed(() => {
  return form.value.title.trim() && 
         form.value.slug.trim() && 
         form.value.content.trim() && 
         form.value.category_id
})

const autoSaveStatusText = computed(() => {
  switch (autoSaveStatus.value) {
    case 'saving': return 'Saving draft...'
    case 'saved': return 'Draft saved'
    case 'error': return 'Failed to save draft'
    default: return ''
  }
})

const formatDate = (date) => {
  return new Date(date).toLocaleString()
}

const handleTitleChange = () => {
  if (!customSlugEdited.value) {
    form.value.slug = generateSlug(form.value.title)
  }
  scheduleAutoSave()
}

const handleContentChange = () => {
  scheduleAutoSave()
}

const regenerateSlug = () => {
  form.value.slug = generateSlug(form.value.title)
  customSlugEdited.value = false
}

const handleFileUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    form.value.featured_image = file
    
    const reader = new FileReader()
    reader.onload = (e) => {
      featuredImagePreview.value = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

const removeFeaturedImage = () => {
  form.value.featured_image = null
  featuredImagePreview.value = null
  
  if (isEditing.value && post.value.featured_image) {
    form.value.featured_image = 'remove'
  }
}

const scheduleAutoSave = () => {
  if (!isEditing.value || !canSave.value) return
  
  clearTimeout(autoSaveTimer.value)
  autoSaveTimer.value = setTimeout(async () => {
    await saveAsDraft(true)
  }, 2000)
}

const toFormData = (data) => {
  const fd = new FormData()
  Object.entries(data).forEach(([k, v]) => {
    if (v !== null && v !== undefined) fd.append(k, v)
  })
  return fd
}

const saveAsDraft = async (isAutoSave = false) => {
  if (!canSave.value) return
  
  const saveData = {
    ...form.value,
    status: 'draft',
    published_at: null
  }
  
  try {
    if (!isAutoSave) {
      isPublishing.value = false
    } else {
      autoSaveStatus.value = 'saving'
      showAutoSaveStatus.value = true
    }
    
    const payload = form.value.featured_image instanceof File ? toFormData(saveData) : saveData
    
    let result
    if (isEditing.value) {
      result = await postsStore.updatePost(route.params.id, payload)
    } else {
      result = await postsStore.createPost(payload)
      if (result?.data?.id) {
        router.replace({ name: 'admin.posts.edit', params: { id: result.data.id } })
      }
    }
    
    if (isAutoSave) {
      autoSaveStatus.value = 'saved'
      setTimeout(() => {
        showAutoSaveStatus.value = false
      }, 2000)
    }
    
    if (!isAutoSave) {
      router.push({ name: 'admin.posts' })
    }
  } catch (error) {
    if (isAutoSave) {
      autoSaveStatus.value = 'error'
    }
    console.error('Error saving draft:', error)
  }
}

const publish = async () => {
  if (!canSave.value) return
  
  isPublishing.value = true
  
  const saveData = {
    ...form.value,
    status: 'published',
    published_at: publishedAtLocal.value ? 
      new Date(publishedAtLocal.value).toISOString() : 
      new Date().toISOString()
  }
  
  const payload = form.value.featured_image instanceof File ? toFormData(saveData) : saveData
  
  try {
    if (isEditing.value) {
      await postsStore.updatePost(route.params.id, payload)
    } else {
      await postsStore.createPost(payload)
    }
    
    router.push({ name: 'admin.posts' })
  } catch (error) {
    console.error('Error publishing post:', error)
  } finally {
    isPublishing.value = false
  }
}

const handleSubmit = () => {
  if (form.value.status === 'published') {
    publish()
  } else {
    saveAsDraft()
  }
}

const loadPost = async () => {
  if (isEditing.value) {
    await postsStore.fetchPost(route.params.id)
    if (post.value) {
      form.value = {
        title: post.value.title || '',
        slug: post.value.slug || '',
        content: post.value.content || '',
        excerpt: post.value.excerpt || '',
        category_id: post.value.category_id || '',
        status: post.value.status || 'draft',
        published_at: post.value.published_at,
        featured_image: null
      }
      
      if (post.value.featured_image) {
        featuredImagePreview.value = post.value.featured_image
      }
      
      if (post.value.published_at) {
        const date = new Date(post.value.published_at)
        publishedAtLocal.value = date.toISOString().slice(0, 16)
      }
      
      customSlugEdited.value = true
    }
  }
}

onMounted(async () => {
  document.title = `${isEditing.value ? 'Edit' : 'Create'} Post - ModernBlog Admin`
  
  await Promise.all([
    categoriesStore.fetchCategories(),
    loadPost()
  ])
})

watch(() => form.value.status, (newStatus) => {
  if (newStatus === 'published' && !publishedAtLocal.value) {
    const now = new Date()
    publishedAtLocal.value = now.toISOString().slice(0, 16)
  }
})

watch(() => publishedAtLocal.value, (newValue) => {
  if (newValue) {
    form.value.published_at = new Date(newValue).toISOString()
  }
})
</script>