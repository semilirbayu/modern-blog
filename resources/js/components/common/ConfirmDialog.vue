<template>
  <Teleport to="body">
    <Transition
      enter-active-class="duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="show"
        class="fixed inset-0 z-50 overflow-y-auto"
        @click="onBackdropClick"
      >
        <div class="flex min-h-full items-center justify-center p-4">
          <Transition
            enter-active-class="duration-300 ease-out"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="duration-200 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
          >
            <div
              v-if="show"
              class="relative w-full max-w-md transform rounded-lg bg-white p-6 shadow-xl transition-all"
              @click.stop
            >
              <!-- Dialog Content -->
              <div class="mb-4">
                <div class="flex items-center">
                  <div
                    v-if="type === 'danger'"
                    class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100"
                  >
                    <svg
                      class="h-6 w-6 text-red-600"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke-width="1.5"
                      stroke="currentColor"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"
                      />
                    </svg>
                  </div>
                  
                  <div
                    v-else-if="type === 'warning'"
                    class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-yellow-100"
                  >
                    <svg
                      class="h-6 w-6 text-yellow-600"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke-width="1.5"
                      stroke="currentColor"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"
                      />
                    </svg>
                  </div>
                  
                  <div
                    v-else
                    class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100"
                  >
                    <svg
                      class="h-6 w-6 text-blue-600"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke-width="1.5"
                      stroke="currentColor"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"
                      />
                    </svg>
                  </div>
                </div>
              </div>

              <div class="text-center">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-2">
                  {{ title }}
                </h3>
                <div class="text-sm text-gray-500">
                  <p v-if="message">{{ message }}</p>
                  <slot v-else />
                </div>
              </div>

              <!-- Actions -->
              <div class="mt-6 flex gap-3 justify-end">
                <button
                  type="button"
                  class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                  @click="onCancel"
                  :disabled="loading"
                >
                  {{ cancelText }}
                </button>
                <button
                  type="button"
                  :class="confirmButtonClasses"
                  @click="onConfirm"
                  :disabled="loading"
                >
                  <LoadingSpinner
                    v-if="loading"
                    variant="button"
                    color="white"
                  />
                  <span v-else>{{ confirmText }}</span>
                </button>
              </div>
            </div>
          </Transition>
        </div>
        
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
      </div>
    </Transition>
  </Teleport>
</template>

<script>
import LoadingSpinner from './LoadingSpinner.vue'

export default {
  name: 'ConfirmDialog',
  components: {
    LoadingSpinner
  },
  props: {
    show: {
      type: Boolean,
      default: false
    },
    title: {
      type: String,
      default: 'Confirm Action'
    },
    message: {
      type: String,
      default: ''
    },
    confirmText: {
      type: String,
      default: 'Confirm'
    },
    cancelText: {
      type: String,
      default: 'Cancel'
    },
    type: {
      type: String,
      default: 'info',
      validator: (value) => ['info', 'warning', 'danger'].includes(value)
    },
    loading: {
      type: Boolean,
      default: false
    },
    closeOnBackdrop: {
      type: Boolean,
      default: true
    }
  },
  emits: ['confirm', 'cancel', 'close'],
  data() {
    return {
      _handleEscape: null
    }
  },
  computed: {
    confirmButtonClasses() {
      const baseClasses = 'inline-flex justify-center rounded-md px-4 py-2 text-sm font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed min-w-[80px]'
      
      if (this.type === 'danger') {
        return `${baseClasses} border-transparent bg-red-600 text-white hover:bg-red-700 focus:ring-red-500`
      } else if (this.type === 'warning') {
        return `${baseClasses} border-transparent bg-yellow-600 text-white hover:bg-yellow-700 focus:ring-yellow-500`
      } else {
        return `${baseClasses} border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500`
      }
    }
  },
  methods: {
    onConfirm() {
      this.$emit('confirm')
    },
    onCancel() {
      this.$emit('cancel')
      this.$emit('close')
    },
    onBackdropClick() {
      if (this.closeOnBackdrop && !this.loading) {
        this.onCancel()
      }
    }
  },
  mounted() {
    this._handleEscape = (e) => {
      if (e.key === 'Escape' && this.show && !this.loading) this.onCancel()
    }
    document.addEventListener('keydown', this._handleEscape)
  },
  beforeUnmount() {
    document.removeEventListener('keydown', this._handleEscape)
  }
}
</script>