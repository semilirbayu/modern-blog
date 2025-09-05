<template>
  <div :class="containerClasses">
    <div :class="spinnerClasses">
      <svg 
        :class="svgClasses" 
        :width="size" 
        :height="size" 
        viewBox="0 0 38 38"
        xmlns="http://www.w3.org/2000/svg"
      >
        <defs>
          <linearGradient x1="8.042%" y1="0%" x2="65.682%" y2="23.865%" id="a">
            <stop :stop-color="color" stop-opacity="0" offset="0%"/>
            <stop :stop-color="color" stop-opacity=".631" offset="63.146%"/>
            <stop :stop-color="color" offset="100%"/>
          </linearGradient>
        </defs>
        <g fill="none" fill-rule="evenodd">
          <g transform="translate(1 1)">
            <path 
              d="m36 18c0-9.94-8.06-18-18-18" 
              id="Oval-2" 
              :stroke="color" 
              stroke-width="2"
            >
              <animateTransform
                attributeName="transform"
                type="rotate"
                from="0 18 18"
                to="360 18 18"
                dur="0.9s"
                repeatCount="indefinite"
              />
            </path>
            <circle fill="url(#a)" cx="36" cy="18" r="1">
              <animateTransform
                attributeName="transform"
                type="rotate"
                from="0 18 18"
                to="360 18 18"
                dur="0.9s"
                repeatCount="indefinite"
              />
            </circle>
          </g>
        </g>
      </svg>
    </div>
    <div v-if="text" :class="textClasses">
      {{ text }}
    </div>
  </div>
</template>

<script>
export default {
  name: 'LoadingSpinner',
  props: {
    size: {
      type: [String, Number],
      default: 24
    },
    color: {
      type: String,
      default: 'currentColor'
    },
    text: {
      type: String,
      default: ''
    },
    center: {
      type: Boolean,
      default: false
    },
    overlay: {
      type: Boolean,
      default: false
    },
    variant: {
      type: String,
      default: 'default',
      validator: (value) => ['default', 'small', 'large', 'button'].includes(value)
    }
  },
  computed: {
    containerClasses() {
      const classes = []
      
      if (this.center) {
        classes.push('flex items-center justify-center')
      } else {
        classes.push('inline-flex items-center')
      }
      
      if (this.overlay) {
        classes.push('fixed inset-0 bg-white bg-opacity-75 z-50')
      }
      
      return classes.join(' ')
    },
    spinnerClasses() {
      const classes = ['animate-spin']
      
      if (this.text) {
        classes.push('mr-2')
      }
      
      return classes.join(' ')
    },
    svgClasses() {
      const classes = []
      
      switch (this.variant) {
        case 'small':
          classes.push('w-4 h-4')
          break
        case 'large':
          classes.push('w-8 h-8')
          break
        case 'button':
          classes.push('w-4 h-4')
          break
        default:
          classes.push('w-6 h-6')
      }
      
      return classes.join(' ')
    },
    textClasses() {
      const classes = ['text-sm']
      
      if (this.variant === 'small') {
        classes.push('text-xs')
      } else if (this.variant === 'large') {
        classes.push('text-base')
      }
      
      return classes.join(' ')
    }
  }
}
</script>