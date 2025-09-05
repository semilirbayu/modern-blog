import { ref, onMounted, onUnmounted, computed } from 'vue'

export function useInfiniteScroll(loadMore, options = {}) {
  const {
    distance = 100,          // Distance from bottom to trigger loading (pixels)
    immediate = true,        // Whether to start observing immediately
    disabled = ref(false),   // Whether infinite scroll is disabled
    interval = 100,          // Throttle interval for scroll events (ms)
    direction = 'bottom',    // Scroll direction: 'bottom' | 'top'
    target = null           // Target element to observe (default: window)
  } = options

  const isLoading = ref(false)
  const isObserving = ref(false)
  const arrivedState = ref({
    bottom: false,
    top: false
  })

  let targetElement = null
  let throttleTimer = null

  const getTargetElement = () => {
    if (target) {
      return typeof target === 'string' 
        ? document.querySelector(target)
        : target.value || target
    }
    return null
  }

  const getScrollElement = () => {
    return targetElement || window
  }

  const getScrollValues = () => {
    if (targetElement) {
      return {
        scrollTop: targetElement.scrollTop,
        scrollHeight: targetElement.scrollHeight,
        clientHeight: targetElement.clientHeight
      }
    } else {
      return {
        scrollTop: window.pageYOffset || document.documentElement.scrollTop,
        scrollHeight: document.documentElement.scrollHeight,
        clientHeight: window.innerHeight
      }
    }
  }

  const checkIfShouldLoad = () => {
    if (disabled.value || isLoading.value) return false

    const { scrollTop, scrollHeight, clientHeight } = getScrollValues()

    // Check bottom direction
    if (direction === 'bottom') {
      const distanceFromBottom = scrollHeight - scrollTop - clientHeight
      arrivedState.value.bottom = distanceFromBottom <= distance
      return arrivedState.value.bottom
    }

    // Check top direction
    if (direction === 'top') {
      arrivedState.value.top = scrollTop <= distance
      return arrivedState.value.top
    }

    return false
  }

  const handleScroll = () => {
    if (throttleTimer) return

    throttleTimer = setTimeout(() => {
      if (checkIfShouldLoad()) {
        executeLoadMore()
      }
      throttleTimer = null
    }, interval)
  }

  const executeLoadMore = async () => {
    if (isLoading.value || disabled.value) return

    isLoading.value = true
    
    try {
      await loadMore()
    } catch (error) {
      console.error('Infinite scroll load more error:', error)
    } finally {
      isLoading.value = false
    }
  }

  const startObserving = () => {
    if (isObserving.value) return

    targetElement = getTargetElement()
    const scrollElement = getScrollElement()

    if (scrollElement) {
      scrollElement.addEventListener('scroll', handleScroll, { passive: true })
      // Also listen for resize events in case content changes
      window.addEventListener('resize', handleScroll, { passive: true })
      isObserving.value = true

      // Check initial state
      handleScroll()
    }
  }

  const stopObserving = () => {
    if (!isObserving.value) return

    const scrollElement = getScrollElement()
    if (scrollElement) {
      scrollElement.removeEventListener('scroll', handleScroll)
      window.removeEventListener('resize', handleScroll)
    }

    isObserving.value = false

    // Clear any pending throttle timer
    if (throttleTimer) {
      clearTimeout(throttleTimer)
      throttleTimer = null
    }
  }

  const reset = () => {
    arrivedState.value = {
      bottom: false,
      top: false
    }
    isLoading.value = false
  }

  // Computed properties
  const canLoadMore = computed(() => {
    return !disabled.value && !isLoading.value && isObserving.value
  })

  const arrivedAtBottom = computed(() => arrivedState.value.bottom)
  const arrivedAtTop = computed(() => arrivedState.value.top)

  // Auto-start if immediate is true
  if (immediate) {
    onMounted(() => {
      startObserving()
    })
  }

  // Clean up on unmount
  onUnmounted(() => {
    stopObserving()
  })

  // Manual trigger function
  const trigger = () => {
    if (canLoadMore.value) {
      executeLoadMore()
    }
  }

  // Scroll to position functions
  const scrollToTop = (behavior = 'smooth') => {
    const scrollElement = getScrollElement()
    if (targetElement) {
      targetElement.scrollTo({ top: 0, behavior })
    } else {
      window.scrollTo({ top: 0, behavior })
    }
  }

  const scrollToBottom = (behavior = 'smooth') => {
    const { scrollHeight } = getScrollValues()
    const scrollElement = getScrollElement()
    
    if (targetElement) {
      targetElement.scrollTo({ top: scrollHeight, behavior })
    } else {
      window.scrollTo({ top: scrollHeight, behavior })
    }
  }

  const scrollBy = (offset, behavior = 'smooth') => {
    const scrollElement = getScrollElement()
    
    if (targetElement) {
      targetElement.scrollBy({ top: offset, behavior })
    } else {
      window.scrollBy({ top: offset, behavior })
    }
  }

  return {
    // State
    isLoading,
    isObserving,
    canLoadMore,
    arrivedAtBottom,
    arrivedAtTop,
    arrivedState,

    // Methods
    startObserving,
    stopObserving,
    reset,
    trigger,
    
    // Scroll utilities
    scrollToTop,
    scrollToBottom,
    scrollBy
  }
}

// Utility function for creating infinite scroll with intersection observer
export function useInfiniteScrollObserver(loadMore, options = {}) {
  const {
    rootMargin = '100px',
    threshold = 0.1,
    target = null,
    immediate = true,
    disabled = ref(false)
  } = options

  const isLoading = ref(false)
  const targetRef = ref(null)
  let observer = null

  const startObserving = () => {
    if (observer || disabled.value) return

    const targetElement = target?.value || targetRef.value
    if (!targetElement) return

    observer = new IntersectionObserver(
      ([entry]) => {
        if (entry.isIntersecting && !isLoading.value && !disabled.value) {
          executeLoadMore()
        }
      },
      { rootMargin, threshold }
    )

    observer.observe(targetElement)
  }

  const stopObserving = () => {
    if (observer) {
      observer.disconnect()
      observer = null
    }
  }

  const executeLoadMore = async () => {
    if (isLoading.value || disabled.value) return

    isLoading.value = true
    
    try {
      await loadMore()
    } catch (error) {
      console.error('Infinite scroll observer load more error:', error)
    } finally {
      isLoading.value = false
    }
  }

  if (immediate) {
    onMounted(() => {
      startObserving()
    })
  }

  onUnmounted(() => {
    stopObserving()
  })

  return {
    targetRef,
    isLoading,
    startObserving,
    stopObserving
  }
}