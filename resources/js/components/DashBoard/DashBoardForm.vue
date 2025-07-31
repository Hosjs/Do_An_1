<template>
  <div class="dashboard-container">
    <div class="stats-cards">
      <div class="stat-card yellow">
        <div class="icon">💽</div>
        <div class="info">
          <h3>Người dùng</h3>
          <p>3</p>
          <small>Updated now</small>
        </div>
      </div>

      <div class="stat-card green">
        <div class="icon">💵</div>
        <div class="info">
          <h3>Học sinh</h3>
          <p>1</p>
          <small>Last day</small>
        </div>
      </div>

      <div class="stat-card red">
        <div class="icon">⚠️</div>
        <div class="info">
          <h3>Giáo viên</h3>
          <p>1</p>
          <small>In the last hour</small>
        </div>
      </div>

      <div class="stat-card blue">
        <div class="icon">🐦</div>
        <div class="info">
          <h3>Đang hoạt động</h3>
          <p>1</p>
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
import { onMounted } from 'vue'
import Chart from 'chart.js/auto'

onMounted(() => {
  const ctx = document.getElementById('userChart')
  new Chart(ctx, {
    type: 'line',
    data: {
      labels: ['0h', '4h', '8h', '12h', '16h', '20h', '24h'],
      datasets: [
        {
          label: 'Users',
          data: [120, 200, 300, 500, 700, 850, 900],
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

<style scoped>
.dashboard-container {
  padding: 1rem;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background: #f8f9fa;
  min-height: 100vh;
  font-size: 0.9rem;
}

/* Header */
.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}
.header-actions span {
  margin-left: 0.75rem;
  font-size: 0.8rem;
  color: #6b7280;
}

/* Cards */
.stats-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); 
  gap: 0.75rem;
  margin-bottom: 1.5rem;
}

.stat-card {
  display: flex;
  align-items: center;
  padding: 0.75rem;
  border-radius: 8px;
  color: #fff;
}
.stat-card .icon {
  font-size: 1.5rem;
  margin-right: 0.75rem;
}

.stat-card.yellow { background: #f59e0b; }
.stat-card.green  { background: #10b981; }
.stat-card.red    { background: #ef4444; }
.stat-card.blue   { background: #3b82f6; }

/* Chart */
.chart-card {
  background: #fff;
  padding: 1rem;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.chart-card h2 {
  margin-bottom: 0.75rem;
  font-size: 1rem;
}
.chart-card small {
  font-weight: normal;
  color: #6b7280;
  font-size: 0.8rem;
}

</style>
