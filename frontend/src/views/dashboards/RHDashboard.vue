<template>
  <DashboardLayout>
    <div class="space-y-6">
      <!-- En-tête -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Tableau de bord RH</h1>
        <p class="mt-2 text-gray-600">Gestion des ressources humaines et utilisateurs</p>
      </div>

      <!-- Statistiques RH -->
      <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg border">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <Icon icon="mdi:account-group" class="h-8 w-8 text-blue-500" />
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Total Utilisateurs</dt>
                  <dd class="text-2xl font-semibold text-gray-900">247</dd>
                </dl>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
              <span class="font-medium text-green-600">+8</span>
              <span class="text-gray-500">ce mois</span>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg border">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <Icon icon="mdi:account-plus" class="h-8 w-8 text-green-500" />
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Nouveaux</dt>
                  <dd class="text-2xl font-semibold text-gray-900">12</dd>
                </dl>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
              <span class="text-gray-500">Cette semaine</span>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg border">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <Icon icon="mdi:account-check" class="h-8 w-8 text-purple-500" />
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Actifs</dt>
                  <dd class="text-2xl font-semibold text-gray-900">231</dd>
                </dl>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
              <span class="font-medium text-green-600">94%</span>
              <span class="text-gray-500">du total</span>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg border">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <Icon icon="mdi:shield-account" class="h-8 w-8 text-orange-500" />
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Rôles définis</dt>
                  <dd class="text-2xl font-semibold text-gray-900">5</dd>
                </dl>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
              <span class="text-gray-500">Système</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Actions rapides RH -->
      <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <router-link 
          to="/rh/users/create"
          class="relative group bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-xl border border-blue-200 hover:border-blue-300 transition-all transform hover:scale-105"
        >
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="h-12 w-12 bg-blue-500 rounded-xl flex items-center justify-center">
                <Icon icon="mdi:account-plus" class="h-6 w-6 text-white" />
              </div>
            </div>
            <div class="ml-4">
              <h3 class="text-lg font-medium text-blue-900">Créer utilisateur</h3>
              <p class="text-sm text-blue-700">Ajouter un nouveau membre</p>
            </div>
          </div>
        </router-link>

        <router-link 
          to="/rh/users"
          class="relative group bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-xl border border-green-200 hover:border-green-300 transition-all transform hover:scale-105"
        >
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="h-12 w-12 bg-green-500 rounded-xl flex items-center justify-center">
                <Icon icon="mdi:account-group" class="h-6 w-6 text-white" />
              </div>
            </div>
            <div class="ml-4">
              <h3 class="text-lg font-medium text-green-900">Gérer utilisateurs</h3>
              <p class="text-sm text-green-700">Liste et modification</p>
            </div>
          </div>
        </router-link>

        <router-link 
          to="/rh/reports"
          class="relative group bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-xl border border-purple-200 hover:border-purple-300 transition-all transform hover:scale-105"
        >
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="h-12 w-12 bg-purple-500 rounded-xl flex items-center justify-center">
                <Icon icon="mdi:chart-line" class="h-6 w-6 text-white" />
              </div>
            </div>
            <div class="ml-4">
              <h3 class="text-lg font-medium text-purple-900">Rapports RH</h3>
              <p class="text-sm text-purple-700">Statistiques détaillées</p>
            </div>
          </div>
        </router-link>
      </div>

      <!-- Utilisateurs récents -->
      <div class="bg-white shadow-sm rounded-lg border">
        <div class="px-4 py-5 sm:p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Utilisateurs récents</h3>
            <router-link 
              to="/rh/users" 
              class="text-sm font-medium text-blue-600 hover:text-blue-500"
            >
              Voir tous →
            </router-link>
          </div>
          
          <div class="overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Utilisateur
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Rôle
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Statut
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Inscription
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="user in recentUsers" :key="user.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="h-10 w-10 flex-shrink-0">
                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                          <span class="text-sm font-medium text-white">
                            {{ user.initials }}
                          </span>
                        </div>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                        <div class="text-sm text-gray-500">{{ user.email }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getRoleBadgeColor(user.role)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                      {{ getRoleText(user.role) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getStatusBadgeColor(user.status)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                      {{ user.status === 'active' ? 'Actif' : 'Inactif' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ user.created_at }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Icon } from '@iconify/vue'
import DashboardLayout from '@/layouts/DashboardLayout.vue'

const recentUsers = ref([
  {
    id: 1,
    name: 'Marie Dubois',
    email: 'marie.dubois@email.com',
    role: 'utilisateur',
    status: 'active',
    initials: 'MD',
    created_at: 'Il y a 2 jours'
  },
  {
    id: 2,
    name: 'Jean Martin',
    email: 'jean.martin@email.com',
    role: 'bibliothecaire',
    status: 'active',
    initials: 'JM',
    created_at: 'Il y a 3 jours'
  },
  {
    id: 3,
    name: 'Sophie Laurent',
    email: 'sophie.laurent@email.com',
    role: 'responsable_validation',
    status: 'active',
    initials: 'SL',
    created_at: 'Il y a 1 semaine'
  }
])

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

const getRoleText = (role) => {
  const texts = {
    admin: 'Admin',
    responsable_rh: 'RH',
    responsable_validation: 'Validation',
    bibliothecaire: 'Bibliothécaire',
    utilisateur: 'Utilisateur'
  }
  return texts[role] || role
}

const getStatusBadgeColor = (status) => {
  return status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
}
</script>