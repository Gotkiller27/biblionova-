<template>
  <DashboardLayout>
    <!-- Welcome Message -->
    <div class="mb-8">
      <h2 class="text-3xl font-playfair text-[#2D2A26] mb-2">Bienvenue {{ authStore.userName }} !</h2>
      <p class="text-text-secondary font-inter">
        Vous êtes connecté en tant que {{ authStore.userRoleLabel }}. Explorez le catalogue documentaire BiblioNova ou soumettez une nouvelle demande de dépôt.
      </p>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <router-link
        to="/new-request"
        class="biblio-card"
      >
        <div class="flex items-center gap-4">
          <div class="w-14 h-14 rounded-xl bg-[#C5A46D]/10 flex items-center justify-center">
            <i class="pi pi-plus-circle text-3xl text-[#C5A46D]"></i>
          </div>
          <div>
            <h3 class="text-lg font-playfair font-semibold text-[#2D2A26]">Nouvelle demande</h3>
            <p class="text-text-secondary font-inter text-sm">Soumettre un document</p>
          </div>
        </div>
      </router-link>

      <router-link
        to="/catalog"
        class="biblio-card"
      >
        <div class="flex items-center gap-4">
          <div class="w-14 h-14 rounded-xl bg-[#5C4033]/10 flex items-center justify-center">
            <i class="pi pi-book text-3xl text-[#5C4033]"></i>
          </div>
          <div>
            <h3 class="text-lg font-playfair font-semibold text-[#2D2A26]">Catalogue</h3>
            <p class="text-text-secondary font-inter text-sm">Parcourir les références</p>
          </div>
        </div>
      </router-link>

      <router-link
        to="/my-requests"
        class="biblio-card"
      >
        <div class="flex items-center gap-4">
          <div class="w-14 h-14 rounded-xl bg-[#7A5C43]/10 flex items-center justify-center">
            <i class="pi pi-file text-3xl text-[#7A5C43]"></i>
          </div>
          <div>
            <h3 class="text-lg font-playfair font-semibold text-[#2D2A26]">Mes demandes</h3>
            <p class="text-text-secondary font-inter text-sm">Suivre le statut</p>
          </div>
        </div>
      </router-link>
    </div>

    <!-- Stats -->
    <div class="biblio-card">
      <h3 class="text-xl font-playfair font-semibold text-[#2D2A26] mb-6">Statistiques</h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="text-center p-6 bg-paper-ivory rounded-xl">
          <div class="text-3xl font-playfair font-bold text-[#5C4033] mb-1">3</div>
          <div class="text-text-secondary font-inter text-sm">Demandes soumises</div>
        </div>
        <div class="text-center p-6 bg-paper-ivory rounded-xl">
          <div class="text-3xl font-playfair font-bold text-[#7A5C43] mb-1">127</div>
          <div class="text-text-secondary font-inter text-sm">Documents consultés</div>
        </div>
        <div class="text-center p-6 bg-paper-ivory rounded-xl">
          <div class="text-3xl font-playfair font-bold text-[#C5A46D] mb-1">45</div>
          <div class="text-text-secondary font-inter text-sm">Téléchargements</div>
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
  console.log('User Dashboard loaded for:', authStore.userName)
})
</script>
