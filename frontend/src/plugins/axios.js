import axios from 'axios'

// Configuration de base pour Sanctum SPA
const axiosInstance = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000',
  withCredentials: true,
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  }
})

// Intercepteur pour gérer le CSRF token automatiquement
axiosInstance.interceptors.request.use(async (config) => {
  // Pour les requêtes POST, PUT, PATCH, DELETE, on a besoin du CSRF token
  if (['post', 'put', 'patch', 'delete'].includes(config.method)) {
    // Vérifier si on a déjà le CSRF token
    if (!document.cookie.includes('XSRF-TOKEN')) {
      try {
        // Obtenir le CSRF cookie - use axiosInstance so it uses the same baseURL and withCredentials
        await axiosInstance.get('/sanctum/csrf-cookie')
      } catch (error) {
        console.log('Erreur lors de l\'obtention du CSRF token:', error)
      }
    }
  }
  
  return config
}, (error) => {
  return Promise.reject(error)
})

// Intercepteur pour gérer les erreurs de réponse
axiosInstance.interceptors.response.use(
  (response) => response,
  (error) => {
    // Si on reçoit une erreur 401 (non authentifié), on peut rediriger vers la page de login
    if (error.response?.status === 401) {
      // Ne pas rediriger automatiquement, laisser les composants gérer
      console.log('Non authentifié')
    }
    
    // Si erreur 419 (CSRF token mismatch), on peut réessayer après refresh du token
    if (error.response?.status === 419) {
      console.log('CSRF token expiré')
    }
    
    return Promise.reject(error)
  }
)

export default axiosInstance