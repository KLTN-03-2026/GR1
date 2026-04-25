<template>
  <div class="reports-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h2 class="h3 mb-1 text-dark fw-bold">{{ pageTitle }}</h2>
        <p class="text-muted mb-0">{{ pageSubtitle }}</p>
      </div>
      <div class="d-flex gap-2">
        <div class="d-flex align-items-center bg-white border border-secondary-subtle rounded-pill px-3 shadow-sm me-2" style="height: 42px;">
          <i class="bi bi-calendar3 text-primary me-2"></i>
          <input type="date" v-model="startDate" class="form-control border-0 bg-transparent p-0 text-secondary" style="width: 125px; box-shadow: none; outline: none;:focus {box-shadow: none}" />
          <span class="text-muted fw-bold mx-2">→</span>
          <input type="date" v-model="endDate" class="form-control border-0 bg-transparent p-0 text-secondary" style="width: 125px; box-shadow: none; outline: none;" />
          <div class="vr mx-3 text-secondary" style="height: 20px;"></div>
          <button class="btn btn-sm btn-primary rounded-circle d-flex align-items-center justify-content-center p-0" title="Áp dụng Mốc Thời Gian" @click="fetchReportsData" style="width: 28px; height: 28px; transform: translateX(4px);">
            <i class="bi bi-search small"></i>
          </button>
        </div>
        <button class="btn btn-primary d-flex align-items-center px-4" style="border-radius: 999px;">
          <i class="bi bi-download me-2"></i> Trích xuất dữ liệu
        </button>
      </div>
    </div>

    <!-- Quick Stats Row -->
    <div class="row g-3 mb-4" v-if="reportType === 'overview'">
      <div class="col-md-4">
        <div class="card card-custom h-100">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <h6 class="text-muted mb-0 fw-semibold">Tỷ lệ tương tác tích cực</h6>
            </div>
            <div class="fs-2 fw-bold text-dark mb-1">{{ totalUsers > 0 ? ((activeUsers / totalUsers) * 100).toFixed(1) : 0 }}%</div>
            <p class="small text-muted mb-0">Người dùng được mã hóa hợp lệ</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card card-custom h-100">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <h6 class="text-muted mb-0 fw-semibold">Lưu trữ Đánh giá</h6>
            </div>
            <div class="fs-2 fw-bold text-dark mb-1">{{ totalReviews.toLocaleString() }}</div>
            <p class="small text-muted mb-0">Khối lượng Feedback hệ thống ghi nhận</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card card-custom bg-primary text-white h-100">
          <div class="card-body position-relative overflow-hidden">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <h6 class="text-white-50 mb-0 fw-semibold">Thang điểm trung bình</h6>
              <i class="bi bi-star-fill text-white-50"></i>
            </div>
            <div class="fs-2 fw-bold mb-1">{{ avgRating }} / 5.0</div>
            <p class="small text-white-50 mb-0">Chất lượng dịch vụ lõi</p>
            <i class="bi bi-award position-absolute" style="font-size: 8rem; right: -20px; bottom: -40px; opacity: 0.1;"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Stats Row -> Specific Reports -->
    <div class="row g-3 mb-4" v-if="['trips', 'users'].includes(reportType)">
      <!-- Lịch trình -->
      <div class="col-md-6" v-if="reportType === 'trips'">
        <div class="card card-custom h-100">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <h6 class="text-muted mb-0 fw-semibold">Tổng lượt tạo lịch trình</h6>
            </div>
            <div class="fs-2 fw-bold text-dark mb-1">{{ totalTrips.toLocaleString() }}</div>
            <p class="small text-muted mb-0">so với tháng trước (10,873)</p>
          </div>
        </div>
      </div>
      
      <div class="col-md-6" v-if="reportType === 'trips'">
        <div class="card card-custom bg-primary text-white h-100">
          <div class="card-body position-relative overflow-hidden">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <h6 class="text-white-50 mb-0 fw-semibold">Tổng chi tiêu dự kiến</h6>
              <i class="bi bi-wallet2 text-white-50"></i>
            </div>
            <div class="fs-2 fw-bold mb-1">{{ formatCurrency(avgBudget) }}</div>
            <p class="small text-white-50 mb-0">Chi tiêu trung bình/chuyến</p>
            <i class="bi bi-graph-up-arrow position-absolute" style="font-size: 8rem; right: -20px; bottom: -40px; opacity: 0.1;"></i>
          </div>
        </div>
      </div>

      <div class="col-md-6" v-if="reportType === 'users'">
        <div class="card card-custom h-100">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <h6 class="text-muted mb-0 fw-semibold">Người dùng mới</h6>
            </div>
            <div class="fs-2 fw-bold text-dark mb-1">{{ totalUsers.toLocaleString() }}</div>
            <p class="small text-muted mb-0">Tổng tài khoản đã đăng ký</p>
          </div>
        </div>
      </div>
      
      <div class="col-md-6" v-if="reportType === 'users'">
        <div class="card card-custom h-100">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <h6 class="text-muted mb-0 fw-semibold">Nhóm du lịch</h6>
            </div>
            <div class="fs-2 fw-bold text-dark mb-1">{{ totalGroups.toLocaleString() }}</div>
            <p class="small text-muted mb-0">Cộng đồng đang hoạt động</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 mb-4">
      <div :class="reportType === 'overview' ? 'col-lg-8' : 'col-12'" v-if="['overview', 'trips', 'users'].includes(reportType)">
        <div class="card card-custom h-100">
          <div class="card-header border-bottom-0 bg-transparent pt-4 pb-0 d-flex justify-content-between">
            <h5 class="mb-0 fw-bold">{{ getChartTitle() }}</h5>
          </div>
          <div class="card-body">
            <apexchart type="bar" height="320" :options="barChartOptions" :series="filteredBarSeries"></apexchart>
          </div>
        </div>
      </div>
      <div :class="reportType === 'overview' ? 'col-lg-4' : 'col-lg-4'" v-if="['overview', 'places'].includes(reportType)">
        <div class="card card-custom h-100">
          <div class="card-header border-bottom-0 bg-transparent pt-4 pb-0">
            <h5 class="mb-0 fw-bold">Phân bổ danh mục địa điểm</h5>
            <p class="text-muted small">Tỷ lệ các danh mục được thêm vào lịch trình nhiều nhất</p>
          </div>
          <div class="card-body d-flex justify-content-center align-items-center">
            <apexchart type="donut" width="300" :options="donutOptions" :series="donutSeries"></apexchart>
          </div>
        </div>
      </div>

      <!-- Data Table placed inline if places mode -->
      <div class="col-lg-8" v-if="reportType === 'places'">
        <div class="card card-custom h-100 mb-0">
          <div class="card-header border-bottom bg-transparent py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">10 địa điểm nổi bật nhất tháng</h5>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                  <tr>
                    <th class="ps-4 text-secondary py-3">TÊN ĐỊA ĐIỂM</th>
                    <th class="text-secondary py-3">LƯỢT CHỌN</th>
                    <th class="text-secondary py-3">ĐÁNH GIÁ</th>
                    <th class="pe-4 text-end text-secondary py-3">XU HƯỚNG</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(place, index) in topPlaces" :key="index">
                    <td class="ps-4 py-3">
                      <div class="d-flex align-items-center">
                        <span class="fw-bold text-dark">{{ place.name || 'Đang tải...' }}</span>
                      </div>
                    </td>
                    <td class="py-3 fw-semibold text-dark">{{ place.selections.toLocaleString() }}</td>
                    <td class="py-3">
                      <div class="d-flex align-items-center"><i class="bi bi-star-fill text-warning me-1 small"></i><span class="fw-bold">{{ place.rating.toFixed(1) }}</span></div>
                    </td>
                    <td class="pe-4 text-end py-3">
                      <span class="badge" :class="place.trend > 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'">{{ Math.abs(place.trend) }}%</span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Data Table (Overview mode - Full width) -->
    <div class="card card-custom mb-5" v-if="reportType === 'overview'">
      <div class="card-header border-bottom bg-transparent py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">10 địa điểm nổi bật nhất tháng</h5>
        <button class="btn btn-sm btn-light">Xem toàn bộ</button>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th class="py-3 text-secondary fw-semibold text-uppercase" style="font-size: 0.75rem"># Tên địa điểm</th>
              <th class="py-3 text-secondary fw-semibold text-uppercase" style="font-size: 0.75rem">Danh mục</th>
              <th class="py-3 text-secondary fw-semibold text-uppercase" style="font-size: 0.75rem">Lượt chọn</th>
              <th class="py-3 text-secondary fw-semibold text-uppercase" style="font-size: 0.75rem">Đánh giá</th>
              <th class="py-3 text-secondary fw-semibold text-uppercase text-end" style="font-size: 0.75rem">Biến động</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(place, index) in topPlaces" :key="index">
              <td class="py-3">
                <div class="d-flex align-items-center">
                  <div class="bg-light rounded p-2 me-3">
                    <i class="bi bi-geo-alt-fill text-primary" v-if="index < 3"></i>
                    <i class="bi bi-geo-alt text-secondary" v-else></i>
                  </div>
                  <span class="fw-bold text-dark">{{ place.name || 'Đang tải...' }}</span>
                </div>
              </td>
              <td class="py-3">
                <span class="badge bg-light text-secondary border px-2 py-1">{{ place.category }}</span>
              </td>
              <td class="py-3 fw-semibold">{{ place.selections.toLocaleString() }}</td>
              <td class="py-3">
                <div class="d-flex align-items-center">
                  <i class="bi bi-star-fill text-warning me-1" style="font-size: 0.8rem"></i>
                  <span class="fw-bold">{{ place.rating }}</span>
                </div>
              </td>
              <td class="py-3 text-end">
                <span class="badge" :class="place.trend > 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'">
                  <i class="bi" :class="place.trend > 0 ? 'bi-arrow-up-right' : 'bi-arrow-down-right'"></i>
                  {{ Math.abs(place.trend) }}%
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'Reports',
  data() {
    return {
      reportType: 'overview',
      startDate: '',
      endDate: '',
      statsRaw: null,
      totalTrips: 0,
      totalUsers: 0,
      totalGroups: 0,
      avgBudget: 0,
      activeUsers: 0,
      totalReviews: 0,
      avgRating: 0,
      
      barSeries: [{
        name: 'Lượt xếp lịch',
        data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
      }],
      barChartOptions: {
        chart: { type: 'bar', fontFamily: 'Inter, sans-serif', toolbar: { show: false } },
        colors: ['#3874ff', '#10b981'],
        plotOptions: { bar: { borderRadius: 4, columnWidth: '40%' } },
        dataLabels: { enabled: false },
        xaxis: {
          categories: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
          axisBorder: { show: false },
          axisTicks: { show: false },
          labels: { style: { colors: '#64748b' } }
        },
        yaxis: { labels: { style: { colors: '#64748b' } } },
        grid: { borderColor: '#f1f5f9', strokeDashArray: 4, yaxis: { lines: { show: true } }, xaxis: { lines: { show: false } } }
      },
      donutSeries: [],
      donutOptions: {
        chart: { type: 'donut', fontFamily: 'Inter, sans-serif' },
        labels: [],
        colors: ['#3874ff', '#0ea5e9', '#10b981', '#f59e0b', '#94a3b8', '#ec4899', '#8b5cf6'],
        plotOptions: { pie: { donut: { size: '75%' } } },
        dataLabels: { enabled: false },
        stroke: { show: false },
        legend: { position: 'bottom', markers: { radius: 12 } }
      },
      topPlaces: []
    }
  },
  watch: {
    '$route.query.type': {
      immediate: true,
      handler(newType) {
        if (newType && ['overview', 'trips', 'users', 'places'].includes(newType)) {
          this.reportType = newType;
        } else {
          this.reportType = 'overview';
        }
      }
    }
  },
  computed: {
    filteredBarSeries() {
      if (!this.barSeries || this.barSeries.length === 0) return [];
      if (this.reportType === 'trips') {
        return this.barSeries.filter(series => series.name === 'Lượt xếp lịch');
      }
      if (this.reportType === 'users') {
        return this.barSeries.filter(series => series.name === 'Tài khoản đăng ký');
      }
      return this.barSeries;
    },
    pageTitle() {
      switch (this.reportType) {
        case 'trips': return 'Báo cáo Lịch trình & Ngân sách';
        case 'users': return 'Báo cáo Người dùng đăng ký';
        case 'places': return 'Báo cáo Địa điểm nổi bật';
        default: return 'Báo cáo Hoạt động Cốt lõi';
      }
    },
    pageSubtitle() {
      switch (this.reportType) {
        case 'trips': return 'Theo dõi mức độ tăng trưởng lượng lịch trình lập ra và kiểm soát ngân sách trung bình.';
        case 'users': return 'Đo lường mức độ thu hút tài khoản đăng ký mới và tương tác các cộng đồng sinh hoạt.';
        case 'places': return 'Xác định các tọa độ điểm đến du lịch thịnh hành và xu hướng lựa chọn của du khách.';
        default: return 'Giám sát chỉ số tương tác, tỷ lệ chuyển đổi và vòng đời phản hồi của toàn bộ hệ thống.';
      }
    }
  },
  mounted() {
    this.fetchReportsData()
  },
  methods: {
    getChartTitle() {
      if (this.reportType === 'trips') return 'Theo dõi Lưu lượng Lịch trình';
      if (this.reportType === 'users') return 'Biểu đồ Tăng trưởng Nguồn Người dùng';
      return 'Biểu đồ Tăng trưởng Tổng quan 2026';
    },
    formatCurrency(value) {
      if (!value) return '0 đ';
      return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(Number(value));
    },
    async fetchReportsData() {
      try {
        const token = localStorage.getItem('key_admin')
        const headers = token ? { Authorization: `Bearer ${token}` } : {}
        
        let url = 'http://127.0.0.1:8000/api/admin/statistics?time_filter=year'
        if (this.startDate && this.endDate) {
          url += `&start_date=${this.startDate}&end_date=${this.endDate}`
        }
        
        const statsRes = await axios.get(url, { headers })
        const data = statsRes.data?.data
        
        if (data) {
          this.totalTrips = data.total_trips || 0
          this.totalUsers = data.total_users || 0
          this.totalGroups = data.total_groups || 0
          this.avgBudget = data.avg_budget || 0
          this.activeUsers = data.active_users || 0
          this.totalReviews = data.total_reviews || 0
          this.avgRating = data.avg_rating || 0
          
          if (data.top_places) {
            this.topPlaces = data.top_places
          }
          
          if (data.monthly_trips) {
            this.barSeries = [{
              name: 'Lượt xếp lịch',
              data: data.monthly_trips
            }, {
              name: 'Tài khoản đăng ký',
              data: data.monthly_users || []
            }]
            
            if (data.chart_labels) {
              this.barChartOptions = {
                ...this.barChartOptions,
                xaxis: {
                  ...this.barChartOptions.xaxis,
                  categories: data.chart_labels
                }
              }
            }
          }
          
          if (data.places_by_category && data.places_by_category.length > 0) {
            this.donutSeries = data.places_by_category.map(item => item.total)
            this.donutOptions = {
              ...this.donutOptions,
              labels: data.places_by_category.map(item => item.ten_danh_muc)
            }
          }
        }
      } catch (error) {
        console.error("Lỗi khi tải dữ liệu reports", error)
      }
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

.bg-primary { background-color: #3874ff !important; }
.text-primary { color: #3874ff !important; }
</style>
