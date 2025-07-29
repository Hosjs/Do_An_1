<template>
  <div class="home-container">
    <Header @toggleSidebar="toggleSidebar" />

    <div class="content">
      <h1>Đăng ký thi</h1>
      <div class="exam-card">
        <h2>Bài thi đánh giá tư duy 1 (DEMO)</h2>
        <div class="exam-info">
          <p><strong>Hình thức thi:</strong> <span class="badge online">Thi trực tuyến</span></p>
          <p><strong>Thời gian đăng ký:</strong> 05/12/2021 - 23/01/2022</p>
          <p><strong>Lệ phí:</strong> Miễn phí</p>
          <p><strong>Thời gian thi:</strong> 05/12/2021 - 23/01/2022</p>
        </div>
        <div class="actions">
          <button class="btn-detail" @click="handleViewDetails">Chi tiết</button>
          <button class="btn-test" @click="handleStartTest">Thi ngay</button>
        </div>
      </div>
      <div class="exam-card">
        <h2>Bài thi đánh giá tư duy 2 (DEMO)</h2>
        <div class="exam-info">
          <p><strong>Hình thức thi:</strong> <span class="badge online">Thi trực tuyến</span></p>
          <p><strong>Thời gian đăng ký:</strong> 05/12/2021 - 23/01/2022</p>
          <p><strong>Lệ phí:</strong> Miễn phí</p>
          <p><strong>Thời gian thi:</strong> 05/12/2021 - 23/01/2022</p>
        </div>
        <div class="actions">
          <button class="btn-detail" @click="handleViewDetails">Chi tiết</button>
          <button class="btn-test" @click="handleStartTest">Thi ngay</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import Header from '@/components/Header.vue'

const router = useRouter()

function toggleSidebar() {
  console.log('Mở/tắt sidebar')
}

function handleViewDetails() {
  router.push('/admin/tests')
}

function handleStartTest() {
  router.push('/admin/tests')
}
</script>

<style scoped src="@/assets/css/home.css" />
