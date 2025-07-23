import { defineStore } from 'pinia'
import axios from 'axios'
import { useAuthStore } from './auth'

export const useTestStore = defineStore('test', {
  state: () => ({
    tests: [],
    selectedTest: null,
  }),
  actions: {
    async fetchTests() {
      const auth = useAuthStore();
      const res = await axios.get('/api/tests', {
        headers: {
            Authorization: `Bearer ${auth.token}`,
        },
      })
      this.tests = res.data
    },
    async fetchTestDetail(id) {
      const auth = useAuthStore();
      const res = await axios.get(`/api/tests/${id}`, {
        headers: {
            Authorization: `Bearer ${auth.token}`,
        },
      })
      this.selectedTest = res.data
    },
    async deleteTest(id) {
      const auth = useAuthStore()
      await axios.delete(`/api/tests/${id}`, {
        headers: {
          Authorization: `Bearer ${auth.token}`
        }
      })
    },
    async fetchTestBegin(id) {
      const auth = useAuthStore();
      const res = await axios.get(`/api/tests/testBegin/${id}`, {
        headers: {
            Authorization: `Bearer ${auth.token}`,
        },
      })
      this.selectedTest = res.data
    }
    
  }
})
