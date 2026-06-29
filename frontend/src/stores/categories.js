import { defineStore } from 'pinia'
import axios from '@/plugins/axios'

export const useCategoriesStore = defineStore('categories', {
  state: () => ({
    categories: [],
    category: null,
    categoryTree: [],
    flatList: [],
    statistics: null,
    loading: false,
    error: null,
    pagination: {
      current_page: 1,
      per_page: 15,
      total: 0,
      last_page: 0,
    },
    filters: {
      search: '',
      status: '',
      parent_id: '',
      with_trashed: false,
      only_trashed: false,
    },
    sort: {
      sort_by: 'name',
      sort_order: 'asc',
    },
  }),

  getters: {
    totalPages: (state) => state.pagination.last_page,
    hasNextPage: (state) => state.pagination.current_page < state.pagination.last_page,
    hasPrevPage: (state) => state.pagination.current_page > 1,
    rootCategories: (state) => state.categories.filter(cat => !cat.parent_id),
    activeCategories: (state) => state.categories.filter(cat => cat.status === 'active'),
  },

  actions: {
    async fetchCategories(page = 1) {
      try {
        this.loading = true
        this.error = null

        const params = {
          page,
          per_page: this.pagination.per_page,
          ...this.filters,
          ...this.sort,
        }

        const response = await axios.get('/api/v1/categories', { params })

        this.categories = response.data.data.data
        this.pagination = {
          current_page: response.data.data.meta.current_page,
          per_page: response.data.data.meta.per_page,
          total: response.data.data.meta.total,
          last_page: response.data.data.meta.total_pages,
        }

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la récupération des catégories'
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchCategoryTree() {
      try {
        this.loading = true
        this.error = null

        const response = await axios.get('/api/v1/categories/tree')
        this.categoryTree = response.data.data

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la récupération de l\'arborescence'
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchFlatList() {
      try {
        this.loading = true
        this.error = null

        const response = await axios.get('/api/v1/categories/flat-list')
        this.flatList = response.data.data

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la récupération de la liste plate'
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchCategory(id) {
      try {
        this.loading = true
        this.error = null

        const response = await axios.get(`/api/v1/categories/${id}`)
        this.category = response.data.data.category
        this.statistics = response.data.data.statistics

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la récupération de la catégorie'
        throw error
      } finally {
        this.loading = false
      }
    },

    async createCategory(categoryData) {
      try {
        this.loading = true
        this.error = null

        const response = await axios.post('/api/v1/categories', categoryData)

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la création de la catégorie'
        throw error
      } finally {
        this.loading = false
      }
    },

    async updateCategory(id, categoryData) {
      try {
        this.loading = true
        this.error = null

        const response = await axios.put(`/api/v1/categories/${id}`, categoryData)

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la modification de la catégorie'
        throw error
      } finally {
        this.loading = false
      }
    },

    async deleteCategory(id) {
      try {
        this.loading = true
        this.error = null

        const response = await axios.delete(`/api/v1/categories/${id}`)

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la suppression de la catégorie'
        throw error
      } finally {
        this.loading = false
      }
    },

    async restoreCategory(id) {
      try {
        this.loading = true
        this.error = null

        const response = await axios.post(`/api/v1/categories/${id}/restore`)

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la restauration de la catégorie'
        throw error
      } finally {
        this.loading = false
      }
    },

    async forceDeleteCategory(id) {
      try {
        this.loading = true
        this.error = null

        const response = await axios.delete(`/api/v1/categories/${id}/force-delete`)

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la suppression définitive de la catégorie'
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchStatistics(categoryId = null) {
      try {
        this.loading = true
        this.error = null

        const url = categoryId 
          ? `/api/v1/categories/${categoryId}/statistics`
          : '/api/v1/categories/statistics'

        const response = await axios.get(url)
        this.statistics = response.data.data

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la récupération des statistiques'
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchAvailableParents(excludeCategoryId = null) {
      try {
        this.loading = true
        this.error = null

        const url = excludeCategoryId
          ? `/api/v1/categories/${excludeCategoryId}/available-parents`
          : '/api/v1/categories/available-parents'

        const response = await axios.get(url)

        return response.data.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la récupération des parents disponibles'
        throw error
      } finally {
        this.loading = false
      }
    },

    async moveCategory(id, parentId) {
      try {
        this.loading = true
        this.error = null

        const response = await axios.post(`/api/v1/categories/${id}/move`, {
          parent_id: parentId,
        })

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors du déplacement de la catégorie'
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchCategoryPath(id) {
      try {
        this.loading = true
        this.error = null

        const response = await axios.get(`/api/v1/categories/${id}/path`)

        return response.data.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la récupération du chemin'
        throw error
      } finally {
        this.loading = false
      }
    },

    async searchCategories(search, filters = {}) {
      try {
        this.loading = true
        this.error = null

        const params = {
          search,
          ...filters,
        }

        const response = await axios.get('/api/v1/categories/search', { params })

        return response.data.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la recherche'
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchTopCategories(limit = 10) {
      try {
        this.loading = true
        this.error = null

        const response = await axios.get('/api/v1/categories/top', {
          params: { limit },
        })

        return response.data.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la récupération des top catégories'
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchRecentCategories(limit = 5) {
      try {
        this.loading = true
        this.error = null

        const response = await axios.get('/api/v1/categories/recent', {
          params: { limit },
        })

        return response.data.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la récupération des catégories récentes'
        throw error
      } finally {
        this.loading = false
      }
    },

    setFilters(filters) {
      this.filters = { ...this.filters, ...filters }
      this.pagination.current_page = 1
    },

    setSort(sortBy, sortOrder) {
      this.sort = { sort_by: sortBy, sort_order: sortOrder }
    },

    resetFilters() {
      this.filters = {
        search: '',
        status: '',
        parent_id: '',
        with_trashed: false,
        only_trashed: false,
      }
      this.pagination.current_page = 1
    },
  },
})
