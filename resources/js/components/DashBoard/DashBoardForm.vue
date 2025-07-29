<template>
  <div class="dashboard-container">
    <!-- Statistic cards -->
    <div class="stats-cards">
      <div class="stat-card yellow">
        <div class="icon">💽</div>
        <div class="info">
          <h3>Capacity</h3>
          <p>105GB</p>
          <small>Updated now</small>
        </div>
      </div>

      <div class="stat-card green">
        <div class="icon">💵</div>
        <div class="info">
          <h3>Revenue</h3>
          <p>$1,345</p>
          <small>Last day</small>
        </div>
      </div>

      <div class="stat-card red">
        <div class="icon">⚠️</div>
        <div class="info">
          <h3>Errors</h3>
          <p>23</p>
          <small>In the last hour</small>
        </div>
      </div>

      <div class="stat-card blue">
        <div class="icon">🐦</div>
        <div class="info">
          <h3>Followers</h3>
          <p>+45</p>
          <small>Updated now</small>
        </div>
      </div>
    </div>

    <!-- Chart area -->
    <div class="chart-card">
      <h2>Users Behavior <small>(24 Hours performance)</small></h2>
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
  padding: 1rem; /* giảm padding */
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background: #f8f9fa;
  min-height: 100vh;
  font-size: 0.9rem; /* thu nhỏ font toàn trang */
}

/* Header */
.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem; /* giảm khoảng cách */
}
.header-actions span {
  margin-left: 0.75rem;
  font-size: 0.8rem; /* nhỏ hơn */
  color: #6b7280;
}

/* Cards */
.stats-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); 
  /* tự co giãn theo màn hình */
  gap: 0.75rem; /* thu nhỏ khoảng cách */
  margin-bottom: 1.5rem;
}

.stat-card {
  display: flex;
  align-items: center;
  padding: 0.75rem; /* nhỏ hơn */
  border-radius: 8px;
  color: #fff;
}
.stat-card .icon {
  font-size: 1.5rem; /* nhỏ hơn */
  margin-right: 0.75rem;
}

.stat-card.yellow { background: #f59e0b; }
.stat-card.green  { background: #10b981; }
.stat-card.red    { background: #ef4444; }
.stat-card.blue   { background: #3b82f6; }

/* Chart */
.chart-card {
  background: #fff;
  padding: 1rem; /* nhỏ hơn */
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.chart-card h2 {
  margin-bottom: 0.75rem;
  font-size: 1rem; /* nhỏ hơn */
}
.chart-card small {
  font-weight: normal;
  color: #6b7280;
  font-size: 0.8rem;
}

</style>
