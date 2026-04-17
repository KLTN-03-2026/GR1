<template>
  <div class="admin-reviews-container">
    <div class="page-header">
      <div class="header-content">
        <h2>Quản lý Đánh giá & Phản hồi</h2>
      </div>
      <div class="header-actions">
        <!-- Bộ lọc trạng thái -->
        <div class="filter-wrapper">
          <i class="bi bi-funnel filter-icon"></i>
          <select v-model="filterStatus" class="status-filter" @change="fetchReviews">
            <option value="all">Tất cả trạng thái</option>
            <option value="0">Chờ duyệt</option>
            <option value="1">Đã hiển thị</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Khối Header Thống Kê Nhanh -->
    <div class="stats-grid">
      <div class="stat-card modern-card pending-card">
        <div class="stat-icon-wrapper">
          <i class="bi bi-hourglass-split"></i>
        </div>
        <div class="stat-info">
          <h3>Chờ duyệt</h3>
          <p>{{ pendingCount }}</p>
        </div>
      </div>
      <div class="stat-card modern-card approved-card">
        <div class="stat-icon-wrapper">
          <i class="bi bi-check-circle-fill"></i>
        </div>
        <div class="stat-info">
          <h3>Đã hiển thị</h3>
          <p>{{ approvedCount }}</p>
        </div>
      </div>
      <div class="stat-card modern-card total-card">
        <div class="stat-icon-wrapper">
          <i class="bi bi-bar-chart-fill"></i>
        </div>
        <div class="stat-info">
          <h3>Tổng số</h3>
          <p>{{ allReviews.length }}</p>
        </div>
      </div>
    </div>

    <!-- Bảng Dữ Liệu Đánh Giá -->
    <div class="reviews-content-wrapper">
      <div v-if="isLoading" class="loading-state glass-panel">
        <div class="spinner"></div>
        <p>Đang tải dữ liệu đánh giá...</p>
      </div>
      <div v-else-if="filteredReviews.length === 0" class="empty-state glass-panel">
        <div class="empty-illustration">
          <i class="bi bi-clipboard-x"></i>
        </div>
        <p>Không có đánh giá nào phù hợp với bộ lọc hiện tại.</p>
      </div>
      <table v-else class="reviews-table modern-table">
        <thead>
          <tr>
            <th>Khách hàng</th>
            <th>Địa điểm</th>
            <th>Đánh giá</th>
            <th class="col-content">Nội dung phản hồi</th>
            <th>Trạng thái</th>
            <th class="col-actions">Thao tác</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="review in filteredReviews" :key="review.id" class="table-row-card">
            <td class="col-user">
              <div class="user-info">
                <div class="user-avatar gradient-avatar">
                  {{ review.nguoi_dung?.ten?.charAt(0) || review.ten_nguoi_danh_gia?.charAt(0) || 'U' }}
                </div>
                <div class="user-details">
                  <strong>{{ review.nguoi_dung?.ten || review.ten_nguoi_danh_gia || 'Người dùng ẩn danh' }}</strong>
                  <span class="user-email">{{ review.nguoi_dung?.email || 'Đánh giá khách/Google' }}</span>
                </div>
              </div>
            </td>
            <td class="col-place">
              <strong class="place-name">{{ review.dia_diem?.ten_dia_diem || 'Địa điểm đã bị xoá' }}</strong>
            </td>
            <td class="col-rating">
              <div class="stars">
                <i v-for="n in 5" :key="n" 
                   :class="n <= review.so_sao ? 'bi bi-star-fill text-warning' : 'bi bi-star text-muted'"></i>
              </div>
            </td>
            <td class="col-content">
              <div class="review-text-container custom-scrollbar">
                 <p class="review-text">{{ review.noi_dung || '(Không có văn bản)' }}</p>
              </div>
            </td>
            <td class="col-status">
              <div class="status-badge" :class="review.trang_thai == 1 ? 'approved' : 'pending'">
                <span class="status-dot"></span>
                {{ review.trang_thai == 1 ? 'Đã duyệt' : 'Chờ duyệt' }}
              </div>
            </td>
            <td class="col-actions">
              <div class="action-buttons">
                <!-- Nút Duyệt / Ẩn -->
                <button v-if="review.trang_thai == 0" class="btn-action btn-approve" @click="approveReview(review.id)" title="Duyệt cho phép hiển thị">
                  <i class="bi bi-check-lg"></i>
                </button>
                <button v-else class="btn-action btn-hide" @click="hideReview(review.id)" title="Huỷ duyệt/ẩn đánh giá này">
                  <i class="bi bi-eye-slash-fill"></i>
                </button>
                
                <!-- Nút Xoá -->
                <button class="btn-action btn-delete" @click="confirmDelete(review.id)" title="Xóa vĩnh viễn bài đánh giá">
                  <i class="bi bi-trash3-fill"></i>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import api from '../../config/api';

