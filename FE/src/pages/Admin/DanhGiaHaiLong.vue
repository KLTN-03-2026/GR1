<template>
  <div class="dg-page">
    <!-- Header -->
    <div class="dg-header">
      <div>
        <h1 class="dg-title"><i class="bi bi-emoji-smile me-2"></i>Đánh giá mức độ hài lòng</h1>
        <p class="dg-subtitle">Tổng hợp phản hồi của người dùng sau khi tạo lịch trình</p>
      </div>
      <button class="btn-refresh" @click="fetchData" :disabled="loading">
        <i class="bi" :class="loading ? 'bi-hourglass-split spin' : 'bi-arrow-clockwise'"></i>
        Làm mới
      </button>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="dg-loading">
      <div class="spinner"></div><span>Đang tải dữ liệu...</span>
    </div>

    <template v-else>
      <!-- Stat Cards -->
      <div class="stat-row">
        <div class="stat-card c-blue">
          <div class="stat-icon"><i class="bi bi-chat-square-text-fill"></i></div>
          <div class="stat-body">
            <div class="stat-val">{{ stats.tong_so ?? 0 }}</div>
            <div class="stat-lbl">Tổng đánh giá</div>
          </div>
        </div>
        <div class="stat-card c-green">
          <div class="stat-icon"><i class="bi bi-star-fill"></i></div>
          <div class="stat-body">
            <div class="stat-val">{{ formatScore(stats.diem_trung_binh) }}</div>
            <div class="stat-lbl">Điểm trung bình / 5</div>
          </div>
        </div>
        <div class="stat-card c-purple">
          <div class="stat-icon"><i class="bi bi-emoji-laughing-fill"></i></div>
          <div class="stat-body">
            <div class="stat-val">{{ positiveRate }}%</div>
            <div class="stat-lbl">Tỉ lệ hài lòng (4–5★)</div>
          </div>
        </div>
        <div class="stat-card c-orange">
          <div class="stat-icon"><i class="bi bi-emoji-frown-fill"></i></div>
          <div class="stat-body">
            <div class="stat-val">{{ negativeRate }}%</div>
            <div class="stat-lbl">Tỉ lệ chưa hài lòng (1–2★)</div>
          </div>
        </div>
      </div>

      <!-- Charts Row -->
      <div class="chart-row">
        <!-- Donut breakdown -->
        <div class="chart-card">
          <div class="chart-head">
            <i class="bi bi-pie-chart-fill me-2 text-purple"></i>Phân bố mức độ
          </div>
          <div class="face-breakdown">
            <div v-for="face in faceBreakdown" :key="face.value" class="face-row">
              <span class="face-emoji">{{ face.icon }}</span>
              <span class="face-name">{{ face.label }}</span>
              <div class="face-bar-wrap">
                <div class="face-bar" :style="{ width: faceBarWidth(face.count), background: face.color }"></div>
              </div>
              <span class="face-count">{{ face.count }}</span>
              <span class="face-pct">{{ facePct(face.count) }}%</span>
            </div>
          </div>
          <!-- Score display -->
          <div class="score-display">
            <div class="score-big">{{ formatScore(stats.diem_trung_binh) }}</div>
            <div class="score-stars">
              <i v-for="s in 5" :key="s" class="bi"
                :class="s <= Math.round(stats.diem_trung_binh ?? 0) ? 'bi-star-fill text-warning' : 'bi-star text-muted'">
              </i>
            </div>
            <div class="score-label">Điểm trung bình hệ thống</div>
          </div>
        </div>

        <!-- Trend chart (30 days) -->
        <div class="chart-card chart-card--wide">
          <div class="chart-head">
            <i class="bi bi-graph-up me-2 text-blue"></i>Xu hướng 30 ngày gần nhất
          </div>
          <apexchart v-if="trendSeries[0].data.length" type="area" height="220"
            :options="trendOptions" :series="trendSeries" />
          <div v-else class="empty-chart">
            <i class="bi bi-calendar-x fs-3 text-muted"></i>
            <p class="text-muted mt-2">Chưa có dữ liệu xu hướng</p>
          </div>
        </div>
      </div>

      <!-- Filter + Table -->
      <div class="table-card">
        <div class="table-head">
          <h5><i class="bi bi-list-ul me-2"></i>Danh sách đánh giá</h5>
          <div class="table-filters">
            <select v-model="filterRating" @change="fetchData" class="filter-select">
              <option value="">Tất cả mức độ</option>
              <option v-for="f in faceBreakdown" :key="f.value" :value="f.value">
                {{ f.icon }} {{ f.label }}
              </option>
            </select>
            <div class="search-wrap">
              <i class="bi bi-search"></i>
              <input v-model="searchQ" @input="debouncedSearch" placeholder="Tìm nội dung..." />
            </div>
          </div>
        </div>

        <div class="table-responsive">
          <table class="dg-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Mức độ</th>
                <th>Nội dung đóng góp</th>
                <th>Thời gian</th>
                <th>Thao tác</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="!list.length">
                <td colspan="5" class="text-center py-5 text-muted">Chưa có đánh giá nào</td>
              </tr>
              <tr v-for="item in list" :key="item.id">
                <td class="text-muted small">{{ item.id }}</td>
                <td>
                  <span class="face-badge" :style="{ background: faceMap[item.muc_do_hai_long]?.light }">
                    {{ faceMap[item.muc_do_hai_long]?.icon }}
                    <span :style="{ color: faceMap[item.muc_do_hai_long]?.color }">
                      {{ faceMap[item.muc_do_hai_long]?.label }}
                    </span>
                  </span>
                </td>
                <td class="noi-dung-cell">
                  <span v-if="item.noi_dung">{{ item.noi_dung }}</span>
                  <span v-else class="text-muted small fst-italic">Không có nội dung</span>
                </td>
                <td class="text-muted small">{{ formatDate(item.created_at) }}</td>
                <td>
                  <button class="btn-del" @click="deleteItem(item)" title="Xoá">
                    <i class="bi bi-trash3"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="pagination.last_page > 1" class="dg-pagination">
          <button :disabled="pagination.current_page <= 1" @click="goPage(pagination.current_page - 1)">
            <i class="bi bi-chevron-left"></i>
          </button>
          <span>Trang {{ pagination.current_page }} / {{ pagination.last_page }}</span>
          <button :disabled="pagination.current_page >= pagination.last_page" @click="goPage(pagination.current_page + 1)">
            <i class="bi bi-chevron-right"></i>
          </button>
        </div>
      </div>
    </template>
  </div>
