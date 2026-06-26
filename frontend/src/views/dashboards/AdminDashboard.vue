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
        <p class="text-3xl font-playfair font-semibold text-text-primary">1,247</p>
        <div class="mt-4 flex items-center text-sm font-inter text-green-600">
          <i class="pi pi-arrow-up mr-1"></i>
          <span>+12% ce mois</span>
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
        <p class="text-3xl font-playfair font-semibold text-text-primary">8,342</p>
        <div class="mt-4 flex items-center text-sm font-inter text-green-600">
          <i class="pi pi-arrow-up mr-1"></i>
          <span>+8% ce mois</span>
        </div>
      </div>

      <!-- Pending Requests -->
      <div class="biblio-card">
        <div class="flex items-center justify-between mb-4">
          <div class="w-14 h-14 rounded-xl bg-[#C5A46D]/10 flex items-center justify-center">
            <i class="pi pi-file-plus text-2xl text-[#C5A46D]"></i>
          </div>
        </div>
        <p class="text-text-secondary font-inter text-sm mb-1">Demandes en attente</p>
        <p class="text-3xl font-playfair font-semibold text-text-primary">24</p>
        <div class="mt-4 flex items-center text-sm font-inter text-red-600">
          <i class="pi pi-arrow-up mr-1"></i>
          <span>+3 aujourd'hui</span>
        </div>
      </div>

      <!-- Downloads -->
      <div class="biblio-card">
        <div class="flex items-center justify-between mb-4">
          <div class="w-14 h-14 rounded-xl bg-[#2D2A26]/10 flex items-center justify-center">
            <i class="pi pi-download text-2xl text-[#2D2A26]"></i>
          </div>
        </div>
        <p class="text-text-secondary font-inter text-sm mb-1">Téléchargements</p>
        <p class="text-3xl font-playfair font-semibold text-text-primary">12,847</p>
        <div class="mt-4 flex items-center text-sm font-inter text-green-600">
          <i class="pi pi-arrow-up mr-1"></i>
          <span>+15% ce mois</span>
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
import { onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import DashboardLayout from '@/layouts/DashboardLayout.vue'

const authStore = useAuthStore()

onMounted(() => {
  console.log('Admin Dashboard loaded for:', authStore.userName)
})
</script>
