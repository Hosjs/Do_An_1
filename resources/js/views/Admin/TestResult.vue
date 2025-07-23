<template>
  <div class="page-container">
    <h1>Kết quả</h1>
    <TestResultForm
      :score="$router.options.history.state.score"
      :total="$router.options.history.state.total"
      :wrong-answers="$router.options.history.state.wrongAnswers"
    />
  </div>
</template>

<script setup>
import TestResultForm from '@/components/TestResult/TestResultForm.vue'
</script>
