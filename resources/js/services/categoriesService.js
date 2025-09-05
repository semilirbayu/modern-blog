import api from './api.js'

const categoriesService = {
    async getCategories() {
        return api.get('/categories')
    },

    async getCategory(id) {
        return api.get(`/categories/${id}`)
    },

    async createCategory(categoryData) {
        return api.post('/categories', categoryData)
    },

    async updateCategory(id, categoryData) {
        return api.put(`/categories/${id}`, categoryData)
    },

    async deleteCategory(id) {
        return api.delete(`/categories/${id}`)
    },

    async getPublicCategories() {
        return api.get('/public/categories')
    },

    async getCategoryBySlug(slug) {
        return api.get(`/public/categories/${slug}`)
    },

    async getCategoryPosts(slug, params = {}) {
        return api.get(`/public/categories/${slug}/posts`, { params })
    },

    async bulkDelete(categoryIds) {
        return api.delete('/categories/bulk', {
            data: { ids: categoryIds }
        })
    }
}

export default categoriesService