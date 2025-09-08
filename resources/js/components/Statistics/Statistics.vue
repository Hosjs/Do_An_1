<template>
  <div class="ra-page">
    <!-- Search bar -->
    <div class="gtf-card ra-search">
      <input type="search" v-model="query.search" placeholder="Tìm kiếm học sinh, đề thi, câu hỏi..." @keyup.enter="refresh" />
      <button class="btn" @click="refresh">Tìm</button>
    </div>
    <!-- Filters -->
    <div class="gtf-card ra-filters">
      <div class="filters-row">
        <div class="field">
          <label>Môn học</label>
          <select v-model="query.subject_id">
            <option value="">Tất cả</option>
            <option v-for="s in subjects" :key="s.id" :value="s.id">{{ s.name }}</option>
          </select>
        </div>

        <div class="field">
          <label>Khoảng thời gian</label>
          <div class="date-range">
            <input type="date" v-model="query.from_date" />
            <span class="sep">—</span>
            <input type="date" v-model="query.to_date" />
          </div>
        </div>

        <div class="field">
          <label>Điểm đỗ (≥)</label>
          <input type="number" min="0" max="100" v-model.number="query.pass_mark" />
        </div>

        <div class="field">
          <label>Kiểu đề</label>
          <select v-model="query.test_type">
            <option value="">Tất cả</option>
            <option v-for="t in testTypes" :key="t.id" :value="t.id">{{ t.name }}</option>
          </select>
        </div>

        <div class="actions">
          <button class="btn" @click="refresh">Lọc</button>
          <button class="btn ghost" @click="resetFilters">Đặt lại</button>
        </div>
      </div>
    </div>

    <!-- KPI cards -->
    <div class="ra-kpi">
      <div class="gtf-card kpi">
        <div class="kpi-title">Bài đã nộp</div>
        <div class="kpi-value">{{ stats.total_submissions }}</div>
      </div>
      <div class="gtf-card kpi">
        <div class="kpi-title">Điểm trung bình</div>
        <div class="kpi-value">{{ stats.avg_score.toFixed(1) }}</div>
      </div>
      <div class="gtf-card kpi">
        <div class="kpi-title">Tỉ lệ đỗ</div>
        <div class="kpi-value">{{ (stats.pass_rate * 100).toFixed(0) }}%</div>
      </div>
      <div class="gtf-card kpi">
        <div class="kpi-title">Thời gian TB</div>
        <div class="kpi-value">{{ stats.avg_duration_min }} phút</div>
      </div>

      <!-- THÊM MỚI: KPI tổng câu đúng/sai lấy từ user_answers -->
      <div class="gtf-card kpi">
        <div class="kpi-title">Câu đúng</div>
        <div class="kpi-value">{{ totalCorrect }}</div>
      </div>
      <div class="gtf-card kpi">
        <div class="kpi-title">Câu sai</div>
        <div class="kpi-value">{{ totalWrong }}</div>
      </div>
    </div>

    <!-- Charts -->
    <div class="ra-charts">
      <div class="gtf-card chart-card">
        <div class="chart-header">
          <h4>Phân bố điểm (0–100)</h4><span class="badge">{{ histogram.length }} bins</span>
        </div>
        <div class="chart-body">
          <!-- SVG histogram -->
          <svg :viewBox="`0 0 ${chartW} ${chartH}`" class="histogram">
            <g v-for="(b, i) in histogram" :key="i">
              <rect
                :x="i * barW + barGap/2"
                :y="chartH - (b.count / maxCount) * (chartH - 20)"
                :width="barW - barGap"
                :height="(b.count / maxCount) * (chartH - 20)"
                rx="4" ry="4"
              ></rect>
            </g>
            <!-- x-axis ticks -->
            <g v-for="t in 5" :key="t">
              <text :x="(t-1)* (chartW/4)" :y="chartH" class="axis-tick">{{ (t-1)*25 }}</text>
            </g>
          </svg>
        </div>
      </div>
    </div>
    <!-- THÊM MỚI: Bảng chi tiết user_answers -->
    <div class="gtf-card ra-table">
      <div class="table-head">
        <h4>Chi tiết câu trả lời</h4>
        <div class="table-search">
          <input type="search" v-model="tableSearchQuery" placeholder="Lọc trong bảng..." />
        </div>
        <div class="pager">
          <span class="badge">{{ filteredUserAnswers.length }}</span>
        </div>
      </div>
      <table>
        <thead>
          <tr>
            <th>Học sinh</th>
            <th>Đề thi</th>
            <th>Câu hỏi (ID)</th>
            <th>Điểm</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="ua in filteredUserAnswers"
            :key="`${ua.user_id}-${ua.test_id}-${ua.question_id}-${ua.answered_at}`"
          >
            <td>{{ ua.user_name }}</td>
            <td>{{ ua.test_title }}</td>
            <td>#{{ ua.question_id }}</td>
            <td>
              <span :class="Number(ua.score) >= 1 ? 'pill ok' : 'pill no'">
                {{ Number(ua.score) >= 1 ? '1' : '0' }}
              </span>
            </td>
          </tr>
          <tr v-if="!filteredUserAnswers.length">
            <td colspan="5" class="empty">Không có dữ liệu</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { onMounted, reactive, computed, ref } from 'vue'
