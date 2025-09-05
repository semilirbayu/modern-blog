<template>
  <div>
    <h2 class="text-2xl font-semibold text-gray-900 mb-6">Posts Management</h2>
    <div class="card">
      <div class="card-body">
        <p>Posts management page is working! This is a simplified version to test routing.</p>
        <p>The full Posts component has complex imports that are causing issues.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'

onMounted(() => {
  document.title = 'Posts - ModernBlog Admin'
})
</script>