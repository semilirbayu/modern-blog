<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          Sign in to ModernBlog
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
          Admin Dashboard Access
        </p>
      </div>
      
      <form class="mt-8 space-y-6" @submit.prevent="handleLogin">
        <div class="space-y-4">
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">
              Email Address
            </label>
            <input
              id="email"
              v-model="form.email"
              name="email"
              type="email"
              required
              class="form-input mt-1"
              :class="{ 'border-red-300': errors.email || fieldErrors.email }"
              placeholder="admin@example.com"
              @blur="handleFieldBlur('email')"
              @input="clearFieldError('email')"
            />
            <p v-if="errors.email || fieldErrors.email" class="form-error">
              {{ errors.email?.[0] || fieldErrors.email }}
            </p>
          </div>
          
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">
              Password
            </label>
            <input
              id="password"
              v-model="form.password"
              name="password"
              type="password"
              required
              class="form-input mt-1"
              :class="{ 'border-red-300': errors.password || fieldErrors.password }"
              placeholder="Your password"
              @blur="handleFieldBlur('password')"
              @input="clearFieldError('password')"
            />
            <p v-if="errors.password || fieldErrors.password" class="form-error">
              {{ errors.password?.[0] || fieldErrors.password }}
            </p>
          </div>
        </div>

        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <input
              id="remember_me"
              v-model="form.remember"
              name="remember"
              type="checkbox"
              class="form-checkbox"
            />
            <label for="remember_me" class="ml-2 block text-sm text-gray-900">
              Remember me
            </label>
          </div>
        </div>

        <div>
          <button
            type="submit"
            :disabled="loading || !isFormValid"
            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <LoadingSpinner 
              v-if="loading" 
              variant="button" 
              color="white" 
              class="mr-2"
            />
            {{ loading ? 'Signing in...' : 'Sign in' }}
          </button>
        </div>
        
        <div v-if="error" class="alert alert-error">
          {{ error }}
        </div>
        
        <div v-if="success" class="alert alert-success">
          Login successful! Redirecting...
        </div>
      </form>
      
      <div class="text-center">
        <router-link to="/" class="text-blue-600 hover:text-blue-500">
          ← Back to Blog
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '../../composables/useAuth.js'
import { validateEmail, validatePassword, validateForm } from '../../utils/validation.js'
import LoadingSpinner from '../../components/common/LoadingSpinner.vue'

const router = useRouter()
const { login, isAuthenticated } = useAuth()

const form = reactive({
  email: '',
  password: '',
  remember: false
})

const errors = ref({})
const fieldErrors = ref({})
const loading = ref(false)
const error = ref('')
const success = ref(false)

const isFormValid = computed(() => {
  return form.email && form.password && Object.keys(fieldErrors.value).length === 0
})

const validateField = (field, value) => {
  switch (field) {
    case 'email':
      return validateEmail(value)
    case 'password':
      return validatePassword(value, 6)
    default:
      return null
  }
}

const handleFieldBlur = (field) => {
  const error = validateField(field, form[field])
  if (error) {
    fieldErrors.value[field] = error
  } else {
    delete fieldErrors.value[field]
  }
}

const clearFieldError = (field) => {
  delete fieldErrors.value[field]
  if (errors.value[field]) {
    delete errors.value[field]
  }
}

const handleLogin = async () => {
  // Validate form before submitting
  const validation = validateForm(
    { email: form.email, password: form.password },
    {
      email: [validateEmail],
      password: [(value) => validatePassword(value, 6)]
    }
  )
  
  if (!validation.isValid) {
    fieldErrors.value = validation.errors
    return
  }
  
  loading.value = true
  errors.value = {}
  fieldErrors.value = {}
  error.value = ''
  success.value = false
  
  try {
    const result = await login({
      email: form.email.trim(),
      password: form.password,
      remember: form.remember
    })
    
    if (result.success) {
      success.value = true
      // Immediate redirect without delay for better UX
      router.push({ name: 'admin.dashboard' })
    } else {
      error.value = result.error || 'Login failed. Please check your credentials.'
    }
  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors || {}
    }
    error.value = err.response?.data?.message || err.message || 'Login failed. Please try again.'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  document.title = 'Admin Login - ModernBlog'
  
  // Redirect if already authenticated
  if (isAuthenticated.value) {
    router.push({ name: 'admin.dashboard' })
  }
})
</script>