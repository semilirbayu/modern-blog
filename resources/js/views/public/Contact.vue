<template>
  <div class="max-w-4xl mx-auto">
    <div class="text-center py-12">
      <h1 class="text-4xl font-bold text-gray-900 mb-4">Contact Us</h1>
      <p class="text-xl text-gray-600 mb-12">
        Have a question or want to get in touch? We'd love to hear from you.
      </p>
      
      <div class="grid md:grid-cols-2 gap-12">
        <!-- Contact Form -->
        <div class="card">
          <div class="card-header">
            <h2 class="text-xl font-semibold">Send us a message</h2>
          </div>
          <div class="card-body">
            <form @submit.prevent="submitForm" class="space-y-6">
              <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                  Name *
                </label>
                <input
                  id="name"
                  v-model="form.name"
                  type="text"
                  required
                  class="form-input"
                  :class="{ 'border-red-300': errors.name }"
                  placeholder="Your full name"
                />
                <p v-if="errors.name" class="form-error">{{ errors.name[0] }}</p>
              </div>
              
              <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                  Email *
                </label>
                <input
                  id="email"
                  v-model="form.email"
                  type="email"
                  required
                  class="form-input"
                  :class="{ 'border-red-300': errors.email }"
                  placeholder="your.email@example.com"
                />
                <p v-if="errors.email" class="form-error">{{ errors.email[0] }}</p>
              </div>
              
              <div>
                <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">
                  Subject *
                </label>
                <input
                  id="subject"
                  v-model="form.subject"
                  type="text"
                  required
                  class="form-input"
                  :class="{ 'border-red-300': errors.subject }"
                  placeholder="What is this about?"
                />
                <p v-if="errors.subject" class="form-error">{{ errors.subject[0] }}</p>
              </div>
              
              <div>
                <label for="message" class="block text-sm font-medium text-gray-700 mb-1">
                  Message *
                </label>
                <textarea
                  id="message"
                  v-model="form.message"
                  rows="6"
                  required
                  class="form-textarea"
                  :class="{ 'border-red-300': errors.message }"
                  placeholder="Tell us more about your message..."
                ></textarea>
                <p v-if="errors.message" class="form-error">{{ errors.message[0] }}</p>
              </div>
              
              <div class="flex items-center justify-between">
                <button
                  type="submit"
                  :disabled="loading"
                  class="btn btn-primary"
                >
                  <span v-if="loading" class="spinner h-4 w-4 mr-2"></span>
                  {{ loading ? 'Sending...' : 'Send Message' }}
                </button>
                
                <button
                  type="button"
                  @click="resetForm"
                  class="btn btn-outline"
                >
                  Clear
                </button>
              </div>
              
              <div v-if="success" class="alert alert-success">
                Thank you for your message! We'll get back to you soon.
              </div>
              
              <div v-if="generalError" class="alert alert-error">
                {{ generalError }}
              </div>
            </form>
          </div>
        </div>
        
        <!-- Contact Information -->
        <div class="space-y-8">
          <div class="card">
            <div class="card-body text-center">
              <div class="text-blue-600 mb-4">
                <svg class="h-8 w-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
              </div>
              <h3 class="text-lg font-semibold mb-2">Email</h3>
              <p class="text-gray-600">hello@modernblog.com</p>
            </div>
          </div>
          
          <div class="card">
            <div class="card-body text-center">
              <div class="text-green-600 mb-4">
                <svg class="h-8 w-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </div>
              <h3 class="text-lg font-semibold mb-2">Location</h3>
              <p class="text-gray-600">Everywhere on the web</p>
            </div>
          </div>
          
          <div class="card">
            <div class="card-body text-center">
              <div class="text-purple-600 mb-4">
                <svg class="h-8 w-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <h3 class="text-lg font-semibold mb-2">Response Time</h3>
              <p class="text-gray-600">Within 24 hours</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue'

const form = reactive({
  name: '',
  email: '',
  subject: '',
  message: ''
})

const errors = ref({})
const loading = ref(false)
const success = ref(false)
const generalError = ref('')

const submitForm = async () => {
  loading.value = true
  errors.value = {}
  success.value = false
  generalError.value = ''
  
  try {
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1000))
    
    // For demo purposes, just show success
    success.value = true
    resetForm()
  } catch (err) {
    generalError.value = 'Failed to send message. Please try again later.'
  } finally {
    loading.value = false
  }
}

const resetForm = () => {
  Object.keys(form).forEach(key => {
    form[key] = ''
  })
  errors.value = {}
  success.value = false
  generalError.value = ''
}

onMounted(() => {
  document.title = 'Contact - ModernBlog'
})
</script>