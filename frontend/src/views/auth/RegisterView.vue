<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-[#F8F5F0] via-[#F0EAE0] to-[#EDE4D6] p-4">
    <div class="w-full max-w-2xl">
      <!-- Logo et Titre -->
      <div class="text-center mb-10">
        <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-[#5C4033] mb-6 shadow-lg">
          <i class="pi pi-book text-5xl text-[#C5A46D]"></i>
        </div>
        <h1 class="text-4xl font-serif text-[#2D2A26] mb-3" style="font-family: 'Playfair Display', serif;">Créer un compte BiblioNova</h1>
        <p class="text-gray-600 font-sans">
          Ou
          <router-link
            to="/auth/login"
            class="text-[#C5A46D] font-medium hover:underline transition-all"
          >
            connectez-vous à votre compte existant
          </router-link>
        </p>
      </div>

      <!-- Formulaire -->
      <div class="bg-white rounded-2xl shadow-xl shadow-black/5 border border-[#E2D7C6] p-8">
        <form @submit.prevent="handleRegister" class="space-y-6">
          <!-- Messages -->
          <div v-if="errorMessage" class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
            <p class="text-red-700 text-sm">{{ errorMessage }}</p>
          </div>
          
          <div v-if="successMessage" class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
            <p class="text-green-700 text-sm">{{ successMessage }}</p>
          </div>

          <!-- Prénom et Nom -->
          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-2">
              <label for="first_name" class="block text-[#2D2A26] font-medium font-sans">Prénom</label>
              <input
                id="first_name"
                v-model="form.first_name"
                type="text"
                required
                class="w-full px-4 py-3 rounded-lg border border-[#E2D7C6] bg-white text-[#2D2A26] focus:outline-none focus:border-[#C5A46D] focus:ring-2 focus:ring-[#C5A46D]/20 transition-all"
                placeholder="Prénom"
              />
              <p v-if="errors.first_name" class="text-red-600 text-sm">{{ errors.first_name[0] }}</p>
            </div>

            <div class="space-y-2">
              <label for="last_name" class="block text-[#2D2A26] font-medium font-sans">Nom</label>
              <input
                id="last_name"
                v-model="form.last_name"
                type="text"
                required
                class="w-full px-4 py-3 rounded-lg border border-[#E2D7C6] bg-white text-[#2D2A26] focus:outline-none focus:border-[#C5A46D] focus:ring-2 focus:ring-[#C5A46D]/20 transition-all"
                placeholder="Nom"
              />
              <p v-if="errors.last_name" class="text-red-600 text-sm">{{ errors.last_name[0] }}</p>
            </div>
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
            <p v-if="errors.email" class="text-red-600 text-sm">{{ errors.email[0] }}</p>
          </div>

          <!-- Téléphone -->
          <div class="space-y-2">
            <label for="phone" class="block text-[#2D2A26] font-medium font-sans">Téléphone (optionnel)</label>
            <input
              id="phone"
              v-model="form.phone"
              type="tel"
              class="w-full px-4 py-3 rounded-lg border border-[#E2D7C6] bg-white text-[#2D2A26] focus:outline-none focus:border-[#C5A46D] focus:ring-2 focus:ring-[#C5A46D]/20 transition-all"
              placeholder="+33 1 23 45 67 89"
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
                placeholder="Au moins 8 caractères"
              />
              <button 
                type="button"
                @click="showPassword = !showPassword"
                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-[#5C4033] transition-colors"
              >
                <i :class="showPassword ? 'pi pi-eye-slash' : 'pi pi-eye'"></i>
              </button>
            </div>
            <p v-if="errors.password" class="text-red-600 text-sm">{{ errors.password[0] }}</p>
          </div>

          <!-- Confirmer le mot de passe -->
          <div class="space-y-2">
            <label for="password_confirmation" class="block text-[#2D2A26] font-medium font-sans">Confirmer le mot de passe</label>
            <div class="relative">
              <input
                id="password_confirmation"
                v-model="form.password_confirmation"
                :type="showConfirmPassword ? 'text' : 'password'"
                required
                class="w-full px-4 py-3 pr-12 rounded-lg border border-[#E2D7C6] bg-white text-[#2D2A26] focus:outline-none focus:border-[#C5A46D] focus:ring-2 focus:ring-[#C5A46D]/20 transition-all"
                placeholder="Confirmer le mot de passe"
              />
              <button 
                type="button"
                @click="showConfirmPassword = !showConfirmPassword"
                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-[#5C4033] transition-colors"
              >
                <i :class="showConfirmPassword ? 'pi pi-eye-slash' : 'pi pi-eye'"></i>
              </button>
            </div>
          </div>

          <!-- Conditions d'utilisation -->
          <div class="flex items-start gap-3">
            <input
              id="terms"
              v-model="acceptTerms"
              type="checkbox"
              required
              class="mt-1 w-4 h-4 text-[#5C4033] border-[#E2D7C6] rounded focus:ring-[#C5A46D]"
            />
            <label for="terms" class="text-gray-600 text-sm font-sans">
              J'accepte les
              <a href="#" class="text-[#C5A46D] hover:underline">conditions d'utilisation</a>
              et la
              <a href="#" class="text-[#C5A46D] hover:underline">politique de confidentialité</a>
            </label>
          </div>

          <!-- Bouton d'inscription -->
          <button
            type="submit"
            :disabled="isLoading || !acceptTerms"
            class="w-full bg-[#5C4033] text-white font-medium py-3 px-4 rounded-lg hover:bg-[#6D4C3D] transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
          >
            <span v-if="isLoading" class="pi pi-spin pi-spinner"></span>
            <span v-else>{{ isLoading ? 'Création...' : 'Créer mon compte' }}</span>
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const showPassword = ref(false)
const showConfirmPassword = ref(false)

// Form data
const form = reactive({
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  password: '',
  password_confirmation: ''
})

// State
const isLoading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')
const errors = ref({})
const acceptTerms = ref(false)

// Handle registration
const handleRegister = async () => {
  try {
    isLoading.value = true
    errorMessage.value = ''
    successMessage.value = ''
    errors.value = {}

    const result = await authStore.register(form)

    if (result.success) {
      // Redirect to login with success message
      router.push({
        name: 'Login',
        query: {
          message: 'Inscription réussie ! Vous pouvez maintenant vous connecter.'
        }
      })
    }
  } catch (error) {
    errorMessage.value = error.message
    errors.value = error.errors || {}
  } finally {
    isLoading.value = false
  }
}
</script>
