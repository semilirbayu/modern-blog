<template>
  <div class="markdown-editor">
    <!-- Toolbar -->
    <div class="flex items-center justify-between border border-gray-300 rounded-t-md bg-gray-50 px-3 py-2">
      <div class="flex items-center gap-1">
        <!-- Mode Toggle -->
        <div class="flex bg-white rounded-md border border-gray-300 mr-4">
          <button
            @click="activeTab = 'write'"
            :class="[
              'px-3 py-1 text-sm font-medium rounded-l-md',
              activeTab === 'write' 
                ? 'bg-blue-100 text-blue-700 border-r border-gray-300' 
                : 'text-gray-600 hover:text-gray-800'
            ]"
          >
            Write
          </button>
          <button
            @click="activeTab = 'preview'"
            :class="[
              'px-3 py-1 text-sm font-medium rounded-r-md',
              activeTab === 'preview' 
                ? 'bg-blue-100 text-blue-700' 
                : 'text-gray-600 hover:text-gray-800'
            ]"
          >
            Preview
          </button>
        </div>

        <!-- Formatting Buttons (only show in write mode) -->
        <div v-if="activeTab === 'write'" class="flex items-center gap-1">
          <button
            v-for="tool in formatTools"
            :key="tool.name"
            @click="insertMarkdown(tool)"
            :title="tool.title"
            class="p-1.5 text-gray-600 hover:text-gray-800 hover:bg-gray-200 rounded"
          >
            <component :is="tool.icon" class="w-4 h-4" />
          </button>
          
          <div class="w-px h-6 bg-gray-300 mx-2"></div>
          
          <button
            @click="insertMarkdown(linkTool)"
            title="Insert Link"
            class="p-1.5 text-gray-600 hover:text-gray-800 hover:bg-gray-200 rounded"
          >
            <LinkIcon class="w-4 h-4" />
          </button>
          
          <button
            @click="insertMarkdown(imageTool)"
            title="Insert Image"
            class="p-1.5 text-gray-600 hover:text-gray-800 hover:bg-gray-200 rounded"
          >
            <PhotoIcon class="w-4 h-4" />
          </button>
          
          <button
            @click="insertMarkdown(codeTool)"
            title="Insert Code Block"
            class="p-1.5 text-gray-600 hover:text-gray-800 hover:bg-gray-200 rounded"
          >
            <CodeBracketIcon class="w-4 h-4" />
          </button>
        </div>
      </div>

      <!-- Word Count & Character Count -->
      <div class="flex items-center gap-4 text-sm text-gray-500">
        <span>{{ wordCount }} words</span>
        <span>{{ characterCount }} characters</span>
      </div>
    </div>

    <!-- Editor Content -->
    <div class="relative">
      <!-- Write Mode -->
      <div v-if="activeTab === 'write'" class="relative">
        <textarea
          ref="textareaRef"
          :value="modelValue"
          @input="handleInput"
          @keydown="handleKeydown"
          @select="updateCursorPosition"
          @click="updateCursorPosition"
          class="w-full h-96 p-4 font-mono text-sm border-l border-r border-b border-gray-300 rounded-b-md resize-y focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': error }"
          placeholder="Start writing your post in Markdown..."
        ></textarea>
        
        <!-- Character limit indicator -->
        <div 
          v-if="maxLength && modelValue.length > maxLength * 0.8"
          class="absolute bottom-2 right-2 text-xs"
          :class="{
            'text-amber-600': modelValue.length > maxLength * 0.9,
            'text-red-600': modelValue.length > maxLength,
            'text-gray-500': modelValue.length <= maxLength * 0.9
          }"
        >
          {{ modelValue.length }}{{ maxLength ? `/${maxLength}` : '' }}
        </div>
      </div>

      <!-- Preview Mode -->
      <div 
        v-if="activeTab === 'preview'" 
        class="min-h-96 p-4 border-l border-r border-b border-gray-300 rounded-b-md bg-white overflow-auto"
      >
        <div v-if="!modelValue.trim()" class="text-gray-400 italic">
          Nothing to preview yet. Switch to Write mode to start writing.
        </div>
        <div v-else class="prose prose-sm max-w-none" v-html="renderedContent"></div>
      </div>
    </div>

    <!-- Error Display -->
    <div v-if="error" class="mt-1 text-sm text-red-600">
      {{ error }}
    </div>

    <!-- Help Text -->
    <div class="mt-2 text-xs text-gray-500">
      <details class="cursor-pointer">
        <summary class="hover:text-gray-700">Markdown formatting help</summary>
        <div class="mt-2 grid grid-cols-2 gap-4 text-xs">
          <div>
            <div class="font-medium mb-1">Headers</div>
            <div class="font-mono text-gray-600"># H1, ## H2, ### H3</div>
          </div>
          <div>
            <div class="font-medium mb-1">Emphasis</div>
            <div class="font-mono text-gray-600">**bold** *italic*</div>
          </div>
          <div>
            <div class="font-medium mb-1">Links</div>
            <div class="font-mono text-gray-600">[text](url)</div>
          </div>
          <div>
            <div class="font-medium mb-1">Lists</div>
            <div class="font-mono text-gray-600">- item or 1. item</div>
          </div>
        </div>
      </details>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, nextTick, onMounted } from 'vue'
