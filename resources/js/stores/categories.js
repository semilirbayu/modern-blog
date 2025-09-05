import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import categoriesService from '../services/categoriesService.js'

export const useCategoriesStore = defineStore('categories', () => {
    const categories = ref([])
    const currentCategory = ref(null)
    const categoryPosts = ref([])
    const loading = ref(false)
    const error = ref(null)
    const pagination = ref({
        current_page: 1,
        last_page: 1,
        per_page: 12,
        total: 0
    })

    const categoriesWithPostCounts = computed(() => {
        return categories.value.map(category => ({
            ...category,
            posts_count: category.posts_count || 0
        }))
    })

    const fetchCategories = async () => {
        loading.value = true
        error.value = null
        
        try {
            const response = await categoriesService.getCategories()
            categories.value = response.data
            return response
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to fetch categories'
            throw err
        } finally {
            loading.value = false
        }
    }

    const fetchCategory = async (id) => {
        loading.value = true
        error.value = null
        
        try {
            const response = await categoriesService.getCategory(id)
            currentCategory.value = response.data
            return response
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to fetch category'
            throw err
        } finally {
            loading.value = false
        }
    }

    const fetchCategoryBySlug = async (slug) => {
        loading.value = true
        error.value = null
        
        try {
            const response = await categoriesService.getCategoryBySlug(slug)
            currentCategory.value = response.data
            return response
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to fetch category'
            throw err
        } finally {
            loading.value = false
        }
    }

    const fetchCategoryPosts = async (slug, params = {}) => {
        loading.value = true
        error.value = null
        
        try {
            const response = await categoriesService.getCategoryPosts(slug, params)
            categoryPosts.value = response.data.data
            
            // Update pagination from meta object
            if (response.data.meta) {
                pagination.value = {
                    current_page: response.data.meta.current_page,
                    last_page: response.data.meta.last_page,
                    per_page: response.data.meta.per_page,
                    total: response.data.meta.total
                }
            }
            
            return response
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to fetch category posts'
            throw err
        } finally {
            loading.value = false
        }
    }

    const createCategory = async (categoryData) => {
        loading.value = true
        error.value = null
        
        try {
            const response = await categoriesService.createCategory(categoryData)
            categories.value.push(response.data)
            return response
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to create category'
            throw err
        } finally {
            loading.value = false
        }
    }

    const updateCategory = async (id, categoryData) => {
        loading.value = true
        error.value = null
        
        try {
            const response = await categoriesService.updateCategory(id, categoryData)
            const index = categories.value.findIndex(category => category.id === id)
            if (index !== -1) {
                categories.value[index] = response.data
            }
            if (currentCategory.value?.id === id) {
                currentCategory.value = response.data
            }
            return response
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to update category'
            throw err
        } finally {
            loading.value = false
        }
    }

    const deleteCategory = async (id) => {
        loading.value = true
        error.value = null
        
        try {
            await categoriesService.deleteCategory(id)
            categories.value = categories.value.filter(category => category.id !== id)
            if (currentCategory.value?.id === id) {
                currentCategory.value = null
            }
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to delete category'
            throw err
        } finally {
            loading.value = false
        }
    }

    const clearCurrentCategory = () => {
        currentCategory.value = null
        categoryPosts.value = []
    }

    const fetchPublicCategories = async () => {
        loading.value = true
        error.value = null
        
        try {
            const response = await categoriesService.getPublicCategories()
            categories.value = response.data
            return response
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to fetch categories'
            throw err
        } finally {
            loading.value = false
        }
    }

    const clearError = () => {
        error.value = null
    }

    return {
        categories,
        currentCategory,
        categoryPosts,
        loading,
        error,
        pagination,
        categoriesWithPostCounts,
        fetchCategories,
        fetchPublicCategories,
        fetchCategory,
        fetchCategoryBySlug,
        fetchCategoryPosts,
        createCategory,
        updateCategory,
        deleteCategory,
        clearCurrentCategory,
        clearError
    }
})