<template>
  <div id="math-result" class="page-container">
    <div class="result-summary">
      🎯 Bạn đúng {{ score }}/{{ total }} câu – điểm: {{ finalScore }}/10
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

    <!-- Các nút chức năng -->
    <div class="btn-container">
      <button class="btn-back" @click="goBack">
        ⬅️ Quay lại danh sách bài test
      </button>
      <button class="btn-solution" @click="goSolution">
        📖 Cách giải
      </button>
    </div>
  </div>
</template>

<script setup>
import { onMounted, nextTick, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import { renderByMathjax } from 'mathjax-vue3'
import { useAuthStore } from '../../store/auth'

const route = useRoute()
const router = useRouter()

const props = defineProps({
  score: { type: Number, required: true },
  total: { type: Number, required: true },
  wrongAnswers: { type: Array, default: () => [] }
})

const finalScore = computed(() => ((props.score / props.total) * 10).toFixed(2))

function goBack() {
  const role = useAuthStore().role
  if (role === 'Student') router.push('/student/tests')
  else if (role === 'Admin') router.push('/admin/tests')
  else if (role === 'Teacher') router.push('/teacher/tests')
}

function goSolution() {
  const url = `/test-answer`
  window.open(url, '_blank')
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
  background: #e0f7e9;
  border-radius: 8px;
  padding: 1rem 0;
  box-shadow: 0 2px 8px rgba(22, 163, 74, 0.08);
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
  padding: 1.25rem 1.5rem;
  margin-bottom: 1.5rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  transition: transform 0.2s ease-in-out, box-shadow 0.2s;
  border-top: 1px solid #f3f4f6;
  border-bottom: 1px solid #f3f4f6;
}

.answer-card:hover {
  transform: translateY(-2px) scale(1.01);
  box-shadow: 0 4px 16px rgba(239, 68, 68, 0.12);
}

.answer-card p {
  margin: 0.3rem 0;
  font-size: 1.05rem;
  line-height: 1.6;
}

.answer-card .question-title {
  font-weight: bold;
  color: #1f2937;
  margin-bottom: 0.5rem;
  font-size: 1.1rem;
}

.answer-card .correct-answer {
  color: #16a34a;
  font-weight: bold;
  background: #e0f7e9;
  border-radius: 4px;
  padding: 0.2rem 0.5rem;
  display: inline-block;
}

.answer-card .user-answer {
  color: #dc2626;
  font-weight: bold;
  background: #fee2e2;
  border-radius: 4px;
  padding: 0.2rem 0.5rem;
  display: inline-block;
}
.btn-container {
  margin-top: 2rem;
  display: flex;
  justify-content: center;
  gap: 1rem;
}

/* Nút quay lại */
.btn-back {
  background: linear-gradient(135deg, #2563eb, #1d4ed8);
  color: white;
  font-size: 1.05rem;
  font-weight: 600;
  padding: 0.7rem 1.4rem;
  border: none;
  border-radius: 9999px;
  cursor: pointer;
  box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3);
  transition: all 0.2s ease-in-out;
}
.btn-back:hover {
  background: linear-gradient(135deg, #1d4ed8, #1e40af);
  transform: translateY(-2px);
}

/* Nút cách giải */
.btn-solution {
  background: linear-gradient(135deg, #16a34a, #15803d);
  color: white;
  font-size: 1.05rem;
  font-weight: 600;
  padding: 0.7rem 1.4rem;
  border: none;
  border-radius: 9999px;
  cursor: pointer;
  box-shadow: 0 4px 10px rgba(22, 163, 74, 0.3);
  transition: all 0.2s ease-in-out;
}
.btn-solution:hover {
  background: linear-gradient(135deg, #15803d, #166534);
  transform: translateY(-2px);
}
</style>
