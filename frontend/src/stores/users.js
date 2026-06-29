import { defineStore } from 'pinia'
import axios from '@/plugins/axios'

export const useUsersStore = defineStore('users', {
  state: () => ({
    users: [],
    user: null,
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
      role: '',
      status: '',
      from_date: '',
      to_date: '',
      with_trashed: false,
      only_trashed: false,
    },
    sort: {
      sort_by: 'created_at',
      sort_order: 'desc',
    },
  }),

  getters: {
    totalPages: (state) => state.pagination.last_page,
    hasNextPage: (state) => state.pagination.current_page < state.pagination.last_page,
    hasPrevPage: (state) => state.pagination.current_page > 1,
  },

  actions: {
    async fetchUsers(page = 1) {
      try {
        this.loading = true
        this.error = null

        const params = {
          page,
          per_page: this.pagination.per_page,
          ...this.filters,
          ...this.sort,
        }

        const response = await axios.get('/api/v1/users', { params })

        this.users = response.data.data.data
        this.pagination = {
          current_page: response.data.data.meta.current_page,
          per_page: response.data.data.meta.per_page,
          total: response.data.data.meta.total,
          last_page: response.data.data.meta.total_pages,
        }

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la récupération des utilisateurs'
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchUser(id) {
      try {
        this.loading = true
        this.error = null

        const response = await axios.get(`/api/v1/users/${id}`)
        this.user = response.data.data

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la récupération de l\'utilisateur'
        throw error
      } finally {
        this.loading = false
      }
    },

    async createUser(userData) {
      try {
        this.loading = true
        this.error = null

        const response = await axios.post('/api/v1/users', userData)

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la création de l\'utilisateur'
        throw error
      } finally {
        this.loading = false
      }
    },

    async updateUser(id, userData) {
      try {
        this.loading = true
        this.error = null

        const response = await axios.put(`/api/v1/users/${id}`, userData)

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la modification de l\'utilisateur'
        throw error
      } finally {
        this.loading = false
      }
    },

    async deleteUser(id) {
      try {
        this.loading = true
        this.error = null

        const response = await axios.delete(`/api/v1/users/${id}`)

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la suppression de l\'utilisateur'
        throw error
      } finally {
        this.loading = false
      }
    },

    async restoreUser(id) {
      try {
        this.loading = true
        this.error = null

        const response = await axios.post(`/api/v1/users/${id}/restore`)

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la restauration de l\'utilisateur'
        throw error
      } finally {
        this.loading = false
      }
    },

    async forceDeleteUser(id) {
      try {
        this.loading = true
        this.error = null

        const response = await axios.delete(`/api/v1/users/${id}/force-delete`)

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la suppression définitive de l\'utilisateur'
        throw error
      } finally {
        this.loading = false
      }
    },

    // Role Management
    async assignRole(userId, role) {
      try {
        this.loading = true
        this.error = null

        const response = await axios.post(`/api/v1/users/${userId}/assign-role`, { role })

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de l\'attribution du rôle'
        throw error
      } finally {
        this.loading = false
      }
    },

    async removeRole(userId, role) {
      try {
        this.loading = true
        this.error = null

        const response = await axios.delete(`/api/v1/users/${userId}/remove-role`, { data: { role } })

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la suppression du rôle'
        throw error
      } finally {
        this.loading = false
      }
    },

    async syncRoles(userId, roles) {
      try {
        this.loading = true
        this.error = null

        const response = await axios.post(`/api/v1/users/${userId}/sync-roles`, { roles })

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la synchronisation des rôles'
        throw error
      } finally {
        this.loading = false
      }
    },

    // Permission Management
    async assignPermission(userId, permission) {
      try {
        this.loading = true
        this.error = null

        const response = await axios.post(`/api/v1/users/${userId}/assign-permission`, { permission })

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de l\'attribution de la permission'
        throw error
      } finally {
        this.loading = false
      }
    },

    async removePermission(userId, permission) {
      try {
        this.loading = true
        this.error = null

        const response = await axios.delete(`/api/v1/users/${userId}/remove-permission`, { data: { permission } })

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la suppression de la permission'
        throw error
      } finally {
        this.loading = false
      }
    },

    // Status Management
    async updateStatus(userId, status) {
      try {
        this.loading = true
        this.error = null

        const response = await axios.put(`/api/v1/users/${userId}/status`, { status })

        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la mise à jour du statut'
        throw error
      } finally {
        this.loading = false
      }
    },

    // Export
    async exportUsers(format = 'xlsx') {
      try {
        this.loading = true
        this.error = null

        const params = {
          format,
          ...this.filters,
        }

        const response = await axios.get('/api/v1/users/export', {
          params,
          responseType: 'blob',
        })

        return response
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de l\'export'
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
        role: '',
        status: '',
        from_date: '',
        to_date: '',
        with_trashed: false,
        only_trashed: false,
      }
      this.pagination.current_page = 1
    },
  },
})
