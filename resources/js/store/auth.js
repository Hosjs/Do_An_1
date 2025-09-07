import { defineStore } from 'pinia'
import axios from 'axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,

    role: (state) => state.user?.roles?.[0]?.name || '',

    roleLabel: (state) => {
      const role = state.user?.roles?.[0]?.name || ''
      const map = {
        Admin: 'Quản trị viên',
        Student: 'Sinh viên',
        Teacher: 'Giảng viên'
      }
      return map[role] || 'Người dùng'
    }
  },

  actions: {
    async login(email, password) {
      try {
        const response = await axios.post('/api/login', { email, password })

        this.token = response.data.access_token
        localStorage.setItem('token', this.token)
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
        this.user = response.data.user
      } catch (error) {
        console.error('Login failed:', error.response?.data || error)
        this.logout()
        throw error
      }
    },

    async fetchUser() {
      try {
        const response = await axios.get('/api/user', {
          headers: {
            Authorization: `Bearer ${this.token}`,
          },
        })
        this.user = response.data
      } catch (error) {
        this.user = null
        this.logout()
        throw error
      }
    },

    logout() {
        axios.post('/api/logout', {}, {
            headers: {
            Authorization: `Bearer ${this.token}`,
            }
        }).catch(error => {
            console.warn('Logout failed on server:', error)
        })

        this.token = null
        this.user = null
        localStorage.removeItem('token')
        delete axios.defaults.headers.common['Authorization']
    }
  },
})
