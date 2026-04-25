<template>
  <div class="dashboard">
    <div class="hero-card mb-4">
      <div class="hero-copy">
        <p class="hero-eyebrow">Da Nang Travel Planner</p>
        <h1 class="page-title">Dashboard quản trị hệ thống đề xuất lịch trình và tối ưu ngân sách</h1>
        <p class="page-subtitle">
          Trang này tập trung vào dữ liệu phục vụ đề tài: người dùng, nhân sự vận hành và các hạng mục phân tích
          cần bổ sung sau. Không sử dụng mô hình đơn đặt hàng hay doanh thu.
        </p>
      </div>

      <button class="btn btn-refresh" :disabled="isLoading" @click="fetchDashboardData">
        <span v-if="isLoading" class="spinner-border spinner-border-sm me-2" role="status"></span>
        <i v-else class="bi bi-arrow-clockwise me-2"></i>Làm mới
      </button>
    </div>

    <div v-if="errorMessage" class="alert alert-danger dashboard-alert mb-4">
      {{ errorMessage }}
    </div>

    <div class="section-heading">
      <h2>Tổng quan hiện có</h2>
      <p>Dữ liệu đang hiển thị từ các API đã nối được</p>
    </div>

    <div class="row g-3 mb-4">
      <div class="col-md-6 col-xl-3">
        <div class="stats-card card-users">
          <div class="stats-icon">
            <i class="bi bi-people-fill"></i>
          </div>
          <div class="stats-content">
            <p class="stats-label">Người dùng hệ thống</p>
            <h2 class="stats-value">{{ formatNumber(totalUsers) }}</h2>
            <small class="stats-note">Tổng số tài khoản người dùng đang có</small>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-xl-3">
        <div class="stats-card card-team">
          <div class="stats-icon">
            <i class="bi bi-person-badge-fill"></i>
          </div>
          <div class="stats-content">
            <p class="stats-label">Nhân sự vận hành</p>
            <h2 class="stats-value">{{ formatNumber(totalAdmins) }}</h2>
            <small class="stats-note">Bao gồm quản trị viên và nhân viên</small>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-xl-3">
        <div class="stats-card card-active">
          <div class="stats-icon">
            <i class="bi bi-person-check-fill"></i>
          </div>
          <div class="stats-content">
            <p class="stats-label">Nhân sự hoạt động</p>
            <h2 class="stats-value">{{ formatNumber(activeAdmins) }}</h2>
            <small class="stats-note">Tài khoản có thể truy cập hệ thống</small>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-xl-3">
        <div class="stats-card card-inactive">
          <div class="stats-icon">
            <i class="bi bi-person-lock-fill"></i>
          </div>
          <div class="stats-content">
            <p class="stats-label">Nhân sự tạm khóa</p>
            <h2 class="stats-value">{{ formatNumber(inactiveAdmins) }}</h2>
            <small class="stats-note">Cần kiểm tra nếu số lượng tăng bất thường</small>
          </div>
        </div>
      </div>
    </div>

    <div class="section-heading">
      <h2>Chỉ số theo đề tài</h2>
      <p>Những ô chưa có nguồn dữ liệu thật sẽ được để trống rõ ràng</p>
    </div>

    <div class="row g-3 mb-4">
      <div class="col-md-6 col-xl-3">
        <div class="stat-card stat-blue">
          <div class="stat-icon">
            <i class="bi bi-map"></i>
          </div>
          <div class="stat-details">
            <span class="stat-label">Chuyến đi đề xuất</span>
            <div class="stat-value" v-if="stats">{{ formatNumber(stats.total_trips) }}</div>
            <div class="stat-value" v-else>--</div>
            <div class="stat-trend" :class="getTripGrowthClass()" v-if="stats">
              <i class="bi" :class="getTripGrowthIcon()"></i> 
              {{ Math.abs(stats.trips_growth_percentage) }}% so với tháng trước
            </div>
            <div class="stat-trend increasing" v-else>
              <i class="bi bi-arrow-up"></i> ...
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-xl-3">
        <div class="stat-card stat-green">
          <div class="stat-icon">
            <i class="bi bi-geo-alt-fill"></i>
          </div>
          <div class="stat-details">
            <span class="stat-label">Địa điểm du lịch</span>
            <div class="stat-value" v-if="stats">{{ formatNumber(stats.total_places) }}</div>
            <div class="stat-value" v-else>--</div>
            <div class="stat-trend">
              Hệ thống đã sẵn sàng
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-xl-3">
        <div class="stat-card stat-orange">
          <div class="stat-icon">
            <i class="bi bi-wallet2"></i>
          </div>
          <div class="stat-details">
            <span class="stat-label">Ngân sách TB</span>
            <div class="stat-value" v-if="stats" style="font-size: 1.25rem;">{{ formatCurrency(stats.avg_budget) }}</div>
            <div class="stat-value" v-else>--</div>
            <div class="stat-trend text-muted">
              Dữ liệu từ người dùng
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-xl-3">
        <div class="stat-card stat-purple">
          <div class="stat-icon">
            <i class="bi bi-stars"></i>
          </div>
          <div class="stat-details">
            <span class="stat-label">Đánh giá hệ thống</span>
            <div class="stat-value" v-if="stats">{{ stats.avg_rating }}</div>
            <div class="stat-value" v-else>--</div>
            <div class="stat-trend increasing">
              <i class="bi bi-star-fill text-warning me-1"></i> Trung bình
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- PHOENIX STYLE CHART -->
    <div class="row g-3 mb-4">
      <div class="col-lg-8">
        <div class="card card-custom h-100">
          <div class="card-header border-bottom-0 pb-0">
            <div class="d-flex justify-content-between align-items-center w-100">
              <div>
                <h5 class="mb-0">Thống kê Lịch trình & Tài khoản</h5>
                <p class="text-muted small mb-0 mt-1" style="font-size: 0.8rem">Lịch trình đề xuất so với Người dùng mới</p>
              </div>
              <div>
                <select v-model="timeFilter" @change="fetchDashboardData" class="form-select form-select-sm border-0 bg-light fw-bold text-secondary" style="cursor: pointer; box-shadow: none;">
                  <option value="year">Năm nay</option>
                  <option value="this_month">Tháng này</option>
                  <option value="last_month">Tháng trước</option>
                </select>
              </div>
            </div>
          </div>
          <div class="card-body pt-2">
            <apexchart type="area" height="330" :options="chartOptions" :series="series"></apexchart>
          </div>
        </div>
      </div>
      <!-- SECOND INDISPUTABLE CHART: DONUT CHART -->
      <div class="col-lg-4">
        <div class="card card-custom h-100">
          <div class="card-header border-bottom-0 pb-0">
            <h5 class="mb-0">Tỷ trọng Danh mục Địa điểm</h5>
            <p class="text-muted small mb-0 mt-1" style="font-size: 0.8rem">Phân bổ kho dữ liệu toàn hệ thống</p>
          </div>
          <div class="card-body d-flex justify-content-center align-items-center pt-2">
            <apexchart type="donut" width="310" :options="donutOptions" :series="donutSeries"></apexchart>
          </div>
        </div>
      </div>
    </div>

    <!-- ── MỨC ĐỘ YÊU THÍCH ĐỊA ĐIỂM ── -->
    <div class="row g-3 mb-4">
      <!-- Chart cột trái -->
      <div class="col-lg-8">
        <div class="card card-custom fav-chart-card h-100">
          <div class="card-header border-bottom-0 pb-0">
            <div class="d-flex justify-content-between align-items-center w-100">
              <div>
                <h5 class="mb-0 d-flex align-items-center gap-2" style="color: #e11d48; font-weight: 800;">
                  <span class="fav-icon-wrap"><i class="bi bi-heart-fill"></i></span>
                  Mức độ yêu thích địa điểm
                </h5>
                <p class="text-muted small mb-0 mt-1" style="font-size:0.8rem">Top 10 địa điểm được người dùng thêm vào yêu thích nhiều nhất</p>
              </div>
            </div>
          </div>
          <div class="card-body pt-3 pb-3">
            <template v-if="favoriteItems.length">
              <div class="custom-html-chart">
                <!-- Vạch dọc phân cách trục tung (Y-axis) để ra đúng tinh thần chart -->
                <div class="chart-y-axis"></div>
                <div class="html-chart-row" v-for="(item, idx) in favoriteItems" :key="item.id">
                  <!-- Tên địa điểm -->
                  <div class="h-c-label text-truncate" :title="item.name">{{ item.name }}</div>
                  
                  <!-- Thanh Progress Area -->
                  <div class="h-c-track-container">
                    <div class="h-c-track">
                      <!-- Màu nhạt/xám cho hạng 4 trở xuống, màu chàm cho Top 3 -->
                      <div class="h-c-fill"
                           :class="idx < 3 ? 'fill-top' : 'fill-rest'"
                           :style="{ width: (item.count / (favoriteItems[0]?.count || 1) * 100) + '%' }">
                      </div>
                    </div>
                  </div>

                  <!-- Con số nằm trọn vẹn ở cuối -->
                  <div class="h-c-value">{{ formatNumber(item.count) }}</div>
                </div>
              </div>
            </template>
            <template v-else>
              <div class="empty-state py-5 text-center mt-3">
                <div class="empty-icon-circle mb-3 mx-auto"><i class="bi bi-heart text-muted"></i></div>
                <p class="text-muted fw-500">Chưa có dữ liệu thống kê yêu thích</p>
              </div>
            </template>
          </div>
        </div>
      </div>

      <!-- Summary cột phải -->
      <div class="col-lg-4">
        <div class="d-flex flex-column gap-3 h-100">
          <!-- Tổng lượt yêu thích -->
          <div class="card card-custom fav-summary-card">
            <div class="card-body d-flex align-items-center gap-3 py-3">
              <div class="fav-sum-icon" style="background:#e11d48">
                <i class="bi bi-heart-fill"></i>
              </div>
              <div>
                <div class="fav-sum-label">Tổng lượt yêu thích</div>
                <div class="fav-sum-value">{{ stats ? formatNumber(stats.total_favorites) : '--' }}</div>
              </div>
            </div>
          </div>

          <!-- Địa điểm được yêu thích nhất -->
          <div class="card card-custom fav-summary-card flex-grow-1" v-if="favoriteItems.length">
            <div class="card-body py-3">
              <div class="fav-sum-label mb-2"><i class="bi bi-bar-chart-fill text-primary me-1"></i>Top 3 nổi bật</div>
              <div v-for="(item, i) in favoriteItems.slice(0, 3)" :key="i" class="fav-top3-row">
                <span :class="['fav-top3-rank', 'rank-' + (i+1)]"><i class="bi bi-award-fill"></i></span>
                <div class="fav-top3-info">
                  <span class="fav-top3-name">{{ item.name }}</span>
                  <span class="fav-top3-count"><i class="bi bi-heart-fill"></i> {{ formatNumber(item.count) }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row g-3 mb-4">
      <div class="col-lg-6">
        <div class="card card-custom h-100">
          <div class="card-header border-bottom-0 pb-0">
            <div class="d-flex align-items-center gap-2">
              <span class="fav-icon-wrap" style="background: #f0fdfa; color: #0d9488"><i class="bi bi-star-fill"></i></span>
              <h5 class="mb-0 fw-bold" style="color: #0d9488;">Địa điểm nổi bật</h5>
            </div>
            <p class="text-muted small mb-0 mt-2" style="font-size: 0.8rem">Các địa điểm được nhắc đến/thêm vào lịch trình nhiều nhất</p>
          </div>
          <div class="card-body pt-0 pb-2">
            <template v-if="popularPlacesItems && popularPlacesItems.length">
              <div class="popular-html-chart mt-2">
                <div
                  class="popular-row"
                  v-for="(item, idx) in popularPlacesItems"
                  :key="item.id_dia_diem || item.id || idx"
                >
                  <div class="popular-label text-truncate" :title="getPopularPlaceName(item)">
                    {{ getPopularPlaceName(item) }}
                  </div>
                  <div class="popular-track-wrap">
                    <div class="popular-track">
                      <div
                        class="popular-fill"
                        :class="idx < 3 ? 'popular-fill-top' : ''"
                        :style="{ width: getPopularBarWidth(item) }"
                      ></div>
                    </div>
                  </div>
                  <div class="popular-value">{{ formatNumber(getPopularPlaceCount(item)) }}</div>
                </div>
              </div>
            </template>
            <template v-else>
              <div class="empty-state py-5 mt-4">
                <i class="bi bi-star text-muted fs-4"></i>
                <p class="text-muted small mt-2">Chưa có dữ liệu nổi bật</p>
              </div>
            </template>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="card card-custom h-100">
          <div class="card-header border-bottom-0 pb-0">
            <div class="d-flex align-items-center gap-2">
              <span class="fav-icon-wrap" style="background: #eff6ff; color: #2563eb"><i class="bi bi-people-fill"></i></span>
              <h5 class="mb-0 fw-bold" style="color: #2563eb;">Cộng đồng Mạng lưới</h5>
            </div>
            <p class="text-muted small mb-0 mt-2" style="font-size: 0.8rem">Top 5 nhóm du lịch đông thành viên nhất</p>
          </div>
          <div class="card-body pt-0 pb-2">
            <template v-if="stats && stats.top_groups && stats.top_groups.length">
              <apexchart type="bar" height="280" :options="horizontalBarOptions" :series="horizontalBarSeries"></apexchart>
            </template>
            <template v-else>
              <div class="empty-state py-5 mt-4">
                <i class="bi bi-inboxes text-muted fs-4"></i>
                <p class="text-muted small mt-2">Chưa có dữ liệu nhóm du lịch</p>
              </div>
            </template>
          </div>
        </div>
      </div>
    </div>

    <div class="row mb-4">
      <div class="col-12">
        <div class="card card-custom">
          <div class="card-header">
            <h5 class="mb-0">Tình hình nhân sự quản trị</h5>
          </div>
          <div class="card-body">
            <div class="overview-grid">
              <div class="overview-item">
                <span class="overview-label">Quản trị viên</span>
                <strong class="overview-value">{{ formatNumber(adminRoleCount) }}</strong>
              </div>

              <div class="overview-item">
                <span class="overview-label">Nhân viên</span>
                <strong class="overview-value">{{ formatNumber(staffRoleCount) }}</strong>
              </div>

              <div class="overview-item">
                <span class="overview-label">Tỷ lệ tài khoản hoạt động</span>
                <strong class="overview-value">{{ activeRate }}%</strong>
              </div>

              <div class="overview-item">
                <span class="overview-label">Thông điệp tự động</span>
                <strong class="overview-value overview-text" :class="systemRecommendationClass">
                  {{ systemRecommendation }}
                </strong>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="section-heading">
      <h2>Dữ liệu vận hành gần đây</h2>
      <p>Ưu tiên các thông tin đang hỗ trợ trực tiếp cho quản trị hệ thống</p>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="card card-custom">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Nhân viên gần đây</h5>
            <router-link to="/admin/danh-sach-nhan-vien" class="text-primary small text-decoration-none">
              Xem danh sách
            </router-link>
          </div>
          <div class="card-body p-0">
            <div v-if="isLoading" class="empty-state">
              <i class="bi bi-arrow-repeat spin"></i>
              <p>Đang tải dữ liệu dashboard...</p>
            </div>

            <div v-else-if="!recentAdmins.length" class="empty-state">
              <i class="bi bi-inboxes"></i>
              <p>Chưa có dữ liệu nhân viên để hiển thị</p>
            </div>

            <div v-else class="table-responsive">
              <table class="table table-hover mb-0">
                <thead>
                  <tr>
                    <th>Nhân viên</th>
                    <th>Email</th>
                    <th>Số điện thoại</th>
                    <th>Chức vụ</th>
                    <th>Trạng thái</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="admin in recentAdmins" :key="admin.id">
                    <td class="fw-600">{{ getDisplayName(admin) }}</td>
                    <td>{{ admin.email || 'Chưa có email' }}</td>
                    <td>{{ admin.so_dien_thoai || admin.sdt || 'Chưa cập nhật' }}</td>
                    <td>{{ getRole(admin) }}</td>
                    <td>
                      <span class="badge" :class="getStatusClass(admin)">
                        {{ getStatusLabel(admin) }}
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

const USERS_API_URL = 'http://127.0.0.1:8000/api/admin/nguoi-dungs/get-data'
const ADMINS_API_URL = 'http://127.0.0.1:8000/api/admin/danh-sach-nhan-vien'

export default {
  name: 'Dashboard',
  data() {
    return {
      isLoading: false,
      errorMessage: '',
      timeFilter: 'year',
      users: [],
      admins: [],
      stats: null,
      series: [
        {
          name: 'Lượt tạo lịch trình (Thực tế)',
          data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0] // Default, updated on load
        },
        {
          name: 'Dự kiến (Projection)',
          data: [15, 20, 25, 30, 45, 60, 50, 40, 35, 30, 25, 20] // Static mock for Projection
        }
      ],
      chartOptions: {
        chart: {
          height: 330,
          type: 'area',
          fontFamily: 'Inter, sans-serif',
          toolbar: { show: false },
          zoom: { enabled: false }
        },
        colors: ['#3874ff', '#94a3b8'],
        dataLabels: { enabled: false },
        stroke: {
          curve: 'smooth',
          width: [3, 2],
          dashArray: [0, 5]
        },
        fill: {
          type: ['gradient', 'solid'],
          gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.4,
            opacityTo: 0.05,
            stops: [0, 90, 100]
          },
          opacity: [1, 0.1]
        },
        xaxis: {
          categories: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
          axisBorder: { show: false },
          axisTicks: { show: false },
          labels: {
            style: { colors: '#64748b', fontSize: '13px' },
            hideOverlappingLabels: true
          }
        },
        yaxis: {
          labels: {
            style: { colors: '#64748b', fontSize: '13px' },
            formatter: (val) => { return val }
          }
        },
        grid: {
          borderColor: '#f1f5f9',
          strokeDashArray: 4,
          yaxis: { lines: { show: true } },
          xaxis: { lines: { show: false } },
          padding: { top: 0, right: 0, bottom: 0, left: 10 }
        },
        legend: {
          position: 'top',
          horizontalAlign: 'right',
          markers: { radius: 12 }
        },
        tooltip: {
          theme: 'light',
          y: { formatter: function (val) { return val + " lượt" } }
        }
      },
      donutSeries: [44, 55, 41, 15],
      donutOptions: {
        chart: { type: 'donut', fontFamily: 'Inter, sans-serif' },
        labels: ['Ẩm thực', 'Check-in', 'Giải trí', 'Tâm linh'],
        colors: ['#3874ff', '#10b981', '#f59e0b', '#0ea5e9', '#ec4899', '#8b5cf6', '#94a3b8'],
        plotOptions: { pie: { donut: { size: '75%' } } },
        dataLabels: { enabled: false },
        stroke: { show: false },
        legend: { position: 'bottom', markers: { radius: 12 } },
        tooltip: { theme: 'light', y: { formatter: function (val) { return val + " địa điểm" } } }
      },
      horizontalBarSeries: [{ name: 'Thành viên', data: [] }],
      horizontalBarOptions: {
        chart: { type: 'bar', fontFamily: 'Inter, sans-serif', toolbar: { show: false } },
        plotOptions: { bar: { horizontal: true, borderRadius: 4, barHeight: '50%' } },
        colors: ['#0ea5e9'],
        dataLabels: { enabled: true, style: { colors: ['#fff'] } },
        xaxis: { categories: [], labels: { show: false }, axisBorder: { show: false }, axisTicks: { show: false } },
        yaxis: { labels: { style: { colors: '#64748b', fontWeight: 600 } } },
        grid: { show: false },
        tooltip: { theme: 'light' }
      },
      // Favorite places configurations
      favoriteItems: [],
      // Popular places config (Ít màu sắc, chuyên nghiệp)
      popularPlacesItems: [],
      popBarSeries: [{ name: 'Lượt chọn', data: [] }],
      popBarChartOptions: {
        chart: { type: 'bar', fontFamily: 'Inter, sans-serif', toolbar: { show: false }, animations: { speed: 800 } },
        grid: { 
          show: true, borderColor: '#f1f5f9', strokeDashArray: 4, 
          xaxis: { lines: { show: true } }, yaxis: { lines: { show: false } },
          padding: { top: 0, right: 10, bottom: 0, left: 10 }
        },
        plotOptions: { bar: { horizontal: true, borderRadius: 3, barHeight: '40%', dataLabels: { position: 'right' } } },
        colors: ['#0d9488'], // Muted elegant Teal
        dataLabels: { enabled: true, textAnchor: 'start', formatter: (val) => val.toLocaleString('vi-VN'), style: { colors: ['#475569'], fontSize: '11px', fontWeight: 600 }, offsetX: 6 },
        xaxis: { categories: [], labels: { show: true, style: { colors: '#94a3b8', fontSize: '12px' }, formatter: function (val) { return Math.round(val) } }, axisBorder: { show: false }, axisTicks: { show: false } },
        yaxis: { labels: { style: { colors: '#334155', fontWeight: 600 }, maxWidth: 160 } },
        tooltip: { theme: 'light', y: { formatter: function (val) { return val + " lượt đưa vào lịch trình" } } }
      }
    }
  },
  computed: {
    totalUsers() {
      return this.users.length
    },
    totalAdmins() {
      return this.admins.length
    },
    activeAdmins() {
      return this.admins.filter((admin) => Number(admin.trang_thai ?? admin.trang_thai_hoat_dong) === 1).length
    },
    inactiveAdmins() {
      return this.totalAdmins - this.activeAdmins
    },
    adminRoleCount() {
      return this.admins.filter((admin) => Number(admin.id_chuc_vu || admin.chuc_vu) === 1).length
    },
    staffRoleCount() {
      return this.admins.filter((admin) => Number(admin.id_chuc_vu || admin.chuc_vu) === 2).length
    },
    activeRate() {
      if (!this.totalAdmins) return 0
      return Math.round((this.activeAdmins / this.totalAdmins) * 100)
    },
    systemRecommendation() {
      if (!this.stats) return 'Đang tổng hợp dữ liệu hệ thống...';
      if (this.activeRate < 50) return 'Cảnh báo: Hơn một nửa tài khoản nhân sự đang bị khóa.';
      if (this.stats.trips_growth_percentage < 0) return 'Chú ý: Nhu cầu tạo lịch trình đang suy giảm so với tháng trước.';
      return 'Hệ thống đang tăng trưởng và vận hành rất ổn định.';
    },
    systemRecommendationClass() {
      if (!this.stats) return 'text-muted';
      if (this.activeRate < 50 || this.stats.trips_growth_percentage < -10) return 'text-danger';
      if (this.stats.trips_growth_percentage < 0) return 'text-warning';
      return 'text-success';
    },
    isSuperAdmin() {
      const raw = localStorage.getItem('admin_data');
      if (raw) {
        try {
          const adminData = JSON.parse(raw);
          return Number(adminData?.id_chuc_vu || adminData?.chuc_vu) === 1;
        } catch(e) {}
      }
      return false;
    },
    recentAdmins() {
      return [...this.admins]
        .sort((a, b) => new Date(b.updated_at || b.created_at || 0) - new Date(a.updated_at || a.created_at || 0))
        .slice(0, 6)
    },
  },
  mounted() {
    this.fetchDashboardData()
  },
  methods: {
    getTripGrowthClass() {
      if (!this.stats) return 'text-muted';
      const pct = this.stats.trips_growth_percentage;
      return pct > 0 ? 'increasing' : (pct < 0 ? 'decreasing' : 'text-muted');
    },
    getTripGrowthIcon() {
      if (!this.stats) return 'bi-circle';
      const pct = this.stats.trips_growth_percentage;
      return pct > 0 ? 'bi-arrow-up' : (pct < 0 ? 'bi-arrow-down' : 'bi-dash-lg');
    },
    authHeader() {
      const token = localStorage.getItem('key_admin')
      return token
        ? {
            headers: {
              Authorization: `Bearer ${token}`,
            },
          }
        : {}
    },
    async fetchDashboardData() {
      this.isLoading = true
      this.errorMessage = ''

      try {
        const statsRes = await axios.get(`http://127.0.0.1:8000/api/admin/statistics?time_filter=${this.timeFilter}`, this.authHeader())
        this.stats = statsRes.data?.data
        
        // Update Chart Labels dynamically
        if (this.stats?.chart_labels) {
          this.chartOptions = {
            ...this.chartOptions,
            xaxis: {
              ...this.chartOptions.xaxis,
              categories: this.stats.chart_labels
            }
          }
        }

        // Update Actual & Users series using backend data
        if (this.stats?.monthly_trips) {
          const newSeries = [...this.series];
          newSeries[0].data = this.stats.monthly_trips;
          
          if (this.stats.monthly_users) {
            newSeries[1].name = 'Tài khoản đăng ký mới';
            newSeries[1].data = this.stats.monthly_users;
          }
          
          this.series = newSeries;
        }

        if (this.stats?.places_by_category && this.stats.places_by_category.length > 0) {
          this.donutSeries = this.stats.places_by_category.map(item => item.total);
          this.donutOptions = {
            ...this.donutOptions,
            labels: this.stats.places_by_category.map(item => item.ten_danh_muc)
          };
        }
        
        if (this.stats?.top_groups && this.stats.top_groups.length > 0) {
          this.horizontalBarSeries = [{
            name: 'Thành viên',
            data: this.stats.top_groups.map(group => group.members)
          }];
          this.horizontalBarOptions = {
            ...this.horizontalBarOptions,
            xaxis: {
              ...this.horizontalBarOptions.xaxis,
              categories: this.stats.top_groups.map(group => group.name)
            }
          };
        }

        // Favorite places chart — data từ /api/admin/statistics
        if (this.stats?.top_favorites && this.stats.top_favorites.length > 0) {
          this.favoriteItems = this.stats.top_favorites.map(item => ({
            id: item.id_dia_diem ?? item.id,
            name: item.ten_dia_diem ?? item.name ?? 'Địa điểm',
            category: item.loai_dia_diem ?? item.category ?? '',
            count: Number(item.so_luot_yeu_thich ?? item.count ?? 0)
          }));
        }

        // Popular places (Địa điểm nổi bật)
        if (this.stats?.top_places && this.stats.top_places.length > 0) {
          this.popularPlacesItems = this.stats.top_places;
          this.popBarSeries = [{ name: 'Lượt được chọn', data: this.popularPlacesItems.map(i => i.selections) }];
          this.popBarChartOptions = {
            ...this.popBarChartOptions,
            xaxis: {
              ...this.popBarChartOptions.xaxis,
              categories: this.popularPlacesItems.map(i => i.name)
            }
          };
        }

        const promises = [axios.get(USERS_API_URL, this.authHeader())];
        if (this.isSuperAdmin) {
          promises.push(axios.get(ADMINS_API_URL, this.authHeader()));
        }

        const responses = await Promise.all(promises);
        this.users = Array.isArray(responses[0].data?.data) ? responses[0].data.data : [];
        if (this.isSuperAdmin && responses.length > 1) {
          this.admins = Array.isArray(responses[1].data?.data) ? responses[1].data.data : [];
        } else {
          this.admins = [];
        }
      } catch (error) {
        this.users = []
        this.admins = []
        this.errorMessage = error.response?.data?.message || 'Không thể tải dữ liệu dashboard lúc này.'
      } finally {
        this.isLoading = false
      }
    },
    formatNumber(value) {
      return new Intl.NumberFormat('vi-VN').format(Number(value || 0))
    },
    formatCurrency(value) {
      return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(Number(value || 0))
    },
    getDisplayName(admin) {
      return admin.ho_ten || admin.ten || admin.email || `Nhân viên #${admin.id ?? ''}`
    },
    getRole(admin) {
      if (admin.ten_chuc_vu) {
        return admin.ten_chuc_vu
      }

      const roleMap = {
        1: 'Quản trị viên',
        2: 'Nhân viên',
      }

      return roleMap[Number(admin.id_chuc_vu || admin.chuc_vu)] || 'Chưa rõ'
    },
    getStatusLabel(admin) {
      return Number(admin.trang_thai ?? admin.trang_thai_hoat_dong) === 1 ? 'Đang hoạt động' : 'Tạm khóa'
    },
    getStatusClass(admin) {
      return Number(admin.trang_thai ?? admin.trang_thai_hoat_dong) === 1 ? 'badge-success' : 'badge-danger'
    },
    getPopularPlaceName(item) {
      return item?.name || item?.ten_dia_diem || 'Địa điểm'
    },
    getPopularPlaceCount(item) {
      return Number(item?.selections ?? item?.count ?? item?.total ?? 0)
    },
    getPopularBarWidth(item) {
      const maxValue = Math.max(
        ...this.popularPlacesItems.map((place) => this.getPopularPlaceCount(place)),
        1
      )
      const pct = (this.getPopularPlaceCount(item) / maxValue) * 100
      return `${Math.max(pct, 8)}%`
    },
  },
}
</script>

