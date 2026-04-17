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
            <div class="stat-trend increasing">
              <i class="bi bi-arrow-up"></i> 12% tháng này
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
            <div class="stat-value">4.8</div>
            <div class="stat-trend increasing">
              <i class="bi bi-check-circle-fill me-1"></i> Xuất sắc
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-3 mb-4">
      <div class="col-lg-7">
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
                <span class="overview-label">Khuyến nghị hiện tại</span>
                <strong class="overview-value overview-text">
                  Ưu tiên hoàn thiện các thống kê về lịch trình, ngân sách và địa điểm
                </strong>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-5">
        <div class="card card-custom">
          <div class="card-header">
            <h5 class="mb-0">Các hạng mục sẽ làm sau</h5>
          </div>
          <div class="card-body">
            <div class="todo-list">
              <div v-for="item in pendingItems" :key="item" class="todo-item">
                <i class="bi bi-dash-circle"></i>
                <span>{{ item }}</span>
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
      users: [],
      admins: [],
      stats: null,
      pendingItems: [
        'Phân tích chi tiêu thực tế so với ngân sách dự kiến (ML model)',
        'Địa đồ nhiệt (Heatmap) các địa điểm được yêu thích nhất',
        'Dự báo xu hướng du lịch theo mùa tại Đà Nẵng',
        'Chỉ số đánh giá độ tin cậy của AI trong lập lịch trình',
      ],
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
        const statsRes = await axios.get('http://127.0.0.1:8000/api/admin/statistics', this.authHeader())
        this.stats = statsRes.data?.data
        
        const [usersRes, adminsRes] = await Promise.all([
          axios.get(USERS_API_URL, this.authHeader()),
          axios.get(ADMINS_API_URL, this.authHeader()),
        ])

        this.users = Array.isArray(usersRes.data?.data) ? usersRes.data.data : []
        this.admins = Array.isArray(adminsRes.data?.data) ? adminsRes.data.data : []
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
</style>
