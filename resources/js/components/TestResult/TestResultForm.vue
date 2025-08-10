<template>
  <div id="math-result" class="page-container">
    <h1>🎉 Kết quả bài làm</h1>

    <div class="result-summary">
      🎯 Bạn đúng {{ score }}/{{ total }} câu – điểm: {{ finalScore }}/10
    </div>

    <div class="time-summary">
      <div><strong>Bắt đầu:</strong> {{ formatDate(startAt) }}</div>
      <div><strong>Kết thúc:</strong> {{ formatDate(endAt) }}</div>
      <div><strong>Thời gian làm:</strong> {{ formatDuration(timeSpentSeconds) }}</div>
    </div>

    <div v-if="wrongAnswers?.length" class="wrong-answers-container">
      <h2 class="font-bold text-lg mb-4 text-red-600">Các câu làm sai:</h2>
      <div v-for="(item, idx) in wrongAnswers" :key="idx" class="answer-card">
        <p class="question-title">Câu hỏi:</p>
        <div v-html="item.questionContent"></div>
        <p class="correct-answer">Đáp án đúng: <span v-html="item.correctAnswer"></span></p>
        <p class="user-answer">Bạn chọn: <span v-html="item.userAnswer"></span></p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, nextTick, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import { renderByMathjax } from 'mathjax-vue3'

const route = useRoute()
const router = useRouter()

const props = defineProps({
  score: { type: Number, required: true },
  total: { type: Number, required: true },
  wrongAnswers: { type: Array, default: () => [] }
})

const finalScore = computed(() => ((props.score / props.total) * 10).toFixed(2))

const testId = route.query.testId ? Number(route.query.testId) : null
const startAt = route.query.startAt || null
const endAt = route.query.endAt || null
const timeSpentSeconds = route.query.timeSpent ? Number(route.query.timeSpent) : null

async function saveScore(testId, userId) {
  const score10 = finalScore.value
  try {
    await axios.put(`/api/tests/${testId}/score`, {
      user_id: userId,
      score: score10
    })
  } catch (e) {
    console.error('Lỗi lưu điểm:', e)
  }
}

function formatDate(iso) {
  if (!iso) return '—'
  try { return new Date(iso).toLocaleString() } catch { return '—' }
}
function formatDuration(s) {
  if (!s && s !== 0) return '—'
  const hh = Math.floor(s / 3600)
  const mm = Math.floor((s % 3600) / 60)
  const ss = s % 60
  const two = n => (n < 10 ? `0${n}` : `${n}`)
  return hh > 0 ? `${two(hh)}:${two(mm)}:${two(ss)}` : `${two(mm)}:${two(ss)}`
}

function blockBack() {
  window.history.pushState(null, '', window.location.href)
  const onPop = () => {
    window.history.pushState(null, '', window.location.href)
  }
  window.addEventListener('popstate', onPop)

  window.addEventListener('beforeunload', () => {
    window.removeEventListener('popstate', onPop)
  })
}

onMounted(async () => {
  blockBack()

  await nextTick()
  const el = document.getElementById('math-result')
  if (el) renderByMathjax(el)
})
</script>

<style>
.page-container {
  padding: 2rem;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background: #f9fafb;
  min-height: 100vh;
}

h1 {
  font-size: 2rem;
  font-weight: bold;
  color: #1d4ed8;
  margin-bottom: 1.5rem;
  text-align: center;
}

.result-summary {
  text-align: center;
  font-size: 1.25rem;
  font-weight: bold;
  color: #16a34a;
  margin-bottom: 1rem;
}

.time-summary {
  max-width: 900px;
  margin: 0 auto 1.5rem auto;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: .75rem;
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  padding: .75rem 1rem;
}

.wrong-answers-container {
  max-width: 900px;
  margin: 0 auto;
}

.answer-card {
  background: #fff;
  border-left: 6px solid #ef4444;
  border-radius: 8px;
  padding: 1.25rem;
  margin-bottom: 1.5rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  transition: transform 0.2s ease-in-out;
}
.answer-card:hover { transform: translateY(-2px); }

.answer-card p { margin: 0.3rem 0; font-size: 1rem; }
.answer-card .question-title { font-weight: bold; color: #1f2937; margin-bottom: 0.5rem; }
.answer-card .correct-answer { color: #16a34a; font-weight: bold; }
.answer-card .user-answer { color: #dc2626; font-weight: bold; }
</style>
