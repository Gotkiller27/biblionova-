<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-orange-50 to-red-100">
    <div class="max-w-md w-full space-y-8">
      <div>
        <div class="mx-auto h-16 w-16 bg-gradient-to-r from-orange-600 to-red-600 rounded-full flex items-center justify-center">
          <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
          </svg>
        </div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          Nouveau mot de passe
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
          Choisissez un nouveau mot de passe sécurisé pour votre compte
        </p>
      </div>
      
      <form class="mt-8 space-y-6" @submit.prevent="handleResetPassword">
        <div class="bg-white rounded-xl shadow-lg p-8 space-y-6">
          <!-- Messages de succès -->
          <div v-if="successMessage" class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex">
              <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
              </svg>
              <div class="ml-3">
                <p class="text-sm text-green-800">{{ successMessage }}</p>
                <p class="text-xs text-green-700 mt-1">Vous allez être redirigé vers la page de connexion</p>
              </div>
            </div>
          </div>

          <!-- Messages d'erreur -->
          <div v-if="errorMessage" class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
              <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
              </svg>
              <div class="ml-3">
                <p class="text-sm text-red-800">{{ errorMessage }}</p>
              </div>
            </div>
          </div>
          
          <div class="space-y-4" v-if="!successMessage">
            <!-- Email (readonly) -->
            <div>
              <label for="email" class="block text-sm font-medium text-gray-700">Adresse email</label>
              <input
                id="email"
                v-model="form.email"
                type="email"
                readonly
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm bg-gray-50 focus:outline-none"
                placeholder="votre@email.com"
              />
            </div>

            <!-- New Password -->
            <div>
              <label for="password" class="block text-sm font-medium text-gray-700">Nouveau mot de passe</label>
              <input
                id="password"
                v-model="form.password"
                type="password"
                required
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                :class="{ 'border-red-500': errors.password }"
                placeholder="Au moins 8 caractères"
              />
              <p v-if="errors.password" class="mt-1 text-sm text-red-600">{{ errors.password[0] }}</p>
            </div>

            <!-- Confirm Password -->
            <div>
              <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmer le nouveau mot de passe</label>
              <input
                id="password_confirmation"
                v-model="form.password_confirmation"
                type="password"
                required
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                placeholder="Confirmer le mot de passe"
              />
            </div>

            <!-- Password Requirements -->
            <div class="bg-blue-50 rounded-lg p-3">
              <div class="flex">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"/>
                </svg>
                <div class="ml-3">
                  <p class="text-sm text-blue-800 font-medium">Exigences du mot de passe :</p>
                  <ul class="text-xs text-blue-700 mt-1 space-y-1">
                    <li>• Au moins 8 caractères</li>
                    <li>• Mélange de lettres et chiffres recommandé</li>
                    <li>• Évitez les mots de passe trop simples</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <div v-if="!successMessage">
            <button
              type="submit"
              :disabled="isLoading"
              class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
            >
              <span v-if="isLoading" class="absolute left-0 inset-y-0 flex items-center pl-3">
                <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
              </span>
              {{ isLoading ? 'Mise à jour...' : 'Mettre à jour le mot de passe' }}
            </button>
          </div>

          <!-- Back to login -->
          <div class="text-center">
            <router-link 
              to="/auth/login" 
              class="inline-flex items-center text-sm font-medium text-orange-600 hover:text-orange-500"
            >
              <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
              </svg>
              Retour à la connexion
            </router-link>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

// Form data
const form = reactive({
  token: '',
  email: '',
  password: '',
  password_confirmation: ''
})

// State
const isLoading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')
const errors = ref({})

// Handle reset password
const handleResetPassword = async () => {
  try {
    isLoading.value = true
    errorMessage.value = ''
    successMessage.value = ''
    errors.value = {}

    const result = await authStore.resetPassword(form)
    
    if (result.success) {
      successMessage.value = result.message
      
      // Redirect to login after 3 seconds
      setTimeout(() => {
        router.push({
          name: 'Login',
          query: { 
            message: 'Mot de passe mis à jour avec succès ! Vous pouvez maintenant vous connecter.' 
          }
        })
      }, 3000)
    }
  } catch (error) {
    errorMessage.value = error.message
    errors.value = error.errors || {}
  } finally {
    isLoading.value = false
  }
}

// Initialize form with URL parameters
onMounted(() => {
  form.token = route.query.token || ''
  form.email = route.query.email || ''
  
  if (!form.token || !form.email) {
    errorMessage.value = 'Lien de réinitialisation invalide ou expiré'
  }
})
</script>