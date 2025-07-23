import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'
import Login from '../views/Login.vue'
import AdminLayout from '../layouts/AdminLayout.vue'
import StudentLayout from '../layouts/StudentLayout.vue'
import { useAuthStore } from '../store/auth'

// Admin pages
import AdminGenerateTest from '@/views/Admin/GenerateTest.vue'
import ViewTestList from '../views/Admin/ViewTestList.vue'
import UserManager from '../views/Admin/UserManager.vue'
import TestDetail from '@/components/TestGenerate/TestDetail.vue'

// Common test page (ví dụ thử MathJax hoặc mẫu test nhanh)
import TestJax from '../views/TestJax.vue'

const routes = [
  {
    path: '/testjax',
    component: TestJax,
    meta: { requiresAuth: false }
  },
  {
    path: '/',
    component: Home,
    meta: { requiresAuth: false }
  },
  {
    path: '/login',
    component: Login,
    meta: { requiresGuest: false }
  },
  {
    path: '/admin',
    component: AdminLayout,
    meta: { requiresAuth: true, role: 'Admin' },
    children: [
      { path: '', name: 'AdminHome', component: Home },
      { path: 'users', component: UserManager },
      { path: 'generate-test', name: 'AdminGenerateTest', component: AdminGenerateTest },
      { path: 'tests', component: ViewTestList },
      {
        path: '/admin/tests/test-begin/:id',
        name: 'TestBegin',
        component: () => import('@/views/Admin/TestBegin.vue'),
        meta: { requiresAuth: true, role: 'Admin' },
        props: true,
      },
      {
        path: '/admin/tests/result',
        name: 'TestResult',
        component: () => import('@/views/admin/TestResult.vue'),
        props: true
      }
    ]
    
  },
  

  {
    path: '/student',
    component: StudentLayout,
    meta: { requiresAuth: true, role: 'Student' },
    children: [
      { path: '', name: 'StudentHome', component: Home },
      { path: 'tests', component: ViewTestList },
      {
        path: 'tests/:id',
        name: 'StudentTestDetail',
        component: TestDetail,
        props: true
      }
    ]
  },
  {
    path: '/teacher',
    component: StudentLayout,
    meta: { requiresAuth: true, role: 'Teacher' },
    children: [
      { path: '', name: 'TeacherHome', component: Home },
      { path: 'tests', component: ViewTestList },
      {
        path: 'tests/:id',
        name: 'TeacherTestDetail',
        component: TestDetail,
        props: true
      }
    ]
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Global navigation guard
router.beforeEach(async (to, from, next) => {
  const auth = useAuthStore()

  if (auth.token && !auth.user) {
    try {
      await auth.fetchUser()
    } catch (error) {
      auth.logout()
    }
  }

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return next('/login')
  }

  if (to.meta.requiresGuest && auth.isAuthenticated) {
    return next('/')
  }

  if (to.meta.role && auth.role !== to.meta.role) {
    return next('/')
  }

  return next()
})

export default router
