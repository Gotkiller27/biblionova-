<template>
  <DashboardLayout>
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <!-- Total Users -->
      <div class="biblio-card">
        <div class="flex items-center justify-between mb-4">
          <div class="w-14 h-14 rounded-xl bg-[#5C4033]/10 flex items-center justify-center">
            <i class="pi pi-users text-2xl text-[#5C4033]"></i>
          </div>
        </div>
        <p class="text-text-secondary font-inter text-sm mb-1">Utilisateurs</p>
        <p class="text-3xl font-playfair font-semibold text-text-primary">{{ stats.total_users || 0 }}</p>
        <div class="mt-4 flex items-center text-sm font-inter text-green-600">
          <i class="pi pi-arrow-up mr-1"></i>
          <span>+{{ stats.new_users_this_month || 0 }} ce mois</span>
        </div>
      </div>

      <!-- Active Users -->
      <div class="biblio-card">
        <div class="flex items-center justify-between mb-4">
          <div class="w-14 h-14 rounded-xl bg-green-100 flex items-center justify-center">
            <i class="pi pi-user-check text-2xl text-green-700"></i>
          </div>
        </div>
        <p class="text-text-secondary font-inter text-sm mb-1">Utilisateurs Actifs</p>
        <p class="text-3xl font-playfair font-semibold text-text-primary">{{ stats.active_users || 0 }}</p>
        <div class="mt-4 flex items-center text-sm font-inter text-blue-600">
          <i class="pi pi-clock mr-1"></i>
          <span>{{ stats.recent_logins || 0 }} connexions récentes</span>
        </div>
      </div>

      <!-- Suspended Users -->
      <div class="biblio-card">
        <div class="flex items-center justify-between mb-4">
          <div class="w-14 h-14 rounded-xl bg-red-100 flex items-center justify-center">
            <i class="pi pi-user-minus text-2xl text-red-700"></i>
          </div>
        </div>
        <p class="text-text-secondary font-inter text-sm mb-1">Utilisateurs Suspendus</p>
        <p class="text-3xl font-playfair font-semibold text-text-primary">{{ stats.suspended_users || 0 }}</p>
        <div class="mt-4 flex items-center text-sm font-inter text-red-600">
          <i class="pi pi-exclamation-triangle mr-1"></i>
          <span>À surveiller</span>
        </div>
      </div>

      <!-- Total References -->
      <div class="biblio-card">
        <div class="flex items-center justify-between mb-4">
          <div class="w-14 h-14 rounded-xl bg-[#7A5C43]/10 flex items-center justify-center">
            <i class="pi pi-book text-2xl text-[#7A5C43]"></i>
          </div>
        </div>
        <p class="text-text-secondary font-inter text-sm mb-1">Références</p>
        <p class="text-3xl font-playfair font-semibold text-text-primary">{{ stats.total_references || 0 }}</p>
        <div class="mt-4 flex items-center text-sm font-inter text-green-600">
          <i class="pi pi-check mr-1"></i>
          <span>{{ stats.published_references || 0 }} publiées</span>
        </div>
      </div>

      <!-- Total Categories -->
      <div class="biblio-card">
        <div class="flex items-center justify-between mb-4">
          <div class="w-14 h-14 rounded-xl bg-purple-100 flex items-center justify-center">
            <i class="pi pi-sitemap text-2xl text-purple-700"></i>
          </div>
        </div>
        <p class="text-text-secondary font-inter text-sm mb-1">Catégories</p>
        <p class="text-3xl font-playfair font-semibold text-text-primary">{{ stats.total_categories || 0 }}</p>
        <div class="mt-4 flex items-center text-sm font-inter text-blue-600">
          <i class="pi pi-folder mr-1"></i>
          <span>{{ stats.root_categories || 0 }} racines</span>
        </div>
      </div>

      <!-- Total Authors -->
      <div class="biblio-card">
        <div class="flex items-center justify-between mb-4">
          <div class="w-14 h-14 rounded-xl bg-orange-100 flex items-center justify-center">
            <i class="pi pi-user text-2xl text-orange-700"></i>
          </div>
        </div>
        <p class="text-text-secondary font-inter text-sm mb-1">Auteurs</p>
        <p class="text-3xl font-playfair font-semibold text-text-primary">{{ stats.total_authors || 0 }}</p>
        <div class="mt-4 flex items-center text-sm font-inter text-green-600">
          <i class="pi pi-heart mr-1"></i>
          <span>{{ stats.alive_authors || 0 }} vivants</span>
        </div>
      </div>

      <!-- Total Publishers -->
      <div class="biblio-card">
        <div class="flex items-center justify-between mb-4">
          <div class="w-14 h-14 rounded-xl bg-cyan-100 flex items-center justify-center">
            <i class="pi pi-building text-2xl text-cyan-700"></i>
          </div>
        </div>
        <p class="text-text-secondary font-inter text-sm mb-1">Éditeurs</p>
        <p class="text-3xl font-playfair font-semibold text-text-primary">{{ stats.total_publishers || 0 }}</p>
        <div class="mt-4 flex items-center text-sm font-inter text-blue-600">
          <i class="pi pi-book mr-1"></i>
          <span>Maisons d'édition</span>
        </div>
      </div>

      <!-- Total Keywords -->
      <div class="biblio-card">
        <div class="flex items-center justify-between mb-4">
          <div class="w-14 h-14 rounded-xl bg-purple-100 flex items-center justify-center">
            <i class="pi pi-tags text-2xl text-purple-700"></i>
          </div>
        </div>
        <p class="text-text-secondary font-inter text-sm mb-1">Mots-clés</p>
        <p class="text-3xl font-playfair font-semibold text-text-primary">{{ stats.total_keywords || 0 }}</p>
        <div class="mt-4 flex items-center text-sm font-inter text-purple-600">
          <i class="pi pi-hashtag mr-1"></i>
          <span>Tags disponibles</span>
        </div>
      </div>
    </div>

    <!-- Quick Actions & Content Management -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
      <!-- Gestion Système -->
      <div class="biblio-card">
        <h3 class="text-xl font-playfair font-semibold text-text-primary mb-6">Gestion Système</h3>
        <div class="space-y-4">
          <router-link
            to="/admin/users"
            class="flex items-center gap-4 p-4 rounded-xl border border-paper-border hover:border-[#C5A46D] transition-all"
          >
            <div class="w-12 h-12 rounded-lg bg-[#5C4033]/10 flex items-center justify-center">
              <i class="pi pi-users text-lg text-[#5C4033]"></i>
            </div>
            <div class="flex-1">
              <p class="font-inter font-medium text-text-primary">Gestion des utilisateurs</p>
              <p class="text-text-secondary font-inter text-sm">Créer, modifier, supprimer des comptes</p>
            </div>
            <i class="pi pi-chevron-right text-text-secondary"></i>
          </router-link>

          <router-link
            to="/admin/roles"
            class="flex items-center gap-4 p-4 rounded-xl border border-paper-border hover:border-[#C5A46D] transition-all"
          >
            <div class="w-12 h-12 rounded-lg bg-[#7A5C43]/10 flex items-center justify-center">
              <i class="pi pi-shield text-lg text-[#7A5C43]"></i>
            </div>
            <div class="flex-1">
              <p class="font-inter font-medium text-text-primary">Gestion des rôles</p>
              <p class="text-text-secondary font-inter text-sm">Assigner des permissions</p>
            </div>
            <i class="pi pi-chevron-right text-text-secondary"></i>
          </router-link>
        </div>
      </div>

      <!-- Gestion du Contenu -->
      <div class="biblio-card">
        <h3 class="text-xl font-playfair font-semibold text-text-primary mb-6">Gestion du Contenu</h3>
        <div class="space-y-4">
          <router-link
            to="/admin/deposit-requests"
            class="flex items-center gap-4 p-4 rounded-xl border border-paper-border hover:border-[#C5A46D] transition-all"
          >
            <div class="w-12 h-12 rounded-lg bg-[#C5A46D]/10 flex items-center justify-center">
              <i class="pi pi-file-check text-lg text-[#C5A46D]"></i>
            </div>
            <div class="flex-1">
              <p class="font-inter font-medium text-text-primary">Demandes de dépôt</p>
              <p class="text-text-secondary font-inter text-sm">Approuver et rejeter</p>
            </div>
            <i class="pi pi-chevron-right text-text-secondary"></i>
          </router-link>

          <router-link
            to="/admin/references"
            class="flex items-center gap-4 p-4 rounded-xl border border-paper-border hover:border-[#C5A46D] transition-all"
          >
            <div class="w-12 h-12 rounded-lg bg-[#2D2A26]/10 flex items-center justify-center">
              <i class="pi pi-book text-lg text-[#2D2A26]"></i>
            </div>
            <div class="flex-1">
              <p class="font-inter font-medium text-text-primary">Catalogue global</p>
              <p class="text-text-secondary font-inter text-sm">Voir toutes les références</p>
            </div>
            <i class="pi pi-chevron-right text-text-secondary"></i>
          </router-link>
        </div>
      </div>
    </div>

    <!-- Recent Activity -->
    <div class="biblio-card">
      <div class="flex items-center justify-between mb-6">
        <h3 class="text-xl font-playfair font-semibold text-text-primary">Activité Récente</h3>
        <button class="text-[#C5A46D] font-inter hover:underline">Voir tout</button>
      </div>

      <div class="space-y-4">
        <div class="flex items-center gap-4 p-4 bg-paper-ivory rounded-xl">
          <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
            <i class="pi pi-check text-xl text-green-700"></i>
          </div>
          <div class="flex-1">
            <p class="font-inter font-medium text-text-primary">Nouvelle demande approuvée</p>
            <p class="text-text-secondary font-inter text-sm">Marie Dubois - Thèse sur l'IA • il y a 2 heures</p>
          </div>
        </div>

        <div class="flex items-center gap-4 p-4 bg-paper-ivory rounded-xl">
          <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
            <i class="pi pi-user-plus text-xl text-blue-700"></i>
          </div>
          <div class="flex-1">
            <p class="font-inter font-medium text-text-primary">Nouvel utilisateur inscrit</p>
            <p class="text-text-secondary font-inter text-sm">Jean Martin - Rôle: Utilisateur • il y a 4 heures</p>
          </div>
        </div>

        <div class="flex items-center gap-4 p-4 bg-paper-ivory rounded-xl">
          <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center">
            <i class="pi pi-download text-xl text-purple-700"></i>
          </div>
          <div class="flex-1">
            <p class="font-inter font-medium text-text-primary">Document téléchargé</p>
            <p class="text-text-secondary font-inter text-sm">Guide de programmation Java • 127 fois aujourd'hui</p>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import axios from '@/plugins/axios'
import DashboardLayout from '@/layouts/DashboardLayout.vue'

const authStore = useAuthStore()
const stats = ref({
  total_users: 0,
  active_users: 0,
  suspended_users: 0,
  new_users_this_month: 0,
  recent_logins: 0,
  total_categories: 0,
  active_categories: 0,
  root_categories: 0,
  total_authors: 0,
  alive_authors: 0,
  deceased_authors: 0,
  total_publishers: 0,
  total_keywords: 0,
  total_references: 0,
  published_references: 0,
})

const loadStats = async () => {
  try {
    const response = await axios.get('/api/v1/dashboard/admin')
    stats.value = response.data.data
  } catch (error) {
    console.error('Error loading dashboard stats:', error)
  }
}

onMounted(() => {
  console.log('Admin Dashboard loaded for:', authStore.userName)
  loadStats()
})
</script>
