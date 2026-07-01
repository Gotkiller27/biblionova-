import axios from 'axios'
import { useAuthStore } from '@/stores/auth'

// Configuration de base pour Sanctum SPA
const axiosInstance = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000',
  withCredentials: true,
  withXSRFToken:true,
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  }
})

// Intercepteur pour gérer les erreurs de réponse
axiosInstance.interceptors.response.use(
  (response) => response,
  (error) => {
    // Si on reçoit une erreur 401 (non authentifié), on réinitialise le store
    if (error.response?.status === 401) {
      console.log('Non authentifié')
      try {
        const authStore = useAuthStore()
        authStore.$patch({
          user: null,
          isAuthenticated: false,
          redirectUrl: null,
          authInitialized: false
        })
      } catch (storeError) {
        console.error('Erreur lors de la réinitialisation du store auth:', storeError)
      }
    }

    // Si erreur 419 (CSRF token mismatch), on peut réessayer après refresh du token
    if (error.response?.status === 419 && !error.config._retry) {
      console.log('CSRF token expiré, tentative de rafraîchissement...')
      error.config._retry = true
      
      return axiosInstance.get('/sanctum/csrf-cookie')
        .then(() => {
          // Relancer la requête originale après avoir obtenu le nouveau cookie
          return axiosInstance(error.config)
        })
        .catch((csrfError) => {
          console.error('Échec du rafraîchissement du cookie CSRF:', csrfError)
          return Promise.reject(error)
        })
    }

    return Promise.reject(error)
  }
)

export default axiosInstance