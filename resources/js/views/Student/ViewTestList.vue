<template>
  <div>
    <h2 class="text-xl font-bold mb-4">📄 Danh sách đề thi</h2>

    <TestList :tests="tests" :onViewDetail="viewDetail" />

    <!-- Chỉ hiển thị khi có dữ liệu -->
    <TestDetail
      v-if="showModal && selectedTest"
      :test="selectedTest"
      @close="showModal = false"

    />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useTestStore } from '@/store/test'
import TestList from '@/components/TestGenerate/TestList.vue'
import TestDetail from '@/components/TestGenerate/TestDetail.vue'

const store = useTestStore()
const showModal = ref(false)

const tests = computed(() => store.tests)
const selectedTest = computed(() => store.selectedTest)

const viewDetail = async (id) => {
  await store.fetchTestDetail(id)

  if (store.selectedTest) {
    showModal.value = true
  } else {
    console.warn('Không có dữ liệu đề thi để hiển thị')
  }
}

onMounted(() => {
  store.fetchTests()
})
</script>
