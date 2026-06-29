import { defineStore } from 'pinia'
import axios from '@/plugins/axios'

export const useAuthorsStore = defineStore('authors', {
  state: () => ({
    authors: [],
    author: null,
    statistics: null,
    nationalities: [],
    topAuthors: [],
    recentAuthors: [],
    coAuthors: [],
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
      nationality: '',
      status: '',
      with_trashed: false,
      only_trashed: false,
    },
    sort: {
      sort_by: 'last_name',
      sort_order: 'asc',
    },
  }),

  getters: {
    totalPages: (state) => state.pagination.last_page,
    hasNextPage: (state) => state.pagination.current_page < state.pagination.last_page,
    hasPrevPage: (state) => state.pagination.current_page > 1,
  },

  actions: {
    async fetchAuthors(page = 1) {
      try {
        this.loading = true
        this.error = null

        const params = {
          page,
          per_page: this.pagination.per_page,
          ...this.filters,
          ...this.sort,
        }

        const response = await axios.get('/api/v1/authors', { params })

        this.authors = response.data.data.data
        this.pagination = {
          current_page: response.data.data.meta.current_page,
          per_page: response.data.data.meta.per_page,
          total: response.data.data.meta.total,
          last_page: response.data.data.meta.total_pages,
        }

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la récupération des auteurs'
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchAuthor(id) {
      try {
        this.loading = true
        this.error = null

        const response = await axios.get(`/api/v1/authors/${id}`)
        this.author = response.data.data.author
        this.statistics = response.data.data.statistics

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la récupération de l\'auteur'
        throw error
      } finally {
        this.loading = false
      }
    },

    async createAuthor(authorData) {
      try {
        this.loading = true
        this.error = null

        const formData = new FormData()
        Object.keys(authorData).forEach(key => {
          if (authorData[key] !== null && authorData[key] !== undefined) {
            formData.append(key, authorData[key])
          }
        })

        const response = await axios.post('/api/v1/authors', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la création de l\'auteur'
        throw error
      } finally {
        this.loading = false
      }
    },

    async updateAuthor(id, authorData) {
      try {
        this.loading = true
        this.error = null

        const formData = new FormData()
        Object.keys(authorData).forEach(key => {
          if (authorData[key] !== null && authorData[key] !== undefined) {
            formData.append(key, authorData[key])
          }
        })

        const response = await axios.post(`/api/v1/authors/${id}`, formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          },
          params: {
            _method: 'PUT'
          }
        })

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la modification de l\'auteur'
        throw error
      } finally {
        this.loading = false
      }
    },

    async deleteAuthor(id) {
      try {
        this.loading = true
        this.error = null

        const response = await axios.delete(`/api/v1/authors/${id}`)

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la suppression de l\'auteur'
        throw error
      } finally {
        this.loading = false
      }
    },

    async restoreAuthor(id) {
      try {
        this.loading = true
        this.error = null

        const response = await axios.post(`/api/v1/authors/${id}/restore`)

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la restauration de l\'auteur'
        throw error
      } finally {
        this.loading = false
      }
    },

    async forceDeleteAuthor(id) {
      try {
        this.loading = true
        this.error = null

        const response = await axios.delete(`/api/v1/authors/${id}/force-delete`)

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la suppression définitive de l\'auteur'
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchStatistics(authorId = null) {
      try {
        this.loading = true
        this.error = null

        const url = authorId 
          ? `/api/v1/authors/${authorId}/statistics`
          : '/api/v1/authors/statistics'

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

    async searchAuthors(search, limit = 20) {
      try {
        this.loading = true
        this.error = null

        const response = await axios.get('/api/v1/authors/search', {
          params: { search, limit }
        })

        return response.data.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la recherche'
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchNationalities() {
      try {
        this.loading = true
        this.error = null

        const response = await axios.get('/api/v1/authors/nationalities')
        this.nationalities = response.data.data

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la récupération des nationalités'
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchTopAuthors(limit = 10) {
      try {
        this.loading = true
        this.error = null

        const response = await axios.get('/api/v1/authors/top', {
          params: { limit }
        })

        this.topAuthors = response.data.data

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la récupération des top auteurs'
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchRecentAuthors(limit = 5) {
      try {
        this.loading = true
        this.error = null

        const response = await axios.get('/api/v1/authors/recent', {
          params: { limit }
        })

        this.recentAuthors = response.data.data

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la récupération des auteurs récents'
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchCoAuthors(authorId) {
      try {
        this.loading = true
        this.error = null

        const response = await axios.get(`/api/v1/authors/${authorId}/co-authors`)
        this.coAuthors = response.data.data

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la récupération des co-auteurs'
        throw error
      } finally {
        this.loading = false
      }
    },

    async addCoAuthor(authorId, coAuthorId) {
      try {
        this.loading = true
        this.error = null

        const response = await axios.post(`/api/v1/authors/${authorId}/co-authors`, {
          co_author_id: coAuthorId
        })

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de l\'ajout du co-auteur'
        throw error
      } finally {
        this.loading = false
      }
    },

    async removeCoAuthor(authorId, coAuthorId) {
      try {
        this.loading = true
        this.error = null

        const response = await axios.delete(`/api/v1/authors/${authorId}/co-authors`, {
          params: { co_author_id: coAuthorId }
        })

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la suppression du co-auteur'
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
        nationality: '',
        status: '',
        with_trashed: false,
        only_trashed: false,
      }
      this.pagination.current_page = 1
    },
  },
})
