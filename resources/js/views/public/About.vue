<template>
  <div class="max-w-4xl mx-auto">
    <div class="text-center py-12">
      <h1 class="text-4xl font-bold text-gray-900 mb-8">About ModernBlog</h1>
      
      <div class="prose prose-lg mx-auto">
        <p class="lead text-xl text-gray-600 mb-8">
          ModernBlog is a cutting-edge blogging platform built with modern web technologies 
          to provide the best experience for both content creators and readers.
        </p>
        
        <div class="grid md:grid-cols-2 gap-8 text-left">
          <div class="card">
            <div class="card-body">
              <h3 class="text-xl font-semibold mb-4">Built with Modern Tech</h3>
              <ul class="space-y-2 text-gray-600">
                <li>• Laravel 11 - Robust PHP framework</li>
                <li>• Vue.js 3 - Progressive JavaScript framework</li>
                <li>• Tailwind CSS - Utility-first CSS framework</li>
                <li>• Vite - Fast build tool and dev server</li>
                <li>• Pinia - State management for Vue</li>
              </ul>
            </div>
          </div>
          
          <div class="card">
            <div class="card-body">
              <h3 class="text-xl font-semibold mb-4">Key Features</h3>
              <ul class="space-y-2 text-gray-600">
                <li>• API-first architecture</li>
                <li>• Responsive design</li>
                <li>• SEO optimized</li>
                <li>• Fast performance</li>
                <li>• Admin dashboard</li>
              </ul>
            </div>
          </div>
        </div>
        
        <div class="mt-12 text-center">
          <h3 class="text-2xl font-semibold mb-4">Our Mission</h3>
          <p class="text-gray-600 max-w-2xl mx-auto">
            We believe in the power of sharing knowledge and stories. ModernBlog provides 
            a platform where writers can focus on creating amazing content while we handle 
            the technical complexities behind the scenes.
          </p>
        </div>
        
        <div class="mt-12 text-center">
          <router-link to="/contact" class="btn btn-primary btn-lg">
            Get in Touch
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'

onMounted(() => {
  document.title = 'About - ModernBlog'
})
</script>