import { useMarkdown } from '@/composables/useMarkdown'

// Icons (using Heroicons as inline SVG for simplicity)
const BoldIcon = {
  template: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 4h8a4 4 0 010 8H6zM6 12h9a4 4 0 110 8H6z"/></svg>`
}

const ItalicIcon = {
  template: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 4h4l-2 14h-4l2-14z"/></svg>`
}

const ListBulletIcon = {
  template: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>`
}

const NumberedListIcon = {
  template: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>`
}

const QuoteIcon = {
  template: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>`
}

const LinkIcon = {
  template: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>`
}

const PhotoIcon = {
  template: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>`
}

const CodeBracketIcon = {
  template: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>`
}

const H1Icon = {
  template: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21V3h3v7h4V3h3v18h-3v-7H10v7H7z"/><text x="16" y="18" font-size="8" fill="currentColor">1</text></svg>`
}

const H2Icon = {
  template: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21V3h3v7h4V3h3v18h-3v-7H10v7H7z"/><text x="16" y="18" font-size="8" fill="currentColor">2</text></svg>`
}

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  error: {
    type: String,
    default: null
  },
  maxLength: {
    type: Number,
    default: null
  },
  placeholder: {
    type: String,
    default: 'Start writing your post in Markdown...'
  }
})

const emit = defineEmits(['update:modelValue', 'change'])

const { parseAndSanitize } = useMarkdown()

const activeTab = ref('write')
const textareaRef = ref(null)
const cursorPosition = ref(0)

const formatTools = [
  { 
    name: 'bold', 
    icon: BoldIcon, 
    title: 'Bold (Ctrl+B)',
    prefix: '**', 
    suffix: '**',
    placeholder: 'bold text'
  },
  { 
    name: 'italic', 
    icon: ItalicIcon, 
    title: 'Italic (Ctrl+I)',
    prefix: '*', 
    suffix: '*',
    placeholder: 'italic text'
  },
  { 
    name: 'h1', 
    icon: H1Icon, 
    title: 'Heading 1',
    prefix: '# ', 
    suffix: '',
    placeholder: 'Heading'
  },
  { 
    name: 'h2', 
    icon: H2Icon, 
    title: 'Heading 2',
    prefix: '## ', 
    suffix: '',
    placeholder: 'Heading'
  },
  { 
    name: 'list', 
    icon: ListBulletIcon, 
    title: 'Bullet List',
    prefix: '- ', 
    suffix: '',
    placeholder: 'List item'
  },
  { 
    name: 'numbered-list', 
    icon: NumberedListIcon, 
    title: 'Numbered List',
    prefix: '1. ', 
    suffix: '',
    placeholder: 'List item'
  },
  { 
    name: 'quote', 
    icon: QuoteIcon, 
    title: 'Quote',
    prefix: '> ', 
    suffix: '',
    placeholder: 'Quote'
  }
]

const linkTool = {
  name: 'link',
  prefix: '[',
  suffix: '](url)',
  placeholder: 'link text'
}

const imageTool = {
  name: 'image',
  prefix: '![',
  suffix: '](image-url)',
  placeholder: 'alt text'
}

const codeTool = {
  name: 'code',
  prefix: '```\n',
  suffix: '\n```',
  placeholder: 'your code here'
}

const wordCount = computed(() => {
  if (!props.modelValue.trim()) return 0
  return props.modelValue.trim().split(/\s+/).length
})

const characterCount = computed(() => {
  return props.modelValue.length
})

const renderedContent = computed(() => {
  if (!props.modelValue.trim()) return ''
  return parseAndSanitize(props.modelValue)
})

const handleInput = (event) => {
  const value = event.target.value
  emit('update:modelValue', value)
  emit('change', value)
}

const handleKeydown = (event) => {
  // Handle keyboard shortcuts
  if (event.ctrlKey || event.metaKey) {
    switch (event.key) {
      case 'b':
        event.preventDefault()
        insertMarkdown(formatTools.find(t => t.name === 'bold'))
        break
      case 'i':
        event.preventDefault()
        insertMarkdown(formatTools.find(t => t.name === 'italic'))
        break
      case 'k':
        event.preventDefault()
        insertMarkdown(linkTool)
        break
    }
  }
  
  // Handle Tab for indentation
  if (event.key === 'Tab') {
    event.preventDefault()
    insertText('  ') // Insert 2 spaces
  }
}

