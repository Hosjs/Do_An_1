// /src/store/useResultsStats.js
import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useResultsStats = defineStore('resultsStats', () => {
  const stats = ref({
    total_submissions: 0,
    avg_score: 0,
    pass_rate: 0,
    avg_duration_min: 0,
  })

  const histogram = ref([])
  const perQuestion = ref([])
  const topStudents = ref([])
  const results = ref([])
  const userAnswers = ref([])

  // Trang hiển thị ở /admin/statistics, dữ liệu JSON lấy tại:
  const BASE_URL = import.meta.env.VITE_API_URL || window.location.origin
  const STATS_ENDPOINT = '/admin/statistics' // ✅ endpoint BE bạn vừa tạo

  async function fetchAll(query) {
    try {
      const u = new URL(STATS_ENDPOINT, BASE_URL)
      Object.entries(query || {}).forEach(([k, v]) => {
        if (v !== '' && v != null) u.searchParams.set(k, v)
      })

      const res = await fetch(u.toString(), {
        method: 'GET',
        credentials: 'include',
        headers: { Accept: 'application/json' },
      })

      const ct = res.headers.get('content-type') || ''
      if (!res.ok) {
        const bodyText = await res.text()
        console.error('[stats] API not ok', res.status, res.statusText, bodyText.slice(0, 200))
        return false
      }
      if (!ct.includes('application/json')) {
        const bodyText = await res.text()
        console.error('[stats] Expect JSON but got:', ct, bodyText.slice(0, 200))
        return false
      }

      const data = await res.json()
      console.log('[stats] payload:', data)

      // BE tối thiểu trả: { user_answers: [...] }
      userAnswers.value = Array.isArray(data.user_answers) ? data.user_answers : []

      // Các phần khác nếu BE có trả thì set, không thì reset về rỗng/0 để tránh dữ liệu cũ
      histogram.value   = Array.isArray(data.histogram)     ? data.histogram     : []
      perQuestion.value = Array.isArray(data.per_question)  ? data.per_question  : []
      topStudents.value = Array.isArray(data.top_students)  ? data.top_students  : []
      results.value     = Array.isArray(data.results)       ? data.results       : []
      stats.value = {
        total_submissions: Number(data.total_submissions ?? 0),
        avg_score: Number(data.avg_score ?? 0),
        pass_rate: Number(data.pass_rate ?? 0),
        avg_duration_min: Number(data.avg_duration_min ?? 0),
      }

      return true
    } catch (err) {
      console.error('[stats] fetchAll error:', err)
      return false
    }
  }

  function exportCSV() {
    const header = ['#', 'Họ tên', 'Lớp', 'Điểm', 'Thời gian (phút)', 'Ngày làm']
    const rows = topStudents.value.map((r, i) => [
      i + 1,
      (r.full_name ?? '').replaceAll(',', ' '),
      (r.class_name ?? '').replaceAll(',', ' '),
      (r.score ?? 0),
      Math.round(r.duration_min ?? 0),
      r.submitted_at ?? '',
    ])

    const csv = [header, ...rows].map(cols => cols.join(',')).join('\n')
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' })
    const url = URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = 'top_students.csv'
    document.body.appendChild(a)
    a.click()
    document.body.removeChild(a)
    URL.revokeObjectURL(url)
  }

  return {
    stats,
    histogram,
    perQuestion,
    topStudents,
    results,
    userAnswers,
    fetchAll,
    exportCSV,
  }
})
