<template>
  <div class="page-wrap">
    <header class="page-header">
      <div>
        <h1 class="title">Quản lý câu hỏi</h1>
        <p class="sub">Đề thi #{{ testId }} · {{ questions.length }} câu hỏi</p>
      </div>
      <div class="row">
        <input v-model="q" class="input" type="search" placeholder="Tìm theo nội dung…" />
        <button class="btn" @click="refresh" :disabled="loading">
          {{ loading ? 'Đang tải…' : 'Làm mới' }}
        </button>
        <button class="btn primary" @click="openAdd">➕ Thêm câu hỏi</button>
        <button class="btn" @click="goBack">← Quay lại</button>
      </div>
    </header>

    <!-- Empty -->
    <div v-if="!loading && !filtered.length" class="empty">
      <div class="empty-emoji">📭</div>
      <p>Chưa có câu hỏi nào.</p>
    </div>

    <!-- Table list -->
    <div v-else class="card content-scroll">
      <table class="table">
        <thead>
          <tr>
            <th style="width:52px">#</th>
            <th>Nội dung</th>
            <th style="width:160px">Loại</th>
            <th style="width:100px">Môn học</th>
            <th style="width:160px">Ngày tạo</th>
            <th style="width:220px">Hành động</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(qst, idx) in filtered" :key="qst.id">
            <td>{{ idx + 1 }}</td>
            <td>
              <div class="name">{{ truncate(qst.content) }}</div>
            </td>
            <td>
              <span class="pill">{{ qst.type?.name || '—' }}</span>
            </td>
            <td>{{ qst.subject?.name || '—' }}</td>
            <td>{{ formatDate(qst.created_at) }}</td>
            <td>
              <div class="actions">
                <button class="btn" @click="openEdit(qst)">✏️ Sửa</button>
                <button class="btn danger" @click="askDelete(qst)">🗑️ Xóa</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal thêm/sửa -->
    <div v-if="showForm" class="modal-backdrop" @click.self="closeForm">
      <div class="modal">
        <h4 style="margin:0">{{ editing ? '✏️ Chỉnh sửa câu hỏi' : '➕ Thêm câu hỏi' }}</h4>
        <div class="form">
          <textarea v-model="form.content" placeholder="Nhập nội dung câu hỏi…" />

          <select v-model="form.type_id" aria-label="Chọn loại câu hỏi">
            <option disabled value="">Loại câu hỏi</option>
            <option v-for="t in types" :key="t.id" :value="t.id">{{ t.name }}</option>
          </select>
          <select v-model="form.subject_id" aria-label="Chọn môn học">
            <option disabled value="">Chọn môn học</option>
            <option v-for="s in subjects" :key="s.id" :value="s.id">{{ s.name }}</option>
          </select>
          </div>
        <div class="modal-actions">
          <button class="btn" @click="closeForm">Hủy</button>
          <button class="btn primary" :disabled="loading" @click="saveQuestion">
            {{ loading ? 'Đang lưu…' : 'Lưu' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Confirm delete -->
    <div v-if="confirming" class="modal-backdrop" @click.self="confirming=false">
      <div class="modal">
        <h4>🗑️ Xác nhận xóa</h4>
        <p>
          Bạn có chắc muốn xóa câu hỏi:
          <strong>{{ truncate(target?.content, 80) }}</strong>?
        </p>
        <div class="modal-actions">
          <button class="btn" @click="confirming=false">Hủy</button>
          <button class="btn danger" :disabled="loading" @click="confirmDelete">Xóa</button>
        </div>
      </div>
    </div>
  </div>
</template>
//
<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useTestStore } from '@/store/test'
import { useAuthStore } from '../../store/auth'
import axios from 'axios'

const auth = useAuthStore()
const subjects = ref([])
const types = ref([])

async function fetchSubjects() {
  const res = await axios.get('/api/subjects', {
    headers: {
      Authorization: `Bearer ${auth.token}`,
    },
  })
  subjects.value = res.data
}
async function fetchTypes() {
  const res = await axios.get('/api/question-types', {
    headers: {
      Authorization: `Bearer ${auth.token}`,
    },
  })
  types.value = res.data
}

onMounted(() => {
  fetchSubjects()
  fetchTypes()
  refresh()
})

const route = useRoute()
const router = useRouter()
const store = useTestStore()

const testId = computed(() => route.params.id || route.params.testId)
const loading = ref(false)
const q = ref('')
const confirming = ref(false)
const target = ref(null)
const form = ref({ id: null, content: '', type_id: '', subject_id: '' })
const showForm = ref(false)
const editing = ref(false)

const questions = computed(() => store.questions)
const filtered = computed(() => {
  const term = q.value.trim().toLowerCase()
  if (!term) return questions.value
  return questions.value.filter(qst =>
    (qst.content || '').toLowerCase().includes(term)
  )
})

function formatDate(d) {
  if (!d) return '—'
  try { return new Date(d).toLocaleDateString() } catch { return '—' }
}
function truncate(text, max = 100) {
  if (!text) return ''
  const s = String(text)
  return s.length > max ? s.slice(0, max) + '…' : s
}

async function refresh() {
  loading.value = true
  try {
    await store.fetchQuestions(testId.value)
  } finally {
    loading.value = false
  }
}

function goBack() {
  router.push({ name: 'TestManagement' })
}

function openAdd() {
  editing.value = false
  form.value = { id: null, content: '', type_id: '', subject_id: '' }
  showForm.value = true
}
function openEdit(qst) {
  editing.value = true
  form.value = {
    id: qst.id,
    content: qst.content,
    type_id: qst.type?.id || '',
    subject_id: qst.subject?.id || ''
  }
  showForm.value = true
}
function closeForm() {
  showForm.value = false
}

async function saveQuestion() {
  loading.value = true
  try {
    if (editing.value) {
      await store.updateQuestion(form.value.id, form.value)
    } else {
      await store.addQuestion(testId.value, { ...form.value, test_id: testId.value })
    }
    await refresh()
    closeForm()
  } catch (e) {
    alert('Lưu câu hỏi thất bại')
    console.error(e)
  } finally {
    loading.value = false
  }
}

function askDelete(qst) {
  target.value = qst
  confirming.value = true
}
async function confirmDelete() {
  if (!target.value) return
  loading.value = true
  try {
    await store.deleteQuestion(target.value.id)
    await refresh()
    confirming.value = false
    target.value = null
  } catch (e) {
    alert('Xóa thất bại')
    console.error(e)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  refresh()
})
</script>

<style scoped>
.page-wrap {
  display: flex;
  flex-direction: column;
  height: 100vh;
  padding: 16px;
}
.page-header {
  display:flex;
  align-items:center;
  justify-content:space-between;
  margin-bottom:12px;
  gap:8px;
  flex-wrap:wrap;
}
.title { margin:0; font-size:20px; }
.sub { margin:2px 0 0; color:#6b7280; }
.row { display:flex; gap:8px; align-items:center; }
.btn { padding:8px 12px; border:1px solid #e5e7eb; border-radius:8px; background:#fff; cursor:pointer; }
.btn.primary { background:#2563eb; color:#fff; }
.btn.danger { background:#dc2626; color:#fff; }
.input { padding:6px 10px; border:1px solid #e5e7eb; border-radius:6px; }
.card { background:#fff; border-radius:8px; border:1px solid #e5e7eb; overflow:hidden; }
.table { width:100%; border-collapse:collapse; }
.table th, .table td { padding:8px 12px; border-bottom:1px solid #e5e7eb; text-align:left; }
.name { font-weight:500; }
.pill { display:inline-block; background:#f3f4f6; padding:2px 6px; border-radius:6px; font-size:12px; }
.actions { display:flex; gap:6px; }
.empty { text-align:center; padding:40px; color:#6b7280; }
.empty-emoji { font-size:40px; margin-bottom:8px; }
.modal-backdrop {
  position:fixed; inset:0; background:rgba(0,0,0,0.4);
  display:flex; align-items:center; justify-content:center;
}
.modal {
  background:#fff; padding:16px; border-radius:8px; width:500px; max-width:90%;
}
.modal-actions { display:flex; justify-content:flex-end; gap:8px; margin-top:12px; }
.form { display:flex; flex-direction:column; gap:8px; margin:12px 0; }
.form textarea, .form input {
  padding:8px; border:1px solid #e5e7eb; border-radius:6px; width:100%;
}
.content-scroll {
  flex: 1;
  overflow-y: auto;
}
</style>
