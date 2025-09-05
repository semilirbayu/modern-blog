import api from './api.js'

const postsService = {
    async getPosts(params = {}) {
        return api.get('/posts', { params })
    },

    async getPost(id) {
        return api.get(`/posts/${id}`)
    },

    async createPost(postData) {
        return api.post('/posts', postData)
    },

    async updatePost(id, postData) {
        return api.put(`/posts/${id}`, postData)
    },

    async deletePost(id) {
        return api.delete(`/posts/${id}`)
    },

    async getPublicPosts(params = {}) {
        return api.get('/public/posts', { params })
    },

    async getPublicPost(slug) {
        return api.get(`/public/posts/${slug}`)
    },

    async uploadImage(formData) {
        return api.post('/posts/upload-image', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })
    },

    async togglePostStatus(id) {
        return api.patch(`/posts/${id}/toggle-status`)
    },

    async bulkDelete(postIds) {
        return api.delete('/posts/bulk', {
            data: { ids: postIds }
        })
    },

    async getPostsByCategory(categoryId, params = {}) {
        return api.get(`/posts/category/${categoryId}`, { params })
    },

    async getPostsByAuthor(authorId, params = {}) {
        return api.get(`/posts/author/${authorId}`, { params })
    },

    async searchPosts(params = {}) {
        return api.get('/posts/search', { params })
    },

    async searchPublicPosts(params = {}) {
        return api.get('/public/posts/search', { params })
    },

    async advancedSearch(criteria = {}) {
        return api.post('/posts/advanced-search', criteria)
    },

    async getRelatedPosts(postId, params = {}) {
        return api.get(`/posts/${postId}/related`, { params })
    },

    async getPopularPosts(params = {}) {
        return api.get('/posts/popular', { params })
    },

    async getFeaturedPosts(params = {}) {
        return api.get('/posts/featured', { params })
    }
}

export default postsService