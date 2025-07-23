<template>
  <div class="test-preview" id="math-container">
    <h2 class="text-2xl font-bold mb-6">📘 Bắt đầu làm bài</h2>

    <div v-if="test && test.details && test.details.length">
      <div
        v-for="(detail, index) in test.details"
        :key="detail.id"
        class="mb-6 border-b pb-4"
      >
        <p class="font-semibold mb-2">Câu {{ index + 1 }}:</p>

        <!-- Câu hỏi chứa công thức toán (v-html) -->
        <p class="text-lg leading-relaxed" v-html="detail.question.content"></p>

        <!-- Lựa chọn đáp án -->
        <ul v-if="detail.question.answers && detail.question.answers.length" class="mt-3 space-y-1">
          <li v-for="answer in detail.question.answers" :key="answer.id">
            <label>
              <input
                type="radio"
                :name="'q_' + detail.question.id"
                :value="answer.id"
                v-model="answers[detail.question.id]"
                :disabled="isSubmitted"
              />
              <span v-html="answer.content"></span>
            </label>
          </li>
        </ul>
      </div>

      <!-- Nút nộp bài -->
      <div class="text-center mt-6">
        <button
          v-if="!isSubmitted"
          @click="submitTest"
          class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700"
        >
          ✅ Nộp bài
        </button>
      </div>
    </div>

    <div v-else class="text-gray-500">Đang tải đề thi...</div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useTestStore } from '@/store/test'
import { renderByMathjax } from 'mathjax-vue3'
import { nextTick } from 'vue'

const route = useRoute()
const store = useTestStore()
const answers = ref({})
const isSubmitted = ref(false)
const score = ref(0)

const test = computed(() => store.selectedTest)
const total = computed(() => test.value?.details?.length || 0)

import { useRouter } from 'vue-router'
const router = useRouter()

function submitTest() {
  if (isSubmitted.value) return

  let correct = 0
  const resultDetails = []

  for (const detail of test.value.details) {
    const questionId = detail.question.id
    const selectedAnswerId = answers.value[questionId]
    const correctAnswer = detail.question.answers.find(a => a.is_correct == 1)
    const userAnswer = detail.question.answers.find(a => a.id === selectedAnswerId)

    if (selectedAnswerId && correctAnswer && selectedAnswerId === correctAnswer.id) {
      correct++
    } else {
      resultDetails.push({
        questionContent: detail.question.content,
        correctAnswer: correctAnswer?.content,
        userAnswer: userAnswer?.content || '(Trống)'
      })
    }
  }

  router.push({
    name: 'TestResult',
    state: {
      score: correct,
      total: test.value.details.length,
      wrongAnswers: resultDetails
    }
  })
}

onMounted(async () => {
  const id = route.params.id
  await store.fetchTestDetail(id)

  setTimeout(() => {
    const el = document.getElementById('math-container')
    if (el) renderByMathjax(el)
  }, 0)
})

watch(test, async () => {
  await nextTick()
  const el = document.getElementById('math-container')
  if (el) renderByMathjax(el)
})

</script>
