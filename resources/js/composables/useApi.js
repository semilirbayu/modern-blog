import { ref, reactive } from 'vue'

export function useApi(apiFunction) {
    const loading = ref(false)
    const error = ref(null)
    const data = ref(null)

    const execute = async (...params) => {
        loading.value = true
        error.value = null
        
        try {
            const response = await apiFunction(...params)
            data.value = response.data
            return { success: true, data: response.data }
        } catch (err) {
            error.value = err.response?.data?.message || err.message || 'An error occurred'
            return { 
                success: false, 
                error: error.value,
                status: err.response?.status
            }
        } finally {
            loading.value = false
        }
    }

    const reset = () => {
        loading.value = false
        error.value = null
        data.value = null
    }

    const clearError = () => {
        error.value = null
    }

    return {
        loading,
        error,
        data,
        execute,
        reset,
        clearError
    }
}

export function useApiCollection(apiFunction) {
    const items = ref([])
    const loading = ref(false)
    const error = ref(null)
    const pagination = reactive({
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0
    })

    const fetch = async (params = {}) => {
        loading.value = true
        error.value = null
        
        try {
            const response = await apiFunction(params)
            items.value = response.data.data || response.data
            
            if (response.data.current_page) {
                Object.assign(pagination, {
                    current_page: response.data.current_page,
                    last_page: response.data.last_page,
                    per_page: response.data.per_page,
                    total: response.data.total
                })
            }
            
            return { success: true, data: response.data }
        } catch (err) {
            error.value = err.response?.data?.message || err.message || 'An error occurred'
            return { 
                success: false, 
                error: error.value,
                status: err.response?.status
            }
        } finally {
            loading.value = false
        }
    }

    const add = (item) => {
        items.value.unshift(item)
        pagination.total += 1
    }

    const update = (id, updatedItem) => {
        const index = items.value.findIndex(item => item.id === id)
        if (index !== -1) {
            items.value[index] = updatedItem
        }
    }

    const remove = (id) => {
        items.value = items.value.filter(item => item.id !== id)
        pagination.total -= 1
    }

    const reset = () => {
        items.value = []
        loading.value = false
        error.value = null
        Object.assign(pagination, {
            current_page: 1,
            last_page: 1,
            per_page: 10,
            total: 0
        })
    }

    const clearError = () => {
        error.value = null
    }

    return {
        items,
        loading,
        error,
        pagination,
        fetch,
        add,
        update,
        remove,
        reset,
        clearError
    }
}

export function useApiForm(submitFunction) {
    const form = reactive({})
    const errors = ref({})
    const loading = ref(false)
    const success = ref(false)

    const submit = async (formData = form) => {
        loading.value = true
        errors.value = {}
        success.value = false
        
        try {
            const response = await submitFunction(formData)
            success.value = true
            return { success: true, data: response.data }
        } catch (err) {
            if (err.response?.status === 422) {
                errors.value = err.response.data.errors || {}
            }
            return { 
                success: false, 
                error: err.response?.data?.message || err.message || 'Validation failed',
                errors: errors.value
            }
        } finally {
            loading.value = false
        }
    }

    const reset = () => {
        Object.keys(form).forEach(key => {
            delete form[key]
        })
        errors.value = {}
        loading.value = false
        success.value = false
    }

    const setFormData = (data) => {
        Object.assign(form, data)
    }

    const clearErrors = () => {
        errors.value = {}
    }

    const getFieldError = (field) => {
        return errors.value[field]?.[0] || null
    }

    const hasFieldError = (field) => {
        return !!errors.value[field]?.length
    }

    return {
        form,
        errors,
        loading,
        success,
        submit,
        reset,
        setFormData,
        clearErrors,
        getFieldError,
        hasFieldError
    }
}