import { useResultsStats } from '@/store/useResultsStats'
import { useGenerateTest } from '@/store/useGenerateTest'
window.resultsStore = useResultsStats()
const isLoading = ref(false)
const errorMsg = ref('')

const chartW = 560, chartH = 160
const barGap = 4
const barGap2 = 2

const tableSearchQuery = ref('')
const page = ref(1)
const hasMore = ref(false)

const resultsStore = useResultsStats()
const { subjects, types: testTypes, fetchData: fetchMeta } = useGenerateTest()

const query = reactive({
  search: '',
  subject_id: '',
  test_type: '',
  from_date: '',
  to_date: '',
  pass_mark: 50,
  page: 1,
  per_page: 10,
})

const stats = computed(() => resultsStore.stats)
const histogram = computed(() => resultsStore.histogram)
const perQuestion = computed(() => resultsStore.perQuestion)
const topStudents = computed(() => resultsStore.topStudents)
const results = computed(() => resultsStore.results)

/* THÊM MỚI: user_answers + KPI tổng đúng/sai */
const userAnswers = computed(() => resultsStore.userAnswers || [])
const filteredUserAnswers = computed(() => {
  if (!tableSearchQuery.value) {
    return userAnswers.value
  }
  const q = tableSearchQuery.value.toLowerCase().trim()
  return userAnswers.value.filter(ua =>
    ua.user_name?.toLowerCase().includes(q) ||
    ua.test_title?.toLowerCase().includes(q) ||
    String(ua.question_id).includes(q)
  )
})

const totalCorrect = computed(() => Math.round(stats.value.total_submissions * stats.value.pass_rate))
const totalWrong = computed(() => Math.max(0, stats.value.total_submissions - totalCorrect.value))

const maxCount = computed(() => Math.max(1, ...histogram.value.map(b => b.count)))
const barW = computed(() => chartW / Math.max(1, histogram.value.length))
const barW2 = computed(() => chartW / Math.max(1, perQuestion.value.length))

async function refresh() {
  isLoading.value = true
  errorMsg.value = ''
  query.page = page.value
  try {
    const ok = await resultsStore.fetchAll(query)
    if (!ok) errorMsg.value = 'Không tải được dữ liệu. Kiểm tra API_URL, CORS, or route backend.'
    hasMore.value = ok && results.value.length === query.per_page
  } catch (e) {
    console.error(e)
    errorMsg.value = 'Đã xảy ra lỗi khi tải dữ liệu.'
  } finally {
    isLoading.value = false
  }
}


