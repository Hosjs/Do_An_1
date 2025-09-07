<template>
  <div class="gtf-page">
    <div class="gtf-card">
      <!-- Thông tin bài thi (theo chiều dọc) -->
      <div class="gtf-header column">
        <div class="field wide">
          <label for="title">Tên bài thi</label>
          <input
            id="title"
            type="text"
            v-model="title"
            placeholder="Nhập tên bài thi..."
            aria-label="Tên bài thi"
            class="input-large"
          />
        </div>
        <div class="field wide">
          <label for="description">Mô tả</label>
          <textarea
            id="description"
            v-model="description"
            placeholder="Mô tả ngắn về bài thi..."
            rows="3"
            aria-label="Mô tả bài thi"
            class="textarea-large"
          ></textarea>
        </div>
      </div>

      <!-- Danh sách khối -->
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

          <!-- Nút xóa block ngay trong block -->
          <div class="field">
            <button class="btn danger small" type="button" @click="removeBlock(i)">🗑️ Xóa khối</button>
          </div>
        </div>
      </div>

      <!-- Nút thao tác -->
      <div class="buttons">
        <button class="btn ghost" type="button" @click="addBlock">+ Thêm khối</button>

        <div class="spacer"></div>
        <button class="btn" type="button" @click="handleSubmit">Tạo đề</button>
        <button class="btn danger" type="button" @click="resetTest">🗑️ Xóa đề</button>
      </div>
    </div>

    <div v-if="warnings.length" class="gtf-card warn">
      <h4>Cảnh báo</h4>
      <ul class="warn-list">
        <li v-for="w in warnings" :key="w">{{ w }}</li>
      </ul>
    </div>

    <!-- GIỮ NGUYÊN phần danh sách câu hỏi -->
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
import { ref, onMounted, watch } from 'vue'
import '@/assets/css/generate-test.css'
import { renderByMathjax } from 'mathjax-vue3'
import { useGenerateTest } from '@/store/useGenerateTest.js'

const {
  structure, subjects, types,
  warnings, questions,
  fetchData, addBlock, submit,title,description
} = useGenerateTest()

function resetTest() {
  if (Array.isArray(structure)) {
    structure.splice(0, structure.length, { subject_id: '', type_id: '', quantity: 1 })
  } else if (structure.value) {
    structure.value.splice(0, structure.value.length, { subject_id: '', type_id: '', quantity: 1 })
  }
  questions.value = []
  warnings.value = []
  testTitle.value = ''
  testDescription.value = ''
}

function removeBlock(i) {
  if (Array.isArray(structure)) {
    if (structure.length > 1) structure.splice(i, 1)
  } else if (structure.value) {
    if (structure.value.length > 1) structure.value.splice(i, 1)
  }
}

function handleSubmit() {
  submit({
    title,
    description,
    structure: structure.value
  })
}

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