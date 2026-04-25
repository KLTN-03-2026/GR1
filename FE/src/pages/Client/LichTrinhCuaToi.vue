<template>
  <div class="ltct-page">
    <div class="container ltct-container">

      <!-- Header -->
      <div class="ltct-header">
        <div>
          <h1><i class="bi bi-collection-fill me-2 text-brand"></i>Lịch trình của tôi</h1>
          <p>Tất cả các chuyến đi bạn đã lên kế hoạch.</p>
        </div>
        <router-link to="/tao-lich-trinh" class="btn-brand-lg">
          <i class="bi bi-plus-circle me-2"></i>Tạo chuyến đi mới
        </router-link>
      </div>

      <!-- Not logged in -->
      <div v-if="!token" class="ltct-empty">
        <i class="bi bi-lock-fill"></i>
        <h4>Bạn chưa đăng nhập</h4>
        <p>Vui lòng đăng nhập để xem lịch trình của bạn.</p>
        <router-link to="/client/dang-nhap" class="btn-brand-lg">Đăng nhập ngay</router-link>
      </div>

      <!-- Loading -->
      <div v-else-if="loading" class="ltct-loading">
        <div class="spinner"></div>
        <span>Đang tải lịch trình...</span>
      </div>

      <!-- Empty list -->
      <div v-else-if="chuyenDis.length === 0" class="ltct-empty">
        <i class="bi bi-calendar-x"></i>
        <h4>Chưa có chuyến đi nào</h4>
        <p>Hãy tạo chuyến đi đầu tiên của bạn ngay hôm nay!</p>
        <router-link to="/tao-lich-trinh" class="btn-brand-lg">Tạo ngay</router-link>
      </div>

      <!-- Trip list -->
      <div v-else class="trips-grid">
        <div v-for="trip in chuyenDis" :key="trip.id" class="trip-card">
          <div class="trip-card-header">
            <div class="trip-icon-wrap">
              <i class="bi bi-airplane-fill"></i>
            </div>
            <div class="trip-card-status" :class="trip.tinh_trang == 1 ? 'active' : 'done'">
              {{ trip.tinh_trang == 1 ? 'Đang lên kế hoạch' : 'Hoàn thành' }}
            </div>
          </div>

          <h4 class="trip-title">{{ trip.ten_chuyen_di }}</h4>

          <div class="trip-info-rows">
            <div class="trip-info-row" v-if="trip.ngay_bat_dau">
              <i class="bi bi-calendar3 me-2"></i>
              <span>{{ formatDate(trip.ngay_bat_dau) }} → {{ getNgayKetThuc(trip.ngay_bat_dau, trip.so_ngay) }}</span>
            </div>
            <div class="trip-info-row">
              <i class="bi bi-sun me-2"></i>
              <span>{{ formatDuration(trip.so_ngay) }}</span>
            </div>
            <div class="trip-info-row">
              <i class="bi bi-people me-2"></i>
              <span>{{ trip.so_nguoi || 1 }} thành viên</span>
            </div>
            <div class="trip-info-row" v-if="trip.ngan_sach > 0">
              <i class="bi bi-wallet2 me-2"></i>
              <span>Ngân sách: {{ formatCurrency(trip.ngan_sach) }}</span>
            </div>
            <div class="trip-info-row" v-if="trip.chu_thich">
              <i class="bi bi-chat-left-text me-2"></i>
              <span class="text-muted">{{ trip.chu_thich }}</span>
            </div>
          </div>

          <div class="trip-card-footer">
            <span class="trip-date-created">Tạo: {{ formatDateFull(trip.created_at) }}</span>
            <div class="trip-footer-actions">
              <button class="btn-share" @click="openShareModal(trip)" title="Gửi vào nhóm">
                <i class="bi bi-share-fill"></i>
              </button>
              <button class="btn-map" @click="$router.push(`/lich-trinh/${trip.id}`)" title="Xem chi tiết">
                <i class="bi bi-eye-fill me-1"></i>Chi tiết
              </button>
              <button class="btn-delete" @click="confirmDelete(trip)" title="Xóa chuyến đi">
                <i class="bi bi-trash3"></i>
              </button>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- Removed map modal -->

    <!-- Delete confirm modal -->
    <div v-if="deleteTarget" class="modal-overlay" @click.self="deleteTarget = null">
      <div class="modal-box">
        <div class="modal-icon">
          <i class="bi bi-exclamation-triangle-fill"></i>
        </div>
        <h4>Xóa chuyến đi?</h4>
        <p>Bạn có chắc muốn xóa <strong>"{{ deleteTarget.ten_chuyen_di }}"</strong>? Hành động này không thể hoàn tác.
        </p>
        <div class="modal-actions">
          <button class="btn-ghost" @click="deleteTarget = null">Hủy</button>
          <button class="btn-danger" @click="doDelete" :disabled="deleting">
            <span v-if="deleting"><i class="bi bi-hourglass-split me-1"></i>Đang xóa...</span>
            <span v-else><i class="bi bi-trash3 me-1"></i>Xóa</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Modal chọn nhóm chia sẻ -->
    <div v-if="showShareModal" class="share-modal-overlay d-flex align-items-center justify-content-center" @click.self="closeShareModal">
      <div class="share-modal-box bg-white p-5 rounded-4 shadow-lg w-100 animate-in position-relative" style="max-width: 440px;">
        <button class="btn-close position-absolute top-0 end-0 m-4" @click="closeShareModal"></button>
        <div class="text-center mb-4 mt-2">
          <div class="share-icon-wrap mb-3 mx-auto shadow-sm">
             <i class="bi bi-send-check-fill fs-2 text-white"></i>
          </div>
          <h4 class="fw-bold mb-1 text-dark">Chia sẻ lịch trình</h4>
          <p class="text-muted mb-0" style="font-size: 0.95rem;">Chọn nhóm để gửi chuyến đi "{{ tripToShare?.ten_chuyen_di || '' }}"</p>
        </div>
        
        <div class="form-group mb-4">
          <label class="fw-bold mb-2 text-dark" style="font-size: 0.95rem;">Bạn muốn gửi vào nhóm nào?</label>
          <select v-model="selectedGroupToShare" class="form-select border-2 shadow-none modal-select text-dark" style="padding: 0.9rem 1rem; border-radius: 12px; font-weight: 500; cursor: pointer; border-color: #e2e8f0; background-color: #f8fafc;">
            <option :value="null">-- Click để chọn nhóm --</option>
            <option v-for="g in myJoinedGroups" :key="g.id" :value="g">{{ g.ten_nhom }}</option>
          </select>
        </div>

        <button class="btn w-100 rounded-pill fw-bold text-white shadow-sm mt-3" @click="shareToGroup" :disabled="!selectedGroupToShare || sendingShare" style="background: #10b981; padding: 0.9rem; font-size: 1.05rem; transition: all 0.2s;">
          <span v-if="sendingShare" class="d-flex align-items-center justify-content-center"><span class="spinner-border spinner-border-sm me-2"></span> Đang gửi...</span>
          <span v-else class="d-flex align-items-center justify-content-center"><i class="bi bi-chat-heart-fill me-2 fs-5"></i> Chia sẻ ngay</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const BASE = 'http://localhost:8000/api';

