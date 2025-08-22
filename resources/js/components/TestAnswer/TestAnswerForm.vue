<template>
  <div id="test-answer" class="answer-page">
    <div class="page-header">
      <h1>📖 Cách giải & Đáp án của bạn</h1>
      <div class="subtitle" v-if="testTitle">Bài: {{ testTitle }}</div>

      <div class="actions">
        <input v-model="keyword" type="text" class="search" placeholder="Tìm theo nội dung câu hỏi…" />
        <label class="checkbox">
          <input type="checkbox" v-model="wrongOnly" />
          Chỉ hiện câu làm sai
        </label>
        <label class="checkbox">
          <input type="checkbox" v-model="onlyHasSolution" />
          Chỉ hiện câu có cách giải
        </label>
        <button class="btn" @click="expandAll(true)">Mở tất cả</button>
        <button class="btn outline" @click="expandAll(false)">Đóng tất cả</button>
      </div>
    </div>
    <div v-if="loading" class="loading">Đang tải dữ liệu…</div>
    <div v-else-if="error" class="error">{{ error }}</div>

    <div v-else class="qa-list">
      <div v-for="(q, idx) in filtered" :key="q.question_id" class="qa-card">
        <div class="qa-header" @click="q._open = !q._open">
          <div class="left">
            <span class="badge">Câu {{ idx + 1 }}</span>
            <span class="status" :class="q.is_correct ? 'correct' : 'wrong'">
              {{ q.is_correct ? 'Đúng' : 'Sai' }}
            </span>
          </div>
          <span class="toggle">{{ q._open ? 'Ẩn' : 'Xem' }}</span>
        </div>

        <transition name="fade">
          <div v-show="q._open" class="qa-body">
            <div class="q-content" v-html="q.content"></div>

            <!-- Choices (nếu có) -->
            <div v-if="q.choices && q.choices.length" class="choices">
              <div class="title">Các lựa chọn:</div>
              <ul>
                <li v-for="c in q.choices" :key="c.id" :class="[{ correct: c.is_correct }]">
                  <span v-html="c.content"></span>
                  <strong v-if="c.is_correct"> (Đúng)</strong>
                </li>
              </ul>
            </div>

            <!-- Đáp án đúng (cho tự luận/điền khuyết) -->
            <div class="correct-block" v-if="!q.choices || !q.choices.length">
              <div class="title">Đáp án đúng:</div>
              <div v-if="Array.isArray(q.correct_answer)">
                <ul class="correct-list">
                  <li v-for="(v, i) in q.correct_answer" :key="i" v-html="v"></li>
                </ul>
              </div>
              <div v-else v-html="q.correct_answer || '—'"></div>
            </div>

            <!-- Cách giải -->
            <div class="solution" v-if="q.solution">
              <div class="title">Cách giải:</div>
              <div v-html="q.solution"></div>
            </div>

            <!-- Ảnh / Công thức (nếu có) -->
            <div v-if="q.image_url" class="q-image">
              <img :src="asset(q.image_url)" alt="image" />
            </div>
            <div v-if="q.formula_latex" class="q-formula" v-html="q.formula_latex"></div>
          </div>
        </transition>
      </div>

      <div v-if="!filtered.length" class="empty">Không có câu nào phù hợp bộ lọc.</div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, nextTick } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import { renderByMathjax } from 'mathjax-vue3'
import { useAuthStore } from '@/store/auth'

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()

const testId = computed(() => Number(route.query.testId || route.params.id || 0))

const loading = ref(true)
const error = ref('')
const testTitle = ref('')
const rows = reactive([])

const keyword = ref('')
const wrongOnly = ref(true)        // 👉 mặc định hiện câu sai
const onlyHasSolution = ref(false) // hiện câu có cách giải

function asset(path) {
  if (!path) return ''
  if (path.startsWith('http')) return path
  return '/' + path.replace(/^\/+/, '')
}

async function fetchData() {
  loading.value = true
  error.value = ''
  try {
    const res = await axios.get(`/api/tests/${testId.value}/user-solutions`, {
      params: { includeChoices: 1 },
      headers: auth.token ? { Authorization: `Bearer ${auth.token}` } : {}
    })
    testTitle.value = res.data?.title || ''
    const qs = Array.isArray(res.data?.questions) ? res.data.questions : []
    rows.splice(0)
    for (const q of qs) rows.push({ ...q, _open: !q.is_correct }) // mở sẵn câu sai
  } catch (e) {
    console.error(e)
    error.value = 'Không tải được dữ liệu.'
  } finally {
    loading.value = false
    await nextTick()
    const el = document.getElementById('test-answer')
    if (el) renderByMathjax(el)
  }
}

const filtered = computed(() => {
  let arr = rows
  if (wrongOnly.value) arr = arr.filter(q => q.is_correct === false)
  if (onlyHasSolution.value) arr = arr.filter(q => !!q.solution)
  if (keyword.value.trim()) {
    const kw = keyword.value.toLowerCase()
    arr = arr.filter(q => (q.content || '').toLowerCase().includes(kw))
  }
  return arr
})

function expandAll(open) { rows.forEach(r => (r._open = open)) }
onMounted(fetchData)
</script>
