<template>
  <div class="home-container">

    <!-- Hero Section -->
    <section class="hero">
      <div class="hero-content">
        <h1>Chào mừng bạn đến với <span class="highlight">TSA Test</span></h1>
        <p>
          Nền tảng thi thử trực tuyến giúp bạn luyện tập và chuẩn bị tốt nhất cho
          kỳ thi đánh giá tư duy.
        </p>
        <button class="btn-cta" @click="handleStartTest">🚀 Bắt đầu thi ngay</button>
      </div>
    </section>

    <!-- Features Section -->
    <section class="features">
      <h2 class="section-title">Tại sao chọn TSA Test?</h2>
      <div class="feature-list">
        <div class="feature-card">
          <i class="fa-solid fa-laptop"></i>
          <h3>Thi thử trực tuyến</h3>
          <p>Làm bài thi mô phỏng TSA thật, mọi lúc mọi nơi.</p>
        </div>
        <div class="feature-card">
          <i class="fa-solid fa-chart-line"></i>
          <h3>Kết quả chi tiết</h3>
          <p>Xem điểm và phân tích ưu – nhược điểm ngay sau khi nộp bài.</p>
        </div>
        <div class="feature-card">
          <i class="fa-solid fa-book"></i>
          <h3>Ngân hàng đề thi</h3>
          <p>Tổng hợp nhiều dạng câu hỏi, cập nhật thường xuyên.</p>
        </div>
      </div>
    </section>

    <!-- Exams Section -->
    <section class="exams">
      <h2 class="section-title">Đăng ký thi thử</h2>
      <div class="exam-list">
        <div class="exam-card">
          <h3>Bài thi đánh giá tư duy 1 (DEMO)</h3>
          <div class="exam-info">
            <p><strong>Hình thức thi:</strong> <span class="badge online">Trực tuyến</span></p>
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
          <h3>Bài thi đánh giá tư duy 2 (DEMO)</h3>
          <div class="exam-info">
            <p><strong>Hình thức thi:</strong> <span class="badge online">Trực tuyến</span></p>
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
    </section>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '../store/auth'

const router = useRouter()
const authStore = useAuthStore()

function handleViewDetails() {
  if (authStore.role === 'Student') {
    router.push('/student/exam-details')
  } else if (authStore.role === 'Teacher') {
    router.push('/teacher/exam-details')
  } else if (authStore.role === 'Admin') {
    router.push('/admin/exam-details')
  } else {
    router.push('/login')
  }
}

function handleStartTest() {
  if (authStore.role === 'Student') {
    router.push('/student/exam-details')
  } else if (authStore.role === 'Teacher') {
    router.push('/teacher/exam-details')
  } else if (authStore.role === 'Admin') {
    router.push('/admin/exam-details')
  } else {
    router.push('/login')
  }
}
</script>
<style scoped src="@/assets/css/home.css" />