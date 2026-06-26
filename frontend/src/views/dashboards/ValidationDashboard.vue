<template>
  <DashboardLayout>
    <div class="space-y-6">
      <!-- En-tête -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Validation des demandes</h1>
        <p class="mt-2 text-gray-600">Gestion et validation des demandes de dépôt documentaire</p>
      </div>

      <!-- Statistiques de validation -->
      <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg border">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <Icon icon="mdi:clock-outline" class="h-8 w-8 text-yellow-500" />
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">En attente</dt>
                  <dd class="text-2xl font-semibold text-gray-900">{{ validationStats.pending }}</dd>
                </dl>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
              <span class="font-medium text-orange-600">Urgent</span>
              <span class="text-gray-500">à traiter</span>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg border">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <Icon icon="mdi:account-check" class="h-8 w-8 text-blue-500" />
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Assignées à moi</dt>
                  <dd class="text-2xl font-semibold text-gray-900">{{ validationStats.assigned }}</dd>
                </dl>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
              <span class="text-gray-500">Ma charge de travail</span>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg border">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <Icon icon="mdi:check-circle" class="h-8 w-8 text-green-500" />
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Validées ce mois</dt>
                  <dd class="text-2xl font-semibold text-gray-900">{{ validationStats.approved }}</dd>
                </dl>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
              <span class="font-medium text-green-600">+15%</span>
              <span class="text-gray-500">vs mois dernier</span>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg border">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <Icon icon="mdi:timer-sand" class="h-8 w-8 text-purple-500" />
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Temps moyen</dt>
                  <dd class="text-2xl font-semibold text-gray-900">2.3j</dd>
                </dl>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
              <span class="font-medium text-green-600">-0.5j</span>
              <span class="text-gray-500">amélioration</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Actions rapides -->
      <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <router-link 
          to="/validation/pending"
          class="relative group bg-gradient-to-br from-yellow-50 to-yellow-100 p-6 rounded-xl border border-yellow-200 hover:border-yellow-300 transition-all transform hover:scale-105"
        >
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="h-12 w-12 bg-yellow-500 rounded-xl flex items-center justify-center">
                  <Icon icon="mdi:file-document-alert" class="h-6 w-6 text-white" />
                </div>
              </div>
              <div class="ml-4">
                <h3 class="text-lg font-medium text-yellow-900">Demandes en attente</h3>
                <p class="text-sm text-yellow-700">{{ validationStats.pending }} à traiter</p>
              </div>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-200 text-yellow-800">
              Urgent
            </span>
          </div>
        </router-link>

        <router-link 
          to="/validation/assigned"
          class="relative group bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-xl border border-blue-200 hover:border-blue-300 transition-all transform hover:scale-105"
        >
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="h-12 w-12 bg-blue-500 rounded-xl flex items-center justify-center">
                <Icon icon="mdi:file-document-check" class="h-6 w-6 text-white" />
              </div>
            </div>
            <div class="ml-4">
              <h3 class="text-lg font-medium text-blue-900">Mes demandes</h3>
              <p class="text-sm text-blue-700">{{ validationStats.assigned }} assignées</p>
            </div>
          </div>
        </router-link>

        <router-link 
          to="/validation/history"
          class="relative group bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-xl border border-green-200 hover:border-green-300 transition-all transform hover:scale-105"
        >
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="h-12 w-12 bg-green-500 rounded-xl flex items-center justify-center">
                <Icon icon="mdi:history" class="h-6 w-6 text-white" />
              </div>
            </div>
            <div class="ml-4">
              <h3 class="text-lg font-medium text-green-900">Historique</h3>
              <p class="text-sm text-green-700">Demandes traitées</p>
            </div>
          </div>
        </router-link>
      </div>

      <!-- Demandes prioritaires -->
      <div class="bg-white shadow-sm rounded-lg border">
        <div class="px-4 py-5 sm:p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Demandes prioritaires</h3>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
              {{ priorityRequests.length }} demandes urgentes
            </span>
          </div>
          
          <div class="space-y-4">
            <div v-for="request in priorityRequests" :key="request.id" class="border border-orange-200 rounded-lg p-4 bg-orange-50">
              <div class="flex items-center justify-between">
                <div class="flex items-start space-x-4">
                  <div class="flex-shrink-0">
                    <div class="h-10 w-10 bg-orange-500 rounded-full flex items-center justify-center">
                      <Icon icon="mdi:alert" class="h-5 w-5 text-white" />
                    </div>
                  </div>
                  <div class="min-w-0 flex-1">
                    <h4 class="text-sm font-medium text-gray-900">{{ request.title }}</h4>
                    <p class="text-sm text-gray-600 mt-1">{{ request.description }}</p>
                    <div class="flex items-center mt-2 space-x-4">
                      <span class="text-xs text-gray-500">
                        <Icon icon="mdi:account" class="inline h-4 w-4 mr-1" />
                        {{ request.applicant }}
                      </span>
                      <span class="text-xs text-gray-500">
                        <Icon icon="mdi:clock" class="inline h-4 w-4 mr-1" />
                        {{ request.submitted_at }}
                      </span>
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        {{ request.priority }}
                      </span>
                    </div>
                  </div>
                </div>
                <div class="flex-shrink-0 flex space-x-2">
                  <button class="inline-flex items-center px-3 py-2 border border-green-300 shadow-sm text-sm leading-4 font-medium rounded-md text-green-700 bg-white hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <Icon icon="mdi:check" class="h-4 w-4 mr-1" />
                    Approuver
                  </button>
                  <button class="inline-flex items-center px-3 py-2 border border-red-300 shadow-sm text-sm leading-4 font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <Icon icon="mdi:close" class="h-4 w-4 mr-1" />
                    Rejeter
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Activité récente de validation -->
      <div class="bg-white shadow-sm rounded-lg border">
        <div class="px-4 py-5 sm:p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Mes validations récentes</h3>
          <div class="flow-root">
            <ul class="-mb-8">
              <li v-for="(activity, index) in recentValidations" :key="index">
                <div class="relative pb-8">
                  <span v-if="index !== recentValidations.length - 1" class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200"></span>
                  <div class="relative flex items-start space-x-3">
                    <div class="relative">
                      <div :class="activity.iconBg" class="h-10 w-10 rounded-full flex items-center justify-center ring-8 ring-white">
                        <Icon :icon="activity.icon" class="h-5 w-5 text-white" />
                      </div>
                    </div>
                    <div class="min-w-0 flex-1">
                      <div>
                        <div class="text-sm">
                          <span class="font-medium text-gray-900">{{ activity.title }}</span>
                        </div>
                        <p class="mt-0.5 text-sm text-gray-500">{{ activity.action }}</p>
                      </div>
                      <div class="mt-2 text-sm text-gray-700">
                        <p>{{ activity.description }}</p>
                      </div>
                      <div class="mt-2 text-sm text-gray-500">
                        {{ activity.time }}
                      </div>
                    </div>
                  </div>
                </div>
              </li>
            </ul>
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

