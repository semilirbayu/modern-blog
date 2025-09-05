<template>
  <div class="text-center py-16">
    <div class="mb-8">
      <div class="text-9xl font-bold text-gray-300 mb-4">404</div>
      <h1 class="text-4xl font-bold text-gray-900 mb-4">Page Not Found</h1>
      <p class="text-xl text-gray-600 mb-8 max-w-md mx-auto">
        Sorry, we couldn't find the page you're looking for. 
        It might have been moved or deleted.
      </p>
    </div>
    
    <div class="space-x-4">
      <router-link to="/" class="btn btn-primary">
        Go Home
      </router-link>
      <button @click="goBack" class="btn btn-outline">
        Go Back
      </button>
    </div>
    
    <div class="mt-12">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">Popular Pages</h3>
      <div class="flex flex-wrap justify-center gap-4">
        <router-link to="/" class="text-blue-600 hover:text-blue-800">
          Home
        </router-link>
        <router-link to="/about" class="text-blue-600 hover:text-blue-800">
          About
        </router-link>
        <router-link to="/contact" class="text-blue-600 hover:text-blue-800">
          Contact
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { onMounted } from 'vue'

const router = useRouter()

const goBack = () => {
  if (window.history.length > 1) {
    router.go(-1)
  } else {
    router.push('/')
  }
}

onMounted(() => {
  document.title = '404 - Page Not Found - ModernBlog'
})
</script>