<template>
  <div class="reports-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h2 class="h3 mb-1 text-dark fw-bold">Báo cáo Thống kê AI</h2>
        <p class="text-muted mb-0">Theo dõi hiệu suất và mức độ tương tác của công cụ</p>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-outline-primary d-flex align-items-center px-4" @click="fetchAiStats" style="border-radius: 999px;">
          <i class="bi bi-arrow-clockwise me-2"></i> Làm mới
        </button>
        <button class="btn btn-primary d-flex align-items-center px-4" @click="exportData" :disabled="exporting" style="border-radius: 999px;">
          <span v-if="exporting" class="spinner-border spinner-border-sm me-2"></span>
          <i v-else class="bi bi-download me-2"></i> Trích xuất dữ liệu
        </button>
      </div>
    </div>

    <!-- Quick Stats Row -->
    <div class="row g-3 mb-4">
      <div class="col-md-4">
        <div class="card card-custom h-100">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <h6 class="text-muted mb-0 fw-semibold">Tổng lịch trình AI tạo</h6>
            </div>
            <div class="fs-2 fw-bold text-dark mb-1">{{ stats ? stats.ai_trips.toLocaleString() : 0 }}</div>
            <p class="small text-muted mb-0">Sử dụng mô hình Hybrid AI</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card card-custom h-100">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <h6 class="text-muted mb-0 fw-semibold">Tốc độ xử lý trung bình</h6>
            </div>
            <div class="fs-2 fw-bold text-dark mb-1">{{ stats ? stats.avg_execution_time : '0.00' }}s</div>
            <p class="small text-muted mb-0">Thời gian sinh nội dung (Gemini)</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card card-custom bg-danger text-white h-100">
          <div class="card-body position-relative overflow-hidden">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <h6 class="text-white-50 mb-0 fw-semibold">Tỉ lệ Gọi AI thất bại</h6>
              <i class="bi bi-exclamation-triangle-fill text-white-50"></i>
            </div>
            <div class="fs-2 fw-bold mb-1">{{ stats ? stats.fallback_rate : 0 }}%</div>
            <p class="small text-white-50 mb-0">Phải dùng thuật toán dự phòng</p>
            <i class="bi bi-shield-x position-absolute" style="font-size: 8rem; right: -20px; bottom: -40px; opacity: 0.1;"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 mb-4">
      <div class="col-lg-4">
        <div class="card card-custom h-100">
          <div class="card-header border-bottom-0 bg-transparent pt-4 pb-0">
            <h5 class="mb-0 fw-bold">Tỉ lệ Ứng dụng AI</h5>
            <p class="text-muted small">So sánh số lịch trình AI vs Thủ công</p>
          </div>
          <div class="card-body d-flex justify-content-center align-items-center">
            <apexchart type="donut" width="300" :options="aiUsageOptions" :series="aiUsageSeries"></apexchart>
          </div>
        </div>
      </div>

      <div class="col-lg-8">
        <div class="card card-custom h-100">
          <div class="card-header border-bottom-0 bg-transparent pt-4 pb-0 d-flex justify-content-between">
            <h5 class="mb-0 fw-bold">Trạng thái Vòng đời Lịch trình</h5>
            <p class="text-muted small mb-0">Tỉ lệ hoàn thành của các chuyến đi</p>
          </div>
          <div class="card-body">
            <apexchart type="bar" height="320" :options="statusChartOptions" :series="statusChartSeries"></apexchart>
          </div>
        </div>
      </div>
    </div>

    <!-- Data Table (Top AI Keywords) -->
    <div class="card card-custom mb-5">
      <div class="card-header border-bottom bg-transparent py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">Top 5 Từ khóa Sở thích (AI Prompt)</h5>
        <span class="badge bg-primary-subtle text-primary">Phân tích từ ghi chú</span>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th class="py-3 text-secondary fw-semibold text-uppercase" style="font-size: 0.75rem"># Top</th>
              <th class="py-3 text-secondary fw-semibold text-uppercase" style="font-size: 0.75rem">Từ khóa yêu cầu</th>
              <th class="py-3 text-secondary fw-semibold text-uppercase text-end" style="font-size: 0.75rem">Tần suất xuất hiện</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(count, keyword, index) in topKeywords" :key="index">
              <td class="py-3 fw-bold text-dark ps-4">{{ index + 1 }}</td>
              <td class="py-3">
                <span class="badge keyword-badge px-3 py-2 text-capitalize fs-6 border" :class="getKeywordBadgeClass(keyword)">
                  <i class="me-2" :class="getKeywordIcon(keyword)"></i>{{ keyword }}
                </span>
              </td>
              <td class="py-3 fw-semibold text-end pe-4">{{ count }} lượt</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../../services/api.js';