function resetFilters() {
  Object.assign(query, { search: '', subject_id: '', test_type: '', from_date: '', to_date: '', pass_mark: 50, page: 1, per_page: 10 })
  tableSearchQuery.value = ''
  page.value = 1
  refresh()
}

function exportCSV() {
  resultsStore.exportCSV(query)
}

function formatDate(s) {
  if (!s) return '-'
  const d = new Date(s)
  return d.toLocaleDateString()
}

onMounted(async () => {
  await fetchMeta()
  await refresh()
})
</script>

<style scoped>
.ra-page { display: grid; gap: 16px; }

.ra-search { display: flex; gap: 8px; }
.ra-search input { flex-grow: 1; height: 40px; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0 12px; font-size: .95rem; outline: none; background: #fff; }
.ra-search input:focus { border-color: #22c55e; box-shadow: 0 0 0 3px rgba(34,197,94,.15); }

.ra-filters .filters-row {
  display: grid;
  gap: 12px;
  grid-template-columns: 1fr 1.2fr 0.7fr 1fr auto;
}
@media (max-width: 900px) {
  .ra-filters .filters-row { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 640px) {
  .ra-filters .filters-row { grid-template-columns: 1fr; }
}
.date-range { display: flex; align-items: center; gap: 8px; }
.date-range .sep { color: #64748b; }

.field { display: grid; gap: 6px; }
.field label { font-size: .85rem; color: #475569; font-weight: 600; }
select, input[type="number"], input[type="date"] {
  height: 40px; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0 12px;
  background: #fff; font-size: .95rem; outline: none;
}
select:focus, input:focus { border-color: #22c55e; box-shadow: 0 0 0 3px rgba(34,197,94,.15); }
.actions { display: flex; align-items: end; gap: 8px; }

.ra-kpi {
  display: grid; gap: 12px; grid-template-columns: repeat(4, 1fr);
}
@media (max-width: 900px) {
  .ra-kpi { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 640px) {
  .ra-kpi { grid-template-columns: 1fr; }
}
.kpi { padding: 14px; }
.kpi-title { color: #64748b; font-size: .9rem; }
.kpi-value { font-size: 1.6rem; font-weight: 700; color: #0f172a; }


.chart-card .chart-header {
  display: flex; align-items: center; gap: 8px; margin-bottom: 8px;
}
.chart-body { overflow-x: auto; }
.histogram, .bars { width: 100%; height: 180px; }
.axis-tick { font-size: 10px; fill: #475569; }

.bar-legend { margin-top: 8px; color: #475569; font-size: .9rem; }

.ra-table .table-head {
  display: flex; justify-content: space-between; align-items: center;
  margin-bottom: 8px; gap: 16px;
}
.ra-table .table-head h4 { flex-shrink: 0; }
.ra-table .table-search { flex-grow: 1; }
.ra-table .table-search input {
  width: 100%;
  height: 36px; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0 12px;
  background: #fff; font-size: .9rem; outline: none;
}
.ra-table .table-search input:focus { border-color: #22c55e; box-shadow: 0 0 0 3px rgba(34,197,94,.15); }
.ra-table table { width: 100%; border-collapse: collapse; }
.ra-table thead th {
  text-align: left; padding: 10px; font-size: .9rem; color: #475569; border-bottom: 1px solid #e5e7eb;
}
.ra-table tbody td { padding: 10px; border-bottom: 1px solid #f1f5f9; }
.ra-table tbody tr:hover { background: #fafafa; }
.empty { text-align: center; color: #64748b; }

/* THÊM MỚI: pill 0/1 cho điểm câu hỏi */
.pill {
  display: inline-flex; align-items: center; justify-content: center;
  min-width: 28px; height: 24px; padding: 0 8px;
  border-radius: 999px; font-weight: 700; font-size: .9rem;
}
.pill.ok { background: #dcfce7; color: #166534; }
.pill.no { background: #fee2e2; color: #991b1b; }
</style>
