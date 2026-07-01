import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

// Auth views
import LoginView from '@/views/auth/LoginView.vue'
import RegisterView from '@/views/auth/RegisterView.vue'
import ForgotPasswordView from '@/views/auth/ForgotPasswordView.vue'
import ResetPasswordView from '@/views/auth/ResetPasswordView.vue'

// Layout
import DashboardLayout from '@/layouts/DashboardLayout.vue'

const routes = [
  // Redirect root to login
  {
    path: '/',
    name: 'Root',
    redirect: '/auth/login'
  },

  // Auth routes (public)
  {
    path: '/auth',
    children: [
      {
        path: 'login',
        name: 'Login',
        component: LoginView,
        meta: { guest: true }
      },
      {
        path: 'register',
        name: 'Register',
        component: RegisterView,
        meta: { guest: true }
      },
      {
        path: 'forgot-password',
        name: 'ForgotPassword',
        component: ForgotPasswordView,
        meta: { guest: true }
      },
      {
        path: 'reset-password',
        name: 'ResetPassword',
        component: ResetPasswordView,
        meta: { guest: true }
      }
    ]
  },

  // Protected routes with DashboardLayout
  {
    path: '/',
    component: DashboardLayout,
    meta: { requiresAuth: true },
    children: [
      // Admin routes
      {
        path: 'admin',
        meta: { roles: ['admin'] },
        children: [
          {
            path: 'dashboard',
            name: 'AdminDashboard',
            component: () => import('@/views/dashboards/AdminDashboard.vue')
          },
          {
            path: 'users',
            name: 'AdminUsers',
            component: () => import('@/views/users/UsersListView.vue')
          },
          {
            path: 'categories',
            name: 'AdminCategories',
            component: () => import('@/views/categories/CategoriesListView.vue')
          },
          {
            path: 'authors',
            name: 'AdminAuthors',
            component: () => import('@/views/authors/AuthorsListView.vue')
          },
          {
            path: 'publishers',
            name: 'AdminPublishers',
            component: () => import('@/views/publishers/PublishersListView.vue')
          },
          {
            path: 'keywords',
            name: 'AdminKeywords',
            component: () => import('@/views/keywords/KeywordsListView.vue')
          },
          {
            path: 'keywords/:id',
            name: 'AdminKeywordDetails',
            component: () => import('@/views/keywords/KeywordDetailsView.vue')
          },
          {
            path: 'roles',
            name: 'AdminRoles',
            component: () => import('@/views/admin/AdminRolesView.vue')
          },
          {
            path: 'deposit-requests',
            name: 'AdminDepositRequests',
            component: () => import('@/views/admin/AdminDepositRequestsView.vue')
          },
          {
            path: 'references',
            name: 'AdminReferences',
            component: () => import('@/views/admin/AdminReferencesView.vue')
          },
        ]
      },

      // RH routes
      {
        path: 'rh',
        meta: { roles: ['admin', 'responsable_rh'] },
        children: [
          {
            path: 'dashboard',
            name: 'RHDashboard',
            component: () => import('@/views/dashboards/RHDashboard.vue')
          },
          {
            path: 'users',
            name: 'RHUsers',
            component: () => import('@/views/users/UsersListView.vue')
          },
          {
            path: 'users/create',
            name: 'RHUsersCreate',
            component: () => import('@/views/users/UserCreateForm.vue')
          },
          {
            path: 'reports',
            name: 'RHReports',
            component: () => import('@/views/rh/RHReportsView.vue')
          },
        ]
      },

      // Validation routes
      {
        path: 'validation',
        meta: { roles: ['admin', 'responsable_validation'] },
        children: [
          {
            path: 'dashboard',
            name: 'ValidationDashboard',
            component: () => import('@/views/dashboards/ValidationDashboard.vue')
          },
          {
            path: 'pending',
            name: 'ValidationPending',
            component: () => import('@/views/validation/ValidationPendingView.vue')
          },
          {
            path: 'assigned',
            name: 'ValidationAssigned',
            component: () => import('@/views/validation/ValidationAssignedView.vue')
          },
          {
            path: 'history',
            name: 'ValidationHistory',
            component: () => import('@/views/validation/ValidationHistoryView.vue')
          },
        ]
      },

      // Bibliothecaire routes
      {
        path: 'bibliothecaire',
        meta: { roles: ['admin', 'bibliothecaire'] },
        children: [
          {
            path: 'dashboard',
            name: 'BibliothecaireDashboard',
            component: () => import('@/views/dashboards/BibliothecaireDashboard.vue')
          },
          {
            path: 'publications',
            name: 'BibliothecairePublications',
            component: () => import('@/views/bibliothecaire/BibliothecairePublicationsView.vue')
          },
          {
            path: 'references',
            name: 'BibliothecaireReferences',
            component: () => import('@/views/bibliothecaire/BibliothecaireReferencesView.vue')
          },
          {
            path: 'categories',
            name: 'BibliothecaireCategories',
            component: () => import('@/views/bibliothecaire/BibliothecaireCategoriesView.vue')
          },
          {
            path: 'archives',
            name: 'BibliothecaireArchives',
            component: () => import('@/views/bibliothecaire/BibliothecaireArchivesView.vue')
          },
        ]
      },

      // User routes (accessible to all authenticated users)
      {
        path: 'dashboard',
        name: 'UserDashboard',
        component: () => import('@/views/dashboards/UserDashboard.vue')
      },
      {
        path: 'new-request',
        name: 'NewRequest',
        component: () => import('@/views/NewRequestView.vue')
      },
      {
        path: 'catalog',
        name: 'Catalog',
        component: () => import('@/views/CatalogView.vue')
      },
      {
        path: 'my-requests',
        name: 'MyRequests',
        component: () => import('@/views/MyRequestsView.vue')
      },
      {
        path: 'profile',
        name: 'Profile',
        component: () => import('@/views/users/UserProfileView.vue')
      },
    ]
  },

  // 404 route
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: () => import('@/views/NotFoundView.vue')
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Navigation guard
router.beforeEach(async (to, from) => {
  const authStore = useAuthStore()
  console.log('📍 Navigation guard triggered:', to.path)

  // Always initialize auth on each navigation to check backend session
  if (!authStore.authInitialized || authStore.isLoading) {
    console.log('⏳ Initializing auth...')
    try {
      await authStore.initAuth()
    } catch (e) {
      console.error('❌ Error initializing auth:', e)
    }
  }

  console.log('ℹ️  Auth status after init:', {
    isAuthenticated: authStore.isAuthenticated,
    user: authStore.user,
    userRole: authStore.userRole,
    redirectUrl: authStore.redirectUrl
  })

  // Handle guest routes (login, register, etc.)
  if (to.meta.guest) {
    if (authStore.isAuthenticated) {
      // User is already authenticated, redirect to dashboard
      const redirectPath = authStore.redirectUrl || '/dashboard'
      console.log('🔓 User already authenticated, redirecting to:', redirectPath)
      return redirectPath
    }
    return true
  }

  // Handle protected routes
  if (to.meta.requiresAuth) {
    if (!authStore.isAuthenticated) {
      // User not authenticated, redirect to login
      console.log('🔒 User not authenticated, redirecting to login')
      return '/auth/login'
    }

    // Check roles if specified
    if (to.meta.roles && to.meta.roles.length > 0) {
      const userRole = authStore.userRole
      const hasRequiredRole = to.meta.roles.includes(userRole)
      
      if (!hasRequiredRole) {
        // User doesn't have required role, redirect to their dashboard
        const redirectPath = authStore.redirectUrl || '/dashboard'
        console.log('❌ User role', userRole, 'not allowed, redirecting to:', redirectPath)
        return redirectPath
      }
    }
  }

  return true
})

export default router
