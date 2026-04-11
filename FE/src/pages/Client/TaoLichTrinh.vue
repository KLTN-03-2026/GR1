<template>
  <div class="tlt-page">
    <!-- ───── Header wizard bar ───── -->
    <div class="tlt-wizard-bar">
      <div class="container">
        <div class="wizard-steps">
          <div v-for="(s, i) in steps" :key="i" class="wizard-step"
            :class="{ active: step === i + 1, done: step > i + 1 }">
            <div class="step-icon">
              <i v-if="step > i + 1" class="bi bi-check-lg"></i>
              <span v-else>{{ i + 1 }}</span>
            </div>
            <span class="step-label">{{ s }}</span>
          </div>
        </div>
      </div>
    </div>

    <div class="container tlt-body">

      <!-- ══════════════════════════════
           BƯỚC 1 – Thông tin chuyến đi
           ══════════════════════════════ -->
      <div v-if="step === 1" class="tlt-section animate-in">
        <div class="section-head">
          <h2><i class="bi bi-calendar-heart me-2 text-brand"></i>Thông tin chuyến đi</h2>
          <p>Nhập các thông tin cơ bản để chúng tôi gợi ý lịch trình phù hợp nhất cho bạn.</p>
        </div>

        <div class="form-grid">
          <div class="form-group full-col">
            <label>Tên chuyến đi <span class="req">*</span></label>
            <input v-model="form.ten_chuyen_di" type="text" class="tlt-input"
              placeholder="Ví dụ: Đà Nẵng cùng gia đình 3N2Đ" />
            <span v-if="errors.ten_chuyen_di" class="err-msg">{{ errors.ten_chuyen_di }}</span>
          </div>

          <div class="form-group">
            <label>Ngày bắt đầu <span class="req">*</span></label>
            <input v-model="form.ngay_bat_dau" type="date" class="tlt-input" :min="today" />
            <span v-if="errors.ngay_bat_dau" class="err-msg">{{ errors.ngay_bat_dau }}</span>
          </div>

          <div class="form-group">
            <label>Ngày kết thúc <span class="req">*</span></label>
            <input v-model="form.ngay_ket_thuc" type="date" class="tlt-input" :min="form.ngay_bat_dau || today" />
            <span v-if="errors.ngay_ket_thuc" class="err-msg">{{ errors.ngay_ket_thuc }}</span>
          </div>

          <div class="form-group">
            <label>Số thành viên</label>
            <div class="number-input">
              <button type="button"
                @click="form.so_luong_thanh_vien = Math.max(1, form.so_luong_thanh_vien - 1)">−</button>
              <span>{{ form.so_luong_thanh_vien }}</span>
              <button type="button" @click="form.so_luong_thanh_vien++">+</button>
            </div>
          </div>

          <div class="form-group">
            <label>Ngân sách dự kiến (VNĐ)</label>
            <input v-model.number="form.ngan_sach_du_kien" type="number" class="tlt-input" placeholder="0" min="0" />
          </div>

          <div class="form-group full-col">
            <label>Ghi chú</label>
            <textarea v-model="form.chu_thich" class="tlt-input" rows="3"
              placeholder="Sở thích, yêu cầu đặc biệt..."></textarea>
          </div>
        </div>

        <!-- Trip summary chip -->
        <div v-if="soNgay > 0" class="trip-chip">
          <i class="bi bi-sun me-2"></i>
          <strong>{{ soNgay }} ngày {{ soNgay - 1 }} đêm</strong> &nbsp;·&nbsp;
          {{ form.so_luong_thanh_vien }} người
          <span v-if="form.ngan_sach_du_kien > 0">
            &nbsp;·&nbsp; Ngân sách ~{{ formatCurrency(form.ngan_sach_du_kien) }}
          </span>
        </div>

        <div class="tlt-actions d-flex gap-3">
          <button class="btn-brand-lg flex-fill" @click="goStep2">
            Tiếp tục – Chọn địa điểm <i class="bi bi-arrow-right ms-2"></i>
          </button>
          <button class="btn-outline-brand-lg flex-fill" @click="generateByAI" :disabled="loadingAI">
            <span v-if="loadingAI" class="spinner-border spinner-border-sm me-2"></span>
            <i v-else class="bi bi-magic me-2"></i> Tạo lịch bằng AI
          </button>
        </div>
      </div>

      <!-- ══════════════════════════════
           BƯỚC 2 – Chọn địa điểm
           ══════════════════════════════ -->
      <div v-if="step === 2" class="tlt-section animate-in">
        <div class="section-head">
          <h2><i class="bi bi-geo-alt me-2 text-brand"></i>Chọn địa điểm muốn ghé thăm</h2>
          <p>Thêm những nơi bạn muốn đến – hệ thống sẽ tự động sắp xếp lịch trình hợp lý.</p>
        </div>

        <!-- Category filter tabs -->
        <div class="filter-tabs">
          <button class="filter-tab" :class="{ active: filterCat === null }" @click="filterCat = null">Tất cả</button>
          <button v-for="cat in categories" :key="cat.id" class="filter-tab" :class="{ active: filterCat === cat.id }"
            @click="filterCat = cat.id">{{ cat.ten_danh_muc }}</button>
        </div>

        <!-- Search bar -->
        <div class="search-bar-wrap mb-4">
          <div class="search-bar">
            <i class="bi bi-search"></i>
            <input v-model="searchQ" type="text" placeholder="Tìm địa điểm..." />
          </div>
          <button v-if="searchQ.trim().length >= 2" class="btn-brand" @click="searchGoogle" :disabled="loadingSerp"
            style="white-space: nowrap;">
            <i class="bi" :class="loadingSerp ? 'bi-hourglass-split spin' : 'bi-google'"></i> Tìm trên Google Maps
          </button>
        </div>

        <!-- SerpApi Loading -->
        <div v-if="loadingSerp" class="tlt-loading">
          <div class="spinner" style="border-top-color: #5a67d8;"></div>
          <span>Đang tìm kiếm trên Google Maps...</span>
        </div>

        <!-- SerpApi Results -->
        <div v-if="serpResults.length > 0" class="serp-results mb-4"
          style="background: #f8fbff; padding: 1.5rem; border-radius: 1rem; border: 1px dashed #c3dafe;">
          <h4 class="mb-3" style="color: #434190; font-size: 1.1rem; font-weight: 700;">
            <i class="bi bi-google me-2"></i>Kết quả từ Google Maps
          </h4>
          <div class="dd-grid">
            <div v-for="(res, i) in serpResults" :key="'serp' + i" class="dd-card">
              <div class="dd-img-wrap">
                <img :src="res.image || 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=400'"
                  class="dd-img" />
                <button class="dd-select-btn" @click="importAndSchedule(res, i)" :disabled="importingId === i"
                  style="background: #4c51bf;">
                  <i class="bi" :class="importingId === i ? 'bi-hourglass-split spin' : 'bi-cloud-download'"></i> Lấy
                </button>
              </div>
              <div class="dd-content">
                <h3 class="dd-title">{{ res.ten_dia_diem }}</h3>
                <p class="dd-desc"><i class="bi bi-geo-alt"></i> {{ res.dia_chi }}</p>
                <div class="dd-meta" style="color: #ed8936;">⭐ {{ res.danh_gia_trung_binh || '4.0' }} / 5</div>
              </div>
            </div>
          </div>
          <button class="btn btn-outline-secondary w-100 mt-3 rounded-pill" @click="serpResults = []">Đóng kết quả
            Google</button>
        </div>

        <!-- Loading -->
        <div v-if="loadingDiaDiem" class="tlt-loading">
          <div class="spinner"></div>
          <span>Đang tải địa điểm...</span>
        </div>

        <!-- Grid -->
        <div v-else class="dd-grid">
          <div v-for="dd in filteredDiaDiem" :key="dd.id" class="dd-card" :class="{ selected: isSelected(dd.id) }">
            <div class="dd-img-wrap">
              <img :src="dd.hinh_anh || 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=400&q=80'"
                :alt="dd.ten_dia_diem" class="dd-img" />
              <div class="dd-badge" v-if="dd.gia_ve > 0">{{ formatCurrency(dd.gia_ve) }}</div>
              <button class="dd-select-btn" :class="{ remove: isSelected(dd.id) }" @click="toggleSelect(dd)">
                <i :class="isSelected(dd.id) ? 'bi bi-check-circle-fill' : 'bi bi-plus-circle'"></i>
                {{ isSelected(dd.id) ? 'Đã chọn' : 'Thêm' }}
              </button>
            </div>
            <div class="dd-info">
              <h5>{{ dd.ten_dia_diem }}</h5>
              <p class="dd-addr"><i class="bi bi-pin-map me-1"></i>{{ dd.dia_chi || 'Đà Nẵng' }}</p>
              <div class="dd-meta">
                <span v-if="dd.gio_mo_cua">
                  <i class="bi bi-clock me-1"></i>{{ dd.gio_mo_cua }} – {{ dd.gio_dong_cua || '21:00' }}
                </span>
                <span v-if="dd.danh_gia_trung_binh">
                  <i class="bi bi-star-fill text-warning me-1"></i>{{ Number(dd.danh_gia_trung_binh).toFixed(1) }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <div v-if="!loadingDiaDiem && filteredDiaDiem.length === 0" class="tlt-empty">
          Không tìm thấy địa điểm phù hợp.
        </div>

        <!-- Bottom bar -->
        <div class="tlt-bottom-bar">
          <div class="tlt-selection-info">
            <span class="sel-count">{{ selectedDiaDiem.length }}</span> địa điểm đã chọn
          </div>
          <div class="tlt-actions-row">
            <button class="btn-ghost" @click="step = 1"><i class="bi bi-arrow-left me-1"></i>Quay lại</button>
            <button class="btn-brand-lg" :disabled="selectedDiaDiem.length === 0" @click="goStep3">
              Tạo lịch trình <i class="bi bi-magic ms-2"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- BƯỚC 3 – Lịch trình tự động -->
      <div v-if="step === 3" class="tlt-section animate-in">
        <div class="section-head">
          <h2><i class="bi bi-calendar-week me-2 text-brand"></i>Lịch trình gợi ý</h2>
          <p>
            Hệ thống đã sắp xếp <strong>{{ selectedDiaDiem.length }} địa điểm</strong> vào
            <strong>{{ soNgay }} ngày</strong>. Bạn có thể điều chỉnh thứ tự và thêm ghi chú.
          </p>
        </div>

        <!-- Budget tracker -->
        <div class="budget-tracker">
          <div class="budget-info">
            <span>Chi phí vé ước tính</span>
            <strong>{{ formatCurrency(tongGiaVe) }}</strong>
          </div>
          <div v-if="form.ngan_sach_du_kien > 0" class="budget-info">
            <span>Ngân sách</span>
            <strong :class="tongGiaVe > form.ngan_sach_du_kien ? 'over' : 'ok'">
              {{ formatCurrency(form.ngan_sach_du_kien) }}
            </strong>
          </div>
          <div v-if="form.ngan_sach_du_kien > 0" class="budget-bar-wrap">
            <div class="budget-bar-fill" :class="tongGiaVe > form.ngan_sach_du_kien ? 'over' : ''"
              :style="{ width: Math.min(100, (tongGiaVe / form.ngan_sach_du_kien) * 100) + '%' }"></div>
          </div>
        </div>

        <!-- Day tabs -->
        <div class="day-tabs">
          <button v-for="n in soNgay" :key="n" class="day-tab" :class="{ active: activeDayTab === n }"
            @click="activeDayTab = n">
            <span class="day-tab-num">Ngày {{ n }}</span>
            <span class="day-tab-date">{{ formatDate(form.ngay_bat_dau, n - 1) }}</span>
          </button>
        </div>

        <!-- 2-col layout: Timeline + Map -->
        <div class="step3-layout">

          <!-- LEFT: Timeline -->
          <div class="step3-left">
            <div class="timeline"
              v-if="lichTrinhTheoNgay[activeDayTab - 1] && lichTrinhTheoNgay[activeDayTab - 1].length > 0">
              <div v-for="(item, idx) in lichTrinhTheoNgay[activeDayTab - 1]" :key="item.id_dia_diem + '-' + idx"
                class="timeline-item">
                <div class="timeline-time">
                  <span class="time-badge">{{ item.gio }}</span>
                  <div class="timeline-line" v-if="idx < lichTrinhTheoNgay[activeDayTab - 1].length - 1"></div>
                </div>
                <div class="timeline-card">
                  <div class="tc-header">
                    <div class="tc-img-wrap">
                      <img
                        :src="item.hinh_anh || 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=200&q=70'"
                        class="tc-img" loading="lazy" />
                    </div>
                    <div class="tc-meta">
                      <h5>{{ item.ten_dia_diem }}</h5>
                      <p><i class="bi bi-pin-map me-1"></i>{{ item.dia_chi || 'Đà Nẵng' }}</p>
                      <p v-if="item.gia_ve > 0">
                        <i class="bi bi-ticket-perforated me-1"></i>{{ formatCurrency(item.gia_ve) }} / người
                      </p>
                    </div>
                    <div class="tc-order-btns">
                      <button @click="moveItem(activeDayTab - 1, idx, -1)" :disabled="idx === 0" title="Lên">
                        <i class="bi bi-arrow-up"></i>
                      </button>
                      <button @click="moveItem(activeDayTab - 1, idx, 1)"
                        :disabled="idx === lichTrinhTheoNgay[activeDayTab - 1].length - 1" title="Xuống">
                        <i class="bi bi-arrow-down"></i>
                      </button>
                      <button class="btn-remove" @click="removeItem(activeDayTab - 1, idx)" title="Xóa">
                        <i class="bi bi-trash3"></i>
                      </button>
                    </div>
                  </div>
                  <div class="tc-note">
                    <i class="bi bi-pencil-square me-1"></i>
                    <input v-model="item.ghi_chu" type="text" placeholder="Thêm ghi chú cho địa điểm này..."
                      class="note-input" />
                  </div>
                </div>
              </div>
            </div>

            <!-- Empty State for Day -->
            <div v-else class="empty-day-placeholder py-5 text-center">
              <div class="empty-icon-wrap mb-3">
                <i class="bi bi-calendar-x"></i>
              </div>
              <h5>Ngày này chưa có lịch trình</h5>
              <p class="text-muted">Quay lại Bước 2 để chọn thêm địa điểm hoặc sử dụng quy hoạch AI.</p>
              <button class="btn-brand-lg mt-2" @click="step = 2">
                <i class="bi bi-plus-circle me-1"></i> Thêm địa điểm
              </button>
            </div>
          </div>

          <!-- RIGHT: Map + Route Info -->
          <div class="step3-right">
            <div class="map-panel">
              <div class="map-panel-head">
                <i class="bi bi-map me-2"></i>Bản đồ vị trí ngày {{ activeDayTab }}
              </div>
              <div id="trip-map" style="height: 500px"></div>
            </div>
          </div>
        </div>

        <div class="tlt-bottom-bar">
          <div></div>
          <div class="tlt-actions-row">
            <button class="btn-ghost" @click="step = 2"><i class="bi bi-arrow-left me-1"></i>Quay lại</button>
            <button class="btn-brand-lg" @click="step = 4">
              Xem tóm tắt &amp; Lưu <i class="bi bi-check2-circle ms-2"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- ══════════════════════════════
           BƯỚC 4 – Xác nhận & Lưu
           ══════════════════════════════ -->
      <div v-if="step === 4" class="tlt-section animate-in">
        <div class="section-head">
          <h2><i class="bi bi-clipboard-check me-2 text-brand"></i>Xác nhận & Lưu lịch trình</h2>
          <p>Kiểm tra lại thông tin trước khi lưu.</p>
        </div>

        <div class="summary-grid">
          <div class="summary-card-big">
            <span class="sum-label">Tên chuyến đi</span>
            <h3>{{ form.ten_chuyen_di }}</h3>
            <div class="sum-chips">
              <span><i class="bi bi-calendar3 me-1"></i>{{ form.ngay_bat_dau }} → {{ form.ngay_ket_thuc }}</span>
              <span><i class="bi bi-people me-1"></i>{{ form.so_luong_thanh_vien }} người</span>
              <span><i class="bi bi-sun me-1"></i>{{ soNgay }} ngày {{ soNgay - 1 }} đêm</span>
            </div>
          </div>

          <div class="summary-stat">
            <i class="bi bi-geo-alt-fill"></i>
            <strong>{{ tongSoDiaDiem }}</strong>
            <span>địa điểm</span>
          </div>
          <div class="summary-stat">
            <i class="bi bi-ticket-perforated-fill"></i>
            <strong>{{ formatCurrency(tongGiaVe) }}</strong>
            <span>Chi phí vé ước tính</span>
          </div>
          <div class="summary-stat" v-if="form.ngan_sach_du_kien > 0">
            <i class="bi bi-wallet2"></i>
            <strong>{{ formatCurrency(form.ngan_sach_du_kien) }}</strong>
            <span>Ngân sách dự kiến</span>
          </div>
        </div>

        <!-- Day-by-day mini summary -->
        <div class="mini-days">
          <div v-for="(day, di) in lichTrinhTheoNgay" :key="di" class="mini-day">
            <div class="mini-day-head">
              <strong>Ngày {{ di + 1 }}</strong>
              <span>{{ formatDate(form.ngay_bat_dau, di) }}</span>
            </div>
            <ul>
              <li v-for="item in day" :key="item.id_dia_diem">
                <i class="bi bi-clock me-1"></i>{{ item.gio }} – {{ item.ten_dia_diem }}
              </li>
              <li v-if="day.length === 0" class="text-muted"><em>Không có địa điểm</em></li>
            </ul>
          </div>
        </div>

        <!-- Save error/success -->
        <div v-if="saveMsg" class="save-alert" :class="saveMsgType">
          <i :class="saveMsgType === 'success' ? 'bi bi-check-circle-fill' : 'bi bi-exclamation-circle-fill'"></i>
          {{ saveMsg }}
        </div>

        <div class="tlt-bottom-bar">
          <div></div>
          <div class="tlt-actions-row">
            <button class="btn-ghost" @click="step = 3" :disabled="saving">
              <i class="bi bi-arrow-left me-1"></i>Quay lại
            </button>
            <button class="btn-brand-lg" @click="saveLichTrinh" :disabled="saving || saveSuccess">
              <span v-if="saving"><i class="bi bi-hourglass-split me-2"></i>Đang lưu...</span>
              <span v-else-if="saveSuccess"><i class="bi bi-check-lg me-2"></i>Đã lưu!</span>
              <span v-else><i class="bi bi-floppy2 me-2"></i>Lưu lịch trình</span>
            </button>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script>
const BASE = 'http://localhost:8000/api';

export default {
  name: 'TaoLichTrinh',

  data() {
    return {
      step: 1,
      steps: ['Thông tin', 'Chọn địa điểm', 'Lịch trình', 'Xác nhận'],
      activeDayTab: 1,

      today: new Date().toISOString().split('T')[0],

      // ── Form bước 1 ──
      form: {
        ten_chuyen_di: '',
        ngay_bat_dau: '',
        ngay_ket_thuc: '',
        so_luong_thanh_vien: 2,
        ngan_sach_du_kien: 0,
        chu_thich: '',
      },
      errors: {},

      // ── Địa điểm ──
      allDiaDiem: [],
      loadingDiaDiem: false,
      selectedDiaDiem: [],
      filterCat: null,
      categories: [],
      searchQ: '',

      // ── SerpApi ──
      loadingSerp: false,
      serpResults: [],
      importingId: null,

      // ── Lịch trình ──
      lichTrinhTheoNgay: [], // Array<Array<item>>

      // ── Lưu ──
      saving: false,
      saveSuccess: false,
      saveMsg: '',
      saveMsgType: 'success',

      // ── Map ──
      mapInstance: null,
      mapLayers: [],

      loadingAI: false,
    };
  },

  mounted() {
    // Load Leaflet CSS dynamically
    if (!document.getElementById('leaflet-css')) {
      const link = document.createElement('link');
      link.id = 'leaflet-css';
      link.rel = 'stylesheet';
      link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
      document.head.appendChild(link);
    }
    // Load Leaflet JS dynamically (if not already loaded)
    if (!window.L) {
      const script = document.createElement('script');
      script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
      document.head.appendChild(script);
    }
    // Load Sortable.js for Drag-and-Drop
    if (!window.Sortable) {
      const script = document.createElement('script');
      script.src = 'https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js';
      document.head.appendChild(script);
    }
  },

  computed: {
    soNgay() {
      if (!this.form.ngay_bat_dau || !this.form.ngay_ket_thuc) return 0;
      const ms = new Date(this.form.ngay_ket_thuc) - new Date(this.form.ngay_bat_dau);
      return Math.max(1, Math.round(ms / 86400000) + 1);
    },

    filteredDiaDiem() {
      let list = this.allDiaDiem;
      if (this.filterCat !== null) {
        list = list.filter(d => d.id_danh_muc === this.filterCat);
      }
      if (this.searchQ.trim()) {
        const q = this.searchQ.toLowerCase();
        list = list.filter(
          d =>
            d.ten_dia_diem.toLowerCase().includes(q) ||
            (d.dia_chi && d.dia_chi.toLowerCase().includes(q))
        );
      }
      return list;
    },

    tongGiaVe() {
      return this.lichTrinhTheoNgay.flat().reduce((sum, item) => {
        return sum + (Number(item.gia_ve) || 0) * this.form.so_luong_thanh_vien;
      }, 0);
    },

    tongSoDiaDiem() {
      return this.lichTrinhTheoNgay.flat().length;
    },
  },

  watch: {
    activeDayTab(newVal) {
      if (this.step === 3) {
        this.$nextTick(() => {
          this.renderMapForDay(newVal - 1);
          this.initSortable();
        });
      }
    },
  },

  methods: {
    // ─── Utilities ───────────────────────────────
    formatCurrency(val) {
      if (!val) return '0đ';
      return Number(val).toLocaleString('vi-VN') + 'đ';
    },

    formatDate(baseDate, addDays) {
      if (!baseDate) return '';
      const d = new Date(baseDate);
      d.setDate(d.getDate() + addDays);
      return d.toLocaleDateString('vi-VN', { weekday: 'short', day: '2-digit', month: '2-digit' });
    },

    isSelected(id) {
      return this.selectedDiaDiem.some(d => d.id === id);
    },

    toggleSelect(dd) {
      const idx = this.selectedDiaDiem.findIndex(d => d.id === dd.id);
      if (idx === -1) {
        this.selectedDiaDiem.push(dd);
      } else {
        this.selectedDiaDiem.splice(idx, 1);
      }
    },

    // ─── SerpApi Google Maps ───────────────────────
    async searchGoogle() {
      if (!this.searchQ.trim()) return;
      this.loadingSerp = true;
      this.serpResults = [];
      try {
        const res = await fetch(`http://localhost:8000/api/serp/search?query=${encodeURIComponent(this.searchQ)}`);
        const json = await res.json();
        if (json.status) {
          this.serpResults = json.data || [];
          if (this.serpResults.length === 0) {
            alert('Không tìm thấy kết quả nào mới trên Google Maps.');
          }
        }
      } catch (e) {
        alert('Lỗi khi gọi API Google Maps: ' + e.message);
      } finally {
        this.loadingSerp = false;
      }
    },

    async importAndSchedule(googlePlace, index) {
      this.importingId = index;

      const payload = {
        ten_dia_diem: googlePlace.ten_dia_diem,
        dia_chi: googlePlace.dia_chi,
        vi_do: googlePlace.vi_do,
        kinh_do: googlePlace.kinh_do,
        danh_gia_trung_binh: googlePlace.danh_gia_trung_binh,
        image: googlePlace.image,
        mo_ta: googlePlace.mo_ta || 'Được thêm tự động từ Google Maps.',
        loai_dia_diem: 'Điểm check-in',
        id_danh_muc: 2,
      };

      try {
        const res = await fetch(`http://localhost:8000/api/serp/import`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const json = await res.json();

        if (!json.status && json.message === 'Địa điểm này đã tồn tại trong hệ thống.') {
          await this.fetchDiaDiem();
          alert('Địa điểm này đã có trong hệ thống nội bộ của chúng tôi! Bạn hãy tìm nó ngay bên dưới.');
          return;
        }

        if (json.status && json.data) {
          this.allDiaDiem.unshift(json.data);
          this.toggleSelect(json.data);
          this.serpResults.splice(index, 1);
        }
      } catch (e) {
        alert('Lỗi hệ thống khi tải địa điểm này: ' + e.message);
      } finally {
        this.importingId = null;
      }
    },

    // ─── Validation bước 1 ───────────────────────
    validateStep1() {
      this.errors = {};
      if (!this.form.ten_chuyen_di.trim()) this.errors.ten_chuyen_di = 'Vui lòng nhập tên chuyến đi.';
      if (!this.form.ngay_bat_dau) {
        this.errors.ngay_bat_dau = 'Vui lòng chọn ngày bắt đầu.';
      } else if (this.form.ngay_bat_dau < this.today) {
        this.errors.ngay_bat_dau = 'Ngày bắt đầu không được nhỏ hơn ngày hiện tại.';
      }

      if (!this.form.ngay_ket_thuc) {
        this.errors.ngay_ket_thuc = 'Vui lòng chọn ngày kết thúc.';
      } else if (this.form.ngay_bat_dau && this.form.ngay_ket_thuc <= this.form.ngay_bat_dau) {
        this.errors.ngay_ket_thuc = 'Ngày kết thúc phải lớn hơn ngày bắt đầu.';
      }
      return Object.keys(this.errors).length === 0;
    },

    goStep2() {
      if (!this.validateStep1()) return;
      this.step = 2;
      if (this.allDiaDiem.length === 0) this.fetchDiaDiem();
    },

    // ─── Fetch địa điểm ─────────────────────────
    async fetchDiaDiem() {
      this.loadingDiaDiem = true;
      try {
        const res = await fetch(`${BASE}/dia-diems/`);
        const json = await res.json();

        this.allDiaDiem = (json.data || []).map(d => ({
          ...d,
          // Đảm bảo có id_danh_muc để lọc
          id_danh_muc: d.id_danh_muc || d.danh_muc || 'other',
          ten_danh_muc: d.loai_dia_diem || 'Khác'
        }));

        // Thu thập các danh mục duy nhất
        const catMap = {};
        this.allDiaDiem.forEach(d => {
          if (d.id_danh_muc && !catMap[d.id_danh_muc]) {
            catMap[d.id_danh_muc] = { id: d.id_danh_muc, ten_danh_muc: d.ten_danh_muc };
          }
        });
        this.categories = Object.values(catMap);
      } catch {
        this.allDiaDiem = [];
      } finally {
        this.loadingDiaDiem = false;
      }
    },

    // ─── Thuật toán phân bổ lịch trình ──────────
    buildSchedule() {
      const days = this.soNgay;
      if (days <= 0 || this.selectedDiaDiem.length === 0) {
        this.lichTrinhTheoNgay = Array.from({ length: days }, () => []);
        return;
      }

      // Sắp xếp theo giờ mở cửa (ưu tiên sáng sớm lên trước)
      const sorted = [...this.selectedDiaDiem].sort((a, b) => {
        const ta = a.gio_mo_cua || '10:00';
        const tb = b.gio_mo_cua || '10:00';
        return ta.localeCompare(tb);
      });

      // Khung giờ mặc định mỗi slot trong ngày
      const timeSlots = ['08:00', '10:30', '13:30', '16:00', '19:00'];

      // Khởi tạo array ngày
      const schedule = Array.from({ length: days }, () => []);

      // Phân bổ tròn (round-robin theo ngày)
      sorted.forEach((dd, i) => {
        const dayIdx = i % days;
        const slotIdx = Math.floor(i / days);
        const gio = timeSlots[slotIdx] || `0${8 + slotIdx * 2}:00`.slice(-5);
        schedule[dayIdx].push({
          id_dia_diem: dd.id,
          ten_dia_diem: dd.ten_dia_diem,
          dia_chi: dd.dia_chi,
          hinh_anh: dd.hinh_anh,
          gia_ve: dd.gia_ve || 0,
          gio_mo_cua: dd.gio_mo_cua,
          gio: dd.gio_mo_cua || gio,
          ghi_chu: '',
          vi_do: dd.vi_do || null,
          kinh_do: dd.kinh_do || null,
        });
      });

      // Sắp xếp từng ngày theo giờ
      schedule.forEach(day => day.sort((a, b) => a.gio.localeCompare(b.gio)));

      this.lichTrinhTheoNgay = schedule;
      this.activeDayTab = 1;
    },

    async generateByAI() {
      if (!this.validateStep1()) return;

      const token = localStorage.getItem('client_token');
      if (!token) {
        alert('Vui lòng đăng nhập để sử dụng tính năng AI.');
        return;
      }

      this.loadingAI = true;
      try {
        const res = await fetch(`${BASE}/client/ai/generate-itinerary`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
          },
          body: JSON.stringify(this.form)
        });
        const json = await res.json();

        if (json.status === 'success') {
          // AI returns Array<Array<item>>
          // We need to enrich it with images/addresses from allDiaDiem if possible
          if (this.allDiaDiem.length === 0) await this.fetchDiaDiem();

          this.lichTrinhTheoNgay = json.data.map(day => {
            return day.map(item => {
              const original = this.allDiaDiem.find(d => d.id === item.id_dia_diem);
              return {
                ...item,
                dia_chi: original?.dia_chi || item.dia_chi || 'Đà Nẵng',
                hinh_anh: item.image || original?.hinh_anh || (original?.image) || 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=200',
                vi_do: item.vi_do || original?.vi_do || null,
                kinh_do: item.kinh_do || original?.kinh_do || null,
                gia_ve: item.gia_ve || original?.gia_ve || 0
              };
            });
          });

          this.step = 3;
          this.activeDayTab = 1;
          this.$nextTick(() => {
            this.initMap();
            this.renderMapForDay(0);
          });
        } else {
          console.error('AI Error Details:', json);
          const detailStr = json.details ? JSON.stringify(json.details) : '';
          alert('AI gặp sự cố: ' + (json.message || 'Lỗi không xác định') + '\n' + detailStr);
        }
      } catch (e) {
        alert('Lỗi kết nối: ' + e.message);
      } finally {
        this.loadingAI = false;
      }
    },

    goStep3() {
      if (this.selectedDiaDiem.length === 0) return;
      this.buildSchedule();
      this.step = 3;
      this.$nextTick(() => {
        this.initMap();
        this.renderMapForDay(0);
        this.initSortable();
      });
    },

    initSortable() {
      const el = document.querySelector('.timeline');
      if (!el || !window.Sortable) return;

      // Cleanup previous instance if any
      if (this.sortableInstance) this.sortableInstance.destroy();

      this.sortableInstance = new window.Sortable(el, {
        animation: 150,
        handle: '.timeline-card',
        ghostClass: 'sortable-ghost',
        onEnd: (evt) => {
          const dayIdx = this.activeDayTab - 1;
          const dayItems = [...this.lichTrinhTheoNgay[dayIdx]];
          const item = dayItems.splice(evt.oldIndex, 1)[0];
          dayItems.splice(evt.newIndex, 0, item);

          // Re-update hours based on new positions
          const timeSlots = ['08:00', '10:30', '13:30', '16:00', '19:00'];
          dayItems.forEach((it, i) => {
            it.gio = timeSlots[i] || `0${8 + i * 2}:00`.slice(-5);
          });

          this.lichTrinhTheoNgay[dayIdx] = dayItems;
          this.$nextTick(() => {
            this.renderMapForDay(dayIdx);
          });
        }
      });
    },

    // ─── Di chuyển & xóa item trong lịch trình ──
    moveItem(dayIdx, itemIdx, direction) {
      const day = this.lichTrinhTheoNgay[dayIdx];
      const target = itemIdx + direction;
      if (target < 0 || target >= day.length) return;
      const tmp = day[target];
      day[target] = day[itemIdx];
      day[itemIdx] = tmp;
    },

    removeItem(dayIdx, itemIdx) {
      this.lichTrinhTheoNgay[dayIdx].splice(itemIdx, 1);
    },

    // ─── Lưu lịch trình ─────────────────────────
    async saveLichTrinh() {
      const token = localStorage.getItem('client_token');
      if (!token) {
        this.saveMsg = 'Bạn cần đăng nhập để lưu lịch trình!';
        this.saveMsgType = 'error';
        return;
      }

      this.saving = true;
      this.saveMsg = '';

      try {
        // 1. Tạo chuyến đi
        const r1 = await fetch(`${BASE}/client/chuyen-di/create`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${token}` },
          body: JSON.stringify(this.form),
        });
        const j1 = await r1.json();
        if (!j1.status) throw new Error(j1.message || 'Lỗi tạo chuyến đi');

        // 2. Lấy id chuyến đi vừa tạo (lấy cái mới nhất của user)
        const r2 = await fetch(`${BASE}/client/chuyen-di/get-data`, {
          headers: { Authorization: `Bearer ${token}` },
        });
        const j2 = await r2.json();
        const newestTrip = j2.data && j2.data[0];
        if (!newestTrip) throw new Error('Không lấy được ID chuyến đi.');

        // 3. Bulk create chi tiết
        const items = this.lichTrinhTheoNgay.flatMap((day, di) =>
          day.map(item => ({
            id_dia_diem: item.id_dia_diem,
            thoi_gian_du_kien: `Ngày ${di + 1} – ${item.gio}`,
            ghi_chu: item.ghi_chu || '',
          }))
        );

        const r3 = await fetch(`${BASE}/client/chi-tiet-chuyen-di/bulk-create`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${token}` },
          body: JSON.stringify({ id_chuyen_di: newestTrip.id, items }),
        });
        const j3 = await r3.json();
        if (!j3.status) throw new Error(j3.message || 'Lỗi lưu chi tiết');

        this.saveSuccess = true;
        this.saveMsg = '🎉 Lịch trình đã được lưu thành công! Chuyển sang trang lịch trình của bạn...';
        this.saveMsgType = 'success';

        setTimeout(() => {
          this.$router.push('/lich-trinh-cua-toi');
        }, 2000);
      } catch (err) {
        this.saveMsg = err.message || 'Có lỗi xảy ra. Vui lòng thử lại.';
        this.saveMsgType = 'error';
      } finally {
        this.saving = false;
      }
    },

    // ─── Map Methods ───────────────────────────────────────────
    initMap() {
      if (this.mapInstance) {
        this.mapInstance.remove();
        this.mapInstance = null;
      }
      const L = window.L;
      if (!L) return;
      this.mapInstance = L.map('trip-map').setView([16.0544, 108.2022], 13);
      L.tileLayer('https://mt1.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
        attribution: '&copy; Google Maps',
        maxZoom: 19,
      }).addTo(this.mapInstance);
    },

    clearMapLayers() {
      if (!this.mapInstance) return;
      this.mapLayers.forEach(layer => this.mapInstance.removeLayer(layer));
      this.mapLayers = [];
    },

    async renderMapForDay(dayIndex) {
      const L = window.L;
      if (!L) {
        // Leaflet may still be loading; retry after a short delay
        setTimeout(() => this.renderMapForDay(dayIndex), 500);
        return;
      }
      if (!this.mapInstance) this.initMap();
      if (!this.mapInstance) return;

      this.clearMapLayers();

      const dayItems = this.lichTrinhTheoNgay[dayIndex] || [];
      const validItems = dayItems.filter(item => item.vi_do && item.kinh_do);

      if (validItems.length === 0) return;

      // Create numbered markers for each location
      const bounds = [];
      validItems.forEach((item, idx) => {
        const lat = parseFloat(item.vi_do);
        const lng = parseFloat(item.kinh_do);
        if (isNaN(lat) || isNaN(lng)) return;

        bounds.push([lat, lng]);

        const icon = L.divIcon({
          className: '',
          html: `<div style="
            background: #5a67d8; color: #fff; border-radius: 50%;
            width: 32px; height: 32px; display: flex; align-items: center;
            justify-content: center; font-weight: 700; font-size: 14px;
            border: 3px solid #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.3);
          ">${idx + 1}</div>`,
          iconSize: [32, 32],
          iconAnchor: [16, 16],
        });

        const imgUrl = item.hinh_anh || 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=200&q=60';
        const marker = L.marker([lat, lng], { icon })
          .bindPopup(`
            <div style="min-width:180px">
              <img src="${imgUrl}" style="width:100%;height:90px;object-fit:cover;border-radius:6px;margin-bottom:6px">
              <strong style="font-size:14px">${item.ten_dia_diem}</strong><br>
              <small style="color:#666">${item.dia_chi || 'Đà Nẵng'}</small><br>
              <small>🕐 ${item.gio}</small>
            </div>
          `, { maxWidth: 220 })
          .addTo(this.mapInstance);

        this.mapLayers.push(marker);
      });

      if (bounds.length > 0) {
        this.mapInstance.fitBounds(bounds, { padding: [40, 40] });
      }
    },
  },
};
</script>

