import { ref, computed, onMounted } from 'vue'

export function useReadingTime() {
  // Average reading speed (words per minute)
  const WORDS_PER_MINUTE = 225 // Average adult reading speed
  const SLOW_READER_WPM = 180  // Slower reading speed
  const FAST_READER_WPM = 300  // Faster reading speed

  const extractTextFromHTML = (html) => {
    if (!html) return ''
    
    // Create a temporary div to parse HTML
    const div = document.createElement('div')
    div.innerHTML = html
    
    // Remove script and style elements
    const scripts = div.querySelectorAll('script, style')
    scripts.forEach(el => el.remove())
    
    // Get text content
    const text = div.textContent || div.innerText || ''
    
    return text.trim()
  }

  const countWords = (text) => {
    if (!text || typeof text !== 'string') return 0
    
    // Remove extra whitespace and split by whitespace
    const words = text
      .replace(/\s+/g, ' ') // Replace multiple spaces with single space
      .trim()
      .split(/\s+/)
      .filter(word => word.length > 0)
    
    return words.length
  }

  const estimateReadingTime = (content, wordsPerMinute = WORDS_PER_MINUTE) => {
    if (!content) return 1
    
    let text = content
    
    // If content appears to be HTML, extract text
    if (content.includes('<') && content.includes('>')) {
      text = extractTextFromHTML(content)
    }
    
    const wordCount = countWords(text)
    
    if (wordCount === 0) return 1
    
    // Calculate reading time in minutes
    const readingTimeMinutes = wordCount / wordsPerMinute
    
    // Round up to nearest minute, minimum 1 minute
    return Math.max(Math.ceil(readingTimeMinutes), 1)
  }

  const getReadingTimeRange = (content) => {
    const slowTime = estimateReadingTime(content, SLOW_READER_WPM)
    const fastTime = estimateReadingTime(content, FAST_READER_WPM)
    
    return {
      min: fastTime,
      max: slowTime,
      average: estimateReadingTime(content, WORDS_PER_MINUTE)
    }
  }

  const formatReadingTime = (minutes, format = 'short') => {
    if (format === 'long') {
      if (minutes === 1) {
        return '1 minute read'
      }
      return `${minutes} minutes read`
    }
    
    // Default short format
    return `${minutes} min`
  }

  const extractHeadings = (content) => {
    if (!content || !content.includes('<')) return []
    
    const div = document.createElement('div')
    div.innerHTML = content
    
    const headings = div.querySelectorAll('h1, h2, h3, h4, h5, h6')
    
    return Array.from(headings).map((heading, index) => {
      const level = parseInt(heading.tagName.charAt(1))
      const text = heading.textContent || ''
      const id = heading.id || `heading-${index}`
      
      // Set ID if not present
      if (!heading.id) {
        heading.id = id
      }
      
      return {
        id,
        text: text.trim(),
        level,
        element: heading
      }
    })
  }

  const getContentStats = (content) => {
    if (!content) {
      return {
        wordCount: 0,
        characterCount: 0,
        readingTime: 1,
        headings: [],
        paragraphs: 0
      }
    }
    
    let text = content
    let wordCount = 0
    let characterCount = 0
    let paragraphs = 0
    let headings = []
    
    // If content is HTML
    if (content.includes('<') && content.includes('>')) {
      const div = document.createElement('div')
      div.innerHTML = content
      
      // Count paragraphs
      paragraphs = div.querySelectorAll('p').length
      
      // Extract headings
      headings = extractHeadings(content)
      
      // Get clean text
      text = extractTextFromHTML(content)
    } else {
      // Plain text - estimate paragraphs by line breaks
      paragraphs = text.split(/\n\s*\n/).filter(p => p.trim()).length
    }
    
    wordCount = countWords(text)
    characterCount = text.length
    
    return {
      wordCount,
      characterCount,
      readingTime: estimateReadingTime(content),
      headings,
      paragraphs
    }
  }

  // Create a reactive reading time calculator
  const createReadingTimeCalculator = (initialContent = '') => {
    const content = ref(initialContent)
    const wordsPerMinute = ref(WORDS_PER_MINUTE)
    
    const stats = computed(() => getContentStats(content.value))
    
    const readingTime = computed(() => 
      estimateReadingTime(content.value, wordsPerMinute.value)
    )
    
    const readingTimeRange = computed(() => 
      getReadingTimeRange(content.value)
    )
    
    const formattedReadingTime = computed(() => 
      formatReadingTime(readingTime.value)
    )
    
    const updateContent = (newContent) => {
      content.value = newContent
    }
    
    const setWordsPerMinute = (wpm) => {
      wordsPerMinute.value = wpm
    }
    
    return {
      content,
      wordsPerMinute,
      stats,
      readingTime,
      readingTimeRange,
      formattedReadingTime,
      updateContent,
      setWordsPerMinute
    }
  }

  return {
    // Core functions
    extractTextFromHTML,
    countWords,
    estimateReadingTime,
    getReadingTimeRange,
    formatReadingTime,
    extractHeadings,
    getContentStats,
    createReadingTimeCalculator,
    
    // Constants
    WORDS_PER_MINUTE,
    SLOW_READER_WPM,
    FAST_READER_WPM
  }
}