<template>
  <div class="test-preview" id="math-container">
    <h2>📘 Đề thi</h2>

    <div v-for="(detail, index) in test.details" :key="detail.id" class="question-block">
      <p class="question-index">Câu {{ index + 1 }}:</p>

      <!-- ✅ Hiển thị nội dung bằng v-html -->
      <p v-html="detail.question.content"></p>

      <div v-if="detail.question.correct_answer" class="correct-answer">
        <strong>Đáp án đúng:</strong>
        <p v-html="detail.question.correct_answer"></p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, watch } from 'vue'
import { renderByMathjax } from 'mathjax-vue3'

const props = defineProps({
  test: Object
})

// Gọi lại render MathJax sau khi component mounted
onMounted(() => {
  setTimeout(() => {
    const el = document.getElementById('math-container')
    if (el) renderByMathjax(el)
  }, 0)
})

// Nếu test thay đổi (khi load lại đề khác)
watch(() => props.test, () => {
  setTimeout(() => {
    const el = document.getElementById('math-container')
    if (el) renderByMathjax(el)
  }, 0)
})
</script>

<style scoped>
.test-preview {
  padding: 2rem;
  font-family: 'Segoe UI', sans-serif;
  background-color: #ffffff;
}

h2 {
  font-size: 1.5rem;
  font-weight: 600;
  margin-bottom: 1.5rem;
}

.question-block {
  margin-bottom: 2rem;
  padding: 1rem 1.5rem;
  background: #f5faff;
  border-left: 4px solid #0d6efd;
  border-radius: 6px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
}

.question-index {
  font-weight: bold;
  color: #333;
  margin-bottom: 0.75rem;
}

.correct-answer {
  margin-top: 1rem;
  padding: 0.5rem 1rem;
  background-color: #e6f4ea;
  color: #1d7a33;
  border-left: 3px solid #2ecc71;
  border-radius: 4px;
}
</style>
