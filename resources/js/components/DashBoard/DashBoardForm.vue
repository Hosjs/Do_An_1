<template>
  <div class="dashboard-container">
    <h1 class="dashboard-title">📊 Thống kê hệ thống</h1>
    <div class="stats-cards">
      <div class="stat-card yellow">
        <div class="icon">👤</div>
        <div>
          <div class="stat-label">Người dùng</div>
          <div class="stat-value">{{ stats.users }}</div>
        </div>
      </div>
      <div class="stat-card green">
        <div class="icon">🎓</div>
        <div>
          <div class="stat-label">Học sinh</div>
          <div class="stat-value">{{ stats.students }}</div>
        </div>
      </div>
      <div class="stat-card blue">
        <div class="icon">👩‍🏫</div>
        <div>
          <div class="stat-label">Giáo viên</div>
          <div class="stat-value">{{ stats.teachers }}</div>
        </div>
      </div>
      <div class="stat-card red">
        <div class="icon">🟢</div>
        <div>
          <div class="stat-label">Đang hoạt động</div>
          <div class="stat-value">{{ stats.active }}</div>
        </div>
      </div>
    </div>
    <div class="chart-section">
      <h2>Biểu đồ người dùng</h2>
      <canvas id="userChart"></canvas>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Chart from 'chart.js/auto'

const stats = ref({
  users: 0,
  students: 0,
  teachers: 0,
  active: 0,
  chart: []
})

async function fetchStats() {
  const res = await axios.get('/api/dashboard-stats')
  stats.value = res.data
}

onMounted(async () => {
  await fetchStats()
  const ctx = document.getElementById('userChart')
  new Chart(ctx, {
    type: 'line',
    data: {
      labels: ['0h', '4h', '8h', '12h', '16h', '20h', '24h'],
      datasets: [
        {
          label: 'Người dùng',
          data: stats.value.chart,
          fill: true,
          borderColor: '#4f46e5',
          backgroundColor: 'rgba(79, 70, 229, 0.2)',
          tension: 0.4
        }
      ]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } }
    }
  })
})
</script>
