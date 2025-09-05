import { ref } from 'vue'

export function useSlug() {
  const generateSlug = (text) => {
    if (!text || typeof text !== 'string') {
      return ''
    }

    return text
      .toString()
      .toLowerCase()
      .trim()
      // Replace spaces with hyphens
      .replace(/\s+/g, '-')
      // Remove all non-word chars except hyphens
      .replace(/[^\w\-]+/g, '')
      // Replace multiple hyphens with single hyphen
      .replace(/\-\-+/g, '-')
      // Remove hyphens from start and end
      .replace(/^-+/, '')
      .replace(/-+$/, '')
  }

  const validateSlug = (slug) => {
    if (!slug || typeof slug !== 'string') {
      return false
    }

    // Check if slug matches the expected pattern
    const slugPattern = /^[a-z0-9]+(?:-[a-z0-9]+)*$/
    return slugPattern.test(slug)
  }

  const createUniqueSlug = async (baseSlug, checkExistence) => {
    if (!checkExistence || typeof checkExistence !== 'function') {
      return baseSlug
    }

    let slug = baseSlug
    let counter = 1

    // Check if the base slug already exists
    while (await checkExistence(slug)) {
      slug = `${baseSlug}-${counter}`
      counter++
    }

    return slug
  }

  const debounceSlugGeneration = (callback, delay = 300) => {
    let timeoutId
    
    return (...args) => {
      clearTimeout(timeoutId)
      timeoutId = setTimeout(() => callback.apply(null, args), delay)
    }
  }

  // Composable for reactive slug generation
  const useReactiveSlug = (sourceText, options = {}) => {
    const {
      autoGenerate = true,
      customEdited = ref(false),
      checkExistence = null,
      debounceDelay = 300
    } = options

    const slug = ref('')
    const isGenerating = ref(false)

    const updateSlug = async (text) => {
      if (!autoGenerate || customEdited.value) {
        return
      }

      isGenerating.value = true
      
      try {
        let newSlug = generateSlug(text)
        
        if (checkExistence) {
          newSlug = await createUniqueSlug(newSlug, checkExistence)
        }
        
        slug.value = newSlug
      } catch (error) {
        console.error('Error generating slug:', error)
      } finally {
        isGenerating.value = false
      }
    }

    const debouncedUpdateSlug = debounceSlugGeneration(updateSlug, debounceDelay)

    const setCustomSlug = (customSlug) => {
      slug.value = generateSlug(customSlug)
      customEdited.value = true
    }

    const resetToAuto = (text) => {
      customEdited.value = false
      updateSlug(text)
    }

    return {
      slug,
      isGenerating,
      updateSlug: debouncedUpdateSlug,
      setCustomSlug,
      resetToAuto,
      customEdited
    }
  }

  // Helper function to extract slug from URL or path
  const extractSlugFromUrl = (url) => {
    if (!url || typeof url !== 'string') {
      return ''
    }

    // Remove protocol and domain if present
    const path = url.replace(/^https?:\/\/[^\/]+/, '')
    
    // Extract the last segment of the path
    const segments = path.split('/').filter(segment => segment.length > 0)
    return segments.pop() || ''
  }

  // Helper function to create a slug from multiple text sources
  const createSlugFromSources = (...sources) => {
    const combinedText = sources
      .filter(source => source && typeof source === 'string')
      .join(' ')
    
    return generateSlug(combinedText)
  }

  // Helper function to suggest alternative slugs
  const suggestAlternativeSlug = (originalSlug) => {
    const suggestions = []
    
    if (!originalSlug) {
      return suggestions
    }

    // Add timestamp-based suggestion
    const timestamp = Date.now().toString().slice(-6)
    suggestions.push(`${originalSlug}-${timestamp}`)

    // Add date-based suggestion
    const today = new Date().toISOString().slice(0, 10).replace(/-/g, '')
    suggestions.push(`${originalSlug}-${today}`)

    // Add random number suggestion
    const randomNum = Math.floor(Math.random() * 1000)
    suggestions.push(`${originalSlug}-${randomNum}`)

    return suggestions
  }

  // Helper function to format slug for display
  const formatSlugForDisplay = (slug, maxLength = 50) => {
    if (!slug || typeof slug !== 'string') {
      return ''
    }

    if (slug.length <= maxLength) {
      return slug
    }

    return slug.substring(0, maxLength - 3) + '...'
  }

  return {
    generateSlug,
    validateSlug,
    createUniqueSlug,
    debounceSlugGeneration,
    useReactiveSlug,
    extractSlugFromUrl,
    createSlugFromSources,
    suggestAlternativeSlug,
    formatSlugForDisplay
  }
}