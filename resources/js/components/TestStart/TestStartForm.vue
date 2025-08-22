<template>
  <div class="test-layout" id="math-container">
    <!-- Sidebar trái -->
    <aside class="sidebar">
      <h3 class="sb-title">👤 Thí sinh</h3>
      <div class="sb-card">
        <div class="row"><span>Họ tên:</span><strong>{{ auth.user?.name }}</strong></div>
        <div class="row"><span>Bài thi:</span><strong>#{{ test?.id }}</strong></div>
      </div>

      <h3 class="sb-title">⏱️ Thời gian</h3>
      <div class="timer-card" :class="{ danger: remainingSeconds <= 60 && !isSubmitted }">
        <div class="time">{{ mm }}:{{ ss }}</div>
        <div class="deadline" v-if="deadlineAt">Hết hạn: {{ formatDate(deadlineAt) }}</div>
      </div>

      <h3 class="sb-title">Tiến độ</h3>
      <div class="sb-card">
        <div class="row"><span>Đã chọn:</span><strong>{{ answeredCount }}</strong></div>
        <div class="row"><span>Tổng câu:</span><strong>{{ total }}</strong></div>
        <div class="progress">
          <div class="bar" :style="{ width: progressPct + '%' }"></div>
        </div>
      </div>

      <button
        class="btn primary w-full"
        :disabled="isSubmitted"
        @click="submitTest"
      >✅ Nộp bài</button>
    </aside>

    <!-- Nội dung đề -->
    <main class="content">
      <h2 class="page-title">📘 Bắt đầu làm bài</h2>

      <div v-if="test && test.details && test.details.length">
        <div
          v-for="(detail, index) in test.details"
          :key="detail.id"
          class="q-block"
        >
          <p class="q-order">Câu {{ index + 1 }}:</p>
          <p class="q-content" v-html="detail.question.content"></p>

          <ul
            v-if="detail.question.answers && detail.question.answers.length"
            class="answers"
          >
            <li
              v-for="answer in detail.question.answers"
              :key="answer.id"
              class="answer-item"
              :class="{ selected: answers[detail.question.id] === answer.id }"
            >
              <label class="answer-label">
                <input
                  type="radio"
                  :name="'q_' + detail.question.id"
                  :value="answer.id"
                  v-model="answers[detail.question.id]"
                  :disabled="isSubmitted"
                  @change="saveDraftDebounced()"
                />
                <span v-html="answer.content"></span>
              </label>
            </li>
          </ul>
        </div>
      </div>

      <div v-else class="muted">Đang tải đề thi...</div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch, nextTick, onBeforeUnmount } from 'vue'
import { useRoute, useRouter, onBeforeRouteLeave } from 'vue-router'
import { useTestStore } from '@/store/test'
import { useAuthStore } from '@/store/auth'
import { renderByMathjax } from 'mathjax-vue3'
import '@/assets/css/test-start-form.css'
import axios from 'axios'

// ===== Store & Router =====
const route = useRoute()
const router = useRouter()
const store = useTestStore()
const auth = useAuthStore()

// ===== State =====
const answers = ref({})
const isSubmitted = ref(false)
const startAt = ref(null)   
const deadlineAt = ref(null)
const remainingSeconds = ref(0)
let timerId = null
let autosaveIntervalId = null

const test = computed(() => store.selectedTest)
const total = computed(() => test.value?.details?.length || 0)
const answeredCount = computed(() =>
  Object.values(answers.value).filter(v => !!v).length
)
const progressPct = computed(() =>
  total.value ? Math.round((answeredCount.value / total.value) * 100) : 0
)

// ===== Helpers =====
const STORAGE_KEY = computed(() => {
  if (!auth?.user?.id || !test?.value?.id) return null
  return `test_draft::u_${auth.user.id}::t_${test.value.id}`
})

const formatDate = (iso) => {
  try {
    const d = new Date(iso)
    return d.toLocaleString()
  } catch { return '' }
}

const two = (n) => (n < 10 ? `0${n}` : `${n}`)
const mm = computed(() => two(Math.floor(remainingSeconds.value / 60)))
const ss = computed(() => two(remainingSeconds.value % 60))

function calcRemaining() {
  if (!deadlineAt.value) return 0
  const diff = Math.max(0, Math.floor((new Date(deadlineAt.value) - new Date()) / 1000))
  return diff
}

// ===== Draft persistence =====
function saveDraft() {
  if (!STORAGE_KEY.value) return
  const payload = {
    answers: answers.value,
    startAt: startAt.value,
    deadlineAt: deadlineAt.value,
    isSubmitted: isSubmitted.value,
    updatedAt: new Date().toISOString()
  }
  localStorage.setItem(STORAGE_KEY.value, JSON.stringify(payload))
}

let debounceT = null
function saveDraftDebounced(wait = 400) {
  if (debounceT) clearTimeout(debounceT)
  debounceT = setTimeout(() => {
    saveDraft()
    // (Tuỳ chọn) autosave lên server
    // safeAutosaveToServer().catch(()=>{})
  }, wait)
}