export default {
  name: 'QuanLyDanhGia',
  data() {
    return {
      isLoading: true,
      filterStatus: 'all',
      allReviews: []
    }
  },
  computed: {
    filteredReviews() {
      if (this.filterStatus === 'all') return this.allReviews;
      return this.allReviews.filter(r => r.trang_thai == this.filterStatus);
    },
    pendingCount() {
      return this.allReviews.filter(r => r.trang_thai == 0).length;
    },
    approvedCount() {
      return this.allReviews.filter(r => r.trang_thai == 1).length;
    }
  },
  mounted() {
    this.fetchReviews();
  },
  methods: {
    async fetchReviews() {
      this.isLoading = true;
      try {
        const response = await fetch(`${api.BASE}/danh-gias`);
        const json = await response.json();
        if (json.status === 'success' && json.data) {
          this.allReviews = json.data;
        }
      } catch (e) {
        console.error("Lỗi khi tải danh sách:", e);
        if (this.$toast) this.$toast.error('Không thể tải danh sách đánh giá.');
      } finally {
        this.isLoading = false;
      }
    },
    async approveReview(id) {
      try {
        const res = await fetch(`${api.BASE}/danh-gias/${id}`, {
          method: 'PUT',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ trang_thai: 1 })
        });
        const json = await res.json();
        if (json.status === 'success') {
          const idx = this.allReviews.findIndex(r => r.id === id);
          if (idx !== -1) {
            this.allReviews[idx].trang_thai = 1;
          }
          if (this.$toast) this.$toast.success('Đã duyệt đánh giá.');
        } else {
          throw new Error('Cập nhật thất bại');
        }
      } catch (e) {
        console.error(e);
        if (this.$toast) this.$toast.error('Lỗi khi duyệt đánh giá.');
      }
    },
    async hideReview(id) {
      try {
        const res = await fetch(`${api.BASE}/danh-gias/${id}`, {
          method: 'PUT',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ trang_thai: 0 })
        });
        const json = await res.json();
        if (json.status === 'success') {
          const idx = this.allReviews.findIndex(r => r.id === id);
          if (idx !== -1) {
            this.allReviews[idx].trang_thai = 0;
          }
          if (this.$toast) this.$toast.success('Đã ẩn đánh giá.');
        } else {
          throw new Error('Cập nhật thất bại');
        }
      } catch (e) {
        console.error(e);
        if (this.$toast) this.$toast.error('Lỗi khi ẩn đánh giá.');
      }
    },
    async confirmDelete(id) {
      if (!confirm("Xác nhận xoá vĩnh viễn đánh giá này?")) {
        return;
      }
      try {
        const res = await fetch(`${api.BASE}/danh-gias/${id}`, {
          method: 'DELETE'
        });
        const json = await res.json();
        if (json.status === 'success') {
          this.allReviews = this.allReviews.filter(r => r.id !== id);
          if (this.$toast) this.$toast.success('Đã xoá đánh giá thành công.');
        } else {
          throw new Error('Xóa thất bại');
        }
      } catch (e) {
        console.error(e);
        if (this.$toast) this.$toast.error('Lỗi khi xoá đánh giá.');
      }
    }
  }
}
</script>

