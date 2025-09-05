<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-semibold text-gray-900">Categories Management</h2>
      <button class="btn btn-primary">
        Create New Category
      </button>
    </div>
    
    <div class="card">
      <div class="card-body">
        <p>Categories management page is working! This is a simplified version to test routing.</p>
        <p>The full Categories component has complex logic that is causing JavaScript errors.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'

onMounted(() => {
  document.title = 'Categories - ModernBlog Admin'
})
</script>