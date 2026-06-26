<template>
  <DashboardLayout>
    <div class="space-y-6">
      <!-- En-tête -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Mon profil</h1>
        <p class="mt-2 text-gray-600">Gérer vos informations personnelles et paramètres de compte</p>
      </div>

      <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Informations du profil -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Informations personnelles -->
          <div class="bg-white shadow-sm rounded-lg border">
            <div class="px-4 py-5 sm:p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Informations personnelles</h3>
              
              <form @submit.prevent="updateProfile" class="space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                  <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">
                      Prénom
                    </label>
                    <input
                      id="first_name"
                      v-model="profileForm.first_name"
                      type="text"
                      required
                      class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    />
                  </div>

                  <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">
                      Nom
                    </label>
                    <input
                      id="last_name"
                      v-model="profileForm.last_name"
                      type="text"
                      required
                      class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    />
                  </div>
                </div>

                <div>
                  <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                    Adresse email
                  </label>
                  <input
                    id="email"
                    v-model="profileForm.email"
                    type="email"
                    required
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  />
                </div>

                <div>
                  <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                    Téléphone
                  </label>
                  <input
                    id="phone"
                    v-model="profileForm.phone"
                    type="tel"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  />
                </div>

                <div class="pt-4">
                  <button
                    type="submit"
                    :disabled="profileLoading"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
                  >
                    <Icon v-if="profileLoading" icon="mdi:loading" class="animate-spin h-4 w-4 mr-2" />
                    <Icon v-else icon="mdi:content-save" class="h-4 w-4 mr-2" />
                    {{ profileLoading ? 'Enregistrement...' : 'Enregistrer' }}
                  </button>
                </div>
              </form>
            </div>
          </div>

          <!-- Changement de mot de passe -->
          <div class="bg-white shadow-sm rounded-lg border">
            <div class="px-4 py-5 sm:p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Changer le mot de passe</h3>
              
              <form @submit.prevent="changePassword" class="space-y-4">
                <div>
                  <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">
                    Mot de passe actuel
                  </label>
                  <input
                    id="current_password"
                    v-model="passwordForm.current_password"
                    type="password"
                    required
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  />
                </div>

                <div>
                  <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">
                    Nouveau mot de passe
                  </label>
                  <input
                    id="new_password"
                    v-model="passwordForm.password"
                    type="password"
                    required
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  />
                </div>

                <div>
                  <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">
                    Confirmer le mot de passe
                  </label>
                  <input
                    id="confirm_password"
                    v-model="passwordForm.password_confirmation"
                    type="password"
                    required
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  />
                </div>

                <!-- Erreurs -->
                <div v-if="passwordError" class="bg-red-50 border border-red-200 rounded-md p-3">
                  <div class="flex">
                    <Icon icon="mdi:alert-circle" class="h-5 w-5 text-red-400" />
                    <div class="ml-3">
                      <p class="text-sm text-red-700">{{ passwordError }}</p>
                    </div>
                  </div>
                </div>

                <!-- Succès -->
                <div v-if="passwordSuccess" class="bg-green-50 border border-green-200 rounded-md p-3">
                  <div class="flex">
                    <Icon icon="mdi:check-circle" class="h-5 w-5 text-green-400" />
                    <div class="ml-3">
                      <p class="text-sm text-green-700">{{ passwordSuccess }}</p>
                    </div>
                  </div>
                </div>

                <div class="pt-4">
                  <button
                    type="submit"
                    :disabled="passwordLoading"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50"
                  >
                    <Icon v-if="passwordLoading" icon="mdi:loading" class="animate-spin h-4 w-4 mr-2" />
                    <Icon v-else icon="mdi:lock-reset" class="h-4 w-4 mr-2" />
                    {{ passwordLoading ? 'Modification...' : 'Changer le mot de passe' }}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- Sidebar avec info compte -->
        <div class="space-y-6">
          <!-- Avatar et infos -->
          <div class="bg-white shadow-sm rounded-lg border">
            <div class="px-4 py-5 sm:p-6">
              <div class="text-center">
                <div class="mx-auto h-20 w-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center mb-4">
                  <span class="text-xl font-bold text-white">
                    {{ authStore.userFullName.split(' ').map(n => n[0]).join('').toUpperCase() }}
                  </span>
                </div>
                <h3 class="text-lg font-medium text-gray-900">{{ authStore.userFullName }}</h3>
                <p class="text-sm text-gray-500">{{ authStore.user?.email }}</p>
                <div class="mt-3">
                  <span :class="getRoleBadgeColor(authStore.userRole)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                    {{ getRoleDisplayName(authStore.userRole) }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Statistiques du compte -->
          <div class="bg-white shadow-sm rounded-lg border">
            <div class="px-4 py-5 sm:p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Mon activité</h3>
              <div class="space-y-3">
                <div class="flex justify-between">
                  <span class="text-sm text-gray-500">Demandes créées</span>
                  <span class="text-sm font-medium text-gray-900">8</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-sm text-gray-500">Documents publiés</span>
                  <span class="text-sm font-medium text-gray-900">5</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-sm text-gray-500">Téléchargements</span>
                  <span class="text-sm font-medium text-gray-900">127</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-sm text-gray-500">Membre depuis</span>
                  <span class="text-sm font-medium text-gray-900">Mars 2024</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Actions du compte -->
          <div class="bg-white shadow-sm rounded-lg border">
            <div class="px-4 py-5 sm:p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Actions du compte</h3>
              <div class="space-y-3">
                <button class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md flex items-center">
                  <Icon icon="mdi:download" class="h-4 w-4 mr-2" />
                  Exporter mes données
                </button>
                <button class="w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-md flex items-center">
                  <Icon icon="mdi:account-remove" class="h-4 w-4 mr-2" />
                  Supprimer mon compte
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { Icon } from '@iconify/vue'
import DashboardLayout from '@/layouts/DashboardLayout.vue'

