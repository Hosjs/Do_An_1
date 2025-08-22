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

    <!-- Empty state -->
    <div v-else-if="!tests || !tests.length" class="empty">
      <div class="empty-emoji">🕳️</div>
      <p>Chưa có đề thi nào.</p>
    </div>

    <!-- List -->
    <ul v-else class="test-grid">
      <li
        v-for="test in tests"
        :key="test.id"
        class="test-card"
      >
        <div class="title-row">
          <h3 class="test-name" :title="test.name">{{ test.name }}</h3>
          <span class="subject-pill" :title="test.subject?.name || 'Không rõ'">
            {{ test.subject?.name || 'Thi thử' }}
          </span>
        </div>

        <div class="sub-row">
          <span class="meta-chip" v-if="test.created_at">
            📅 {{ new Date(test.created_at).toLocaleDateString() }}
          </span>
          <span v-if="test.duration" class="meta-chip">⏱️ {{ test.duration }} phút</span>
          <span v-if="test.questions_count" class="meta-chip">❓ {{ test.questions_count }} câu</span>
        </div>

        <div class="btn-row">
          <button class="btn primary" @click="goToTest(test.id)">
            ▶️ Bắt đầu
          </button>

          <button
            class="icon-btn danger"
            @click="askDelete(test)"
            title="Xóa đề"
            aria-label="Xóa đề"
          >
            🗑️
          </button>
        </div>
      </li>
    </ul>

    <!-- Modal xác nhận xóa -->
    <div v-if="showConfirm" class="modal-backdrop" @click.self="showConfirm=false">
      <div class="modal">
        <h4>🗑️ Xác nhận xóa</h4>
        <p>Bạn có chắc chắn muốn xóa <strong>{{ pendingDelete?.name }}</strong>?</p>
        <div class="modal-actions">
          <button class="btn" @click="showConfirm=false">Hủy</button>
          <button class="btn danger" @click="confirmDelete">Xóa</button>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useTestStore } from '@/store/test'
import { useAuthStore } from '@/store/auth'

const router = useRouter()
const store = useTestStore()
const auth = useAuthStore()

const props = defineProps({
  tests: {
    type: Array,
    default: () => []
  }
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
    // fallback
    router.push(`/tests/test-begin/${id}`)
  }
}

// Modal xác nhận xoá
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

onMounted(() => {
  
})
</script>

<style scoped>
/* Layout tổng thể */
.tests-wrap {
  display: grid;
  gap: 14px;
}

.tests-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.tests-header h2 {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 700;
}

.meta .count-pill {
  background: #eef2ff;
  color: #3730a3;
  padding: 6px 10px;
  border-radius: 999px;
  font-size: 0.85rem;
  font-weight: 600;
}

/* Grid card */
.test-grid {
  display: grid;
  gap: 14px;
  grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
  padding: 0;
  list-style: none;
}

.test-card {
  border: 1px solid #e5e7eb;
  border-radius: 14px;
  padding: 14px;
  background: #fff;
  display: grid;
  gap: 10px;
  box-shadow: 0 1px 0 rgba(16, 24, 40, 0.04);
  transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
}

.test-card:hover {
  transform: translateY(-2px);
  border-color: #c7d2fe;
  box-shadow: 0 6px 16px rgba(59, 130, 246, 0.12);
}

/* Title + Subject */
.title-row {
  display: flex;
  align-items: start;
  justify-content: space-between;
  gap: 10px;
}

.test-name {
  margin: 0;
  font-size: 1rem;
  font-weight: 700;
  line-height: 1.35;
  color: #111827;
  max-width: 75%;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.subject-pill {
  background: #f0fdf4;
  color: #166534;
  border: 1px solid #bbf7d0;
  padding: 4px 8px;
  border-radius: 999px;
  font-size: 0.8rem;
  font-weight: 600;
  flex-shrink: 0;
}

/* Meta chips */
.sub-row {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}

.meta-chip {
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  color: #374151;
  padding: 4px 8px;
  border-radius: 8px;
  font-size: 0.8rem;
}

/* Buttons */
.btn-row {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-top: 6px;
}

.btn {
  appearance: none;
  border: 1px solid #e5e7eb;
  background: #fff;
  color: #111827;
  padding: 8px 12px;
  border-radius: 10px;
  font-weight: 700;
  cursor: pointer;
  transition: background .15s ease, border-color .15s ease, transform .05s ease;
}

.btn:hover { background: #f3f4f6; }
.btn:active { transform: translateY(1px); }

.btn.primary {
  background: #2563eb;
  border-color: #1d4ed8;
  color: #fff;
}
.btn.primary:hover { background: #1d4ed8; }

.icon-btn {
  border: 1px solid transparent;
  background: #f9fafb;
  color: #111827;
  padding: 8px 10px;
  border-radius: 10px;
  cursor: pointer;
  transition: background .15s ease, transform .05s ease;
}
.icon-btn:hover { background: #f3f4f6; }
.icon-btn:active { transform: translateY(1px); }

.icon-btn.danger {
  color: #b91c1c;
  background: #fef2f2;
  border: 1px solid #fee2e2;
}
.icon-btn.danger:hover {
  background: #fee2e2;
}

/* Empty state */
.empty {
  border: 1px dashed #d1d5db;
  border-radius: 12px;
  padding: 28px 16px;
  text-align: center;
  color: #6b7280;
}
.empty-emoji { font-size: 2rem; margin-bottom: 8px; }

/* Modal */
.modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(17, 24, 39, .5);
  display: grid;
  place-items: center;
  z-index: 50;
}
.modal {
  width: min(92vw, 420px);
  background: #fff;
  border-radius: 14px;
  padding: 16px;
  box-shadow: 0 20px 60px rgba(0,0,0,.25);
  border: 1px solid #e5e7eb;
}
.modal h4 { margin: 0 0 6px; font-size: 1.05rem; }
.modal p { margin: 0 0 14px; color: #374151; }
.modal-actions {
  display: flex; justify-content: flex-end; gap: 8px;
}

/* Skeleton */
.skeleton {
  position: relative;
  overflow: hidden;
}
.skeleton::after {
  content: "";
  position: absolute;
  inset: 0;
  background: linear-gradient(90deg, transparent, rgba(0,0,0,.04), transparent);
  animation: shimmer 1.2s infinite;
}
.skeleton-line {
  height: 14px;
  background: #f3f4f6;
  border-radius: 6px;
}
.skeleton-line.w-70 { width: 70%; }
.skeleton-line.w-40 { width: 40%; }
.skeleton-btn {
  height: 36px; width: 96px; border-radius: 10px; background: #f3f4f6;
}

@keyframes shimmer {
  0% { transform: translateX(-120%); }
  100% { transform: translateX(120%); }
}
</style>
