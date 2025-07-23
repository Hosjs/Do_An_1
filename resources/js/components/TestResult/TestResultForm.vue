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
const props = defineProps({
  score: Number,
  total: Number,
  wrongAnswers: Array
})
</script>