const authStore = useAuthStore()

const profileLoading = ref(false)
const passwordLoading = ref(false)
const passwordError = ref('')
const passwordSuccess = ref('')

const profileForm = reactive({
  first_name: '',
  last_name: '',
  email: '',
  phone: ''
})

const passwordForm = reactive({
  current_password: '',
  password: '',
  password_confirmation: ''
})

onMounted(() => {
  // Pré-remplir le formulaire avec les données utilisateur
  if (authStore.user) {
    profileForm.first_name = authStore.user.first_name
    profileForm.last_name = authStore.user.last_name
    profileForm.email = authStore.user.email
    profileForm.phone = authStore.user.phone || ''
  }
})

const updateProfile = async () => {
  try {
    profileLoading.value = true
    // TODO: Appel API pour mettre à jour le profil
    // await authStore.updateProfile(profileForm)
    
    // Simulation
    await new Promise(resolve => setTimeout(resolve, 1000))
    
    console.log('Profil mis à jour:', profileForm)
  } catch (error) {
    console.error('Erreur mise à jour profil:', error)
  } finally {
    profileLoading.value = false
  }
}

const changePassword = async () => {
  try {
    passwordLoading.value = true
    passwordError.value = ''
    passwordSuccess.value = ''

    const result = await authStore.changePassword(passwordForm)
    
    if (result.success) {
      passwordSuccess.value = result.message
      // Réinitialiser le formulaire
      passwordForm.current_password = ''
      passwordForm.password = ''
      passwordForm.password_confirmation = ''
    }
  } catch (error) {
    console.error('Erreur changement mot de passe:', error)
    passwordError.value = error.message || 'Une erreur est survenue'
  } finally {
    passwordLoading.value = false
  }
}

const getRoleBadgeColor = (role) => {
  const colors = {
    admin: 'bg-red-100 text-red-800',
    responsable_rh: 'bg-orange-100 text-orange-800',
    responsable_validation: 'bg-blue-100 text-blue-800',
    bibliothecaire: 'bg-purple-100 text-purple-800',
    utilisateur: 'bg-green-100 text-green-800'
  }
  return colors[role] || 'bg-gray-100 text-gray-800'
}

const getRoleDisplayName = (role) => {
  const roleNames = {
    admin: 'Administrateur',
    responsable_rh: 'Responsable RH',
    responsable_validation: 'Responsable Validation',
    bibliothecaire: 'Bibliothécaire',
    utilisateur: 'Utilisateur'
  }
  return roleNames[role] || 'Utilisateur'
}
</script>