</template>

<script>
import axios from 'axios';

const BASE = 'http://127.0.0.1:8000/api/admin';

const FACE_DATA = [
  { value: 1, icon: '😞', label: 'Rất tệ',    color: '#ef4444', light: '#fef2f2' },
  { value: 2, icon: '😕', label: 'Tệ',        color: '#f97316', light: '#fff7ed' },
  { value: 3, icon: '😐', label: 'Bình thường',color: '#eab308', light: '#fefce8' },
  { value: 4, icon: '😊', label: 'Tốt',       color: '#22c55e', light: '#f0fdf4' },
  { value: 5, icon: '🤩', label: 'Rất tốt',   color: '#10b981', light: '#ecfdf5' },
];

export default {
  name: 'DanhGiaHaiLong',
  data() {
    return {
      loading: false,
      list: [],
      stats: {},
      trend: [],
      pagination: { current_page: 1, last_page: 1 },
      filterRating: '',
      searchQ: '',
      searchTimer: null,
      faceBreakdown: [],
      trendSeries: [
        { name: 'Số đánh giá', data: [] },
        { name: 'Điểm TB', data: [] },
      ],
      trendOptions: {
        chart: { type: 'area', toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
        colors: ['#10b981', '#0ea5e9'],
        stroke: { curve: 'smooth', width: [2, 2] },
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.02 } },
        xaxis: { categories: [], labels: { style: { colors: '#64748b', fontSize: '11px' } }, axisBorder: { show: false }, axisTicks: { show: false } },
        yaxis: [
          { labels: { style: { colors: '#64748b' }, formatter: v => Math.round(v) } },
          { opposite: true, min: 0, max: 5, labels: { style: { colors: '#0ea5e9' }, formatter: v => v.toFixed(1) } },
        ],
        grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
        tooltip: { theme: 'light' },
        legend: { position: 'top', horizontalAlign: 'right', markers: { radius: 12 } },
        dataLabels: { enabled: false },
      },
    };
  },
  computed: {
    faceMap() {
      const m = {};
      FACE_DATA.forEach(f => { m[f.value] = f; });
      return m;
    },
    positiveRate() {
      const total = Number(this.stats.tong_so) || 0;
      if (!total) return 0;
      return Math.round(((Number(this.stats.tot) + Number(this.stats.rat_tot)) / total) * 100);
    },
    negativeRate() {
      const total = Number(this.stats.tong_so) || 0;
      if (!total) return 0;
      return Math.round(((Number(this.stats.rat_te) + Number(this.stats.te)) / total) * 100);
    },
  },
  mounted() {
    this.fetchData();
  },
  methods: {
    authHeader() {
      const token = localStorage.getItem('key_admin');
      return token ? { headers: { Authorization: `Bearer ${token}` } } : {};
    },
    async fetchData(page = 1) {
      this.loading = true;
      try {
        const params = new URLSearchParams({ page, per_page: 15 });
        if (this.filterRating) params.append('rating', this.filterRating);
        if (this.searchQ)     params.append('search', this.searchQ);

        const { data } = await axios.get(`${BASE}/danh-gia-he-thong?${params}`, this.authHeader());
        this.list       = data.data?.data  ?? [];
        this.pagination = { current_page: data.data?.current_page ?? 1, last_page: data.data?.last_page ?? 1 };
        this.stats      = data.stats ?? {};
        this.trend      = data.trend ?? [];

        // Build face breakdown
        const s = this.stats;
        this.faceBreakdown = FACE_DATA.map(f => ({
          ...f,
          count: Number(s[['rat_te','te','binh_thuong','tot','rat_tot'][f.value - 1]] ?? 0),
        }));

        // Build trend chart
        const labels = this.trend.map(t => t.ngay?.slice(5)); // MM-DD
        this.trendOptions = { ...this.trendOptions, xaxis: { ...this.trendOptions.xaxis, categories: labels } };
        this.trendSeries = [
          { name: 'Số đánh giá', data: this.trend.map(t => t.so_luong) },
          { name: 'Điểm TB',     data: this.trend.map(t => parseFloat(t.diem_tb)) },
        ];
      } catch (e) {
        console.error(e);
      } finally {
        this.loading = false;
      }
    },
    goPage(p) { this.fetchData(p); },
    debouncedSearch() {
      clearTimeout(this.searchTimer);
      this.searchTimer = setTimeout(() => this.fetchData(), 400);
    },
    async deleteItem(item) {
      if (!confirm(`Xoá đánh giá #${item.id}?`)) return;
      try {
        await axios.delete(`${BASE}/danh-gia-he-thong/${item.id}`, this.authHeader());
        this.fetchData(this.pagination.current_page);
      } catch (e) {
        alert('Lỗi khi xoá!');
      }
    },
    faceBarWidth(count) {
      const max = Math.max(...this.faceBreakdown.map(f => f.count), 1);
      return Math.max((count / max) * 100, 2) + '%';
    },
    facePct(count) {
      const total = Number(this.stats.tong_so) || 0;
      if (!total) return 0;
      return ((count / total) * 100).toFixed(1);
    },
    formatScore(v) { return v ? Number(v).toFixed(2) : '0.00'; },
    formatDate(d) {
      if (!d) return '';
      return new Date(d).toLocaleString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
    },
  },
};
</script>

