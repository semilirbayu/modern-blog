<template>
  <div class="p-8">
    <h1 class="text-4xl font-bold text-gray-900 mb-6">
      Test Home Page
    </h1>
    <p class="text-lg text-gray-600">
      This is a test page to verify Vue routing is working without store dependencies.
    </p>
    <div class="mt-4">
      <p>Current time: {{ currentTime }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const currentTime = ref('')

onMounted(() => {
  currentTime.value = new Date().toLocaleString()
})
</script>