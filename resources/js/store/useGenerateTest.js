import { ref } from 'vue'
import axios from 'axios'
import { useAuthStore } from './auth'

export function useGenerateTest() {
  const auth = useAuthStore()
  const structure = ref([{ subject_id: '', type_id: '', quantity: 1 }])
  const subjects = ref([])
  const types = ref([])
  const questions = ref([])
  const warnings = ref([])

  const fetchData = async () => {
    const [sub, typ] = await Promise.all([
      axios.get('/api/subjects', {
        headers: {
            Authorization: `Bearer ${auth.token}`,
        },
      }),
      axios.get('/api/question-types', {
        headers: {
            Authorization: `Bearer ${auth.token}`,
        },
      }),
    ])
    subjects.value = sub.data
    types.value = typ.data
  }

  const addBlock = () => {
    structure.value.push({ subject_id: '', type_id: '', quantity: 1 })
  }

  const submit = async () => {
    try {
      const res = await axios.post('/api/tests/generate', {structure: structure.value}, {
        headers: {
            Authorization: `Bearer ${auth.token}`,
        },
      })
      questions.value = res.data.questions || []
      warnings.value = res.data.warnings || []
      alert('✅ Tạo đề thành công')
    } catch (err) {
      alert('❌ Lỗi: ' + (err.response?.data?.message || 'Không xác định'))
    }
  }

  const renderContent = (content) => {
    try {
      return content.replace(/\\frac/g, '⟶ phân số ').replace(/\^/g, ' mũ ')
    } catch {
      return content
    }
  }

  return {
    structure, subjects, types, questions, warnings,
    fetchData, addBlock, submit, renderContent
  }
}