<style scoped>
/* =========== Nền Typography (Premium Feel) =========== */
.admin-reviews-container {
  padding: 2rem;
  background-color: #f6f8fa; /* Nền xám nhạt, sang & sạch hơn */
  min-height: 100vh;
  font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
  color: #334155;
}

/* =========== Header Tiêu Đề =========== */
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.header-content h2 {
  font-size: 1.4rem;
  font-weight: 800;
  margin: 0;
  color: #0f172a;
  letter-spacing: -0.02em;
}

/* =========== Bộ lọc (Filter) =========== */
.filter-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.filter-icon {
  position: absolute;
  left: 1rem;
  color: #64748b;
  font-size: 0.95rem;
}

.status-filter {
  padding: 0.5rem 1.25rem 0.5rem 2.2rem;
  border-radius: 8px;
  border: 1px solid rgba(203, 213, 225, 0.8);
  background: #ffffff;
  font-weight: 500;
  color: #475569;
  outline: none;
  font-size: 0.85rem;
  box-shadow: 0 1px 2px rgba(0,0,0,0.02);
  cursor: pointer;
  min-width: 170px;
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
  transition: all 0.2s ease;
  background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
  background-repeat: no-repeat;
  background-position: right 0.8rem center;
  background-size: 0.9em;
}

.status-filter:hover, .status-filter:focus {
  border-color: #94a3b8;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.04);
}

/* =========== Thẻ Thống Kê (Stats Grid) =========== */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 1.25rem;
  margin-bottom: 2rem;
}

.modern-card {
  background: #ffffff;
  padding: 1.2rem 1.5rem;
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 1.25rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02), 0 1px 2px rgba(0,0,0, 0.01);
  border: 1px solid #f1f5f9;
  transition: all 0.25s ease;
}

.modern-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.05);
}

.stat-icon-wrapper {
  width: 44px;
  height: 44px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.3rem;
}

