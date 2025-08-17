<template>
  <div class="gtf-page">
    <div class="gtf-card">
      <div class="gtf-header">
      </div>

      <div class="structure-list">
        <div v-for="(item, i) in structure" :key="i" class="structure-item">
          <div class="field">
            <label for="subject">Môn học</label>
            <select v-model="item.subject_id" aria-label="Chọn môn học">
              <option disabled value="">Chọn môn học</option>
              <option v-for="s in subjects" :key="s.id" :value="s.id">{{ s.name }}</option>
            </select>
          </div>

          <div class="field">
            <label for="type">Loại câu hỏi</label>
            <select v-model="item.type_id" aria-label="Chọn loại câu hỏi">
              <option disabled value="">Loại câu hỏi</option>
              <option v-for="t in types" :key="t.id" :value="t.id">{{ t.name }}</option>
            </select>
          </div>

          <div class="field">
            <label for="quantity">Số lượng</label>
            <input
              type="number"
              v-model.number="item.quantity"
              placeholder="Số lượng"
              min="1"
              inputmode="numeric"
              aria-label="Số lượng câu hỏi"
            />
          </div>
        </div>
      </div>

      <div class="buttons">
        <button class="btn ghost" type="button" @click="addBlock">+ Thêm khối</button>
        <div class="spacer"></div>
        <button class="btn" type="button" @click="submit">Tạo đề</button>
        <button class="btn danger" type="button" @click="resetTest">🗑️ Xóa đề</button>
      </div>
    </div>

    <div v-if="warnings.length" class="gtf-card warn">
      <h4>Cảnh báo</h4>
      <ul class="warn-list">
        <li v-for="w in warnings" :key="w">{{ w }}</li>
      </ul>
    </div>

    <div v-if="questions.length" class="gtf-card result" id="math-container">
      <div class="gtf-header small">
        <h4>Danh sách câu hỏi</h4>
        <span class="badge">{{ questions.length }}</span>
      </div>
      <ol class="q-list">
        <li v-for="(q, i) in questions" :key="q.id">
          <span class="q-index">{{ i + 1 }}.</span>
          <span class="q-content" v-html="q.content"></span>
        </li>
      </ol>
    </div>
  </div>
</template>

<script setup>
import { onMounted, watch } from 'vue'
import { renderByMathjax } from 'mathjax-vue3'
import { useGenerateTest } from '@/store/useGenerateTest.js'

function resetTest() {
  structure.splice(0, structure.length, { subject_id: '', type_id: '', quantity: 1 })
  questions.value = []
  warnings.value = []
}

function removeBlock(i) {
  if (structure.length > 1) structure.splice(i, 1)
}

const {
  structure, subjects, types,
  warnings, questions,
  fetchData, addBlock, submit
} = useGenerateTest()

onMounted(() => {
  fetchData()
  setTimeout(() => {
    const el = document.getElementById('math-container')
    if (el) renderByMathjax(el)
  }, 0)
})

watch(questions, () => {
  setTimeout(() => {
    const el = document.getElementById('math-container')
    if (el) renderByMathjax(el)
  }, 0)
})
</script>
<style scoped>
/* ===== Layout ===== */
.gtf-page {
  display: grid;
  gap: 16px;
}

.gtf-card {
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 14px;
  padding: 16px;
  box-shadow: 0 1px 0 rgba(16, 24, 40, .02);
}

/* Header */
.gtf-header {
  display: flex;
  align-items: baseline;
  gap: 8px;
  margin-bottom: 12px;
}
.gtf-header h2, .gtf-header h4 { margin: 0; }
.gtf-header.small { justify-content: space-between; }
.gtf-subtitle {
  color: #64748b;
  font-size: .95rem;
}

/* Structure list */
.structure-list {
  display: grid;
  gap: 10px;
}
.structure-item {
  display: grid;
  grid-template-columns: 1fr 1fr minmax(120px, 160px) auto;
  gap: 10px;
  padding: 10px;
  border: 1px dashed #e5e7eb;
  border-radius: 12px;
  background: #fafafa;
}
@media (max-width: 900px) {
  .structure-item {
    grid-template-columns: 1fr 1fr 1fr auto;
  }
}
@media (max-width: 640px) {
  .structure-item {
    grid-template-columns: 1fr;
  }
}

/* Fields */
.field { display: grid; gap: 6px; }
.field label {
  font-size: .85rem;
  color: #475569;
  font-weight: 600;
}
select, input[type="number"] {
  width: 100%;
  height: 40px;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  background: #fff;
  padding: 0 12px;
  font-size: .95rem;
  outline: none;
  transition: border-color .15s ease, box-shadow .15s ease;
}
select:focus, input[type="number"]:focus {
  border-color: #22c55e;
  box-shadow: 0 0 0 3px rgba(34, 197, 94, .15);
}

/* Remove block button */
.icon-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  height: 40px;
  min-width: 40px;
  padding: 0 10px;
  border-radius: 10px;
  border: 1px solid #e5e7eb;
  background: #fff;
  cursor: pointer;
  transition: background .15s ease, border-color .15s ease, transform .05s ease;
}
.icon-btn:hover { background: #f8fafc; }
.icon-btn:active { transform: scale(.98); }
.icon-btn.remove { color: #ef4444; border-color: #fee2e2; }

/* Buttons group */
.buttons {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-top: 12px;
}
.spacer { flex: 1; }

.btn {
  height: 40px;
  padding: 0 14px;
  border-radius: 10px;
  border: 1px solid #16a34a;
  background: #16a34a;
  color: #fff;
  font-weight: 600;
  cursor: pointer;
  transition: filter .15s ease, transform .05s ease;
}
.btn:hover { filter: brightness(1.03); }
.btn:active { transform: translateY(1px); }

.btn.ghost {
  background: #fff;
  color: #16a34a;
  border-color: #a7f3d0;
}
.btn.danger {
  background: #ef4444;
  border-color: #ef4444;
}

/* Warnings */
.gtf-card.warn {
  border-color: #fde68a;
  background: #fffbeb;
}
.warn-list {
  margin: 8px 0 0;
  padding-left: 18px;
  color: #92400e;
}

/* Result list */
.badge {
  display: inline-flex;
  align-items: center;
  height: 24px;
  padding: 0 8px;
  border-radius: 999px;
  background: #f1f5f9;
  font-size: .8rem;
  color: #0f172a;
}
.q-list {
  margin: 8px 0 0;
  padding-left: 20px;
  display: grid;
  gap: 10px;
}
.q-list li {
  background: #fafafa;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  padding: 10px 12px;
  list-style: none;
}
.q-index {
  font-weight: 700;
  color: #0f172a;
  margin-right: 6px;
}
.q-content { display: inline; }

/* Small polishing */
#math-container { scroll-margin-top: 24px; }
</style>