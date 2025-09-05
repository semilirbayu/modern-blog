import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import postsService from '../services/postsService.js'

export const usePostsStore = defineStore('posts', () => {
    const posts = ref([])
    const currentPost = ref(null)
    const publicPosts = ref([])
    const searchResults = ref([])
    const loading = ref(false)
    const searchLoading = ref(false)
    const error = ref(null)
    const pagination = ref({
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0
    })
    const publicPagination = ref({
        current_page: 1,
        last_page: 1,
        per_page: 12,
        total: 0
    })
    const searchQuery = ref('')
    const searchFilters = ref({
        category: null,
        tags: [],
        dateFrom: null,
        dateTo: null,
        author: null
    })

    const publishedPosts = computed(() => {
        return posts.value.filter(post => post.status === 'published')
    })

    const draftPosts = computed(() => {
        return posts.value.filter(post => post.status === 'draft')
    })

    const fetchPosts = async (params = {}) => {
        loading.value = true
        error.value = null
        
        try {
            const response = await postsService.getPosts(params)
            posts.value = response.data.data
            pagination.value = {
                current_page: response.data.current_page,
                last_page: response.data.last_page,
                per_page: response.data.per_page,
                total: response.data.total
            }
            return response
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to fetch posts'
            throw err
        } finally {
            loading.value = false
        }
    }

    const fetchPublicPosts = async (params = {}, { append = false } = {}) => {
        loading.value = true
        error.value = null
        
        try {
            const response = await postsService.getPublicPosts(params)
            const data = response.data.data
            publicPagination.value = {
                current_page: response.data.current_page,
                last_page: response.data.last_page,
                per_page: response.data.per_page,
                total: response.data.total,
            }
            publicPosts.value = append ? [...publicPosts.value, ...data] : data
            return response
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to fetch public posts'
            throw err
        } finally {
            loading.value = false
        }
    }

    const fetchPost = async (id) => {
        loading.value = true
        error.value = null
        
        try {
            const response = await postsService.getPost(id)
            currentPost.value = response.data
            return response
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to fetch post'
            throw err
        } finally {
            loading.value = false
        }
    }

    const fetchPublicPost = async (slug) => {
        loading.value = true
        error.value = null
        
        try {
            const response = await postsService.getPublicPost(slug)
            // Handle nested data structure from API
            currentPost.value = response.data?.data || response.data
            return response
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to fetch post'
            throw err
        } finally {
            loading.value = false
        }
    }

    const createPost = async (postData) => {
        loading.value = true
        error.value = null
        
        try {
            const response = await postsService.createPost(postData)
            posts.value.unshift(response.data)
            return response
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to create post'
            throw err
        } finally {
            loading.value = false
        }
    }

    const updatePost = async (id, postData) => {
        loading.value = true
        error.value = null
        
        try {
            const response = await postsService.updatePost(id, postData)
            const index = posts.value.findIndex(post => post.id === id)
            if (index !== -1) {
                posts.value[index] = response.data
            }
            if (currentPost.value?.id === id) {
                currentPost.value = response.data
            }
            return response
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to update post'
            throw err
        } finally {
            loading.value = false
        }
    }

    const deletePost = async (id) => {
        loading.value = true
        error.value = null
        
        try {
            await postsService.deletePost(id)
            posts.value = posts.value.filter(post => post.id !== id)
            if (currentPost.value?.id === id) {
                currentPost.value = null
            }
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to delete post'
            throw err
        } finally {
            loading.value = false
        }
    }

    const clearCurrentPost = () => {
        currentPost.value = null
    }

    const clearError = () => {
        error.value = null
    }

    // Search functionality
    const searchPosts = async (query, params = {}) => {
        searchLoading.value = true
        error.value = null
        
        try {
            const searchParams = {
                search: query,
                ...params,
                ...searchFilters.value
            }
            
            const response = await postsService.searchPosts(searchParams)
            searchResults.value = response.data.data || response.data
            searchQuery.value = query
            
            // Update pagination if available
            if (response.data.current_page !== undefined) {
                pagination.value = {
                    current_page: response.data.current_page,
                    last_page: response.data.last_page,
                    per_page: response.data.per_page,
                    total: response.data.total
                }
            }
            
            return response
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to search posts'
            searchResults.value = []
            throw err
        } finally {
            searchLoading.value = false
        }
    }

    const searchPublicPosts = async (query, params = {}) => {
        searchLoading.value = true
        error.value = null
        
        try {
            const searchParams = {
                search: query,
                ...params,
                ...searchFilters.value
            }
            
            const response = await postsService.searchPublicPosts(searchParams)
            searchResults.value = response.data.data || response.data
            searchQuery.value = query
            
            return response
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to search public posts'
            searchResults.value = []
            throw err
        } finally {
            searchLoading.value = false
        }
    }

    const setSearchFilters = (filters) => {
        searchFilters.value = { ...searchFilters.value, ...filters }
    }

    const clearSearchFilters = () => {
        searchFilters.value = {
            category: null,
            tags: [],
            dateFrom: null,
            dateTo: null,
            author: null
        }
    }

    const clearSearchResults = () => {
        searchResults.value = []
        searchQuery.value = ''
        clearSearchFilters()
    }

    // Advanced search with multiple criteria
    const advancedSearch = async (criteria = {}) => {
        searchLoading.value = true
        error.value = null
        
        try {
            const response = await postsService.advancedSearch(criteria)
            searchResults.value = response.data.data || response.data
            
            return response
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to perform advanced search'
            searchResults.value = []
            throw err
        } finally {
            searchLoading.value = false
        }
    }

    // Get related posts (similar posts based on category, tags, etc.)
    const getRelatedPosts = async (postId, limit = 4) => {
        try {
            const response = await postsService.getRelatedPosts(postId, { limit })
            return response.data
        } catch (err) {
            console.error('Failed to fetch related posts:', err)
            return []
        }
    }

    const fetchRelatedPosts = async (params = {}) => {
        const response = await postsService.getPublicPosts(params)
        return response.data.data || []
    }

    // Get popular posts
    const getPopularPosts = async (params = {}) => {
        try {
            const response = await postsService.getPopularPosts(params)
            return response.data
        } catch (err) {
            console.error('Failed to fetch popular posts:', err)
            return []
        }
    }

    // Get featured posts
    const getFeaturedPosts = async (params = {}) => {
        try {
            const response = await postsService.getFeaturedPosts(params)
            return response.data
        } catch (err) {
            console.error('Failed to fetch featured posts:', err)
            return []
        }
    }

    // Computed properties for search
    const hasSearchResults = computed(() => searchResults.value.length > 0)
    const isSearching = computed(() => searchQuery.value.length > 0)
    const filteredSearchResults = computed(() => {
        if (!searchResults.value.length) return []
        
        // Apply additional client-side filtering if needed
        let filtered = [...searchResults.value]
        
        // Filter by date range if specified
        if (searchFilters.value.dateFrom || searchFilters.value.dateTo) {
            filtered = filtered.filter(post => {
                const postDate = new Date(post.published_at || post.created_at)
                const fromDate = searchFilters.value.dateFrom ? new Date(searchFilters.value.dateFrom) : null
                const toDate = searchFilters.value.dateTo ? new Date(searchFilters.value.dateTo) : null
                
                if (fromDate && postDate < fromDate) return false
                if (toDate && postDate > toDate) return false
                
                return true
            })
        }
        
        return filtered
    })

    return {
        // Original state
        posts,
        currentPost,
        publicPosts,
        loading,
        error,
        pagination,
        publicPagination,
        publishedPosts,
        draftPosts,
        
        // Search state
        searchResults,
        searchLoading,
        searchQuery,
        searchFilters,
        
        // Original methods
        fetchPosts,
        fetchPublicPosts,
        fetchPost,
        fetchPublicPost,
        createPost,
        updatePost,
        deletePost,
        clearCurrentPost,
        clearError,
        
        // Search methods
        searchPosts,
        searchPublicPosts,
        setSearchFilters,
        clearSearchFilters,
        clearSearchResults,
        advancedSearch,
        getRelatedPosts,
        fetchRelatedPosts,
        getPopularPosts,
        getFeaturedPosts,
        
        // Search computed
        hasSearchResults,
        isSearching,
        filteredSearchResults
    }
})