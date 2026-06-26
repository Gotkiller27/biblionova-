import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

// Auth views
import LoginView from '@/views/auth/LoginView.vue'
import RegisterView from '@/views/auth/RegisterView.vue'
import ForgotPasswordView from '@/views/auth/ForgotPasswordView.vue'
import ResetPasswordView from '@/views/auth/ResetPasswordView.vue'

// Dashboard views
import AdminDashboard from '@/views/dashboards/AdminDashboard.vue'

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

  // Admin routes
  {
    path: '/admin',
    meta: { requiresAuth: true, roles: ['admin'] },
    children: [
      {
        path: 'dashboard',
        name: 'AdminDashboard',
        component: AdminDashboard
      },
      // TODO: Add other admin routes
    ]
  },

  // RH routes
  {
    path: '/rh',
    meta: { requiresAuth: true, roles: ['admin', 'responsable_rh'] },
    children: [
      {
        path: 'dashboard',
        name: 'RHDashboard',
        component: () => import('@/views/dashboards/RHDashboard.vue')
      },
      // TODO: Add other RH routes
    ]
  },

  // Validation routes
  {
    path: '/validation',
    meta: { requiresAuth: true, roles: ['admin', 'responsable_validation'] },
    children: [
      {
        path: 'dashboard',
        name: 'ValidationDashboard',
        component: () => import('@/views/dashboards/ValidationDashboard.vue')
      },
      // TODO: Add other validation routes
    ]
  },

  // Bibliothecaire routes
  {
    path: '/bibliothecaire',
    meta: { requiresAuth: true, roles: ['admin', 'bibliothecaire'] },
    children: [
      {
        path: 'dashboard',
        name: 'BibliothecaireDashboard',
        component: () => import('@/views/dashboards/BibliothecaireDashboard.vue')
      },
      // TODO: Add other bibliothecaire routes
    ]
  },

  // User routes (accessible to all authenticated users)
  {
    path: '/dashboard',
    name: 'UserDashboard',
    component: () => import('@/views/dashboards/UserDashboard.vue'),
    meta: { requiresAuth: true }
  },

  // Common authenticated routes (à développer)
  // {
  //   path: '/profile',
  //   name: 'Profile', 
  //   component: () => import('@/views/ProfileView.vue'),
  //   meta: { requiresAuth: true }
  // },

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
  // #region debug-point B:guard-entry
  fetch('http://127.0.0.1:7777/event', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ sessionId: 'dashboard-refresh-auth', runId: 'pre-fix', hypothesisId: 'B', location: 'frontend/src/router/index.js:beforeEach:entry', msg: '[DEBUG] router guard entered', data: { to: to.path, from: from.path, isAuthenticated: authStore.isAuthenticated, isLoading: authStore.isLoading, redirectUrl: authStore.redirectUrl, userRole: authStore.userRole }, ts: Date.now() }) }).catch(() => {})
  // #endregion
  console.log('📍 Navigation guard triggered:', to.path)

  // Always initialize auth on each navigation to check backend session
  if (!authStore.authInitialized || authStore.isLoading) {
    console.log('⏳ Initializing auth...')
    try {
      await authStore.initAuth()
      // #region debug-point B:guard-after-init
      fetch('http://127.0.0.1:7777/event', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ sessionId: 'dashboard-refresh-auth', runId: 'pre-fix', hypothesisId: 'B', location: 'frontend/src/router/index.js:beforeEach:after-init', msg: '[DEBUG] router guard after initAuth', data: { to: to.path, isAuthenticated: authStore.isAuthenticated, redirectUrl: authStore.redirectUrl, userRole: authStore.userRole, hasUser: !!authStore.user }, ts: Date.now() }) }).catch(() => {})
      // #endregion
    } catch (e) {
      // #region debug-point B:guard-init-error
      fetch('http://127.0.0.1:7777/event', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ sessionId: 'dashboard-refresh-auth', runId: 'pre-fix', hypothesisId: 'B', location: 'frontend/src/router/index.js:beforeEach:init-error', msg: '[DEBUG] router guard initAuth failed', data: { to: to.path, message: e?.message || null }, ts: Date.now() }) }).catch(() => {})
      // #endregion
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
      // #region debug-point E:guest-redirect
      fetch('http://127.0.0.1:7777/event', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ sessionId: 'dashboard-refresh-auth', runId: 'pre-fix', hypothesisId: 'E', location: 'frontend/src/router/index.js:beforeEach:guest-redirect', msg: '[DEBUG] guest route redirected to dashboard', data: { to: to.path, redirectPath, userRole: authStore.userRole }, ts: Date.now() }) }).catch(() => {})
      // #endregion
      console.log('🔓 User already authenticated, redirecting to:', redirectPath)
      return redirectPath
    }
    return true
  }

  // Handle protected routes
  if (to.meta.requiresAuth) {
    if (!authStore.isAuthenticated) {
      // User not authenticated, redirect to login
      // #region debug-point B:auth-redirect-login
      fetch('http://127.0.0.1:7777/event', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ sessionId: 'dashboard-refresh-auth', runId: 'pre-fix', hypothesisId: 'B', location: 'frontend/src/router/index.js:beforeEach:auth-redirect-login', msg: '[DEBUG] protected route redirected to login', data: { to: to.path, from: from.path, isAuthenticated: authStore.isAuthenticated, hasUser: !!authStore.user, redirectUrl: authStore.redirectUrl }, ts: Date.now() }) }).catch(() => {})
      // #endregion
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
        // #region debug-point E:role-redirect
        fetch('http://127.0.0.1:7777/event', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ sessionId: 'dashboard-refresh-auth', runId: 'pre-fix', hypothesisId: 'E', location: 'frontend/src/router/index.js:beforeEach:role-redirect', msg: '[DEBUG] role mismatch redirected to dashboard', data: { to: to.path, userRole, redirectPath, allowedRoles: to.meta.roles }, ts: Date.now() }) }).catch(() => {})
        // #endregion
        console.log('❌ User role', userRole, 'not allowed, redirecting to:', redirectPath)
        return redirectPath
      }
    }
  }

  return true
})

export default router
