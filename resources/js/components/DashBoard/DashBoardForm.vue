<template>
  <div class="dashboard-container">
    <div class="stats-cards">
      <div class="stat-card yellow">
        <div class="icon">💽</div>
        <div class="info">
          <h3>Người dùng</h3>
          <p>{{ stats.users }}</p>
          <small>Updated now</small>
        </div>
      </div>

      <div class="stat-card green">
        <div class="icon">💵</div>
        <div class="info">
          <h3>Học sinh</h3>
          <p>{{ stats.students }}</p>
          <small>Last day</small>
        </div>
      </div>

      <div class="stat-card red">
        <div class="icon">⚠️</div>
        <div class="info">
          <h3>Giáo viên</h3>
          <p>{{ stats.teachers }}</p>
          <small>In the last hour</small>
        </div>
      </div>

      <div class="stat-card blue">
        <div class="icon">🐦</div>
        <div class="info">
          <h3>Đang hoạt động</h3>
          <p>{{ stats.active }}</p>
          <small>Updated now</small>
        </div>
      </div>
    </div>

    <!-- Chart area -->
    <div class="chart-card">
      <h2>Users performance</h2>
      <canvas id="userChart"></canvas>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Chart from 'chart.js/auto'
import axios from 'axios'
import '@/assets/css/Dashboard.css'

const stats = ref({
  users: 0,
  students: 0,
  teachers: 0,
  active: 0,
  chart: []
})

async function fetchStats() {
  const res = await axios.get('/api/dashboard')
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
          label: 'Users',
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