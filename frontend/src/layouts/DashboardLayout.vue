<template>
  <div class="min-h-screen bg-paper-ivory flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-[#5C4033] text-paper-ivory flex flex-col">
      <!-- Logo -->
      <div class="p-6 border-b border-white/10">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-lg bg-[#C5A46D] flex items-center justify-center">
            <i class="pi pi-book text-2xl text-[#5C4033]"></i>
          </div>
          <div>
            <h1 class="text-2xl font-playfair font-semibold text-white">BiblioNova</h1>
            <p class="text-xs text-white/60 font-inter">{{ getRoleDisplayName(authStore.userRole) }}</p>
          </div>
        </div>
      </div>

      <!-- Menu -->
      <nav class="flex-1 p-4">
        <div class="space-y-1">
          <!-- Dashboard -->
          <router-link
            :to="getDashboardRoute()"
            class="flex items-center gap-3 px-4 py-3 rounded-lg font-inter font-medium transition-all hover:bg-white/10"
            :class="isCurrentRoute(getDashboardRoute()) ? 'bg-[#C5A46D]/20 text-[#C5A46D]' : 'text-white'"
          >
            <i class="pi pi-th-large text-lg"></i>
            Tableau de bord
          </router-link>

          <!-- Menu Items -->
          <template v-for="item in getMenuItems()" :key="item.name">
            <div v-if="item.children" class="mt-6">
              <p class="text-xs font-inter font-semibold text-white/40 uppercase tracking-wider mb-2 px-4">
                {{ item.name }}
              </p>
              <div class="space-y-1">
                <router-link
                  v-for="child in item.children"
                  :key="child.name"
                  :to="child.to"
                  class="flex items-center gap-3 px-4 py-2.5 rounded-lg font-inter text-sm transition-all hover:bg-white/10"
                  :class="isCurrentRoute(child.to) ? 'bg-[#C5A46D]/20 text-[#C5A46D]' : 'text-white'"
                >
                  <i :class="child.icon" class="text-base"></i>
                  {{ child.name }}
                </router-link>
              </div>
            </div>

            <router-link
              v-else
              :to="item.to"
              class="flex items-center gap-3 px-4 py-3 rounded-lg font-inter font-medium transition-all hover:bg-white/10"
              :class="isCurrentRoute(item.to) ? 'bg-[#C5A46D]/20 text-[#C5A46D]' : 'text-white'"
            >
              <i :class="item.icon" class="text-lg"></i>
              {{ item.name }}
            </router-link>
          </template>
        </div>
      </nav>

      <!-- User Info & Logout -->
      <div class="p-4 border-t border-white/10">
        <div class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10 cursor-pointer" @click="showUserMenu = !showUserMenu">
          <div class="w-10 h-10 rounded-full bg-[#C5A46D] flex items-center justify-center text-[#5C4033] font-inter font-semibold">
            {{ authStore.userFullName ? authStore.userFullName.split(' ').map(n => n[0]).join('').toUpperCase() : '?' }}
          </div>
          <div class="flex-1">
            <p class="text-sm font-inter font-medium text-white">{{ authStore.userFullName }}</p>
            <p class="text-xs text-white/60 font-inter">{{ authStore.user?.email }}</p>
          </div>
          <i class="pi pi-chevron-up text-white/60 text-xs"></i>
        </div>

        <!-- User Menu Dropdown -->
        <div v-if="showUserMenu" class="mt-2 space-y-1 bg-white/10 rounded-lg">
          <router-link
            to="/profile"
            class="w-full text-left px-4 py-2 rounded-lg text-sm font-inter text-white hover:bg-white/10 flex items-center gap-2"
          >
            <i class="pi pi-user mr-2"></i>Mon profil
          </router-link>
          <button
            @click="handleLogout" class="w-full text-left px-4 py-2 rounded-lg text-sm font-inter text-white hover:bg-white/10 flex items-center gap-2"
          >
            <i class="pi pi-sign-out mr-2"></i>Déconnexion
          </button>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
      <!-- Navbar -->
      <header class="bg-white border-b border-paper-border px-8 py-6">
        <div class="flex items-center justify-between">
          <h2 class="text-2xl font-playfair text-text-primary">{{ pageTitle }}</h2>
          
          <!-- Notifications -->
          <button class="relative p-2 rounded-full hover:bg-paper-parchment transition-all">
            <i class="pi pi-bell text-xl text-text-secondary"></i>
            <Badge value="3" class="absolute -top-1 -right-1"></Badge>
          </button>
        </div>
      </header>

      <!-- Page Content -->
      <main class="flex-1 p-8">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { Badge } from 'primevue/badge'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()
const showUserMenu = ref(false)

const pageTitle = computed(() => {
  const titles = {
    '/admin/dashboard': 'Tableau de bord - Admin',
    '/rh/dashboard': 'Tableau de bord RH',
    '/validation/dashboard': 'Tableau de bord Validation',
    '/bibliothecaire/dashboard': 'Tableau de bord Bibliothécaire',
    '/dashboard': 'Mon tableau de bord'
  }
  return titles[route.path] || 'Tableau de bord'
})

const handleLogout = async () => {
  await authStore.logout()
  router.push('/auth/login')
}

const isCurrentRoute = (path) => {
  return route.path === path || route.path.startsWith(path)
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

const getDashboardRoute = () => {
  const role = authStore.userRole
  const dashboardRoutes = {
    admin: '/admin/dashboard',
    responsable_rh: '/rh/dashboard',
    responsable_validation: '/validation/dashboard',
    bibliothecaire: '/bibliothecaire/dashboard',
    utilisateur: '/dashboard'
  }
  return dashboardRoutes[role] || '/dashboard'
}

const getMenuItems = () => {
  const role = authStore.userRole
  
  const menus = {
    admin: [
      {
        name: 'Gestion',
        children: [
          { name: 'Utilisateurs', icon: 'pi pi-users', to: '/admin/users' },
          { name: 'Rôles & Permissions', icon: 'pi pi-shield', to: '/admin/roles' }
        ]
      },
      {
        name: 'Workflow',
        children: [
          { name: 'Demandes de dépôt', icon: 'pi pi-file-plus', to: '/admin/deposit-requests' }
        ]
      },
      {
        name: 'Catalogue',
        children: [
          { name: 'Références', icon: 'pi pi-book', to: '/admin/references' },
          { name: 'Catégories', icon: 'pi pi-tags', to: '/admin/categories' },
          { name: 'Auteurs', icon: 'pi pi-pencil', to: '/admin/authors' },
          { name: 'Éditeurs', icon: 'pi pi-building', to: '/admin/publishers' },
          { name: 'Mots-clés', icon: 'pi pi-tags', to: '/admin/keywords' }
        ]
      }
    ],
    
    responsable_rh: [
      { name: 'Utilisateurs', icon: 'pi pi-users', to: '/rh/users' },
      { name: 'Rapports', icon: 'pi pi-chart-line', to: '/rh/reports' }
    ],
    
    responsable_validation: [
      { name: 'Demandes assignées', icon: 'pi pi-file-check', to: '/validation/assigned' },
      { name: 'Demandes en attente', icon: 'pi pi-file', to: '/validation/pending' },
      { name: 'Historique', icon: 'pi pi-history', to: '/validation/history' }
    ],
    
    bibliothecaire: [
      {
        name: 'Catalogue',
        children: [
          { name: 'Références', icon: 'pi pi-book', to: '/bibliothecaire/references' },
          { name: 'Catégories', icon: 'pi pi-tags', to: '/bibliothecaire/categories' },
          { name: 'Auteurs', icon: 'pi pi-pencil', to: '/bibliothecaire/authors' },
          { name: 'Éditeurs', icon: 'pi pi-building', to: '/bibliothecaire/publishers' }
        ]
      },
      {
        name: 'Gestion',
        children: [
          { name: 'Publications', icon: 'pi pi-send', to: '/bibliothecaire/publications' },
          { name: 'Archives', icon: 'pi pi-inbox', to: '/bibliothecaire/archives' }
        ]
      }
    ],
    
    utilisateur: [
      { name: 'Catalogue', icon: 'pi pi-book', to: '/catalog' },
      { name: 'Mes demandes', icon: 'pi pi-file', to: '/my-requests' },
      { name: 'Nouvelle demande', icon: 'pi pi-plus-circle', to: '/new-request' }
    ]
  }
  
  return menus[role] || []
}
</script>