const updateCursorPosition = () => {
  nextTick(() => {
    if (textareaRef.value) {
      cursorPosition.value = textareaRef.value.selectionStart
    }
  })
}

const insertMarkdown = (tool) => {
  const textarea = textareaRef.value
  if (!textarea) return

  const start = textarea.selectionStart
  const end = textarea.selectionEnd
  const selectedText = props.modelValue.substring(start, end)
  const placeholder = selectedText || tool.placeholder

  let newText
  let newCursorPos

  if (tool.name === 'h1' || tool.name === 'h2' || tool.name === 'list' || tool.name === 'numbered-list' || tool.name === 'quote') {
    // Line-based formatting
    const beforeCursor = props.modelValue.substring(0, start)
    const afterCursor = props.modelValue.substring(end)
    
    // Find the start of the current line
    const lineStart = beforeCursor.lastIndexOf('\n') + 1
    const lineEnd = afterCursor.indexOf('\n')
    const restOfLine = lineEnd === -1 ? afterCursor : afterCursor.substring(0, lineEnd)
    const currentLine = props.modelValue.substring(lineStart, start) + selectedText + restOfLine
    
    // Check if line already has the formatting
    const isAlreadyFormatted = currentLine.startsWith(tool.prefix.trim())
    
    if (isAlreadyFormatted) {
      // Remove formatting
      const unformattedLine = currentLine.substring(tool.prefix.length)
      newText = beforeCursor.substring(0, lineStart) + unformattedLine + (lineEnd === -1 ? '' : afterCursor.substring(lineEnd))
      newCursorPos = start - tool.prefix.length + selectedText.length
    } else {
      // Add formatting
      const formattedLine = tool.prefix + currentLine
      newText = beforeCursor.substring(0, lineStart) + formattedLine + (lineEnd === -1 ? '' : afterCursor.substring(lineEnd))
      newCursorPos = start + tool.prefix.length + selectedText.length
    }
  } else {
    // Inline formatting
    newText = props.modelValue.substring(0, start) + 
              tool.prefix + 
              placeholder + 
              tool.suffix + 
              props.modelValue.substring(end)
    
    if (selectedText) {
      newCursorPos = start + tool.prefix.length + selectedText.length + tool.suffix.length
    } else {
      newCursorPos = start + tool.prefix.length + placeholder.length
    }
  }

  emit('update:modelValue', newText)
  
  // Restore cursor position
  nextTick(() => {
    textarea.focus()
    textarea.setSelectionRange(newCursorPos, newCursorPos)
  })
}

const insertText = (text) => {
  const textarea = textareaRef.value
  if (!textarea) return

  const start = textarea.selectionStart
  const end = textarea.selectionEnd

  const newText = props.modelValue.substring(0, start) + 
                  text + 
                  props.modelValue.substring(end)

  emit('update:modelValue', newText)
  
  // Restore cursor position
  const newCursorPos = start + text.length
  nextTick(() => {
    textarea.focus()
    textarea.setSelectionRange(newCursorPos, newCursorPos)
  })
}

// Auto-resize textarea
watch(() => props.modelValue, () => {
  nextTick(() => {
    if (textareaRef.value) {
      textareaRef.value.style.height = 'auto'
      textareaRef.value.style.height = Math.max(384, textareaRef.value.scrollHeight) + 'px'
    }
  })
}, { immediate: true })

onMounted(() => {
  if (textareaRef.value) {
    textareaRef.value.focus()
  }
})
</script>

<style scoped>
.markdown-editor {
  @apply relative;
}

.prose {
  color: inherit;
}

.prose h1 {
  @apply text-2xl font-bold mb-4 mt-6 first:mt-0;
}

.prose h2 {
  @apply text-xl font-bold mb-3 mt-5;
}

.prose h3 {
  @apply text-lg font-bold mb-2 mt-4;
}

.prose p {
  @apply mb-4;
}

.prose ul, .prose ol {
  @apply mb-4 pl-6;
}

.prose li {
  @apply mb-1;
}

.prose ul li {
  @apply list-disc;
}

.prose ol li {
  @apply list-decimal;
}

.prose blockquote {
  @apply border-l-4 border-gray-300 pl-4 italic text-gray-600 mb-4;
}

.prose code {
  @apply bg-gray-100 px-1 py-0.5 rounded text-sm font-mono;
}

.prose pre {
  @apply bg-gray-100 p-4 rounded mb-4 overflow-x-auto;
}

.prose pre code {
  @apply bg-transparent p-0;
}

.prose a {
  @apply text-blue-600 hover:text-blue-800 underline;
}

.prose strong {
  @apply font-bold;
}

.prose em {
  @apply italic;
}
</style>