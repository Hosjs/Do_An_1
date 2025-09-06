import { defineStore } from 'pinia'
import axios from 'axios'
import { useAuthStore } from './auth'

export const useTestStore = defineStore('test', {
  state: () => ({
    tests: [],
    selectedTest: null,
    questions: []
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
    },

    // ----------- QUẢN LÝ CÂU HỎI -----------
    async fetchQuestions(testId) {
      const auth = useAuthStore()
      const res = await axios.get(`/api/questions-manage/${testId}`, {
        headers: {
          Authorization: `Bearer ${auth.token}`
        }
      })
      this.questions = res.data
    },

    async addQuestion(testId, data) {
      const auth = useAuthStore()
      const res = await axios.post(`/api/questions-manage/${testId}`, data, {
        headers: {
          Authorization: `Bearer ${auth.token}`
        }
      })
      return res.data
    },

    async updateQuestion(id, data) {
      const auth = useAuthStore()
      const res = await axios.put(`/api/questions/${id}`, data, {
        headers: {
          Authorization: `Bearer ${auth.token}`
        }
      })
      return res.data
    },

    async deleteQuestion(id) {
      const auth = useAuthStore()
      await axios.delete(`/api/questions/${id}`, {
        headers: {
          Authorization: `Bearer ${auth.token}`
        }
      })
    }
  }
})
