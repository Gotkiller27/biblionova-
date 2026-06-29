import { defineStore } from 'pinia'
import axios from '@/plugins/axios'

export const usePublishersStore = defineStore('publishers', {
  state: () => ({
    publishers: [],
    publisher: null,
    statistics: null,
    countries: [],
    cities: [],
    topPublishers: [],
    recentPublishers: [],
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
      country: '',
      city: '',
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
  },

  actions: {
    async fetchPublishers(page = 1) {
      try {
        this.loading = true
        this.error = null

        const params = {
          page,
          per_page: this.pagination.per_page,
          ...this.filters,
          ...this.sort,
        }

        const response = await axios.get('/api/v1/publishers', { params })

        this.publishers = response.data.data.data
        this.pagination = {
          current_page: response.data.data.meta.current_page,
          per_page: response.data.data.meta.per_page,
          total: response.data.data.meta.total,
          last_page: response.data.data.meta.total_pages,
        }

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la récupération des éditeurs'
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchPublisher(id) {
      try {
        this.loading = true
        this.error = null

        const response = await axios.get(`/api/v1/publishers/${id}`)
        this.publisher = response.data.data.publisher
        this.statistics = response.data.data.statistics

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la récupération de l\'éditeur'
        throw error
      } finally {
        this.loading = false
      }
    },

    async createPublisher(publisherData) {
      try {
        this.loading = true
        this.error = null

        const formData = new FormData()
        Object.keys(publisherData).forEach(key => {
          if (publisherData[key] !== null && publisherData[key] !== undefined) {
            formData.append(key, publisherData[key])
          }
        })

        const response = await axios.post('/api/v1/publishers', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la création de l\'éditeur'
        throw error
      } finally {
        this.loading = false
      }
    },

    async updatePublisher(id, publisherData) {
      try {
        this.loading = true
        this.error = null

        const formData = new FormData()
        Object.keys(publisherData).forEach(key => {
          if (publisherData[key] !== null && publisherData[key] !== undefined) {
            formData.append(key, publisherData[key])
          }
        })

        const response = await axios.post(`/api/v1/publishers/${id}`, formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          },
          params: {
            _method: 'PUT'
          }
        })

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la modification de l\'éditeur'
        throw error
      } finally {
        this.loading = false
      }
    },

    async deletePublisher(id) {
      try {
        this.loading = true
        this.error = null

        const response = await axios.delete(`/api/v1/publishers/${id}`)

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la suppression de l\'éditeur'
        throw error
      } finally {
        this.loading = false
      }
    },

    async restorePublisher(id) {
      try {
        this.loading = true
        this.error = null

        const response = await axios.post(`/api/v1/publishers/${id}/restore`)

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la restauration de l\'éditeur'
        throw error
      } finally {
        this.loading = false
      }
    },

    async forceDeletePublisher(id) {
      try {
        this.loading = true
        this.error = null

        const response = await axios.delete(`/api/v1/publishers/${id}/force-delete`)

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la suppression définitive de l\'éditeur'
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchStatistics(publisherId = null) {
      try {
        this.loading = true
        this.error = null

        const url = publisherId 
          ? `/api/v1/publishers/${publisherId}/statistics`
          : '/api/v1/publishers/statistics'

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

    async searchPublishers(search, limit = 20) {
      try {
        this.loading = true
        this.error = null

        const response = await axios.get('/api/v1/publishers/search', {
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

    async fetchCountries() {
      try {
        this.loading = true
        this.error = null

        const response = await axios.get('/api/v1/publishers/countries')
        this.countries = response.data.data

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la récupération des pays'
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchCities() {
      try {
        this.loading = true
        this.error = null

        const response = await axios.get('/api/v1/publishers/cities')
        this.cities = response.data.data

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la récupération des villes'
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchTopPublishers(limit = 10) {
      try {
        this.loading = true
        this.error = null

        const response = await axios.get('/api/v1/publishers/top', {
          params: { limit }
        })

        this.topPublishers = response.data.data

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la récupération des top éditeurs'
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchRecentPublishers(limit = 5) {
      try {
        this.loading = true
        this.error = null

        const response = await axios.get('/api/v1/publishers/recent', {
          params: { limit }
        })

        this.recentPublishers = response.data.data

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la récupération des éditeurs récents'
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
        country: '',
        city: '',
        with_trashed: false,
        only_trashed: false,
      }
      this.pagination.current_page = 1
    },
  },
})