const validationStats = ref({
  pending: 8,
  assigned: 5,
  approved: 42,
  avgTime: 2.3
})

const priorityRequests = ref([
  {
    id: 1,
    title: 'Mémoire de Master - IA et Machine Learning',
    description: 'Travail de recherche sur les algorithmes d\'apprentissage profond',
    applicant: 'Marie Dubois',
    submitted_at: 'Il y a 3 heures',
    priority: 'Urgent'
  },
  {
    id: 2,
    title: 'Thèse de Doctorat - Blockchain et Cryptographie',
    description: 'Étude sur les applications décentralisées et la sécurité',
    applicant: 'Pierre Martin',
    submitted_at: 'Il y a 5 heures',
    priority: 'Élevée'
  }
])

const recentValidations = ref([
  {
    title: 'Rapport de stage validé',
    action: 'Demande approuvée avec succès',
    description: 'Stage en développement web - React et Node.js',
    time: 'Il y a 1 heure',
    icon: 'mdi:check-circle',
    iconBg: 'bg-green-500'
  },
  {
    title: 'Mémoire rejeté',
    action: 'Demande rejetée - modifications nécessaires',
    description: 'Format non conforme aux exigences institutionnelles',
    time: 'Il y a 2 heures',
    icon: 'mdi:close-circle',
    iconBg: 'bg-red-500'
  },
  {
    title: 'Article validé',
    action: 'Demande approuvée après révision',
    description: 'Recherche en intelligence artificielle appliquée',
    time: 'Il y a 4 heures',
    icon: 'mdi:check-circle',
    iconBg: 'bg-green-500'
  }
])
</script>