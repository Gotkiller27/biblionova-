import { defineStore } from 'pinia'
import axios from 'axios'

export const useKeywordsStore = defineStore('keywords', {
  state: () => ({
    keywords: [],
    currentKeyword: null,
    statistics: null,
    tagCloud: [],
    topKeywords: [],
    recentKeywords: [],
    trendingKeywords: [],
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
      with_trashed: false,
      only_trashed: false,
    },
    sortBy: 'name',
    sortOrder: 'asc',
  }),

  getters: {
    totalKeywords: (state) => state.pagination.total,
    totalPages: (state) => state.pagination.last_page,
    currentPage: (state) => state.pagination.current_page,
    hasKeywords: (state) => state.keywords.length > 0,
    isLoading: (state) => state.loading,
    hasError: (state) => state.error !== null,
  },

  actions: {
    async fetchKeywords(page = 1) {
      this.loading = true
      this.error = null

      try {
        const params = {
          page,
          per_page: this.pagination.per_page,
          sort_by: this.sortBy,
          sort_order: this.sortOrder,
          ...this.filters,
        }

        const response = await axios.get('/api/v1/keywords', { params })
        this.keywords = response.data.data.data
        this.pagination = {
          current_page: response.data.data.meta.current_page,
          per_page: response.data.data.meta.per_page,
          total: response.data.data.meta.total,
          last_page: response.data.data.meta.total_pages,
        }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch keywords'
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchKeyword(id) {
      this.loading = true
      this.error = null

      try {
        const response = await axios.get(`/api/v1/keywords/${id}`)
        this.currentKeyword = response.data.data.keyword
        return response.data.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch keyword'
        throw error
      } finally {
        this.loading = false
      }
    },

    async createKeyword(keywordData) {
      this.loading = true
      this.error = null

      try {
        const response = await axios.post('/api/v1/keywords', keywordData)
        await this.fetchKeywords(this.pagination.current_page)
        return response.data.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to create keyword'
        throw error
      } finally {
        this.loading = false
      }
    },

    async updateKeyword(id, keywordData) {
      this.loading = true
      this.error = null

      try {
        const response = await axios.put(`/api/v1/keywords/${id}`, keywordData)
        await this.fetchKeywords(this.pagination.current_page)
        return response.data.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to update keyword'
        throw error
      } finally {
        this.loading = false
      }
    },

    async deleteKeyword(id) {
      this.loading = true
      this.error = null

      try {
        await axios.delete(`/api/v1/keywords/${id}`)
        await this.fetchKeywords(this.pagination.current_page)
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to delete keyword'
        throw error
      } finally {
        this.loading = false
      }
    },

    async restoreKeyword(id) {
      this.loading = true
      this.error = null

      try {
        await axios.post(`/api/v1/keywords/${id}/restore`)
        await this.fetchKeywords(this.pagination.current_page)
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to restore keyword'
        throw error
      } finally {
        this.loading = false
      }
    },

    async forceDeleteKeyword(id) {
      this.loading = true
      this.error = null

      try {
        await axios.delete(`/api/v1/keywords/${id}/force-delete`)
        await this.fetchKeywords(this.pagination.current_page)
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to permanently delete keyword'
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchStatistics() {
      this.loading = true
      this.error = null

      try {
        const response = await axios.get('/api/v1/keywords/statistics')
        this.statistics = response.data.data
        return response.data.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch statistics'
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchKeywordStatistics(id) {
      this.loading = true
      this.error = null

      try {
        const response = await axios.get(`/api/v1/keywords/${id}/statistics`)
        return response.data.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch keyword statistics'
        throw error
      } finally {
        this.loading = false
      }
    },

    async searchKeywords(searchTerm, limit = 20) {
      this.loading = true
      this.error = null

      try {
        const response = await axios.get('/api/v1/keywords/search', {
          params: { search: searchTerm, limit },
        })
        return response.data.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to search keywords'
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchSuggestions(partial, limit = 10) {
      this.loading = true
      this.error = null

      try {
        const response = await axios.get('/api/v1/keywords/suggestions', {
          params: { partial, limit },
        })
        return response.data.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch suggestions'
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchTagCloud(limit = 50) {
      this.loading = true
      this.error = null

      try {
        const response = await axios.get('/api/v1/keywords/tag-cloud', {
          params: { limit },
        })
        this.tagCloud = response.data.data
        return response.data.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch tag cloud'
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchTopKeywords(limit = 10) {
      this.loading = true
      this.error = null

      try {
        const response = await axios.get('/api/v1/keywords/top', {
          params: { limit },
        })
        this.topKeywords = response.data.data
        return response.data.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch top keywords'
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchRecentKeywords(limit = 10) {
      this.loading = true
      this.error = null

      try {
        const response = await axios.get('/api/v1/keywords/recent', {
          params: { limit },
        })
        this.recentKeywords = response.data.data
        return response.data.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch recent keywords'
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchTrendingKeywords(limit = 10) {
      this.loading = true
      this.error = null

      try {
        const response = await axios.get('/api/v1/keywords/trending', {
          params: { limit },
        })
        this.trendingKeywords = response.data.data
        return response.data.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch trending keywords'
        throw error
      } finally {
        this.loading = false
      }
    },

    async importKeywords(keywords) {
      this.loading = true
      this.error = null

      try {
        const response = await axios.post('/api/v1/keywords/import', { keywords })
        await this.fetchKeywords(this.pagination.current_page)
        return response.data.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to import keywords'
        throw error
      } finally {
        this.loading = false
      }
    },

    async exportKeywords() {
      this.loading = true
      this.error = null

      try {
        const response = await axios.get('/api/v1/keywords/export', {
          responseType: 'blob',
        })
        
        const url = window.URL.createObjectURL(new Blob([response.data]))
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', `keywords_export_${new Date().toISOString()}.json`)
        document.body.appendChild(link)
        link.click()
        link.remove()
        window.URL.revokeObjectURL(url)
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to export keywords'
        throw error
      } finally {
        this.loading = false
      }
    },

    setFilters(filters) {
      this.filters = { ...this.filters, ...filters }
    },

    setSort(sortBy, sortOrder) {
      this.sortBy = sortBy
      this.sortOrder = sortOrder
    },

    setPerPage(perPage) {
      this.pagination.per_page = perPage
    },

    resetFilters() {
      this.filters = {
        search: '',
        with_trashed: false,
        only_trashed: false,
      }
      this.sortBy = 'name'
      this.sortOrder = 'asc'
    },

    clearError() {
      this.error = null
    },
  },
})
