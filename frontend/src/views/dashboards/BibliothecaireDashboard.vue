<template>
  <DashboardLayout>
    <div class="space-y-6">
      <!-- En-tête -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Gestion de la bibliothèque</h1>
        <p class="mt-2 text-gray-600">Catalogue, publications et gestion documentaire</p>
      </div>

      <!-- Statistiques bibliothèque -->
      <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg border">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <Icon icon="mdi:book-open-variant" class="h-8 w-8 text-purple-500" />
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Références totales</dt>
                  <dd class="text-2xl font-semibold text-gray-900">{{ libraryStats.totalReferences }}</dd>
                </dl>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
              <span class="font-medium text-green-600">+{{ libraryStats.newThisMonth }}</span>
              <span class="text-gray-500">ce mois</span>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg border">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <Icon icon="mdi:publish" class="h-8 w-8 text-green-500" />
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">À publier</dt>
                  <dd class="text-2xl font-semibold text-gray-900">{{ libraryStats.toPublish }}</dd>
                </dl>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
              <span class="text-gray-500">En attente</span>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg border">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <Icon icon="mdi:download" class="h-8 w-8 text-blue-500" />
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Téléchargements</dt>
                  <dd class="text-2xl font-semibold text-gray-900">{{ libraryStats.downloads }}</dd>
                </dl>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
              <span class="font-medium text-green-600">+{{ libraryStats.downloadsToday }}</span>
              <span class="text-gray-500">aujourd'hui</span>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg border">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <Icon icon="mdi:tag-multiple" class="h-8 w-8 text-orange-500" />
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Catégories</dt>
                  <dd class="text-2xl font-semibold text-gray-900">{{ libraryStats.categories }}</dd>
                </dl>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
              <span class="text-gray-500">Actives</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Actions rapides bibliothécaire -->
      <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <router-link 
          to="/bibliothecaire/publications"
          class="relative group bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-xl border border-green-200 hover:border-green-300 transition-all transform hover:scale-105"
        >
          <div class="flex flex-col items-center text-center">
            <div class="h-12 w-12 bg-green-500 rounded-xl flex items-center justify-center mb-3">
              <Icon icon="mdi:publish" class="h-6 w-6 text-white" />
            </div>
            <h3 class="text-lg font-medium text-green-900">Publications</h3>
            <p class="text-sm text-green-700">{{ libraryStats.toPublish }} en attente</p>
          </div>
        </router-link>

        <router-link 
          to="/bibliothecaire/references"
          class="relative group bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-xl border border-purple-200 hover:border-purple-300 transition-all transform hover:scale-105"
        >
          <div class="flex flex-col items-center text-center">
            <div class="h-12 w-12 bg-purple-500 rounded-xl flex items-center justify-center mb-3">
              <Icon icon="mdi:book-plus" class="h-6 w-6 text-white" />
            </div>
            <h3 class="text-lg font-medium text-purple-900">Références</h3>
            <p class="text-sm text-purple-700">Gérer le catalogue</p>
          </div>
        </router-link>

        <router-link 
          to="/bibliothecaire/categories"
          class="relative group bg-gradient-to-br from-orange-50 to-orange-100 p-6 rounded-xl border border-orange-200 hover:border-orange-300 transition-all transform hover:scale-105"
        >
          <div class="flex flex-col items-center text-center">
            <div class="h-12 w-12 bg-orange-500 rounded-xl flex items-center justify-center mb-3">
              <Icon icon="mdi:tag-multiple" class="h-6 w-6 text-white" />
            </div>
            <h3 class="text-lg font-medium text-orange-900">Catégories</h3>
            <p class="text-sm text-orange-700">{{ libraryStats.categories }} actives</p>
          </div>
        </router-link>

        <router-link 
          to="/bibliothecaire/archives"
          class="relative group bg-gradient-to-br from-gray-50 to-gray-100 p-6 rounded-xl border border-gray-200 hover:border-gray-300 transition-all transform hover:scale-105"
        >
          <div class="flex flex-col items-center text-center">
            <div class="h-12 w-12 bg-gray-500 rounded-xl flex items-center justify-center mb-3">
              <Icon icon="mdi:archive" class="h-6 w-6 text-white" />
            </div>
            <h3 class="text-lg font-medium text-gray-900">Archives</h3>
            <p class="text-sm text-gray-700">Gestion archivage</p>
          </div>
        </router-link>
      </div>

      <!-- Références récentes à publier -->
      <div class="bg-white shadow-sm rounded-lg border">
        <div class="px-4 py-5 sm:p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Prêtes à publier</h3>
            <router-link 
              to="/bibliothecaire/publications" 
              class="text-sm font-medium text-blue-600 hover:text-blue-500"
            >
              Voir toutes →
            </router-link>
          </div>
          
          <div class="space-y-4">
            <div v-for="reference in pendingPublications" :key="reference.id" class="border border-green-200 rounded-lg p-4 bg-green-50">
              <div class="flex items-center justify-between">
                <div class="flex items-start space-x-4">
                  <div class="flex-shrink-0">
                    <div class="h-10 w-10 bg-green-500 rounded-full flex items-center justify-center">
                      <Icon icon="mdi:book-check" class="h-5 w-5 text-white" />
                    </div>
                  </div>
                  <div class="min-w-0 flex-1">
                    <h4 class="text-sm font-medium text-gray-900">{{ reference.title }}</h4>
                    <p class="text-sm text-gray-600 mt-1">{{ reference.description }}</p>
                    <div class="flex items-center mt-2 space-x-4">
                      <span class="text-xs text-gray-500">
                        <Icon icon="mdi:account-edit" class="inline h-4 w-4 mr-1" />
                        {{ reference.author }}
                      </span>
                      <span class="text-xs text-gray-500">
                        <Icon icon="mdi:tag" class="inline h-4 w-4 mr-1" />
                        {{ reference.category }}
                      </span>
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        {{ reference.type }}
                      </span>
                    </div>
                  </div>
                </div>
                <div class="flex-shrink-0 flex space-x-2">
                  <button class="inline-flex items-center px-3 py-2 border border-transparent shadow-sm text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <Icon icon="mdi:publish" class="h-4 w-4 mr-1" />
                    Publier
                  </button>
                  <button class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <Icon icon="mdi:pencil" class="h-4 w-4 mr-1" />
                    Modifier
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Statistiques par catégorie -->
      <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="bg-white shadow-sm rounded-lg border">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Répartition par type</h3>
            <div class="space-y-4">
              <div v-for="type in documentTypes" :key="type.name" class="flex items-center justify-between">
                <div class="flex items-center">
                  <div :class="type.color" class="h-3 w-3 rounded-full mr-3"></div>
                  <span class="text-sm font-medium text-gray-900">{{ type.name }}</span>
                </div>
                <div class="flex items-center">
                  <span class="text-sm text-gray-500 mr-2">{{ type.count }}</span>
                  <div class="w-20 bg-gray-200 rounded-full h-2">
                    <div 
                      :class="type.color" 
                      class="h-2 rounded-full"
                      :style="{ width: type.percentage + '%' }"
                    ></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Activité récente -->
        <div class="bg-white shadow-sm rounded-lg border">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Activité récente</h3>
            <div class="flow-root">
              <ul class="-mb-8">
                <li v-for="(activity, index) in recentActivity" :key="index">
                  <div class="relative pb-8">
                    <span v-if="index !== recentActivity.length - 1" class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200"></span>
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
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Icon } from '@iconify/vue'
import DashboardLayout from '@/layouts/DashboardLayout.vue'

