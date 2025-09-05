<template>
  <span 
    :class="badgeClasses"
    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
  >
    <span 
      :class="dotClasses"
      class="w-1.5 h-1.5 rounded-full mr-1.5"
    ></span>
    {{ displayText }}
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  status: {
    type: String,
    required: true,
    validator: (value) => ['draft', 'published', 'scheduled', 'archived'].includes(value)
  },
  size: {
    type: String,
    default: 'sm',
    validator: (value) => ['xs', 'sm', 'md', 'lg'].includes(value)
  },
  variant: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'minimal', 'solid'].includes(value)
  }
})

const statusConfig = {
  draft: {
    text: 'Draft',
    bgColor: 'bg-yellow-100',
    textColor: 'text-yellow-800',
    dotColor: 'bg-yellow-400',
    solidBg: 'bg-yellow-500',
    solidText: 'text-white'
  },
  published: {
    text: 'Published',
    bgColor: 'bg-green-100',
    textColor: 'text-green-800',
    dotColor: 'bg-green-400',
    solidBg: 'bg-green-500',
    solidText: 'text-white'
  },
  scheduled: {
    text: 'Scheduled',
    bgColor: 'bg-blue-100',
    textColor: 'text-blue-800',
    dotColor: 'bg-blue-400',
    solidBg: 'bg-blue-500',
    solidText: 'text-white'
  },
  archived: {
    text: 'Archived',
    bgColor: 'bg-gray-100',
    textColor: 'text-gray-800',
    dotColor: 'bg-gray-400',
    solidBg: 'bg-gray-500',
    solidText: 'text-white'
  }
}

const sizeClasses = {
  xs: 'text-xs px-2 py-0.5',
  sm: 'text-xs px-2.5 py-0.5',
  md: 'text-sm px-3 py-1',
  lg: 'text-base px-4 py-1.5'
}

const config = computed(() => statusConfig[props.status] || statusConfig.draft)

const displayText = computed(() => config.value.text)

const badgeClasses = computed(() => {
  const baseClasses = ['inline-flex', 'items-center', 'rounded-full', 'font-medium']
  
  // Add size classes
  baseClasses.push(...sizeClasses[props.size].split(' '))
  
  // Add variant-specific classes
  switch (props.variant) {
    case 'solid':
      baseClasses.push(config.value.solidBg, config.value.solidText)
      break
    case 'minimal':
      baseClasses.push(config.value.textColor, 'bg-transparent', 'border', `border-current`)
      break
    default:
      baseClasses.push(config.value.bgColor, config.value.textColor)
  }
  
  return baseClasses
})

const dotClasses = computed(() => {
  const classes = ['w-1.5', 'h-1.5', 'rounded-full', 'mr-1.5']
  
  if (props.variant === 'solid') {
    classes.push('bg-white', 'bg-opacity-75')
  } else {
    classes.push(config.value.dotColor)
  }
  
  return classes
})
</script>

<style scoped>
/* Custom styles for different variants if needed */
.status-badge-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: .8;
  }
}

/* Hover effects for interactive badges */
.status-badge-interactive {
  @apply cursor-pointer transition-colors duration-200;
}

.status-badge-interactive:hover {
  @apply ring-2 ring-offset-1;
}

.status-badge-interactive:hover.status-draft {
  @apply ring-yellow-300;
}

.status-badge-interactive:hover.status-published {
  @apply ring-green-300;
}

.status-badge-interactive:hover.status-scheduled {
  @apply ring-blue-300;
}

.status-badge-interactive:hover.status-archived {
  @apply ring-gray-300;
}
</style>