function loadDraft() {
  if (!STORAGE_KEY.value) return false
  const raw = localStorage.getItem(STORAGE_KEY.value)
  if (!raw) return false
  try {
    const data = JSON.parse(raw)
    if (data?.answers) answers.value = data.answers
    if (data?.startAt) startAt.value = data.startAt
    if (data?.deadlineAt) deadlineAt.value = data.deadlineAt
    if (data?.isSubmitted) isSubmitted.value = data.isSubmitted
    return true
  } catch { return false }
}

function clearDraft() {
  if (STORAGE_KEY.value) localStorage.removeItem(STORAGE_KEY.value)
}

// (Tuỳ chọn) Autosave lên server
async function safeAutosaveToServer() {
  try {
    await axios.post('/api/tests/autosave', {
      test_id: test.value.id,
      user_id: auth.user.id,
      answers: answers.value,
      start_at: startAt.value,
      deadline_at: deadlineAt.value
    }, { headers: { Authorization: `Bearer ${auth.token}` } })
  } catch (e) {

  }
}

// ===== Timer =====
function startTimer() {
  stopTimer()
  remainingSeconds.value = calcRemaining()
  timerId = setInterval(() => {
    remainingSeconds.value = calcRemaining()
    if (remainingSeconds.value <= 0 && !isSubmitted.value) {
        const timeSpentSeconds = Math.max(0, Math.floor((new Date() - new Date(startAt.value)) / 1000))
        localStorage.setItem(submittedKey.value, '1')
      submitTest()
    }
  }, 1000)
}
function stopTimer() {
  if (timerId) clearInterval(timerId)
  timerId = null
}

// ===== Navigation guards =====
window.addEventListener('beforeunload', (e) => {
  if (!isSubmitted.value) {
    saveDraft()
    e.preventDefault()
    e.returnValue = ''
  }
})

onBeforeRouteLeave((to, from, next) => {
  if (isSubmitted.value) return next()
  const ok = confirm('Bài làm chưa nộp. Bạn có chắc muốn rời trang?')
  if (ok) {
    saveDraft()
    next()
  } else {
    next(false)
  }
})

async function submitTest() {
  if (isSubmitted.value) return
  isSubmitted.value = true

  let correct = 0
  const resultDetails = []
  const answersPayload = []

  for (const detail of test.value.details) {
    const qid = detail.question.id
    const selected = answers.value[qid]
    const correctAnswer = detail.question.answers.find(a => a.is_correct == 1)
    const userAnswer = detail.question.answers.find(a => a.id === selected)

    const ok = selected && correctAnswer && selected === correctAnswer.id
    if (ok) correct++

    answersPayload.push({
      question_id: qid,
      answer_id: selected || null,
      score: ok ? 1 : 0
    })

    if (!ok) {
      resultDetails.push({
        questionContent: detail.question.content,
        correctAnswer: correctAnswer?.content,
        userAnswer: userAnswer?.content || '(Trống)'
      })
    }
  }

  try {
    await axios.post('/api/tests/storeUserAnswers', {
      test_id: test.value.id,
      user_id: auth.user.id,
      answers: answersPayload,
      start_at: startAt.value,
      deadline_at: deadlineAt.value
    }, { headers: { Authorization: `Bearer ${auth.token}` } })
  } catch (error) {
    console.error('Lỗi khi lưu đáp án:', error)
  } finally {
    stopTimer()
    clearDraft()
  }

  // Điều hướng theo role
  if (auth.user.role === 'Admin'|| auth.user.role === 'Teacher'|| auth.user.role === 'Student') {
    router.push({ name: 'AdminTestResult' })
  } else {
    router.push({
      name: 'TestResult',
      state: {
        score: correct,
        total: test.value.details.length,
        wrongAnswers: resultDetails
      }
    })
  }
}

// ===== Mount: fetch + khởi tạo thời gian + khôi phục draft + MathJax =====
onMounted(async () => {
  const id = route.params.id
  await store.fetchTestDetail(id)

  // Lấy duration (phút) từ test (bạn có thể đổi src field)
  const durationMin = Number(test.value?.duration || 0) || 45 // fallback 45'
  const now = new Date()

  // Khôi phục draft nếu có
  const hasDraft = loadDraft()

  // Nếu chưa có start/deadline thì tạo mới
  if (!startAt.value) startAt.value = now.toISOString()
  if (!deadlineAt.value) {
    const dl = new Date(startAt.value)
    dl.setMinutes(dl.getMinutes() + durationMin)
    deadlineAt.value = dl.toISOString()
  }

  startTimer()

  // Autosave định kỳ (local + server)
  autosaveIntervalId = setInterval(() => {
    if (!isSubmitted.value) {
      saveDraft()
      // (tuỳ chọn) sync lên server
      // safeAutosaveToServer().catch(()=>{})
    }
  }, 8000)

  await nextTick()
  const el = document.getElementById('math-container')
  if (el) renderByMathjax(el)
})

// Re-render MathJax khi test đổi
watch(test, async () => {
  await nextTick()
  const el = document.getElementById('math-container')
  if (el) renderByMathjax(el)
})

onBeforeUnmount(() => {
  stopTimer()
  if (autosaveIntervalId) clearInterval(autosaveIntervalId)
})
</script>
