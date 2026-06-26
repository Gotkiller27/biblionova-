<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-[#F8F5F0] via-[#F0EAE0] to-[#EDE4D6] p-4">
    <div class="w-full max-w-md">
      <!-- Logo et Titre -->
      <div class="text-center mb-10">
        <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-[#5C4033] mb-6 shadow-lg">
          <i class="pi pi-book text-5xl text-[#C5A46D]"></i>
        </div>
        <h1 class="text-4xl font-serif text-[#2D2A26] mb-3" style="font-family: 'Playfair Display', serif;">BiblioNova</h1>
        <p class="text-gray-600 font-sans">La bibliothèque numérique de la connaissance</p>
      </div>

      <!-- Formulaire -->
      <div class="bg-white rounded-2xl shadow-xl shadow-black/5 border border-[#E2D7C6] p-8">
        <h2 class="text-2xl font-serif text-[#2D2A26] mb-8 text-center" style="font-family: 'Playfair Display', serif;">Connexion</h2>

        <form @submit.prevent="handleLogin" class="space-y-6">
          <!-- Messages -->
          <div v-if="errorMessage" class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
            <p class="text-red-700 text-sm">{{ errorMessage }}</p>
          </div>
          
          <div v-if="successMessage" class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
            <p class="text-green-700 text-sm">{{ successMessage }}</p>
          </div>

          <!-- Email -->
          <div class="space-y-2">
            <label for="email" class="block text-[#2D2A26] font-medium font-sans">Email</label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              required
              class="w-full px-4 py-3 rounded-lg border border-[#E2D7C6] bg-white text-[#2D2A26] focus:outline-none focus:border-[#C5A46D] focus:ring-2 focus:ring-[#C5A46D]/20 transition-all"
              placeholder="votre@email.com"
            />
          </div>

          <!-- Mot de passe -->
          <div class="space-y-2">
            <label for="password" class="block text-[#2D2A26] font-medium font-sans">Mot de passe</label>
            <div class="relative">
              <input
                id="password"
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                required
                class="w-full px-4 py-3 pr-12 rounded-lg border border-[#E2D7C6] bg-white text-[#2D2A26] focus:outline-none focus:border-[#C5A46D] focus:ring-2 focus:ring-[#C5A46D]/20 transition-all"
                placeholder="Votre mot de passe"
              />
              <button 
                type="button"
                @click="showPassword = !showPassword"
                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-[#5C4033] transition-colors"
              >
                <i :class="showPassword ? 'pi pi-eye-slash' : 'pi pi-eye'"></i>
              </button>
            </div>
          </div>

          <!-- Souvenir & Mot de passe oublié -->
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <input
                id="remember"
                v-model="form.remember"
                type="checkbox"
                class="w-4 h-4 text-[#5C4033] border-[#E2D7C6] rounded focus:ring-[#C5A46D]"
              />
              <label for="remember" class="text-gray-600 text-sm font-sans">
                Se souvenir de moi
              </label>
            </div>
            <router-link
              to="/auth/forgot-password"
              class="text-[#C5A46D] font-sans text-sm hover:underline transition-all"
            >
              Mot de passe oublié ?
            </router-link>
          </div>

          <!-- Bouton de connexion -->
          <button
            type="submit"
            :disabled="isLoading"
            class="w-full bg-[#5C4033] text-white font-medium py-3 px-4 rounded-lg hover:bg-[#6D4C3D] transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
          >
            <span v-if="isLoading" class="pi pi-spin pi-spinner"></span>
            <span v-else>Se connecter</span>
          </button>
        </form>

        <!-- Lien d'inscription -->
        <div class="mt-8 text-center">
          <p class="text-gray-600 font-sans">
            Pas encore de compte ?
            <router-link
              to="/auth/register"
              class="text-[#C5A46D] font-medium hover:underline transition-all"
            >
              S'inscrire
            </router-link>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const showPassword = ref(false)

const form = reactive({
  email: '',
  password: '',
  remember: false
})

const isLoading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

const handleLogin = async () => {
  try {
    isLoading.value = true
    errorMessage.value = ''
    successMessage.value = ''

    console.log('📝 Tentative de connexion avec:', form.email)
    const result = await authStore.login(form)
    console.log('✅ Résultat login:', result)
    
    if (result.success) {
      successMessage.value = result.message
      
      const redirect = route.query.redirect || result.redirectUrl || '/dashboard'
      console.log('🚀 Redirection vers:', redirect)
      
      setTimeout(() => {
        router.push(redirect)
      }, 1000)
    }
  } catch (error) {
    console.error('❌ Erreur dans handleLogin:', error)
    errorMessage.value = error.message || 'Une erreur est survenue'
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  if (route.query.message) {
    successMessage.value = route.query.message
  }
})
</script>
