<template>
  <div>
    <div class="result-summary">
      🎯 Bạn đúng {{ score }}/{{ total }} câu – điểm: {{ ((score / total) * 10).toFixed(2) }}/10
    </div>
    <div v-if="wrongAnswers.length" class="wrong-answers-container">
      <h2 class="font-bold text-lg mb-4 text-red-600">Các câu làm sai:</h2>
      <div v-for="(item, idx) in wrongAnswers" :key="idx" class="answer-card">
        <p class="question-title">Câu hỏi:</p>
        <div v-html="item.questionContent"></div>
        <p class="correct-answer">Đáp án đúng: <span v-html="item.correctAnswer"></span></p>
        <p class="user-answer">Bạn chọn: <span v-html="item.userAnswer"></span></p>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from 'axios'
import { renderByMathjax } from 'mathjax-vue3'


const saveScore = async (testId, userId) => {
  const finalScore = ((props.score / props.total) * 10).toFixed(2)

  await axios.put(`/api/tests/${testId}/score`, {
    user_id: userId,
    score: finalScore
  })
}

const props = defineProps({
  score: Number,
  total: Number,
  wrongAnswers: Array
})
</script>
<style>
.result-container {
  padding: 2rem;
  font-size: 1.2rem;
}
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
  margin-bottom: 2rem;
}

.wrong-answers-container {
  max-width: 900px;
  margin: 0 auto;
}

.answer-card {
  background: #fff;
  border-left: 6px solid #ef4444;
  border-radius: 8px;
  padding: 1.25rem;
  margin-bottom: 1.5rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  transition: transform 0.2s ease-in-out;
}

.answer-card:hover {
  transform: translateY(-2px);
}

.answer-card p {
  margin: 0.3rem 0;
  font-size: 1rem;
}

.answer-card .question-title {
  font-weight: bold;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.answer-card .correct-answer {
  color: #16a34a; /* xanh lá */
  font-weight: bold;
}

.answer-card .user-answer {
  color: #dc2626; /* đỏ */
  font-weight: bold;
}

</style>