<style scoped>
/* ──────────── Base ──────────── */
.tlt-page {
  min-height: 100vh;
  background: linear-gradient(160deg, #f0f7ff 0%, #fafbff 60%, #f0fff8 100%);
  font-family: 'Inter', sans-serif;
  color: #1e2d44;
}

.tlt-body {
  padding: 2.5rem 1rem 6rem;
  max-width: 1040px;
}

/* ──────────── Wizard bar ──────────── */
.tlt-wizard-bar {
  background: #fff;
  border-bottom: 1px solid rgba(30, 45, 68, 0.07);
  padding: 1.1rem 0;
  position: sticky;
  top: 0;
  z-index: 50;
  box-shadow: 0 4px 24px rgba(30, 45, 68, 0.06);
}

.wizard-steps {
  display: flex;
  gap: 0;
  align-items: center;
  justify-content: center;
}

.wizard-step {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  padding: 0 1.5rem;
  position: relative;
  color: #a0adbf;
  transition: color 0.3s;
}

.wizard-step+.wizard-step::before {
  content: '';
  position: absolute;
  left: 0;
  top: 50%;
  transform: translateY(-50%);
  width: 1px;
  height: 24px;
  background: #e2e8f0;
}

.wizard-step.active {
  color: #10b981;
}

.wizard-step.done {
  color: #22c55e;
}

.step-icon {
  width: 2rem;
  height: 2rem;
  border-radius: 50%;
  background: #ecf0f6;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 0.85rem;
  transition: all 0.3s;
}

.wizard-step.active .step-icon {
  background: linear-gradient(135deg, #10b981, #0ea5e9);
  color: #fff;
}

.wizard-step.done .step-icon {
  background: #22c55e;
  color: #fff;
}

.step-label {
  font-size: 0.85rem;
  font-weight: 600;
  display: none;
}

@media (min-width: 576px) {
  .step-label {
    display: block;
  }
}

/* ──────────── animate-in ──────────── */
.animate-in {
  animation: fadeUp 0.45s ease both;
}

@keyframes fadeUp {
  from {
    opacity: 0;
    transform: translateY(22px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* ──────────── Section head ──────────── */
.section-head {
  margin-bottom: 2rem;
}

.section-head h2 {
  font-size: 1.75rem;
  font-weight: 800;
  color: #1e2d44;
  margin-bottom: 0.4rem;
}

.section-head p {
  color: #627289;
  margin-bottom: 0;
  font-size: 0.97rem;
}

.text-brand {
  color: #10b981;
}

/* ──────────── Form bước 1 ──────────── */
.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.2rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
}

.full-col {
  grid-column: 1 / -1;
}

label {
  font-size: 0.87rem;
  font-weight: 600;
  color: #3d5166;
}

.req {
  color: #f43f5e;
}

.tlt-input {
  padding: 0.7rem 1rem;
  border: 1.5px solid #dbe3f0;
  border-radius: 0.85rem;
  font-size: 0.96rem;
  background: #fff;
  transition: border-color 0.2s, box-shadow 0.2s;
  outline: none;
  color: #1e2d44;
  width: 100%;
}

.tlt-input:focus {
  border-color: #10b981;
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.12);
}

.err-msg {
  font-size: 0.8rem;
  color: #f43f5e;
}

.number-input {
  display: flex;
  align-items: center;
  gap: 0;
  border: 1.5px solid #dbe3f0;
  border-radius: 0.85rem;
  overflow: hidden;
  background: #fff;
}

.number-input button {
  width: 42px;
  height: 46px;
  background: #f5f8fe;
  border: none;
  font-size: 1.2rem;
  cursor: pointer;
  color: #3d5166;
  transition: background 0.18s;
}

.number-input button:hover {
  background: #e4edfc;
}

.number-input span {
  flex: 1;
  text-align: center;
  font-weight: 700;
  font-size: 1rem;
  color: #1e2d44;
}

.trip-chip {
  display: inline-flex;
  align-items: center;
  background: linear-gradient(90deg, #dcfce7, #d1faf4);
  border: 1px solid #a7f3d0;
  border-radius: 999px;
  padding: 0.55rem 1.25rem;
  font-size: 0.9rem;
  color: #065f46;
  margin-top: 1.2rem;
}

/* ──────────── Actions ──────────── */
.tlt-actions {
  margin-top: 2rem;
}

.tlt-bottom-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 2.5rem;
  padding-top: 1.5rem;
  border-top: 1px solid #e8edf5;
}

.tlt-actions-row {
  display: flex;
  gap: 0.9rem;
  align-items: center;
}

.btn-brand-lg {
  background: linear-gradient(135deg, #10b981, #0ea5e9);
  color: #fff;
  border: none;
  border-radius: 999px;
  padding: 0.75rem 1.9rem;
  font-size: 0.97rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 8px 20px rgba(16, 185, 129, 0.25);
}

.btn-brand-lg:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 12px 28px rgba(16, 185, 129, 0.35);
}

.btn-brand-lg:disabled {
  opacity: 0.55;
  cursor: not-allowed;
}

.btn-outline-brand-lg {
  background: white;
  border: 1.5px solid #10b981;
  color: #10b981;
  border-radius: 999px;
  padding: 0.75rem 1.9rem;
  font-size: 0.97rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-outline-brand-lg:hover:not(:disabled) {
  background: rgba(16, 185, 129, 0.05);
  border-color: #0ea5e9;
  color: #0ea5e9;
  transform: translateY(-2px);
}

.btn-ghost {
  background: transparent;
  color: #5a6d80;
  border: 1.5px solid #d5dde8;
  border-radius: 999px;
  padding: 0.72rem 1.5rem;
  font-size: 0.95rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.18s;
}

.btn-ghost:hover:not(:disabled) {
  background: #f0f4fb;
}

.btn-ghost:disabled {
  opacity: 0.5;
}

/* ──────────── Filter tabs ──────────── */
.filter-tabs {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-bottom: 1.2rem;
}

.filter-tab {
  padding: 0.42rem 1rem;
  border-radius: 999px;
  border: 1.5px solid #d5dde8;
  background: #fff;
  font-size: 0.85rem;
  font-weight: 600;
  color: #5a6d80;
  cursor: pointer;
  transition: all 0.18s;
}

.filter-tab.active,
.filter-tab:hover {
  background: #10b981;
  border-color: #10b981;
  color: #fff;
}

/* ──────────── Search ──────────── */
.search-bar-wrap {
  display: flex;
  gap: 0.75rem;
  align-items: center;
}

.search-bar-wrap .search-bar {
  flex: 1;
  margin-bottom: 0 !important;
}

.search-bar {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  background: #fff;
  border: 1.5px solid #dbe3f0;
  border-radius: 999px;
  padding: 0.55rem 1.2rem;
}

.search-bar i {
  color: #a0adbf;
  font-size: 1rem;
}

.search-bar input {
  border: none;
  outline: none;
  font-size: 0.95rem;
  flex: 1;
  color: #1e2d44;
  background: transparent;
}

/* ──────────── DiaDiem grid ──────────── */
.dd-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
  gap: 1.2rem;
  margin-bottom: 1rem;
}

.dd-card {
  background: #fff;
  border-radius: 1.2rem;
  border: 2px solid transparent;
  box-shadow: 0 6px 20px rgba(30, 45, 68, 0.07);
  overflow: hidden;
  transition: all 0.22s;
}

.dd-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 12px 30px rgba(30, 45, 68, 0.12);
}

.dd-card.selected {
  border-color: #10b981;
  box-shadow: 0 8px 24px rgba(16, 185, 129, 0.2);
}

.dd-img-wrap {
  position: relative;
  overflow: hidden;
}

.dd-img {
  width: 100%;
  height: 170px;
  object-fit: cover;
  display: block;
  transition: transform 0.3s;
}

.dd-card:hover .dd-img {
  transform: scale(1.05);
}

.dd-badge {
  position: absolute;
  top: 10px;
  left: 10px;
  background: rgba(16, 185, 129, 0.92);
  color: #fff;
  font-size: 0.78rem;
  font-weight: 700;
  padding: 0.2rem 0.65rem;
  border-radius: 999px;
}

.dd-select-btn {
  position: absolute;
  bottom: 10px;
  right: 10px;
  display: flex;
  align-items: center;
  gap: 0.35rem;
  background: rgba(255, 255, 255, 0.95);
  border: 1.5px solid #d5dde8;
  border-radius: 999px;
  padding: 0.3rem 0.85rem;
  font-size: 0.82rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.18s;
  color: #1e2d44;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.dd-select-btn i {
  font-size: 1rem;
}

.dd-select-btn:hover {
  background: #10b981;
  border-color: #10b981;
  color: #fff;
}

.dd-select-btn.remove {
  background: #10b981;
  border-color: #10b981;
  color: #fff;
}

.dd-info {
  padding: 1rem;
}

.dd-info h5 {
  font-size: 0.97rem;
  font-weight: 700;
  margin-bottom: 0.3rem;
  color: #1e2d44;
}

.dd-addr {
  font-size: 0.82rem;
  color: #7a8ea0;
  margin-bottom: 0.4rem;
}

.dd-meta {
  display: flex;
  gap: 0.8rem;
  font-size: 0.8rem;
  color: #7a8ea0;
  flex-wrap: wrap;
}

.tlt-selection-info {
  font-size: 0.9rem;
  color: #5a6d80;
  font-weight: 600;
}

.sel-count {
  display: inline-block;
  background: #10b981;
  color: #fff;
  border-radius: 50%;
  width: 1.6rem;
  height: 1.6rem;
  text-align: center;
  line-height: 1.6rem;
  font-size: 0.85rem;
  font-weight: 700;
  margin-right: 0.35rem;
}

/* ──────────── Budget tracker ──────────── */
.budget-tracker {
  background: #fff;
  border-radius: 1.2rem;
  padding: 1rem 1.4rem;
  display: flex;
  align-items: center;
  gap: 2rem;
  flex-wrap: wrap;
  border: 1px solid #e8edf5;
  margin-bottom: 1.5rem;
  box-shadow: 0 4px 14px rgba(30, 45, 68, 0.06);
}

.budget-info {
  display: flex;
  flex-direction: column;
  font-size: 0.85rem;
}

.budget-info span {
  color: #7a8ea0;
}

.budget-info strong {
  font-size: 1.1rem;
  color: #1e2d44;
  font-weight: 800;
}

.budget-info strong.over {
  color: #f43f5e;
}

.budget-info strong.ok {
  color: #10b981;
}

.budget-bar-wrap {
  flex: 1;
  min-width: 120px;
  height: 8px;
  background: #e8edf5;
  border-radius: 999px;
  overflow: hidden;
}

.budget-bar-fill {
  height: 100%;
  background: linear-gradient(90deg, #10b981, #0ea5e9);
  border-radius: 999px;
  transition: width 0.4s ease;
}

.budget-bar-fill.over {
  background: linear-gradient(90deg, #f43f5e, #fb923c);
}

/* ──────────── Day tabs ──────────── */
.day-tabs {
  display: flex;
  gap: 0.55rem;
  flex-wrap: wrap;
  margin-bottom: 1.5rem;
}

.day-tab {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 0.55rem 1.2rem;
  border-radius: 0.9rem;
  border: 1.5px solid #dbe3f0;
  background: #fff;
  cursor: pointer;
  transition: all 0.2s;
  min-width: 80px;
}

.day-tab.active {
  background: linear-gradient(135deg, #10b981, #0ea5e9);
  border-color: transparent;
  color: #fff;
  box-shadow: 0 6px 16px rgba(16, 185, 129, 0.28);
}

.day-tab-num {
  font-weight: 700;
  font-size: 0.88rem;
}

.day-tab-date {
  font-size: 0.75rem;
  opacity: 0.8;
}

/* ──────────── Timeline ──────────── */
.timeline {
  display: flex;
  flex-direction: column;
  gap: 0;
}

.timeline-item {
  display: flex;
  gap: 1rem;
}

.timeline-time {
  width: 62px;
  flex-shrink: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding-top: 0.2rem;
}

.time-badge {
  background: linear-gradient(135deg, #10b981, #0ea5e9);
  color: #fff;
  font-size: 0.75rem;
  font-weight: 700;
  padding: 0.28rem 0.5rem;
  border-radius: 0.6rem;
  white-space: nowrap;
}

.timeline-line {
  flex: 1;
  width: 3px;
  background: linear-gradient(to bottom, #10b981, #0ea5e9, transparent);
  margin: 0.4rem 0;
  min-height: 40px;
  border-radius: 99px;
  opacity: 0.4;
}

.timeline-card {
  flex: 1;
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(10px);
  border-radius: 1.25rem;
  border: 1px solid rgba(255, 255, 255, 0.5);
  padding: 1.2rem;
  margin-bottom: 1.2rem;
  box-shadow: 0 10px 30px rgba(30, 45, 68, 0.08);
  transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.timeline-card:hover {
  transform: translateX(8px);
  box-shadow: 0 15px 40px rgba(30, 45, 68, 0.12);
  border-color: #10b981;
}

.tc-header {
  display: flex;
  gap: 0.9rem;
  align-items: flex-start;
}

.tc-img-wrap {
  flex-shrink: 0;
}

.tc-img {
  width: 70px;
  height: 60px;
  object-fit: cover;
  border-radius: 0.7rem;
  display: block;
}

.tc-meta {
  flex: 1;
}

.tc-meta h5 {
  font-size: 0.93rem;
  font-weight: 700;
  margin-bottom: 0.2rem;
  color: #1e2d44;
}

.tc-meta p {
  font-size: 0.8rem;
  color: #7a8ea0;
  margin-bottom: 0.15rem;
}

.tc-order-btns {
  display: flex;
  flex-direction: column;
  gap: 0.2rem;
  flex-shrink: 0;
}

.tc-order-btns button {
  width: 32px;
  height: 32px;
  border-radius: 0.75rem;
  border: 1px solid #e2e8f0;
  background: #fff;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.9rem;
  color: #64748b;
  transition: all 0.2s;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

.tc-order-btns button:hover:not(:disabled) {
  background: #10b981;
  border-color: #10b981;
  color: #fff;
  transform: scale(1.1);
}

.tc-order-btns button:disabled {
  opacity: 0.2;
  cursor: not-allowed;
}

.tc-order-btns .btn-remove:hover:not(:disabled) {
  background: #f43f5e;
  border-color: #f43f5e;
  color: #fff;
}

.sortable-ghost {
  opacity: 0.4;
  background: #eef4ff;
  border: 2px dashed #3b82f6;
}

.empty-day-placeholder {
  background: rgba(255, 255, 255, 0.5);
  border-radius: 1.5rem;
  border: 2px dashed #dbe3f0;
}

.empty-icon-wrap {
  font-size: 3rem;
  color: #c0cedf;
}

.btn-brand-lg {
  padding: 0.8rem 2rem;
  border-radius: 999px;
  border: none;
  background: linear-gradient(135deg, #10b981, #0ea5e9);
  color: #fff;
  font-weight: 700;
  font-size: 1rem;
  transition: all 0.3s;
  box-shadow: 0 10px 20px rgba(16, 185, 129, 0.2);
}

.btn-brand-lg:hover {
  transform: translateY(-2px);
  box-shadow: 0 15px 30px rgba(16, 185, 129, 0.3);
}

.tc-note {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  margin-top: 0.75rem;
  padding-top: 0.75rem;
  border-top: 1px dashed #e8edf5;
  font-size: 0.82rem;
  color: #a0adbf;
}

.note-input {
  flex: 1;
  border: none;
  outline: none;
  font-size: 0.85rem;
  color: #3d5166;
  background: transparent;
}

.note-input::placeholder {
  color: #c0c8d8;
}

/* ──────────── Summary bước 4 ──────────── */
.summary-grid {
  display: grid;
  grid-template-columns: 1fr repeat(3, auto);
  gap: 1rem;
  align-items: start;
  margin-bottom: 2rem;
  flex-wrap: wrap;
}

.summary-card-big {
  background: linear-gradient(135deg, #f0fff8, #e0f2fe);
  border-radius: 1.2rem;
  padding: 1.4rem 1.6rem;
  border: 1px solid #a7f3d0;
}

.sum-label {
  font-size: 0.78rem;
  text-transform: uppercase;
  letter-spacing: 0.07em;
  color: #065f46;
  font-weight: 700;
}

.summary-card-big h3 {
  font-size: 1.4rem;
  font-weight: 800;
  color: #1e2d44;
  margin: 0.3rem 0 0.65rem;
}

.sum-chips {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.sum-chips span {
  background: rgba(16, 185, 129, 0.1);
  color: #065f46;
  border-radius: 999px;
  padding: 0.22rem 0.75rem;
  font-size: 0.82rem;
  font-weight: 600;
}

.summary-stat {
  background: #fff;
  border-radius: 1.1rem;
  padding: 1.1rem 1.2rem;
  border: 1px solid #e8edf5;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.15rem;
  text-align: center;
  box-shadow: 0 4px 14px rgba(30, 45, 68, 0.06);
  min-width: 120px;
}

.summary-stat i {
  font-size: 1.4rem;
  color: #10b981;
  margin-bottom: 0.3rem;
}

.summary-stat strong {
  font-size: 1.1rem;
  font-weight: 800;
  color: #1e2d44;
}

.summary-stat span {
  font-size: 0.78rem;
  color: #7a8ea0;
}

.mini-days {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.mini-day {
  background: #fff;
  border-radius: 1rem;
  padding: 1rem 1.1rem;
  border: 1px solid #e8edf5;
}

.mini-day-head {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.6rem;
}

.mini-day-head strong {
  font-size: 0.9rem;
  color: #1e2d44;
}

.mini-day-head span {
  font-size: 0.8rem;
  color: #7a8ea0;
}

.mini-day ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.mini-day li {
  font-size: 0.83rem;
  color: #3d5166;
  padding: 0.18rem 0;
}

/* ──────────── Save alert ──────────── */
.save-alert {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  padding: 0.85rem 1.2rem;
  border-radius: 0.9rem;
  font-size: 0.9rem;
  font-weight: 600;
  margin-bottom: 1rem;
}

.save-alert.success {
  background: #dcfce7;
  color: #15803d;
  border: 1px solid #bbf7d0;
}

.save-alert.error {
  background: #fff1f2;
  color: #be123c;
  border: 1px solid #fecdd3;
}

/* ──────────── Loading / Empty ──────────── */
.tlt-loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
  padding: 3rem;
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

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.tlt-empty {
  text-align: center;
  color: #a0adbf;
  padding: 2rem;
  font-size: 0.92rem;
}

@media (max-width: 640px) {
  .form-grid {
    grid-template-columns: 1fr;
  }

  .summary-grid {
    grid-template-columns: 1fr 1fr;
  }

  .dd-grid {
    grid-template-columns: 1fr;
  }

  .step3-layout {
    flex-direction: column;
  }

  .step3-right {
    min-width: unset;
  }
}

/* ──── Step 3: 2-column layout ──── */
.step3-layout {
  display: flex;
  gap: 1.5rem;
  align-items: flex-start;
  margin-bottom: 1.5rem;
}

.step3-left {
  flex: 1;
  min-width: 0;
}

.step3-right {
  flex: 0 0 400px;
  min-width: 320px;
  position: sticky;
  top: 90px;
}

/* ──── Map Panel ──── */
.map-panel {
  background: #fff;
  border-radius: 1.2rem;
  border: 1px solid #e8edf5;
  box-shadow: 0 8px 24px rgba(30, 45, 68, 0.07);
  overflow: hidden;
}

.map-panel-head {
  padding: 0.85rem 1.1rem;
  border-bottom: 1px solid #eef2f7;
  font-weight: 700;
  font-size: 0.92rem;
  color: #1e2d44;
  display: flex;
  align-items: center;
  background: #f8fbff;
  gap: 0.5rem;
}

.route-loading-text {
  margin-left: auto;
  font-weight: 500;
  font-size: 0.8rem;
  color: #5a67d8;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.spin {
  animation: spin 0.9s linear infinite;
  display: inline-block;
}

#trip-map {
  height: 500px;
  width: 100%;
  z-index: 0;
}
</style>
