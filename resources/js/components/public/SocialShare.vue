<template>
  <div class="flex items-center space-x-2">
    <!-- Twitter/X Share -->
    <button
      @click="shareOnTwitter"
      class="p-2 rounded-full hover:bg-blue-50 text-gray-500 hover:text-blue-600 transition-colors group"
      title="Share on Twitter"
      aria-label="Share on Twitter"
    >
      <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
        <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
      </svg>
      <span class="sr-only">Share on Twitter</span>
    </button>

    <!-- Facebook Share -->
    <button
      @click="shareOnFacebook"
      class="p-2 rounded-full hover:bg-blue-50 text-gray-500 hover:text-blue-600 transition-colors group"
      title="Share on Facebook"
      aria-label="Share on Facebook"
    >
      <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
        <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd"/>
      </svg>
      <span class="sr-only">Share on Facebook</span>
    </button>

    <!-- LinkedIn Share -->
    <button
      @click="shareOnLinkedIn"
      class="p-2 rounded-full hover:bg-blue-50 text-gray-500 hover:text-blue-700 transition-colors group"
      title="Share on LinkedIn"
      aria-label="Share on LinkedIn"
    >
      <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
      </svg>
      <span class="sr-only">Share on LinkedIn</span>
    </button>

    <!-- WhatsApp Share -->
    <button
      @click="shareOnWhatsApp"
      class="p-2 rounded-full hover:bg-green-50 text-gray-500 hover:text-green-600 transition-colors group"
      title="Share on WhatsApp"
      aria-label="Share on WhatsApp"
    >
      <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.520-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
      </svg>
      <span class="sr-only">Share on WhatsApp</span>
    </button>

    <!-- Copy Link -->
    <button
      @click="copyLink"
      class="p-2 rounded-full hover:bg-gray-50 text-gray-500 hover:text-gray-700 transition-colors group relative"
      title="Copy link"
      aria-label="Copy link to clipboard"
    >
      <svg v-if="!copied" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
      </svg>
      <svg v-else class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
      </svg>
      <span class="sr-only">{{ copied ? 'Link copied' : 'Copy link' }}</span>
      
      <!-- Tooltip -->
      <div 
        v-if="copied"
        class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white text-xs px-2 py-1 rounded whitespace-nowrap"
      >
        Copied!
      </div>
    </button>

    <!-- Email Share -->
    <button
      @click="shareViaEmail"
      class="p-2 rounded-full hover:bg-gray-50 text-gray-500 hover:text-gray-700 transition-colors group"
      title="Share via email"
      aria-label="Share via email"
    >
      <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
      </svg>
      <span class="sr-only">Share via email</span>
    </button>

    <!-- More Options (Native Share API) -->
    <button
      v-if="canUseNativeShare"
      @click="shareNative"
      class="p-2 rounded-full hover:bg-gray-50 text-gray-500 hover:text-gray-700 transition-colors group"
      title="More sharing options"
      aria-label="More sharing options"
    >
      <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z" />
      </svg>
      <span class="sr-only">More sharing options</span>
    </button>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'

const props = defineProps({
  post: {
    type: Object,
    default: null
  },
  url: {
    type: String,
    default: ''
  },
  title: {
    type: String,
    default: ''
  },
  description: {
    type: String,
    default: ''
  }
})

const route = useRoute()
const copied = ref(false)
const canUseNativeShare = ref(false)

const shareUrl = computed(() => {
  return props.url || window.location.href
})

const shareTitle = computed(() => {
  if (props.title) return props.title
  if (props.post?.title) return props.post.title
  return document.title || 'Check out this article'
})

const shareDescription = computed(() => {
  if (props.description) return props.description
  if (props.post?.excerpt) return props.post.excerpt
  if (props.post?.content) {
    const div = document.createElement('div')
    div.innerHTML = props.post.content
    const text = div.textContent || div.innerText || ''
    return text.length > 160 ? text.substring(0, 160) + '...' : text
  }
  return 'Read this interesting article'
})

const shareOnTwitter = () => {
  const text = encodeURIComponent(`${shareTitle.value}`)
  const url = encodeURIComponent(shareUrl.value)
  const twitterUrl = `https://twitter.com/intent/tweet?text=${text}&url=${url}`
  window.open(twitterUrl, '_blank', 'width=600,height=400')
}

const shareOnFacebook = () => {
  const url = encodeURIComponent(shareUrl.value)
  const facebookUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`
  window.open(facebookUrl, '_blank', 'width=600,height=400')
}

const shareOnLinkedIn = () => {
  const url = encodeURIComponent(shareUrl.value)
  const title = encodeURIComponent(shareTitle.value)
  const summary = encodeURIComponent(shareDescription.value)
  const linkedInUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${url}&title=${title}&summary=${summary}`
  window.open(linkedInUrl, '_blank', 'width=600,height=400')
}

const shareOnWhatsApp = () => {
  const text = encodeURIComponent(`${shareTitle.value}\n${shareUrl.value}`)
  const whatsAppUrl = `https://wa.me/?text=${text}`
  window.open(whatsAppUrl, '_blank')
}

const shareViaEmail = () => {
  const subject = encodeURIComponent(shareTitle.value)
  const body = encodeURIComponent(`${shareDescription.value}\n\nRead more: ${shareUrl.value}`)
  const emailUrl = `mailto:?subject=${subject}&body=${body}`
  window.location.href = emailUrl
}

const copyLink = async () => {
  try {
    await navigator.clipboard.writeText(shareUrl.value)
    copied.value = true
    
    // Reset the copied state after 2 seconds
    setTimeout(() => {
      copied.value = false
    }, 2000)
  } catch (error) {
    console.error('Failed to copy link:', error)
    
    // Fallback for older browsers
    const textArea = document.createElement('textarea')
    textArea.value = shareUrl.value
    document.body.appendChild(textArea)
    textArea.focus()
    textArea.select()
    
    try {
      document.execCommand('copy')
      copied.value = true
      setTimeout(() => {
        copied.value = false
      }, 2000)
    } catch (fallbackError) {
      console.error('Fallback copy failed:', fallbackError)
    }
    
    document.body.removeChild(textArea)
  }
}

const shareNative = async () => {
  if (!navigator.share) return
  
  try {
    await navigator.share({
      title: shareTitle.value,
      text: shareDescription.value,
      url: shareUrl.value
    })
  } catch (error) {
    if (error.name !== 'AbortError') {
      console.error('Native share failed:', error)
    }
  }
}

onMounted(() => {
  // Check if native share API is available
  canUseNativeShare.value = Boolean(navigator.share)
})
</script>

<style scoped>
button:focus {
  outline: 2px solid #3b82f6;
  outline-offset: 2px;
}

button:focus:not(:focus-visible) {
  outline: none;
}
</style>