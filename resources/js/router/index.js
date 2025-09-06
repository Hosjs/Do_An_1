import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/home.vue'
import Login from '../views/Login.vue'
import AdminLayout from '../layouts/AdminLayout.vue'
import StudentLayout from '../layouts/StudentLayout.vue'
import { useAuthStore } from '../store/auth'

//home layout
import HomeLayout from '../layouts/HomeLayout.vue'

// Admin pages
import AdminGenerateTest from '@/views/Admin/GenerateTest.vue'
import ViewTestList from '../views/Admin/ViewTestList.vue'
import UserManager from '../views/Admin/UserManager.vue'
import TestDetail from '@/components/TestGenerate/TestDetail.vue'
import AdTestBegin from '@/views/Admin/TestBegin.vue'
import TestAnswer from '@/views/TestAnswer.vue'
import TestManagement from '../views/Admin/TestManagement.vue'
import TestQuestion from '../components/TestManagement/TestQuestion.vue'

// Student pages
import StTestBegin from '@/views/Student/TestBegin.vue'
import StTestResult from '@/views/Student/TestResult.vue'
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
    component: HomeLayout,
    meta: { requiresAuth: false },
    children: [
      {path: '', name: 'Home', component: Home },
    ]
  },
  {
    path: '/login',
    component: Login,
    meta: { requiresGuest: false }
  },
  {
    path: '/test-answer',
    name: 'TestAnswer',
    component: TestAnswer
  },
  {
    path: '/register',
    name: 'Register',
    component: () => import('../views/Register.vue')
  },
  {
    path: '/admin',
    component: AdminLayout,
    meta: { requiresAuth: true, role: 'Admin' },
    children: [
      { path: '', name: 'AdminHome', component: () => import('../views/Admin/DashBoard.vue') },
      { path: 'users', component: UserManager },
      { path: 'generate-test', name: 'AdminGenerateTest', component: AdminGenerateTest },
      { path: 'tests', component: ViewTestList },
      {
        path: 'tests/test-begin/:id',
        name: 'AdTestBegin',
        component: AdTestBegin,
      },
      {
        path: 'tests/result',
        name: 'TestResult',
        component: () => import('@/views/Admin/TestResult.vue'),
        props: true
      },
      {
        path: 'dashboard',
        name: 'DashBoard',
        component: () => import('../views/Admin/DashBoard.vue'),
        props: true
      },
      {
        path: 'statistics',
        name: 'AdminStatistics',
        component: () => import('@/views/Admin/statistics.vue'),
        props: true
      },
      {
        path: 'test-management',
        name: 'TestManagement',
        component: TestManagement,
        props: true
      },
      {
        path: 'questions-manage/:id',
        name: 'AdminQuestionsManage',
        component: TestQuestion,
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
        path: 'tests/test-begin/:id',
        name: 'StTestBegin',
        component: StTestBegin, 
      },
      {
        path: 'tests/result',
        name: 'TestResultS',
        component: StTestResult,
      },
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
      },
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
