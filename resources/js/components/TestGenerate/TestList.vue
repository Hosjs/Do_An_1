<template>
  <ul>
    <li v-for="test in tests" :key="test.id" class="test-item">
      <span>{{ test.name }} - Môn: {{ test.subject?.name }}</span>
      <button @click="goToTest(test.id)">Bắt đầu</button>
      <button @click="deleteTest(test.id)" class="danger">🗑️</button>
    </li>
  </ul>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useTestStore } from '@/store/test'
import { useAuthStore } from '@/store/auth'

const router = useRouter()
const store = useTestStore()
const auth = useAuthStore()

const props = defineProps({
  tests: Array
})

// Bắt đầu làm bài
function goToTest(id) {
  if(auth.role === 'Admin' ) {
    router.push(`/admin/tests/test-begin/${id}`)
  } else if(auth.role === 'Student'){
    router.push(`/student/tests/test-begin/${id}`)
  }
}

async function deleteTest(id) {
  if (confirm('Bạn có chắc chắn muốn xóa đề này không?')) {
    try {
      await store.deleteTest(id)
      await store.fetchTests()
    } catch (err) {
      alert('Xóa đề thất bại.')
      console.error(err)
    }
  }
}
</script>

<style scoped>
.test-item {
  margin-bottom: 10px;
  display: flex;
  gap: 10px;
  align-items: center;
}
.danger {
  background-color: #dc3545;
  color: white;
  padding: 4px 10px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
.danger:hover {
  background-color: #b02a37;
}
</style>
