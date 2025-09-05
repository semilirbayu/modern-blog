<template>
  <form @submit.prevent="handleSubmit" class="space-y-4">
    <!-- Name Field -->
    <div>
      <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
        Name *
      </label>
      <input
        id="name"
        v-model="form.name"
        type="text"
        class="input-field"
        :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': errors.name }"
        placeholder="Enter category name..."
        required
        @input="handleNameChange"
      />
      <div v-if="errors.name" class="mt-1 text-sm text-red-600">
        {{ errors.name[0] }}
      </div>
    </div>

    <!-- Slug Field -->
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
          placeholder="category-slug"
          required
          @input="customSlugEdited = true"
        />
        <button
          type="button"
          @click="regenerateSlug"
          class="ml-2 px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-md border border-gray-300"
          title="Regenerate slug from name"
        >
          🔄
        </button>
      </div>
      <div v-if="errors.slug" class="mt-1 text-sm text-red-600">
        {{ errors.slug[0] }}
      </div>
      <div class="mt-1 text-xs text-gray-500">
        URL-friendly version of the name. Will be used in category URLs.
      </div>
    </div>

    <!-- Description Field -->
    <div>
      <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
        Description
      </label>
      <textarea
        id="description"
        v-model="form.description"
        rows="3"
        class="input-field"
        :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': errors.description }"
        placeholder="Brief description of this category (optional)..."
      ></textarea>
      <div v-if="errors.description" class="mt-1 text-sm text-red-600">
        {{ errors.description[0] }}
      </div>
      <div class="mt-1 text-xs text-gray-500">
        Optional description that may be displayed in category listings.
      </div>
    </div>

    <!-- Form Actions -->
    <div class="flex items-center justify-between pt-4">
      <div class="flex items-center gap-3">
        <button
          type="submit"
          :disabled="loading || !canSave"
          class="btn btn-primary"
        >
          <LoadingSpinner v-if="loading" class="w-4 h-4 mr-2" />
          {{ isEditing ? 'Update Category' : 'Create Category' }}
        </button>
        
        <button
          type="button"
          @click="handleCancel"
          :disabled="loading"
          class="btn btn-secondary"
        >
          Cancel
        </button>
      </div>

      <!-- Character counts -->
      <div class="flex items-center gap-4 text-xs text-gray-500">
        <div>
          Name: {{ form.name.length }}/100
        </div>
        <div v-if="form.description">
          Description: {{ form.description.length }}/500
        </div>
      </div>
    </div>

    <!-- Validation Summary -->
    <div v-if="Object.keys(errors).length > 0" class="p-3 bg-red-50 border border-red-200 rounded-md">
      <div class="text-sm text-red-800">
        <div class="font-medium mb-2">Please correct the following errors:</div>
        <ul class="list-disc list-inside space-y-1">
          <li v-for="(errorList, field) in errors" :key="field">
            <strong class="capitalize">{{ field }}:</strong> {{ errorList[0] }}
          </li>
        </ul>
      </div>
    </div>

    <!-- Preview -->
    <div v-if="form.name || form.description" class="mt-6 p-4 bg-gray-50 rounded-md">
      <h4 class="text-sm font-medium text-gray-900 mb-2">Preview</h4>
      <div class="space-y-2">
        <div class="font-medium text-gray-900">
          {{ form.name || 'Category Name' }}
        </div>
        <div class="text-sm text-gray-600">
          <span class="font-mono bg-gray-200 px-1 py-0.5 rounded text-xs">
            /categories/{{ form.slug || 'category-slug' }}
          </span>
        </div>
        <div v-if="form.description" class="text-sm text-gray-600">
          {{ form.description }}
        </div>
      </div>
    </div>
  </form>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import { useSlug } from '@/composables/useSlug'

const props = defineProps({
  category: {
    type: Object,
    default: null
  },
  loading: {
    type: Boolean,
    default: false
  },
  errors: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['save', 'cancel'])

const { generateSlug } = useSlug()

const form = ref({
  name: '',
  slug: '',
  description: ''
})

const customSlugEdited = ref(false)

const isEditing = computed(() => !!props.category)

const canSave = computed(() => {
  return form.value.name.trim() && 
         form.value.slug.trim() && 
         form.value.name.length <= 100 &&
         form.value.description.length <= 500
})

const handleNameChange = () => {
  if (!customSlugEdited.value) {
    form.value.slug = generateSlug(form.value.name)
  }
}

const regenerateSlug = () => {
  form.value.slug = generateSlug(form.value.name)
  customSlugEdited.value = false
}

const handleSubmit = () => {
  if (!canSave.value) return
  
  const categoryData = {
    name: form.value.name.trim(),
    slug: form.value.slug.trim(),
    description: form.value.description.trim()
  }
  
  emit('save', categoryData)
}

const handleCancel = () => {
  emit('cancel')
}

// Initialize form with existing category data
const initializeForm = () => {
  if (props.category) {
    form.value = {
      name: props.category.name || '',
      slug: props.category.slug || '',
      description: props.category.description || ''
    }
    customSlugEdited.value = true
  } else {
    form.value = {
      name: '',
      slug: '',
      description: ''
    }
    customSlugEdited.value = false
  }
}

// Watch for changes in the category prop
watch(() => props.category, initializeForm, { immediate: true })

// Auto-focus on the name field when component is mounted
onMounted(() => {
  const nameInput = document.getElementById('name')
  if (nameInput) {
    nameInput.focus()
  }
})
</script>