export default {
  name: 'AiReports',
  data() {
    return {
      exporting: false,
      stats: null,
      topKeywords: {},
      
      aiUsageSeries: [0, 0],
      aiUsageOptions: {
        chart: { type: 'donut', fontFamily: 'Inter, sans-serif' },
        labels: ['Do AI tạo', 'Tạo thủ công'],
        colors: ['#8b5cf6', '#94a3b8'],
        plotOptions: { pie: { donut: { size: '70%' } } },
        dataLabels: { enabled: false },
        stroke: { show: false },
        legend: { position: 'bottom', markers: { radius: 12 } }
      },

      statusChartSeries: [{
        name: 'Số lượng',
        data: [0, 0, 0, 0]
      }],
      statusChartOptions: {
        chart: { type: 'bar', fontFamily: 'Inter, sans-serif', toolbar: { show: false } },
        colors: ['#0ea5e9'],
        plotOptions: { bar: { borderRadius: 4, columnWidth: '45%', distributed: true } },
        dataLabels: { enabled: true, style: { colors: ['#fff'] } },
        xaxis: {
          categories: ['Lên kế hoạch', 'Đang đi', 'Hoàn thành', 'Đã hủy'],
          axisBorder: { show: false },
          axisTicks: { show: false },
          labels: { style: { colors: '#64748b' } }
        },
        yaxis: { labels: { style: { colors: '#64748b' } } },
        grid: { borderColor: '#f1f5f9', strokeDashArray: 4, yaxis: { lines: { show: true } }, xaxis: { lines: { show: false } } },
        colors: ['#f59e0b', '#10b981', '#3b82f6', '#ef4444'],
        legend: { show: false }
      }
    }
  },
  mounted() {
    this.fetchAiStats();
  },
  methods: {
    async fetchAiStats() {
      try {
        const token = localStorage.getItem('key_admin');
        const headers = token ? { Authorization: `Bearer ${token}` } : {};
        
        const res = await api.get('/admin/ai-statistics', { headers });
        const data = res.data?.data;
        
        if (data) {
          this.stats = data;
          this.topKeywords = data.top_ai_keywords || {};

          this.aiUsageSeries = [data.ai_trips || 0, data.manual_trips || 0];

          if (data.status_distribution) {
            this.statusChartSeries = [{
              name: 'Số lượng lịch trình',
              data: [
                data.status_distribution.len_ke_hoach || 0,
                data.status_distribution.dang_di || 0,
                data.status_distribution.hoan_thanh || 0,
                data.status_distribution.da_huy || 0
              ]
            }];
          }
        }
      } catch (error) {
        console.error("Lỗi khi tải dữ liệu thống kê AI", error);
      }
    },
    async exportData() {
      try {
        this.exporting = true;
        const token = localStorage.getItem('key_admin');
        const headers = token ? { Authorization: `Bearer ${token}` } : {};
        
        const response = await api.get('/admin/ai-statistics/export', { headers, responseType: 'blob' });
        
        const blob = new Blob([response.data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
        const link = document.createElement('a');
        link.href = window.URL.createObjectURL(blob);
        link.download = `Bao_Cao_AI_Trips.xlsx`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(link.href);
      } catch (error) {
        console.error("Lỗi khi xuất dữ liệu", error);
        alert('Có lỗi xảy ra khi xuất Excel. Vui lòng thử lại sau.');
      } finally {
        this.exporting = false;
      }
    },
    getKeywordBadgeClass(keyword) {
      if (keyword.includes('Ẩm thực')) return 'bg-orange-subtle text-orange border-orange';
      if (keyword.includes('Check-in')) return 'bg-pink-subtle text-pink border-pink';
      if (keyword.includes('Vui chơi')) return 'bg-blue-subtle text-blue border-blue';
      if (keyword.includes('tâm linh')) return 'bg-purple-subtle text-purple border-purple';
      return 'bg-teal-subtle text-teal border-teal';
    },
    getKeywordIcon(keyword) {
      if (keyword.includes('Ẩm thực')) return 'bi bi-egg-fried';
      if (keyword.includes('Check-in')) return 'bi bi-camera';
      if (keyword.includes('Vui chơi')) return 'bi bi-controller';
      if (keyword.includes('tâm linh')) return 'bi bi-bank';
      return 'bi bi-compass';
    }
  }
}
</script>

<style scoped>
.reports-container {
  animation: fadeIn 0.4s ease-out forwards;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.card-custom {
  background: #ffffff;
  border-radius: 16px;
  border: 1px solid rgba(0,0,0,0.05);
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
  transition: transform 0.2s, box-shadow 0.2s;
}

.card-custom:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
}

/* Custom Badge Colors */
.bg-orange-subtle { background-color: #ffedd5; }
.text-orange { color: #ea580c; }
.border-orange { border-color: #fed7aa !important; }

.bg-pink-subtle { background-color: #fce7f3; }
.text-pink { color: #db2777; }
.border-pink { border-color: #fbcfe8 !important; }

.bg-blue-subtle { background-color: #dbeafe; }
.text-blue { color: #2563eb; }
.border-blue { border-color: #bfdbfe !important; }

.bg-purple-subtle { background-color: #f3e8ff; }
.text-purple { color: #9333ea; }
.border-purple { border-color: #e9d5ff !important; }

.bg-teal-subtle { background-color: #ccfbf1; }
.text-teal { color: #0d9488; }
.border-teal { border-color: #99f6e4 !important; }

.keyword-badge {
  font-weight: 600;
  letter-spacing: 0.2px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.02);
  display: inline-flex;
  align-items: center;
}
</style>
