<template>
  <div 
    v-if="isVisible"
    class="fixed top-0 left-0 right-0 z-50 h-1 bg-gray-200"
  >
    <div 
      class="h-full bg-gradient-to-r from-blue-500 to-blue-600 transition-all duration-150 ease-out"
      :style="{ width: progress + '%' }"
    ></div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick } from 'vue'

const progress = ref(0)
const isVisible = ref(false)

const calculateProgress = () => {
  // Get the main content area (article or main content)
  const article = document.querySelector('article') || document.querySelector('main') || document.body
  
  if (!article) {
    progress.value = 0
    return
  }

  const windowHeight = window.innerHeight
  const documentHeight = article.scrollHeight
  const scrollTop = window.scrollY
  
  // Start showing progress after scrolling a bit
  const startOffset = windowHeight * 0.1 // Start after 10% of viewport height
  
  if (scrollTop < startOffset) {
    progress.value = 0
    isVisible.value = false
    return
  }
  
  isVisible.value = true
  
  // Calculate progress based on article content
  const articleTop = article.offsetTop
  const articleHeight = article.scrollHeight
  const articleBottom = articleTop + articleHeight
  
  // Adjust scroll position relative to article
  const adjustedScrollTop = scrollTop + windowHeight
  const readableArea = articleBottom - articleTop - windowHeight
  const readProgress = (adjustedScrollTop - articleTop) / readableArea
  
  progress.value = Math.min(Math.max(readProgress * 100, 0), 100)
}

const throttledCalculateProgress = (() => {
  let isThrottled = false
  
  return () => {
    if (isThrottled) return
    
    isThrottled = true
    requestAnimationFrame(() => {
      calculateProgress()
      isThrottled = false
    })
  }
})()

onMounted(() => {
  nextTick(() => {
    // Initial calculation
    calculateProgress()
    
    // Add scroll listener
    window.addEventListener('scroll', throttledCalculateProgress, { passive: true })
    window.addEventListener('resize', throttledCalculateProgress, { passive: true })
  })
})

onUnmounted(() => {
  window.removeEventListener('scroll', throttledCalculateProgress)
  window.removeEventListener('resize', throttledCalculateProgress)
})
</script>