export default {
  name: 'LichTrinhCuaToi',

  data() {
    return {
      // Ưu tiên client_token (chuẩn của clientAuth.js), fallback sang DaNangTravel nếu session cũ
      token: localStorage.getItem('client_token') 
        || (localStorage.getItem('DaNangTravel') 
          ? JSON.parse(localStorage.getItem('DaNangTravel')).client_token 
          : null),
      chuyenDis: [],
      loading: false,
      deleteTarget: null,
      deleting: false,

      // Modal chia sẻ
      showShareModal: false,
      tripToShare: null,
      myJoinedGroups: [],
      selectedGroupToShare: null,
      sendingShare: false,
    };
  },

  mounted() {
    if (this.token) this.fetchData();
  },

  methods: {
    async fetchData() {
      this.loading = true;
      try {
        const res = await fetch(`${BASE}/client/chuyen-di/get-data`, {
          headers: { Authorization: `Bearer ${this.token}` },
        });
        if (res.status === 401) {
          // Token hết hạn hoặc không hợp lệ → xóa và yêu cầu đăng nhập lại
          localStorage.removeItem('client_token');
          this.token = null;
          this.chuyenDis = [];
          return;
        }
        const json = await res.json();
        this.chuyenDis = json.data || [];
      } catch {
        this.chuyenDis = [];
      } finally {
        this.loading = false;
      }
    },

    async fetchMyGroups() {
      if(!this.token) return;
      try {
        const [joinedRes, ownedRes] = await Promise.all([
          fetch(`${BASE}/client/nhom-du-lich/get-joined`, { headers: { Authorization: `Bearer ${this.token}` } }),
          fetch(`${BASE}/client/nhom-du-lich/get-my-groups`, { headers: { Authorization: `Bearer ${this.token}` } })
        ]);
        const jData = await joinedRes.json();
        const oData = await ownedRes.json();
        
        const groups = [];
        if (jData.status && jData.data) groups.push(...jData.data);
        if (oData.status && oData.data) groups.push(...oData.data);
        
        this.myJoinedGroups = groups;
      } catch (e) {
        console.error("Lỗi lấy danh sách nhóm", e);
      }
    },

    openShareModal(trip) {
      this.tripToShare = trip;
      this.showShareModal = true;
      if (this.myJoinedGroups.length === 0) {
        this.fetchMyGroups();
      }
    },

    closeShareModal() {
      this.showShareModal = false;
      this.tripToShare = null;
      this.selectedGroupToShare = '';
    },

    async shareToGroup() {
      if (!this.selectedGroupToShare || !this.tripToShare) return;
      this.sendingShare = true;
      try {
        const payload = {
            id_nhom_du_lich: this.selectedGroupToShare.id,
            id_chi_tiet_nhom: this.selectedGroupToShare.id_chi_tiet_nhom,
            message: JSON.stringify({ type: 'itinerary', id: this.tripToShare.id, title: this.tripToShare.ten_chuyen_di })
        };

        const r = await fetch(`http://localhost:8000/api/nhom-chats`, {
          method: 'POST', 
          headers: { 
            'Content-Type': 'application/json', 
            Authorization: `Bearer ${this.token}` 
          },
          body: JSON.stringify(payload)
        });
        const res = await r.json();
        
        if (res.status) {
          this.$toast.success('Gửi lịch trình thành công!');
          this.closeShareModal();
        } else {
          this.$toast.error('Gửi thất bại: ' + res.message);
        }
      } catch (e) {
        this.$toast.error('Lỗi khi chia sẻ.');
        console.error(e);
      } finally {
        this.sendingShare = false;
      }
    },



    // ── UTILITIES ────────────────────────────────
    formatDate(dateStr) {
      if (!dateStr) return '?';
      const d = new Date(dateStr);
      return d.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' });
    },

    formatDateFull(dateStr) {
      if (!dateStr) return '';
      return new Date(dateStr).toLocaleDateString('vi-VN');
    },

    formatCurrency(val) {
      return Number(val).toLocaleString('vi-VN') + 'đ';
    },

    getNgayKetThuc(dateStr, soNgay) {
      if (!dateStr || !soNgay) return '?';
      const d = new Date(dateStr);
      d.setDate(d.getDate() + (soNgay - 1));
      return d.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' });
    },

    formatDuration(soNgay) {
      if (!soNgay) return '?';
      return `${soNgay} ngày ${Math.max(0, soNgay - 1)} đêm`;
    },

    confirmDelete(trip) {
      this.deleteTarget = trip;
    },

    async doDelete() {
      if (!this.deleteTarget) return;
      this.deleting = true;
      try {
        const res = await fetch(`${BASE}/client/chuyen-di/delete`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${this.token}` },
          body: JSON.stringify({ id: this.deleteTarget.id }),
        });
        const json = await res.json();
        if (json.status) {
          this.chuyenDis = this.chuyenDis.filter(t => t.id !== this.deleteTarget.id);
          this.deleteTarget = null;
        }
      } catch { }
      this.deleting = false;
    },
  },
};
</script>

<style scoped>
.ltct-page {
  min-height: 100vh;
  background: linear-gradient(160deg, #f0f7ff 0%, #fafbff 60%, #f0fff8 100%);
  padding-bottom: 5rem;
}

.ltct-container {
  max-width: 1040px;
  padding-top: 3rem;
}

.ltct-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  flex-wrap: wrap;
  gap: 1rem;
  margin-bottom: 2.5rem;
}

.ltct-header h1 {
  font-size: 2rem;
  font-weight: 800;
  color: #1e2d44;
  margin-bottom: 0.25rem;
}

.ltct-header p {
  color: #6b7a90;
  font-size: 0.95rem;
  margin: 0;
}

.text-brand {
  color: #10b981;
}

.btn-brand-lg {
  background: linear-gradient(135deg, #10b981, #0ea5e9);
  color: #fff;
  border: none;
  border-radius: 999px;
  padding: 0.72rem 1.6rem;
  font-size: 0.95rem;
  font-weight: 700;
  cursor: pointer;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  box-shadow: 0 8px 20px rgba(16, 185, 129, 0.25);
  transition: all 0.2s;
}

.btn-brand-lg:hover {
  transform: translateY(-2px);
  color: #fff;
}

/* Empty state */
.ltct-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  padding: 5rem 1rem;
  text-align: center;
  color: #7a8ea0;
}

.ltct-empty i {
  font-size: 3.5rem;
  color: #c0cedf;
}

.ltct-empty h4 {
  font-size: 1.25rem;
  font-weight: 700;
  color: #3d5166;
  margin: 0;
}

.ltct-empty p {
  font-size: 0.93rem;
  margin: 0;
}

/* Loading */
.ltct-loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
  padding: 4rem;
  color: #7a8ea0;
}

.spinner {
  width: 36px;
  height: 36px;
  border: 3.5px solid #e8edf5;
  border-top-color: #10b981;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}

.mini-spinner {
  width: 22px;
  height: 22px;
  border: 2.5px solid #e8edf5;
  border-top-color: #10b981;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* Grid */
.trips-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.3rem;
}

.trip-card {
  background: #fff;
  border-radius: 1.3rem;
  padding: 1.4rem 1.5rem;
  border: 1px solid #e6ecf5;
  box-shadow: 0 6px 22px rgba(30, 45, 68, 0.07);
  display: flex;
  flex-direction: column;
  gap: 0.8rem;
  transition: all 0.22s;
}

.trip-card:hover {
  box-shadow: 0 14px 35px rgba(30, 45, 68, 0.12);
  transform: translateY(-3px);
}

.trip-card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.trip-icon-wrap {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  background: linear-gradient(135deg, #dcfce7, #dbeafe);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.1rem;
  color: #10b981;
}

.trip-card-status {
  font-size: 0.76rem;
  font-weight: 700;
  padding: 0.22rem 0.75rem;
  border-radius: 999px;
}

.trip-card-status.active {
  background: #dcfce7;
  color: #15803d;
}

.trip-card-status.done {
  background: #e0f2fe;
  color: #0369a1;
}

.trip-title {
  font-size: 1.05rem;
  font-weight: 800;
  color: #1e2d44;
  margin: 0;
  line-height: 1.35;
}

.trip-info-rows {
  display: flex;
  flex-direction: column;
  gap: 0.38rem;
}

.trip-info-row {
  display: flex;
  align-items: center;
  font-size: 0.85rem;
  color: #5a6d80;
}

.trip-info-row i {
  color: #10b981;
  font-size: 0.9rem;
}

.trip-card-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: auto;
  padding-top: 0.75rem;
  border-top: 1px solid #f0f4fb;
}

.trip-date-created {
  font-size: 0.78rem;
  color: #a0adbf;
}

.trip-footer-actions {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

/* Map button and Share button */
.btn-share {
  display: inline-flex;  align-items: center; justify-content: center;
  background: #f0fdf4; color: #10b981; border: 1px solid #10b981;
  width: 30px; height: 30px; border-radius: 50%;
  font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: all 0.2s;
}
.btn-share:hover { background: #10b981; color: #fff; transform: translateY(-1px); }

.btn-map {
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
  background: linear-gradient(135deg, #0ea5e9, #10b981);
  color: #fff;
  border: none;
  border-radius: 999px;
  padding: 0.35rem 0.9rem;
  font-size: 0.8rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.18s;
  box-shadow: 0 4px 12px rgba(14, 165, 233, 0.25);
}

.btn-map:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(14, 165, 233, 0.35);
}

.btn-delete {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  border: 1.5px solid #fecdd3;
  background: #fff1f2;
  color: #e11d48;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 0.9rem;
  transition: all 0.18s;
}

.btn-delete:hover {
  background: #e11d48;
  color: #fff;
  border-color: #e11d48;
}

/* ── MAP MODAL ──────────────────────────────── */
.map-overlay {
  position: fixed;
  inset: 0;
  background: rgba(10, 18, 35, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 600;
  backdrop-filter: blur(6px);
  padding: 1rem;
}

.map-modal {
  background: #fff;
  border-radius: 1.5rem;
  width: 100%;
  max-width: 1100px;
  height: 86vh;
  max-height: 780px;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  box-shadow: 0 40px 80px rgba(10, 18, 35, 0.3);
  animation: slideUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) both;
}

@keyframes slideUp {
  from {
    transform: translateY(40px) scale(0.96);
    opacity: 0;
  }

  to {
    transform: translateY(0) scale(1);
    opacity: 1;
  }
}

.map-modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #e8edf5;
  background: linear-gradient(135deg, #f0fdf4, #eff6ff);
  flex-shrink: 0;
}

.map-modal-title {
  font-size: 1.1rem;
  font-weight: 800;
  color: #1e2d44;
  display: flex;
  align-items: center;
}

.map-close-btn {
  width: 36px;
  height: 36px;
  border: none;
  background: #f1f5f9;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 0.95rem;
  color: #64748b;
  transition: all 0.18s;
}

.map-close-btn:hover {
  background: #e11d48;
  color: #fff;
}

.map-modal-body {
  display: flex;
  flex: 1;
  overflow: hidden;
}

/* Sidebar */
.map-sidebar {
  width: 300px;
  flex-shrink: 0;
  border-right: 1px solid #e8edf5;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  background: #fafbff;
}

.map-sidebar-title {
  padding: 0.85rem 1.1rem;
  font-size: 0.85rem;
  font-weight: 700;
  color: #3d5166;
  border-bottom: 1px solid #e8edf5;
  display: flex;
  align-items: center;
  gap: 0.4rem;
  background: #fff;
  flex-shrink: 0;
}

.map-badge {
  background: linear-gradient(135deg, #10b981, #0ea5e9);
  color: #fff;
  font-size: 0.7rem;
  font-weight: 700;
  padding: 0.1rem 0.5rem;
  border-radius: 999px;
  margin-left: auto;
}

.map-sidebar-loading,
.map-sidebar-empty {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 0.6rem;
  padding: 2rem 1rem;
  text-align: center;
  color: #8a9aac;
  font-size: 0.85rem;
}

.map-sidebar-empty i {
  font-size: 2.2rem;
  color: #c8d5e2;
}

.map-place-list {
  flex: 1;
  overflow-y: auto;
  padding: 0.5rem;
}

.map-place-item {
  display: flex;
  gap: 0.75rem;
  padding: 0.75rem 0.85rem;
  border-radius: 0.9rem;
  cursor: pointer;
  transition: all 0.18s;
  margin-bottom: 0.4rem;
  border: 1.5px solid transparent;
}

.map-place-item:hover {
  background: #f0f7ff;
  border-color: #bfdbfe;
}

.map-place-item.active {
  background: #eff6ff;
  border-color: #93c5fd;
}

.place-num {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: linear-gradient(135deg, #10b981, #0ea5e9);
  color: #fff;
  font-size: 0.78rem;
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  margin-top: 1px;
}

.map-place-item.active .place-num {
  background: linear-gradient(135deg, #f97316, #ef4444);
}

.place-info {
  flex: 1;
  min-width: 0;
}

.place-name {
  font-size: 0.87rem;
  font-weight: 700;
  color: #1e2d44;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  margin-bottom: 0.18rem;
}

.place-addr {
  font-size: 0.75rem;
  color: #7a8ea0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  margin-bottom: 0.35rem;
}

.place-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 0.3rem;
}

.place-tag {
  font-size: 0.7rem;
  font-weight: 600;
  padding: 0.12rem 0.5rem;
  border-radius: 999px;
  background: #e0f2fe;
  color: #0369a1;
}

.place-tag.rating {
  background: #fef9c3;
  color: #854d0e;
}

/* Map canvas */
.map-canvas-wrap {
  flex: 1;
  position: relative;
  overflow: hidden;
}

.map-canvas {
  width: 100%;
  height: 100%;
}

.map-canvas-loading {
  position: absolute;
  inset: 0;
  background: #f0f4fb;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  font-size: 0.9rem;
  color: #6b7a90;
}

/* ── DELETE MODAL ──────────────────────────── */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.55);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 700;
  backdrop-filter: blur(4px);
}

.modal-box {
  background: #fff;
  border-radius: 1.4rem;
  padding: 2rem;
  max-width: 420px;
  width: 90%;
  text-align: center;
  animation: popIn 0.28s ease both;
  box-shadow: 0 30px 60px rgba(15, 23, 42, 0.18);
}

@keyframes popIn {
  from {
    transform: scale(0.88);
    opacity: 0;
  }

  to {
    transform: scale(1);
    opacity: 1;
  }
}

.modal-icon {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  background: #fff1f2;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  color: #e11d48;
  margin: 0 auto 1rem;
}

.modal-box h4 {
  font-weight: 800;
  color: #1e2d44;
  margin-bottom: 0.5rem;
}

.modal-box p {
  font-size: 0.92rem;
  color: #5a6d80;
  margin-bottom: 1.5rem;
}

.modal-actions {
  display: flex;
  gap: 0.75rem;
  justify-content: center;
}

.btn-ghost {
  background: transparent;
  color: #5a6d80;
  border: 1.5px solid #d5dde8;
  border-radius: 999px;
  padding: 0.62rem 1.4rem;
  font-size: 0.92rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.18s;
}

.btn-ghost:hover {
  background: #f0f4fb;
}

.btn-danger {
  background: linear-gradient(135deg, #e11d48, #f97316);
  color: #fff;
  border: none;
  border-radius: 999px;
  padding: 0.62rem 1.4rem;
  font-size: 0.92rem;
  font-weight: 700;
  cursor: pointer;
  box-shadow: 0 6px 16px rgba(225, 29, 72, 0.28);
  transition: all 0.18s;
}

.btn-danger:hover:not(:disabled) {
  transform: translateY(-1px);
}

.btn-danger:disabled {
  opacity: 0.55;
  cursor: not-allowed;
}

/* Responsive */
@media (max-width: 640px) {
  .map-modal {
    height: 95vh;
    max-height: none;
    border-radius: 1rem;
  }

  .map-modal-body {
    flex-direction: column;
  }

  .map-sidebar {
    width: 100%;
    height: 220px;
    border-right: none;
    border-bottom: 1px solid #e8edf5;
  }

  .map-place-list {
    display: flex;
    flex-direction: row;
    gap: 0.4rem;
    overflow-x: auto;
    overflow-y: hidden;
    padding: 0.5rem;
  }

  .map-place-item {
    min-width: 200px;
    margin-bottom: 0;
  }
}

/* Modal chia sẻ */
.share-modal-overlay {
  position: fixed; inset: 0; background: rgba(15, 23, 42, 0.65); z-index: 1050; backdrop-filter: blur(8px);
}
.share-icon-wrap {
  width: 76px; height: 76px; border-radius: 50%;
  background: #10b981;
  display: flex; align-items: center; justify-content: center;
}
.modal-select:focus {
  border-color: #4f46e5 !important;
  box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.15) !important;
  background-color: #fff !important;
}
</style>
