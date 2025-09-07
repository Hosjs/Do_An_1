<template>
  <section class="tests-wrap">
    <header class="tests-header">
      <div class="meta">
        <span class="count-pill">{{ tests?.length || 0 }} đề</span>
      </div>
    </header>

    <!-- Loading skeleton -->
    <ul v-if="isLoading" class="test-grid">
      <li v-for="n in 6" :key="n" class="test-card skeleton">
        <div class="skeleton-line w-70"></div>
        <div class="skeleton-line w-40"></div>
        <div class="btn-row">
          <div class="btn skeleton-btn"></div>
          <div class="icon-btn skeleton-btn"></div>
        </div>
      </li>
    </ul>

    <div v-else-if="!tests || !tests.length" class="empty">
      <div class="empty-emoji">🕳️</div>
      <p>Chưa có đề thi nào.</p>
    </div>

    <!-- List -->
    <ul v-else class="test-grid">
      <li v-for="test in tests" :key="test.id" class="test-card">
        <div class="title-row">
          <h3 class="test-name ellipsis-2" :title="test.title || test.name">
            {{ test.title || test.name || 'Đề chưa đặt tên' }}
          </h3>

          <span class="subject-pill" :title="test.subject?.name || 'Thi thử'">
            {{ test.subject?.name || 'Thi thử' }}
          </span>
          <span v-if="test.reviewed" class="badge success" title="Đã chấm">Đã chấm</span>
        </div>

        <p v-if="test.description" class="desc ellipsis-2" v-html="test.description"></p>

        <div class="sub-row">
          <span class="meta-chip" v-if="test.created_at">
            Ngày tạo: {{ new Date(test.created_at).toLocaleDateString() }}
          </span>
          <span v-if="test.duration" class="meta-chip">⏱️ {{ test.duration }} phút</span>
          <span class="meta-chip">số lượng: {{ test.questions_count || test.details_count || 0 }} câu</span>
        </div>

        <div class="btn-row">
          <button class="btn primary" @click="goToTest(test.id)">
            ▶️ Bắt đầu
          </button>

          <button class="icon-btn danger" @click="askDelete(test)" title="Xóa đề" aria-label="Xóa đề">
            🗑️
          </button>
        </div>
      </li>
    </ul>

    <div v-if="showConfirm" class="modal-backdrop" @click.self="showConfirm=false">
      <div class="modal">
        <h4>🗑️ Xác nhận xóa</h4>
        <p>
          Bạn có chắc chắn muốn xóa
          <strong>{{ pendingDelete?.title || pendingDelete?.name || ('#' + pendingDelete?.id) }}</strong>?
        </p>
        <div class="modal-actions">
          <button class="btn" @click="showConfirm=false">Hủy</button>
          <button class="btn danger" @click="confirmDelete">Xóa</button>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useTestStore } from '@/store/test'
import { useAuthStore } from '@/store/auth'
import '@/assets/css/Test-List.css'

const router = useRouter()
const store = useTestStore()
const auth = useAuthStore()

const props = defineProps({
  tests: { type: Array, default: () => [] }
})

const isLoading = ref(false)
const showConfirm = ref(false)
const pendingDelete = ref(null)

// Điều hướng theo role
function goToTest(id) {
  if (auth.role === 'Admin') {
    router.push(`/admin/tests/test-begin/${id}`)
  } else if (auth.role === 'Student') {
    router.push(`/student/tests/test-begin/${id}`)
  } else {
    router.push(`/tests/test-begin/${id}`)
  }
}

function openMySolutions(testId) {
  const role = (auth.role || '').toLowerCase()
  window.open(`/${role || ''}/test/test-answer?testId=${testId}`, '_blank')
}

function askDelete(test) {
  pendingDelete.value = test
  showConfirm.value = true
}

async function confirmDelete() {
  if (!pendingDelete.value) return
  try {
    isLoading.value = true
    await store.deleteTest(pendingDelete.value.id)
    await store.fetchTests()
  } catch (err) {
    console.error(err)
    alert('Xóa đề thất bại.')
  } finally {
    isLoading.value = false
    showConfirm.value = false
    pendingDelete.value = null
  }
}
</script>

<style scoped>
/* 2 dòng, ellipsis mượt cho title & mô tả */
.ellipsis-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* mô tả nhạt màu hơn 1 chút */
.desc {
  color: #6b7280;
  margin: 4px 0 6px;
}

/* badge trạng thái đơn giản */
.badge.success {
  background: #dcfce7;
  color: #166534;
  border-radius: 999px;
  padding: 2px 8px;
  font-size: 12px;
  margin-left: 6px;
}
</style>
