<template>
  <div class="page-wrap">
    <header class="page-header">
      <div>
        <h1 class="title">Quản lý đề thi</h1>
        <p class="sub">{{ tests.length }} đề · Giao diện quản trị riêng</p>
      </div>
      <div class="row">
        <input v-model="q" class="input" type="search" placeholder="Tìm theo tên/mô tả…" />
        <button class="btn" @click="refresh" :disabled="loading">
          {{ loading ? 'Đang tải…' : 'Làm mới' }}
        </button>
      </div>
    </header>

    <!-- Empty -->
    <div v-if="!loading && !filtered.length" class="empty">
      <div class="empty-emoji">📭</div>
      <p>Chưa có đề thi nào.</p>
    </div>

    <!-- Table list (different from TestList.vue) -->
    <div v-else class="card">
      <table class="table">
        <thead>
          <tr>
            <th style="width:52px">#</th>
            <th>Tên đề</th>
            <th style="width:140px">Môn/Loại</th>
            <th style="width:110px">Số câu</th>
            <th style="width:110px">Thời lượng</th>
            <th style="width:140px">Ngày tạo</th>
            <th style="width:220px">Hành động</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(t, idx) in filtered" :key="t.id">
            <td>{{ idx + 1 }}</td>
            <td>
              <div class="name">{{ t.title || t.name || 'Đề chưa đặt tên' }}</div>
              <div v-if="t.description" class="desc" v-html="truncate(t.description)"></div>
            </td>
            <td>
              <span class="pill">{{ t.subject?.name || 'Thi thử' }}</span>
            </td>
            <td>{{ t.details_count || t.questions_count || 0 }}</td>
            <td>{{ t.duration ? t.duration + ' phút' : '—' }}</td>
            <td>{{ formatDate(t.created_at) }}</td>
            <td>
              <div class="actions">
                <button class="btn" @click="viewDetail(t.id)">📋 Xem danh sách câu hỏi</button>
                <button class="btn" @click="goEdit(t.id)">✏️ Chỉnh sửa</button>
                <button class="btn danger" @click="askDelete(t)">🗑️ Xóa</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal: danh sách câu hỏi trong đề -->
    <div v-if="showModal && selectedTest" class="modal-backdrop" @click.self="showModal=false">
      <div class="modal">
        <div class="row" style="justify-content:space-between; align-items:center; margin-bottom:8px;">
          <h4 style="margin:0">📄 Câu hỏi của: {{ selectedTest.title || ('Đề #' + selectedTest.id) }}</h4>
          <div class="row">
            <button class="btn" @click="goEdit(selectedTest.id)">✏️ Chỉnh sửa</button>
            <button class="btn" @click="showModal=false">Đóng</button>
          </div>
        </div>
        <TestDetail :test="selectedTest" />
      </div>
    </div>

    <!-- Confirm delete -->
    <div v-if="confirming" class="modal-backdrop" @click.self="confirming=false">
      <div class="modal">
        <h4>🗑️ Xác nhận xóa</h4>
        <p>
          Bạn có chắc muốn xóa
          <strong>{{ target?.title || target?.name || ('#' + target?.id) }}</strong>?
        </p>
        <div class="modal-actions">
          <button class="btn" @click="confirming=false">Hủy</button>
          <button class="btn danger" :disabled="loading" @click="confirmDelete">Xóa</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useTestStore } from '@/store/test'
import TestDetail from '@/components/TestGenerate/TestDetail.vue'

const router = useRouter()
const store = useTestStore()
const loading = ref(false)
const q = ref('')
const confirming = ref(false)
const target = ref(null)
const showModal = ref(false)

const tests = computed(() => store.tests)
const selectedTest = computed(() => store.selectedTest)
const filtered = computed(() => {
  const term = q.value.trim().toLowerCase()
  if (!term) return tests.value
  return tests.value.filter(t =>
    (t.title || t.name || '').toLowerCase().includes(term) ||
    (t.description || '').toLowerCase().includes(term)
  )
})

function formatDate(d) {
  if (!d) return '—'
  try { return new Date(d).toLocaleDateString() } catch { return '—' }
}
function truncate(html, max = 120) {
  const text = String(html).replace(/<[^>]*>/g, '')
  return text.length > max ? text.slice(0, max) + '…' : text
}

async function refresh() {
  loading.value = true
  try {
    await store.fetchTests()
  } finally {
    loading.value = false
  }
}

async function viewDetail(id) {
  loading.value = true
  try {
    await store.fetchTestDetail(id)
    if (store.selectedTest) showModal.value = true
  } finally {
    loading.value = false
  }
}

function goEdit(id) {
  router.push({ name: 'AdminQuestionsManage', params: { id } })
}

function askDelete(t) {
  target.value = t
  confirming.value = true
}

async function confirmDelete() {
  if (!target.value) return
  loading.value = true
  try {
    await store.deleteTest(target.value.id)
    await store.fetchTests()
    confirming.value = false
    target.value = null
  } catch (e) {
    alert('Xóa đề thất bại')
    console.error(e)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  refresh()
})
</script>

<style scoped src="@/assets/css/TestManagement.css"/>