.pending-card .stat-icon-wrapper { background: #fffbeb; color: #d97706; }
.approved-card .stat-icon-wrapper { background: #f0fdf4; color: #059669; }
.total-card .stat-icon-wrapper { background: #eff6ff; color: #4f46e5; }

.stat-info h3 {
  font-size: 0.75rem;
  font-weight: 700;
  color: #64748b;
  margin: 0 0 0.2rem 0;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.stat-info p {
  font-size: 1.6rem;
  font-weight: 800;
  color: #0f172a;
  margin: 0;
  line-height: 1;
}

/* =========== Thiết kế Bảng Hiện Đại (Rows overlay) =========== */
.reviews-content-wrapper {
  position: relative;
  z-index: 1;
}

.modern-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0 0.6rem; /* Khoảng cách row tinh tế hơn */
  text-align: left;
}

.modern-table th {
  padding: 0 1.25rem 0.6rem 1.25rem;
  font-weight: 600;
  color: #475569;
  font-size: 0.75rem; /* Header typography nhỏ, pro */
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.table-row-card {
  background: white;
  box-shadow: 0 1px 3px rgba(0,0,0,0.02);
  transition: all 0.2s ease;
}

.table-row-card:hover {
  box-shadow: 0 8px 16px rgba(0,0,0,0.04);
  background-color: #fafbfc;
}

.table-row-card td {
  padding: 1.1rem 1.25rem; /* Padding vừa vặn, không quá trống */
  border-top: 1px solid #f8fafc;
  border-bottom: 1px solid #f8fafc;
  vertical-align: top; /* Để text content dài hiển thị mượt */
}

.table-row-card td:first-child {
  border-radius: 10px 0 0 10px;
  border-left: 1px solid #f8fafc;
}

.table-row-card td:last-child {
  border-radius: 0 10px 10px 0;
  border-right: 1px solid #f8fafc;
}

/* Cột Kích Thước */
.col-user { width: 18%; }
.col-place { width: 16%; }
.col-rating { width: 12%; }
.col-content { width: 35%; /* Fixed width to limit overflow */ }
.col-status { width: 10%; }
.col-actions { width: 9%; text-align: right; }

/* Info Người dùng */
.user-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.gradient-avatar {
  flex-shrink: 0;
  width: 36px;
  height: 36px;
  background: linear-gradient(135deg, #a5b4fc, #6366f1);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 0.95rem;
  box-shadow: 0 2px 4px rgba(99, 102, 241, 0.2);
}

.user-details {
  display: flex;
  flex-direction: column;
}

.user-details strong {
  color: #1e293b;
  font-size: 0.85rem;
  font-weight: 600;
}

.user-email {
  color: #64748b;
  font-size: 0.75rem;
}

.place-name {
  color: #1e293b;
  font-size: 0.85rem;
  font-weight: 600;
}

/* Trạng Thái Sao */
.stars {
  display: inline-flex;
  gap: 2px;
  padding-top: 0.15rem;
}

.stars i {
  font-size: 0.9rem;
}

.text-warning { color: #f59e0b; }
.text-muted { color: #e2e8f0; }

/* =========== Thiết kế thanh Scroll gọn gàng & Text =========== */
.review-text-container {
  max-height: 85px; /* Giới hạn độ giãn nở */
  overflow-y: auto; /* Cho phép cuộn nếu quá dài */
  padding-right: 8px; /* Tránh sát mép scrollbar */
  margin-top: -0.15rem;
}

/* Tùy chỉnh Scrollbar cho vùng content */
.custom-scrollbar::-webkit-scrollbar {
  width: 3px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

.review-text {
  margin: 0;
  color: #475569;
  font-size: 0.85rem;
  line-height: 1.6;
  white-space: pre-wrap;
  word-break: break-word;
}

/* Icon / Badge Status */
.status-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.25rem 0.6rem;
  border-radius: 6px;
  font-size: 0.75rem;
  font-weight: 600;
  white-space: nowrap;
}

.status-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
}

.status-badge.pending {
  background: #fffbeb;
  color: #92400e;
}
.status-badge.pending .status-dot { background: #f59e0b; }

.status-badge.approved {
  background: #f0fdf4;
  color: #166534;
}
.status-badge.approved .status-dot { background: #10b981; }

/* Các nút hành động */
.action-buttons {
  display: flex;
  gap: 0.4rem;
  justify-content: flex-end;
}

.btn-action {
  width: 32px;
  height: 32px;
  border-radius: 6px;
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  font-size: 1rem;
}

.btn-approve {
  background: #f0fdf4;
  color: #16a34a;
}

.btn-approve:hover {
  background: #16a34a;
  color: #fff;
}

.btn-hide {
  background: #f1f5f9;
  color: #64748b;
}

.btn-hide:hover {
  background: #94a3b8;
  color: #fff;
}

.btn-delete {
  background: #fef2f2;
  color: #dc2626;
}

.btn-delete:hover {
  background: #dc2626;
  color: #fff;
}

/* Loading & Empty State */
.glass-panel {
  background: #ffffff;
  border-radius: 12px;
  padding: 3rem 2rem;
  text-align: center;
  color: #64748b;
  border: 1px solid #f1f5f9;
  box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}

.loading-state, .empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  font-weight: 500;
  font-size: 0.95rem;
}

.spinner {
  width: 36px;
  height: 36px;
  border: 3px solid #f1f5f9;
  border-top: 3px solid #6366f1;
  border-radius: 50%;
  animation: loadspin 0.8s cubic-bezier(0.4, 0, 0.2, 1) infinite;
  margin-bottom: 1rem;
}

@keyframes loadspin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.empty-illustration {
  font-size: 2.5rem;
  color: #cbd5e1;
  margin-bottom: 1rem;
  background: #f8fafc;
  width: 70px;
  height: 70px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
}
</style>