<style scoped>
.dashboard {
  animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(8px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.hero-card {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 24px;
  padding: 28px;
  border-radius: 24px;
  background:
    radial-gradient(circle at top right, rgba(14, 165, 233, 0.22), transparent 30%),
    linear-gradient(135deg, #0f172a 0%, #102a43 55%, #164e63 100%);
  color: #fff;
  box-shadow: 0 20px 40px rgba(15, 23, 42, 0.14);
}

.hero-copy {
  max-width: 840px;
}

.hero-eyebrow {
  margin-bottom: 10px;
  color: rgba(255, 255, 255, 0.72);
  font-size: 0.8rem;
  font-weight: 700;
  letter-spacing: 0.18em;
  text-transform: uppercase;
}

.page-title {
  font-size: 2rem;
  font-weight: 800;
  line-height: 1.25;
  margin-bottom: 12px;
}

.page-subtitle {
  max-width: 760px;
  margin: 0;
  color: rgba(255, 255, 255, 0.82);
  line-height: 1.7;
}

.btn-refresh {
  white-space: nowrap;
  background: rgba(255, 255, 255, 0.12);
  color: #fff;
  border: 1px solid rgba(255, 255, 255, 0.18);
  border-radius: 12px;
  padding: 10px 18px;
  font-weight: 600;
}

.btn-refresh:hover {
  background: rgba(255, 255, 255, 0.18);
  color: #fff;
}

.dashboard-alert {
  border-radius: 14px;
}

.section-heading {
  margin-bottom: 14px;
}

.section-heading h2 {
  margin: 0 0 4px;
  font-size: 1.1rem;
  font-weight: 700;
  color: #1e293b;
}

.section-heading p {
  margin: 0;
  color: #64748b;
}

.stats-card,
.metric-panel,
.card-custom {
  background: #fff;
  border: 1px solid #e9ecef;
  border-radius: 18px;
  box-shadow: 0 10px 28px rgba(15, 23, 42, 0.04);
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.stats-card:hover,
.metric-panel:hover,
.card-custom:hover {
  transform: translateY(-2px);
  box-shadow: 0 14px 30px rgba(15, 23, 42, 0.08);
}

.stats-card {
  position: relative;
  overflow: hidden;
  display: flex;
  gap: 16px;
  padding: 20px;
}

.stats-card::before {
  content: '';
  position: absolute;
  left: 0;
  right: 0;
  top: 0;
  height: 4px;
}

.card-users::before {
  background: linear-gradient(90deg, #2563eb, #0ea5e9);
}

.card-team::before {
  background: linear-gradient(90deg, #0f766e, #14b8a6);
}

.card-active::before {
  background: linear-gradient(90deg, #16a34a, #22c55e);
}

.card-inactive::before {
  background: linear-gradient(90deg, #dc2626, #f97316);
}

.stats-icon {
  width: 60px;
  height: 60px;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.8rem;
  flex-shrink: 0;
}

.card-users .stats-icon {
  background: rgba(37, 99, 235, 0.12);
  color: #2563eb;
}

.card-team .stats-icon {
  background: rgba(20, 184, 166, 0.12);
  color: #0f766e;
}

.card-active .stats-icon {
  background: rgba(34, 197, 94, 0.12);
  color: #15803d;
}

.card-inactive .stats-icon {
  background: rgba(239, 68, 68, 0.12);
  color: #dc2626;
}

.stats-content {
  flex: 1;
}

.stats-label {
  margin-bottom: 8px;
  font-size: 0.8rem;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: #94a3b8;
  font-weight: 700;
}

.stats-value {
  margin: 0;
  font-size: 1.85rem;
  font-weight: 800;
  color: #0f172a;
}

.stats-note {
  display: inline-block;
  margin-top: 8px;
  color: #64748b;
}

.metric-panel {
  height: 100%;
  padding: 18px;
}

.metric-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
  color: #334155;
  font-weight: 700;
}

.metric-header i {
  color: #0ea5e9;
  font-size: 1.1rem;
}

.metric-empty,
.metric-value {
  padding: 22px 0 14px;
  font-size: 2rem;
  font-weight: 800;
  color: #0f172a;
}
.metric-empty { color: #cbd5e1; }

.metric-footnote {
  margin: 0;
  color: #64748b;
  line-height: 1.6;
}

.card-header {
  background: #f8fafc;
  border-bottom: 1px solid #edf2f7;
  padding: 16px 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.card-header h5 {
  margin: 0;
  font-size: 1rem;
  font-weight: 700;
  color: #1e293b;
}

.overview-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 14px;
}

.overview-item {
  min-height: 116px;
  padding: 16px;
  border-radius: 14px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.overview-label {
  color: #64748b;
  font-size: 0.85rem;
}

.overview-value {
  color: #0f172a;
  font-size: 1.2rem;
}

.overview-text {
  font-size: 0.95rem;
  line-height: 1.6;
  font-weight: 600;
}

.todo-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.todo-item {
  display: flex;
  gap: 12px;
  align-items: flex-start;
  padding: 12px 0;
  border-bottom: 1px solid #edf2f7;
  color: #334155;
}

.todo-item:last-child {
  border-bottom: 0;
  padding-bottom: 0;
}

.todo-item i {
  color: #0ea5e9;
  margin-top: 2px;
}

.table {
  margin-bottom: 0;
}

.table thead th {
  background: #f8fafc;
  color: #64748b;
  font-size: 0.8rem;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  padding: 14px 16px;
  border-bottom: 1px solid #e2e8f0;
}

.table tbody td {
  padding: 14px 16px;
  color: #475569;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
}

.table tbody tr:hover {
  background: #fafcff;
}

.fw-600 {
  font-weight: 700;
  color: #0f172a;
}

.badge {
  padding: 6px 12px;
  border-radius: 999px;
  font-size: 0.75rem;
  font-weight: 700;
}

.badge-success {
  background: rgba(34, 197, 94, 0.12);
  color: #15803d;
}

.badge-danger {
  background: rgba(239, 68, 68, 0.12);
  color: #b91c1c;
}

.empty-state {
  padding: 36px 20px;
  text-align: center;
  color: #94a3b8;
}

.empty-state i {
  display: block;
  font-size: 2rem;
  margin-bottom: 10px;
}

.empty-state p {
  margin: 0;
}

.spin {
  animation: spin 0.9s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }

  to {
    transform: rotate(360deg);
  }
}

@media (max-width: 991.98px) {
  .overview-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 767.98px) {
  .hero-card {
    flex-direction: column;
  }

  .btn-refresh {
    width: 100%;
  }

  .page-title {
    font-size: 1.6rem;
  }
}

/* ──────────── Stat Cards Premium ──────────── */
.stat-card {
  background: #fff;
  border-radius: 1.25rem;
  padding: 1.5rem;
  display: flex;
  align-items: flex-start;
  gap: 1.25rem;
  border: 1px solid #eef2f6;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);
  position: relative;
  overflow: hidden;
}
.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
}
.stat-card::after {
  content: '';
  position: absolute;
  top: 0;
  right: 0;
  width: 100px;
  height: 100px;
  background: radial-gradient(circle at top right, rgba(255,255,255,0.8), transparent 70%);
  pointer-events: none;
}

.stat-icon {
  width: 3.5rem;
  height: 3.5rem;
  border-radius: 1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  flex-shrink: 0;
}

.stat-details {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}
.stat-label {
  font-size: 0.825rem;
  font-weight: 600;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.025em;
}
.stat-value {
  font-size: 1.75rem;
  font-weight: 800;
  color: #0f172a;
  line-height: 1;
}
.stat-trend {
  font-size: 0.78rem;
  font-weight: 600;
  margin-top: 0.5rem;
  color: #94a3b8;
}
.stat-trend.increasing { color: #10b981; }
.stat-trend.decreasing { color: #ef4444; }

/* Colors */
.stat-blue .stat-icon { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
.stat-blue { border-left: 5px solid #3b82f6; }

.stat-green .stat-icon { background: rgba(16, 185, 129, 0.1); color: #10b981; }
.stat-green { border-left: 5px solid #10b981; }

.stat-orange .stat-icon { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
.stat-orange { border-left: 5px solid #f59e0b; }

.stat-purple .stat-icon { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }
.stat-purple { border-left: 5px solid #8b5cf6; }

/* ────────── Favorite Places Chart & Summary ────────── */

.fav-icon-wrap {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: #fff1f2;
  color: #e11d48;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 1.15rem;
  flex-shrink: 0;
}

.fav-summary-card {
  border-radius: 16px !important;
  border: 1px solid #f1f5f9 !important;
  transition: box-shadow 0.2s ease;
}
.fav-summary-card:hover {
  box-shadow: 0 6px 24px rgba(15,23,42,0.08) !important;
}

.fav-sum-icon {
  width: 44px; height: 44px;
  border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 1.1rem;
  color: #fff;
  flex-shrink: 0;
  box-shadow: 0 4px 12px rgba(0,0,0,0.12);
}
.fav-sum-label {
  font-size: 0.75rem;
  font-weight: 600;
  color: #94a3b8;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  margin-bottom: 2px;
}
.fav-sum-value {
  font-size: 1.6rem;
  font-weight: 800;
  color: #0f172a;
  line-height: 1;
}

/* Top 3 rows */
.fav-top3-row {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px 0;
  border-bottom: 1px solid #f8fafc;
}
.fav-top3-row:last-child { border-bottom: none; }

.fav-top3-rank {
  width: 28px; height: 28px;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 0.9rem; font-weight: 800;
  flex-shrink: 0;
  color: #fff;
}
.rank-1 { background: #eab308; } /* Gold */
.rank-2 { background: #94a3b8; } /* Silver */
.rank-3 { background: #d97706; } /* Bronze */

.fav-top3-info {
  flex: 1;
  min-width: 0;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
}
.fav-top3-name {
  font-size: 0.85rem;
  font-weight: 700;
  color: #1e293b;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 140px;
}
.fav-top3-count {
  font-size: 0.78rem;
  font-weight: 700;
  color: #e11d48;
  white-space: nowrap;
  display: flex;
  align-items: center;
  gap: 3px;
}
.fav-top3-count i { font-size: 0.65rem; color: #e11d48; }

/* ────────── Custom HTML Horizontal Bar Chart ────────── */
.custom-html-chart {
  display: flex;
  flex-direction: column;
  gap: 13px;
  position: relative;
  padding-left: 2px;
  padding-right: 5px;
}

.chart-y-axis {
  position: absolute;
  left: 135px; /* Độ rộng của label + khoảng cách để làm trục Y */
  top: -5px; bottom: -5px;
  width: 1px;
  background: #e2e8f0; /* Đường nét đứt hoặc line mờ cực kỳ chuyên nghiệp */
  z-index: 0;
}

.html-chart-row {
  display: flex;
  align-items: center;
  gap: 15px;
  z-index: 1;
}

.h-c-label {
  width: 120px;
  flex-shrink: 0;
  font-size: 0.8rem;
  font-weight: 600;
  color: #475569;
  text-align: right;
}

.h-c-track-container {
  flex-grow: 1;
  display: flex;
  align-items: center;
}

.h-c-track {
  width: 100%;
  height: 8px; /* Thanh progress mỏng thanh mảnh */
  background-color: transparent; /* Tách biệt rõ thanh progress */
}

.h-c-fill {
  height: 100%;
  border-radius: 4px;
  transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
}

.fill-top { background-color: #475569; } /* Xám đậm chuyên nghiệp cho Top 3 */
.fill-rest { background-color: #cbd5e1; } /* Xám mờ cho phần còn lại */

.h-c-value {
  width: 38px;
  flex-shrink: 0;
  text-align: right;
  font-size: 0.82rem;
  font-weight: 800;
  color: #334155;
}

.popular-html-chart {
  display: flex;
  flex-direction: column;
  gap: 12px;
  padding: 10px 0 6px;
}

.popular-row {
  display: grid;
  grid-template-columns: 170px minmax(0, 1fr) 64px;
  align-items: center;
  column-gap: 16px;
}

.popular-label {
  font-size: 0.84rem;
  font-weight: 700;
  color: #334155;
}

.popular-track-wrap {
  width: 100%;
}

.popular-track {
  height: 10px;
  width: 100%;
  border-radius: 999px;
  background: #e2e8f0;
  overflow: hidden;
}

.popular-fill {
  height: 100%;
  border-radius: 999px;
  background: linear-gradient(90deg, #0d9488 0%, #14b8a6 100%);
  box-shadow: 0 4px 12px rgba(13, 148, 136, 0.2);
  transition: width 0.9s cubic-bezier(0.4, 0, 0.2, 1);
}

.popular-fill-top {
  background: linear-gradient(90deg, #0f766e 0%, #0d9488 100%);
}

.popular-value {
  justify-self: end;
  text-align: right;
  font-size: 0.82rem;
  font-weight: 800;
  color: #0f172a;
}

.stat-divider {
  width: 1px;
  height: 30px;
  background: #e2e8f0;
}



/* Empty State */
.empty-icon-circle {
  width: 60px;
  height: 60px;
  background: #f8fafc;
  border-radius: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto;
  font-size: 1.5rem;
}

@media (max-width: 768px) {
  .fav-grid-header { display: none; }
  .fav-premium-item {
    grid-template-columns: 40px 1fr 60px;
    gap: 12px;
    padding: 1rem;
  }
  .fav-prog-col { display: none; }

  .popular-row {
    grid-template-columns: 110px minmax(0, 1fr) 52px;
    column-gap: 10px;
  }

  .popular-label {
    font-size: 0.78rem;
  }

  .popular-value {
    font-size: 0.76rem;
  }
}
</style>
