export const formatDate = (date, options = {}) => {
    if (!date) return ''
    
    const defaultOptions = {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    }
    
    try {
        return new Intl.DateTimeFormat('en-US', { ...defaultOptions, ...options })
            .format(new Date(date))
    } catch (error) {
        console.warn('Invalid date format:', date)
        return ''
    }
}

export const formatDateRelative = (date) => {
    const now = new Date()
    const postDate = new Date(date)
    const diffInMs = now - postDate
    const diffInDays = Math.floor(diffInMs / (1000 * 60 * 60 * 24))
    
    if (diffInDays === 0) {
        return 'Today'
    } else if (diffInDays === 1) {
        return 'Yesterday'
    } else if (diffInDays < 7) {
        return `${diffInDays} days ago`
    } else if (diffInDays < 30) {
        const weeks = Math.floor(diffInDays / 7)
        return `${weeks} week${weeks > 1 ? 's' : ''} ago`
    } else if (diffInDays < 365) {
        const months = Math.floor(diffInDays / 30)
        return `${months} month${months > 1 ? 's' : ''} ago`
    } else {
        const years = Math.floor(diffInDays / 365)
        return `${years} year${years > 1 ? 's' : ''} ago`
    }
}

export const truncateText = (text, maxLength = 100, suffix = '...') => {
    if (!text || text.length <= maxLength) {
        return text
    }
    
    return text.substring(0, maxLength).trim() + suffix
}

export const generateSlug = (text) => {
    return text
        .toLowerCase()
        .trim()
        .replace(/[^\w\s-]/g, '')
        .replace(/[\s_-]+/g, '-')
        .replace(/^-+|-+$/g, '')
}

export const extractExcerpt = (content, maxLength = 160) => {
    const textContent = content.replace(/<[^>]*>/g, '')
    return truncateText(textContent, maxLength)
}

export const debounce = (func, wait) => {
    let timeout
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout)
            func(...args)
        }
        clearTimeout(timeout)
        timeout = setTimeout(later, wait)
    }
}

export const throttle = (func, limit) => {
    let inThrottle
    return function() {
        const args = arguments
        const context = this
        if (!inThrottle) {
            func.apply(context, args)
            inThrottle = true
            setTimeout(() => inThrottle = false, limit)
        }
    }
}

export const capitalize = (str) => {
    if (!str) return ''
    return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase()
}

export const pluralize = (count, singular, plural = null) => {
    if (count === 1) {
        return singular
    }
    return plural || (singular + 's')
}

export const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes'
    
    const k = 1024
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB']
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

export const copyToClipboard = async (text) => {
    try {
        await navigator.clipboard.writeText(text)
        return true
    } catch (err) {
        console.error('Failed to copy text: ', err)
        return false
    }
}

export const validateEmail = (email) => {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    return emailRegex.test(email)
}

export const validateUrl = (url) => {
    try {
        new URL(url)
        return true
    } catch {
        return false
    }
}

export const sanitizeHtml = (html) => {
    const div = document.createElement('div')
    div.textContent = html
    return div.innerHTML
}