<style scoped>
.dg-page { padding: 1.5rem; font-family: 'Inter', sans-serif; color: #1e2d44; }

/* Header */
.dg-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1.8rem; flex-wrap: wrap; gap: 1rem; }
.dg-title  { font-size: 1.6rem; font-weight: 800; margin: 0 0 .25rem; }
.dg-subtitle { color: #627289; font-size: .9rem; margin: 0; }

.btn-refresh {
  display: flex; align-items: center; gap: .45rem;
  padding: .55rem 1.1rem; border-radius: .8rem;
  border: 1.5px solid #dbe3f0; background: #fff;
  font-weight: 600; font-size: .88rem; cursor: pointer;
  transition: all .2s;
}
.btn-refresh:hover { background: #f0f7ff; border-color: #10b981; }

/* Loading */
.dg-loading { display: flex; align-items: center; gap: 1rem; padding: 3rem; color: #627289; }
.spinner { width: 28px; height: 28px; border: 3px solid #e2e8f0; border-top-color: #10b981; border-radius: 50%; animation: spin .8s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
.spin { animation: spin .8s linear infinite; display: inline-block; }

/* Stat Cards */
.stat-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
@media (max-width: 900px) { .stat-row { grid-template-columns: 1fr 1fr; } }
.stat-card {
  display: flex; align-items: center; gap: 1rem;
  background: #fff; border-radius: 1.2rem; padding: 1.2rem 1.4rem;
  box-shadow: 0 4px 16px rgba(30,45,68,.07); border-left: 4px solid;
}
.c-blue   { border-color: #0ea5e9; }
.c-green  { border-color: #10b981; }
.c-purple { border-color: #8b5cf6; }
.c-orange { border-color: #f59e0b; }
.stat-icon { font-size: 1.8rem; }
.c-blue   .stat-icon { color: #0ea5e9; }
.c-green  .stat-icon { color: #10b981; }
.c-purple .stat-icon { color: #8b5cf6; }
.c-orange .stat-icon { color: #f59e0b; }
.stat-val { font-size: 1.6rem; font-weight: 800; line-height: 1; }
.stat-lbl { font-size: .8rem; color: #627289; margin-top: .2rem; }

/* Chart Row */
.chart-row { display: grid; grid-template-columns: 340px 1fr; gap: 1rem; margin-bottom: 1.5rem; }
@media (max-width: 900px) { .chart-row { grid-template-columns: 1fr; } }
.chart-card {
  background: #fff; border-radius: 1.2rem; padding: 1.4rem;
  box-shadow: 0 4px 16px rgba(30,45,68,.07);
}
.chart-card--wide { flex: 1; }
.chart-head { font-weight: 700; font-size: .95rem; margin-bottom: 1rem; color: #1e2d44; }
.text-purple { color: #8b5cf6; }
.text-blue   { color: #0ea5e9; }

/* Face breakdown */
.face-breakdown { margin-bottom: 1.2rem; }
.face-row { display: flex; align-items: center; gap: .5rem; margin-bottom: .55rem; }
.face-emoji { font-size: 1.25rem; width: 28px; text-align: center; }
.face-name  { font-size: .78rem; font-weight: 600; color: #3d5166; width: 80px; }
.face-bar-wrap { flex: 1; height: 8px; background: #f1f5f9; border-radius: 99px; overflow: hidden; }
.face-bar { height: 100%; border-radius: 99px; transition: width .6s ease; }
.face-count { font-size: .82rem; font-weight: 700; width: 28px; text-align: right; }
.face-pct   { font-size: .75rem; color: #627289; width: 44px; text-align: right; }

/* Score display */
.score-display { text-align: center; padding: 1rem 0 .3rem; border-top: 1px solid #f0f4f8; }
.score-big   { font-size: 2.5rem; font-weight: 900; color: #10b981; line-height: 1; }
.score-stars { font-size: 1.2rem; margin: .4rem 0 .3rem; }
.score-label { font-size: .8rem; color: #627289; }

.empty-chart { display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 160px; }

/* Table Card */
.table-card { background: #fff; border-radius: 1.2rem; box-shadow: 0 4px 16px rgba(30,45,68,.07); overflow: hidden; }
.table-head {
  display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;
  padding: 1.2rem 1.4rem; border-bottom: 1px solid #f0f4f8;
}
.table-head h5 { margin: 0; font-size: 1rem; font-weight: 700; }
.table-filters { display: flex; gap: .7rem; flex-wrap: wrap; }
.filter-select {
  padding: .45rem .85rem; border: 1.5px solid #dbe3f0; border-radius: .7rem;
  font-size: .875rem; background: #f8fbff; outline: none;
}
.search-wrap { display: flex; align-items: center; gap: .5rem; background: #f8fbff; border: 1.5px solid #dbe3f0; border-radius: .7rem; padding: .45rem .85rem; }
.search-wrap i { color: #94a3b8; }
.search-wrap input { border: none; background: none; outline: none; font-size: .875rem; width: 160px; }

.dg-table { width: 100%; border-collapse: collapse; }
.dg-table th { background: #f8fbff; padding: .75rem 1rem; font-size: .82rem; font-weight: 700; color: #627289; text-align: left; border-bottom: 1px solid #e8edf5; white-space: nowrap; }
.dg-table td { padding: .75rem 1rem; border-bottom: 1px solid #f0f4f8; font-size: .875rem; vertical-align: middle; }
.dg-table tbody tr:hover { background: #f8fbff; }

.face-badge { display: inline-flex; align-items: center; gap: .35rem; padding: .3rem .65rem; border-radius: .5rem; font-weight: 600; font-size: .82rem; }
.user-cell { display: flex; flex-direction: column; gap: .1rem; }
.user-name  { font-weight: 600; font-size: .875rem; }
.user-email { font-size: .75rem; color: #94a3b8; }
.noi-dung-cell { max-width: 300px; }

.btn-del {
  background: #fef2f2; border: none; color: #ef4444; border-radius: .5rem;
  padding: .35rem .6rem; cursor: pointer; font-size: .9rem; transition: all .2s;
}
.btn-del:hover { background: #ef4444; color: #fff; }

/* Pagination */
.dg-pagination { display: flex; align-items: center; justify-content: center; gap: 1rem; padding: 1rem; border-top: 1px solid #f0f4f8; }
.dg-pagination button { background: #fff; border: 1.5px solid #dbe3f0; border-radius: .6rem; padding: .4rem .8rem; cursor: pointer; font-size: .9rem; transition: all .2s; }
.dg-pagination button:hover:not(:disabled) { border-color: #10b981; color: #10b981; }
.dg-pagination button:disabled { opacity: .4; cursor: not-allowed; }
.dg-pagination span { font-size: .88rem; font-weight: 600; color: #627289; }
</style>
