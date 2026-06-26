import { defineStore } from 'pinia'
import axios from '@/plugins/axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    isAuthenticated: false,
    isLoading: false,
    authInitialized: false,
    redirectUrl: null,
  }),

  getters: {
    userName: (state) => {
      if (!state.user) return null
      return `${state.user.first_name} ${state.user.last_name}`
    },
    userFullName: (state) => {
      if (!state.user) return null
      return `${state.user.first_name} ${state.user.last_name}`
    },
    userRole: (state) => {
      if (!state.user || !state.user.roles || !state.user.roles.length) return null
      return state.user.roles[0].name
    },
    userRoleLabel: (state) => {
      const roleLabels = {
        admin: 'Administrateur',
        responsable_rh: 'Responsable RH',
        responsable_validation: 'Responsable Validation',
        bibliothecaire: 'Bibliothécaire',
        utilisateur: 'Utilisateur'
      }
      return roleLabels[state.userRole] || 'Utilisateur'
    },
    canAccess: (state) => (permission) => {
      if (!state.user || !state.user.permissions) return false
      return state.user.permissions.some(p => p.name === permission)
    },
    hasRole: (state) => (role) => {
      if (!state.user || !state.user.roles) return false
      return state.user.roles.some(r => r.name === role)
    }
  },

  actions: {
    // Get redirect URL based on user role (client side)
    getRedirectUrlByRole(user) {
      if (!user || !user.roles || !user.roles.length) return '/dashboard'
      
      const role = user.roles[0].name
      
      switch(role) {
        case 'admin':
          return '/admin/dashboard'
        case 'responsable_rh':
          return '/rh/dashboard'
        case 'responsable_validation':
          return '/validation/dashboard'
        case 'bibliothecaire':
          return '/bibliothecaire/dashboard'
        default:
          return '/dashboard'
      }
    },

    // Initialize auth status on app start
    async initAuth() {
      try {
        this.isLoading = true
        // #region debug-point A:init-auth-start
        fetch('http://127.0.0.1:7777/event', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ sessionId: 'dashboard-refresh-auth', runId: 'pre-fix', hypothesisId: 'A', location: 'frontend/src/stores/auth.js:initAuth:start', msg: '[DEBUG] initAuth started', data: { hasUser: !!this.user, isAuthenticated: this.isAuthenticated, isLoading: this.isLoading, cookie: document.cookie, origin: window.location.origin }, ts: Date.now() }) }).catch(() => {})
        // #endregion
        console.log('🔍 Checking auth status...')
        const response = await axios.get('/api/v1/auth/check')
        // #region debug-point A:init-auth-response
        fetch('http://127.0.0.1:7777/event', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ sessionId: 'dashboard-refresh-auth', runId: 'pre-fix', hypothesisId: 'A', location: 'frontend/src/stores/auth.js:initAuth:response', msg: '[DEBUG] initAuth received response', data: { success: response?.data?.success, authenticated: response?.data?.data?.authenticated, userEmail: response?.data?.data?.user?.email || null, userRole: response?.data?.data?.user?.roles?.[0]?.name || null }, ts: Date.now() }) }).catch(() => {})
        // #endregion
        console.log('📥 Auth check response:', response)
        
        if (response.data.success && response.data.data.authenticated) {
          this.user = response.data.data.user
          this.isAuthenticated = true
          this.redirectUrl = this.getRedirectUrlByRole(response.data.data.user)
          // #region debug-point E:init-auth-authenticated
          fetch('http://127.0.0.1:7777/event', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ sessionId: 'dashboard-refresh-auth', runId: 'pre-fix', hypothesisId: 'E', location: 'frontend/src/stores/auth.js:initAuth:authenticated', msg: '[DEBUG] initAuth authenticated user', data: { redirectUrl: this.redirectUrl, userEmail: this.user?.email || null, userRole: this.userRole }, ts: Date.now() }) }).catch(() => {})
          // #endregion
          console.log('✅ Authenticated as:', this.user.email)
        } else {
          // #region debug-point A:init-auth-not-authenticated
          fetch('http://127.0.0.1:7777/event', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ sessionId: 'dashboard-refresh-auth', runId: 'pre-fix', hypothesisId: 'A', location: 'frontend/src/stores/auth.js:initAuth:not-authenticated', msg: '[DEBUG] initAuth returned guest state', data: { success: response?.data?.success, authenticated: response?.data?.data?.authenticated || false }, ts: Date.now() }) }).catch(() => {})
          // #endregion
        }
      } catch (error) {
        // #region debug-point C:init-auth-error
        fetch('http://127.0.0.1:7777/event', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ sessionId: 'dashboard-refresh-auth', runId: 'pre-fix', hypothesisId: 'C', location: 'frontend/src/stores/auth.js:initAuth:catch', msg: '[DEBUG] initAuth failed', data: { message: error?.message || null, status: error?.response?.status || null, response: error?.response?.data || null, cookie: document.cookie, origin: window.location.origin }, ts: Date.now() }) }).catch(() => {})
        // #endregion
        console.log('❌ Not authenticated:', error)
        this.user = null
        this.isAuthenticated = false
      } finally {
        this.isLoading = false
        this.authInitialized = true
        // #region debug-point E:init-auth-finally
        fetch('http://127.0.0.1:7777/event', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ sessionId: 'dashboard-refresh-auth', runId: 'pre-fix', hypothesisId: 'E', location: 'frontend/src/stores/auth.js:initAuth:finally', msg: '[DEBUG] initAuth finalized', data: { hasUser: !!this.user, isAuthenticated: this.isAuthenticated, isLoading: this.isLoading, redirectUrl: this.redirectUrl }, ts: Date.now() }) }).catch(() => {})
        // #endregion
      }
    },

    // Login
    async login(credentials) {
      try {
        this.isLoading = true
        console.log('🔑 Tentative de connexion avec credentials:', credentials)
        const response = await axios.post('/api/v1/auth/login', credentials)
        console.log('✅ Réponse du serveur:', response)
        
        if (response.data.success) {
          this.user = response.data.data.user
          this.isAuthenticated = true
          this.redirectUrl = response.data.data.redirect_url
          console.log('✅ Utilisateur connecté:', this.user)
          console.log('✅ URL de redirection:', this.redirectUrl)
          
          return {
            success: true,
            message: response.data.message,
            redirectUrl: response.data.data.redirect_url
          }
        }
      } catch (error) {
        console.error('❌ Erreur de connexion:', error)
        const errorData = error.response?.data || {}
        throw {
          success: false,
          message: errorData.message || 'Erreur de connexion',
          errors: errorData.errors || {}
        }
      } finally {
        this.isLoading = false
      }
    },

    // Register
    async register(userData) {
      try {
        this.isLoading = true
        const response = await axios.post('/api/v1/auth/register', userData)
        
        if (response.data.success) {
          this.user = response.data.data
          this.isAuthenticated = true
          
          return {
            success: true,
            message: response.data.message
          }
        }
      } catch (error) {
        const errorData = error.response?.data || {}
        throw {
          success: false,
          message: errorData.message || 'Erreur d\'inscription',
          errors: errorData.errors || {}
        }
      } finally {
        this.isLoading = false
      }
    },

    // Logout
    async logout() {
      try {
        await axios.post('/api/v1/auth/logout')
      } catch (error) {
        console.log('Logout error:', error)
      } finally {
        this.user = null
        this.isAuthenticated = false
        this.redirectUrl = null
        this.authInitialized = false
      }
    },

    // Get current user
    async me() {
      try {
        const response = await axios.get('/api/v1/auth/me')
        
        if (response.data.success) {
          this.user = response.data.data.user
          this.isAuthenticated = true
          this.redirectUrl = response.data.data.redirect_url
          return response.data.data
        }
      } catch (error) {
        this.user = null
        this.isAuthenticated = false
        throw error
      }
    },

    // Change password
    async changePassword(passwords) {
      try {
        const response = await axios.post('/api/v1/auth/change-password', passwords)
        
        return {
          success: true,
          message: response.data.message
        }
      } catch (error) {
        const errorData = error.response?.data || {}
        throw {
          success: false,
          message: errorData.message || 'Erreur lors du changement de mot de passe',
          errors: errorData.errors || {}
        }
      }
    },

    // Forgot password
    async forgotPassword(email) {
      try {
        const response = await axios.post('/api/v1/auth/forgot-password', { email })
        
        return {
          success: true,
          message: response.data.message
        }
      } catch (error) {
        const errorData = error.response?.data || {}
        throw {
          success: false,
          message: errorData.message || 'Erreur lors de l\'envoi de l\'email',
          errors: errorData.errors || {}
        }
      }
    },

    // Reset password
    async resetPassword(resetData) {
      try {
        const response = await axios.post('/api/v1/auth/reset-password', resetData)
        
        return {
          success: true,
          message: response.data.message
        }
      } catch (error) {
        const errorData = error.response?.data || {}
        throw {
          success: false,
          message: errorData.message || 'Erreur lors de la réinitialisation',
          errors: errorData.errors || {}
        }
      }
    }
  }
})
