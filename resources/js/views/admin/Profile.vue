<template>
  <div>
    <div class="mb-6">
      <h2 class="text-2xl font-semibold text-gray-900">Profile Settings</h2>
    </div>
    
    <div class="card">
      <div class="card-body">
        <p class="text-gray-600">Profile management interface will be implemented here.</p>
        <p class="text-sm text-gray-500 mt-2">
          This will include user information editing, password change, and account settings.
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'

onMounted(() => {
  document.title = 'Profile - ModernBlog Admin'
})
</script>