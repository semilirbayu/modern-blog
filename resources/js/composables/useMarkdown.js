import { ref, computed } from 'vue'
import { marked } from 'marked'
import hljs from 'highlight.js'
import DOMPurify from 'dompurify'

export function useMarkdown() {
  // Configure marked renderer with syntax highlighting
  const renderer = new marked.Renderer()
  
  // Configure code highlighting
  marked.setOptions({
    highlight: function(code, lang) {
      if (lang && hljs.getLanguage(lang)) {
        try {
          return hljs.highlight(code, { language: lang }).value
        } catch (err) {
          console.warn('Highlighting error:', err)
        }
      }
      try {
        return hljs.highlightAuto(code).value
      } catch (err) {
        console.warn('Auto-highlighting error:', err)
        return code
      }
    },
    langPrefix: 'hljs language-',
    breaks: true,
    gfm: true
  })

  const parseMarkdown = (markdown) => {
    if (!markdown || typeof markdown !== 'string') {
      return ''
    }

    try {
      return marked(markdown, { renderer })
    } catch (error) {
      console.error('Markdown parsing error:', error)
      return `<p>Error parsing markdown content</p>`
    }
  }

  // Sanitize HTML to prevent XSS attacks using DOMPurify
  const sanitizeHtml = (html) => {
    if (!html) return ''
    
    // Configure DOMPurify with allowed tags and attributes
    const config = {
      ALLOWED_TAGS: [
        'p', 'br', 'strong', 'em', 'u', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'ul', 'ol', 'li', 'blockquote', 'pre', 'code', 'a', 'img', 'span'
      ],
      ALLOWED_ATTR: [
        'href', 'target', 'rel', 'src', 'alt', 'class', 'title'
      ],
      ALLOW_DATA_ATTR: false,
      FORBID_SCRIPTS: true,
      FORBID_TAGS: ['script', 'object', 'embed', 'form', 'input'],
      FORBID_ATTR: ['style', 'onerror', 'onload', 'onclick'],
      ADD_ATTR: {
        'a': { 'rel': 'noopener noreferrer', 'target': '_blank' }
      }
    }
    
    return DOMPurify.sanitize(html, config)
  }

  // Parse and sanitize markdown
  const parseAndSanitize = (markdown) => {
    const html = parseMarkdown(markdown)
    return sanitizeHtml(html)
  }

  // Extract plain text from markdown
  const extractPlainText = (markdown) => {
    if (!markdown) return ''
    
    // Remove markdown syntax
    let text = markdown
    
    // Remove headers
    text = text.replace(/^#{1,6}\s+/gm, '')
    
    // Remove emphasis
    text = text.replace(/\*\*(.*?)\*\*/g, '$1')
    text = text.replace(/\*(.*?)\*/g, '$1')
    
    // Remove code blocks
    text = text.replace(/```[\s\S]*?```/g, '')
    text = text.replace(/`([^`]+)`/g, '$1')
    
    // Remove links but keep text
    text = text.replace(/\[([^\]]+)\]\([^)]+\)/g, '$1')
    
    // Remove images
    text = text.replace(/!\[[^\]]*\]\([^)]+\)/g, '')
    
    // Remove blockquotes
    text = text.replace(/^>\s+/gm, '')
    
    // Remove list markers
    text = text.replace(/^[\-\*\+]\s+/gm, '')
    text = text.replace(/^\d+\.\s+/gm, '')
    
    // Clean up whitespace
    text = text.replace(/\s+/g, ' ').trim()
    
    return text
  }

  // Generate excerpt from markdown
  const generateExcerpt = (markdown, maxLength = 150) => {
    const plainText = extractPlainText(markdown)
    
    if (plainText.length <= maxLength) {
      return plainText
    }
    
    // Find a good break point (end of sentence or word)
    const truncated = plainText.substring(0, maxLength)
    const lastSentence = truncated.lastIndexOf('.')
    const lastSpace = truncated.lastIndexOf(' ')
    
    let breakPoint = maxLength
    
    if (lastSentence > maxLength * 0.7) {
      breakPoint = lastSentence + 1
    } else if (lastSpace > maxLength * 0.7) {
      breakPoint = lastSpace
    }
    
    return plainText.substring(0, breakPoint).trim() + '...'
  }

  // Count words in markdown
  const countWords = (markdown) => {
    const plainText = extractPlainText(markdown)
    if (!plainText.trim()) return 0
    return plainText.trim().split(/\s+/).length
  }

  // Estimate reading time
  const estimateReadingTime = (markdown, wordsPerMinute = 200) => {
    const wordCount = countWords(markdown)
    return Math.ceil(wordCount / wordsPerMinute)
  }

  // Validate markdown syntax
  const validateMarkdown = (markdown) => {
    const errors = []
    
    if (!markdown || typeof markdown !== 'string') {
      return { isValid: true, errors: [] }
    }
    
    // Check for unclosed code blocks
    const codeBlockMatches = markdown.match(/```/g)
    if (codeBlockMatches && codeBlockMatches.length % 2 !== 0) {
      errors.push('Unclosed code block found')
    }
    
    // Check for unclosed inline code
    const inlineCodeMatches = markdown.match(/`/g)
    if (inlineCodeMatches && inlineCodeMatches.length % 2 !== 0) {
      errors.push('Unclosed inline code found')
    }
    
    // Check for malformed links
    const linkRegex = /\[([^\]]*)\]\(([^)]*)\)/g
    let linkMatch
    while ((linkMatch = linkRegex.exec(markdown)) !== null) {
      if (!linkMatch[2].trim()) {
        errors.push(`Empty URL in link: "${linkMatch[1]}"`)
      }
    }
    
    // Check for malformed images
    const imageRegex = /!\[([^\]]*)\]\(([^)]*)\)/g
    let imageMatch
    while ((imageMatch = imageRegex.exec(markdown)) !== null) {
      if (!imageMatch[2].trim()) {
        errors.push(`Empty image URL: "${imageMatch[1]}"`)
      }
    }
    
    return {
      isValid: errors.length === 0,
      errors
    }
  }

  // Composable for reactive markdown processing
  const useReactiveMarkdown = (source) => {
    const html = computed(() => parseAndSanitize(source.value || ''))
    const plainText = computed(() => extractPlainText(source.value || ''))
    const wordCount = computed(() => countWords(source.value || ''))
    const readingTime = computed(() => estimateReadingTime(source.value || ''))
    const excerpt = computed(() => generateExcerpt(source.value || ''))
    const validation = computed(() => validateMarkdown(source.value || ''))
    
    return {
      html,
      plainText,
      wordCount,
      readingTime,
      excerpt,
      validation
    }
  }

  return {
    parseMarkdown,
    sanitizeHtml,
    parseAndSanitize,
    extractPlainText,
    generateExcerpt,
    countWords,
    estimateReadingTime,
    validateMarkdown,
    useReactiveMarkdown
  }
}