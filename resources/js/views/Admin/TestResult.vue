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
import axios from 'axios'

const saveResult = async () => {
  const payload = {
    test_detail_id: selectedTestId,
    reviewed_by: userId,
    score: props.score,
    work_time: workTime
  }
  await axios.post('/api/essay-reviews', payload)
}

</script>