const libraryStats = ref({
  totalReferences: 1247,
  newThisMonth: 23,
  toPublish: 7,
  downloads: 8429,
  downloadsToday: 47,
  categories: 12
})

const pendingPublications = ref([
  {
    id: 1,
    title: 'Guide de développement React avancé',
    description: 'Techniques avancées et bonnes pratiques pour React 18+',
    author: 'Dr. Martin Dubois',
    category: 'Informatique',
    type: 'Guide'
  },
  {
    id: 2,
    title: 'Méthodologie de recherche quantitative',
    description: 'Approches statistiques pour la recherche académique',
    author: 'Prof. Sophie Laurent',
    category: 'Méthodologie',
    type: 'Manuel'
  }
])

const documentTypes = ref([
  { name: 'Mémoires', count: 456, percentage: 40, color: 'bg-blue-400' },
  { name: 'Thèses', count: 234, percentage: 20, color: 'bg-green-400' },
  { name: 'Articles', count: 321, percentage: 25, color: 'bg-purple-400' },
  { name: 'Rapports', count: 123, percentage: 10, color: 'bg-yellow-400' },
  { name: 'Guides', count: 113, percentage: 5, color: 'bg-red-400' }
])

const recentActivity = ref([
  {
    title: 'Référence publiée',
    action: 'Guide de programmation Python avancée',
    time: 'Il y a 30 minutes',
    icon: 'mdi:publish',
    iconBg: 'bg-green-500'
  },
  {
    title: 'Catégorie créée',
    action: 'Intelligence Artificielle - Sous-catégorie ajoutée',
    time: 'Il y a 1 heure',
    icon: 'mdi:tag-plus',
    iconBg: 'bg-blue-500'
  },
  {
    title: 'Archive créée',
    action: 'Mémoires 2020-2021 archivés automatiquement',
    time: 'Il y a 2 heures',
    icon: 'mdi:archive',
    iconBg: 'bg-gray-500'
  }
])
</script>