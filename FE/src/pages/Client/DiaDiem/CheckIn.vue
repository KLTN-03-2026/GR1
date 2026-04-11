<template>
  <div class="giai-tri-page">
    <section class="hero-section py-5 position-relative">
      <button class="btn btn-success create-itinerary-btn" @click="createItinerary">
        <i class="fas fa-calendar-plus me-2"></i>Tạo lịch trình
      </button>
      <div class="container text-center">
        <h1 class="page-title mb-3">
          <i class="fas fa-camera me-3"></i>Địa điểm Check-in Nổi Bật Đà Nẵng
        </h1>
        <p class="page-subtitle mb-4 text-center">
          <i class="fas fa-star me-2"></i>Khám phá những địa điểm check-in đẹp nhất: Cầu Vàng, Cầu Rồng, Biển Mỹ Khê, Phố cổ Hội An... và nhận đề xuất từ AI sao cho phù hợp ngân sách của bạn.
        </p>
        <div class="search-bar d-flex justify-content-center mb-3">
          <input v-model="tempSearchQuery" type="text" class="form-control me-2" placeholder="Tìm kiếm địa điểm..." style="max-width: 400px;" @keyup.enter="performSearch">
          <button @click="performSearch" class="btn btn-primary me-2">Tìm kiếm</button>
          <button @click="clearSearch" class="btn btn-outline-secondary" v-if="searchQuery">Xóa</button>
          <button class="btn btn-outline-success ms-2" v-if="tempSearchQuery.length >= 2" @click="searchGoogle" :disabled="loadingSerp"><span v-if="loadingSerp" class="spinner-border spinner-border-sm"></span><i v-else class="bi bi-google"></i> Tìm GG Maps</button>
        </div>
        <div class="filter-bar d-flex justify-content-center flex-wrap gap-2">
          <button v-for="filter in filters" :key="filter"
            :class="['btn btn-sm', activeFilter === filter ? 'btn-primary' : 'btn-outline-primary']"
            @click="setFilter(filter)">
            {{ filter }}
          </button>
        </div>
      </div>
    </section>

    <section class="serp-results-section py-4" v-if="serpResults.length > 0">
      <div class="container">
        <div class="glass-panel p-4 mb-4">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="results-title mb-0">
              <i class="bi bi-google me-2 text-primary"></i>Kết quả từ Google Maps
            </h3>
            <button class="btn btn-sm btn-outline-danger" @click="serpResults = []">Đóng kết quả</button>
          </div>
          <div class="row g-3">
            <div v-for="(res, index) in serpResults" :key="index" class="col-12 col-lg-6">
              <div class="serp-item d-flex align-items-start gap-3 p-3 rounded-4 transition-all">
                <div class="serp-item-img flex-shrink-0" :style="{ backgroundImage: `url(${res.image})` }"></div>
                <div class="serp-item-content flex-grow-1">
                  <h6 class="mb-1 text-truncate">{{ res.ten_dia_diem }}</h6>
                  <p class="text-muted small mb-2 text-truncate-2">{{ res.dia_chi }}</p>
                  <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-light text-dark border"><i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ res.vi_do }}, {{ res.kinh_do }}</span>
                    <span class="badge bg-warning text-dark" v-if="res.danh_gia_trung_binh"><i class="bi bi-star-fill me-1"></i>{{ res.danh_gia_trung_binh }}</span>
                  </div>
                  <button 
                    class="btn btn-sm btn-primary w-100 rounded-3" 
                    @click="importAndShow(res, index)"
                    :disabled="importingId === index"
                  >
                    <span v-if="importingId === index" class="spinner-border spinner-border-sm me-1"></span>
                    <i v-else class="bi bi-plus-circle me-1"></i>Lưu vào hệ thống
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="places-section py-4">
      <div class="container">
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-primary" role="status" style="width:3rem;height:3rem;"></div>
          <p class="mt-3 text-muted">Đang tải dữ liệu...</p>
        </div>
        <div v-else-if="error" class="alert alert-danger text-center" role="alert">
          <i class="fas fa-exclamation-triangle me-2"></i>{{ error }}
          <button class="btn btn-sm btn-outline-danger ms-3" @click="fetchData">Thử lại</button>
        </div>
        <div v-else class="row g-4">
          <div v-for="place in filteredPlaces" :key="place.id" class="col-12 col-md-6 col-xl-4">
            <div class="card place-card h-100 shadow-sm">
              <div class="place-image" :style="{ backgroundImage: `url(${place.image})` }">
                <div :class="['place-badge', categoryClass(place.loai_dia_diem)]">{{ place.loai_dia_diem }}</div>
              </div>
              <div class="card-body d-flex flex-column">
                <div class="mb-2">
                  <h5 class="card-title">{{ place.ten_dia_diem }}</h5>
                  <small class="text-muted">📍 {{ place.dia_chi }}</small>
                </div>
                <div class="mb-2">
                  <span class="badge bg-success me-2">Mở: {{ place.gio_mo_cua }}</span>
                  <span class="badge bg-danger">Đóng: {{ place.gio_dong_cua }}</span>
                </div>
                <p class="text-secondary small">{{ place.mo_ta }}</p>
                <div class="mb-2">⭐ {{ place.danh_gia_trung_binh }}</div>
                <div class="mb-3">
                  <strong class="price-text">{{ place.gia_ve == 0 ? 'Miễn phí' : formatPrice(place.gia_ve) }}</strong>
                </div>
                <div class="mt-auto">
                  <button class="btn btn-primary w-100" @click="viewDetail(place)">Xem chi tiết</button>
                </div>
              </div>
            </div>
          </div>
          <div v-if="filteredPlaces.length === 0 && !loading" class="text-center py-4 text-muted">
            <i class="fas fa-search fa-2x mb-2 d-block"></i>Không tìm thấy địa điểm phù hợp
          </div>
        </div>
      </div>
    </section>

    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal-content">
        <button class="btn-close" @click="closeModal">×</button>
        <div class="modal-header">
          <h4>{{ selectedPlace.ten_dia_diem }}</h4>
          <small class="text-muted">{{ selectedPlace.loai_dia_diem }} - ⭐ {{ selectedPlace.danh_gia_trung_binh }}</small>
        </div>
        <div class="modal-body">
          <div v-if="selectedPlace.vi_do && selectedPlace.kinh_do" id="detail-map" style="height: 250px; width: 100%; border-radius: 8px; margin-bottom: 15px; border: 1px solid #e0e0e0; z-index: 1;"></div>
          <div v-else class="alert alert-warning py-2 mb-3" style="font-size: 0.9rem;"><i class="fas fa-map-marker-alt"></i> Chưa có tọa độ bản đồ chi tiết.</div>
          <div class="modal-image" :style="{ backgroundImage: `url(${selectedPlace.image})` }"></div>
          <p>{{ selectedPlace.mo_ta }}</p>
          <ul>
            <li><strong>Địa chỉ:</strong> {{ selectedPlace.dia_chi }}</li>
            <li><strong>Giờ mở / đóng:</strong> {{ selectedPlace.gio_mo_cua }} - {{ selectedPlace.gio_dong_cua }}</li>
            <li><strong>Giá vé:</strong> {{ selectedPlace.gia_ve == 0 ? 'Miễn phí' : formatPrice(selectedPlace.gia_ve) }}</li>
            <li><strong>Loại:</strong> {{ selectedPlace.loai_dia_diem }}</li>
          </ul>
        </div>
        <div class="modal-actions">
          <button class="btn btn-secondary" @click="closeModal">Đóng</button>
          <button class="btn btn-primary" @click="alert('Chức năng thêm vào lịch trình đang phát triển'); closeModal()">Thêm vào lịch trình</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
const BASE = 'http://localhost:8000/api';
export default {
  name: 'CheckIn',
  data() {
    return {
      activeFilter: 'Tất cả',
      filters: ['Tất cả', 'Cầu nổi tiếng', 'Bãi biển', 'Ngắm cảnh', 'Lịch sử', 'Phố cổ', 'Tự nhiên'],
      showModal: false, selectedPlace: null,
      modalMapInstance: null,
      searchQuery: '', tempSearchQuery: '',
      loadingSerp: false,
      serpResults: [],
      importingId: null,
      places: [], loading: false, error: null,
    };
  },
  mounted() { this.fetchData(); },
  computed: {
    filteredPlaces() {
      let f = this.places;
      if (this.activeFilter !== 'Tất cả') f = f.filter(p => p.loai_dia_diem === this.activeFilter);
      if (this.searchQuery) {
        const q = this.searchQuery.toLowerCase();
        f = f.filter(p => p.ten_dia_diem.toLowerCase().includes(q) || (p.mo_ta && p.mo_ta.toLowerCase().includes(q)));
      }
      return f;
    }
  },
  methods: {
    async searchGoogle() {
      if (!this.tempSearchQuery.trim()) return;
      this.loadingSerp = true;
      this.serpResults = [];
      try {
        const res = await fetch(`http://localhost:8000/api/serp/search?query=${encodeURIComponent(this.tempSearchQuery)}`);
        const json = await res.json();
        if (json.status) {
          this.serpResults = json.data || [];
          if (this.serpResults.length === 0) alert('Không tìm thấy kết quả nào mới trên Google Maps.');
        }
      } catch (e) {
        alert('Lỗi Google Maps: ' + e.message);
      } finally {
        this.loadingSerp = false;
      }
    },

    async importAndShow(googlePlace, index) {
      this.importingId = index;
      const typeMap = {
        'AmThuc': ['Quán ăn', 1],
        'CheckIn': ['Điểm check-in', 2],
        'GiaiTri': ['Khu vui chơi', 3],
        'TamLinh': ['Chùa', 4],
      };
      let compName = this.$options.name || 'CheckIn';
      let loai = typeMap[compName] ? typeMap[compName][0] : 'Điểm check-in';
      let dm = typeMap[compName] ? typeMap[compName][1] : 2;

      const payload = {
        ten_dia_diem: googlePlace.ten_dia_diem,
        dia_chi: googlePlace.dia_chi,
        vi_do: googlePlace.vi_do,
        kinh_do: googlePlace.kinh_do,
        danh_gia_trung_binh: googlePlace.danh_gia_trung_binh,
        image: googlePlace.image,
        mo_ta: googlePlace.mo_ta || 'Được thêm chi tiết từ Google Maps.',
        loai_dia_diem: loai, 
        id_danh_muc: dm, 
      };

      try {
        const res = await fetch(`http://localhost:8000/api/serp/import`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const json = await res.json();

        if (!json.status && json.message === 'Địa điểm này đã tồn tại trong hệ thống.') {
          alert('Địa điểm này đã có trong hệ thống nội bộ, bạn có thể tìm thấy ngay bên dưới!');
          return;
        }

        if (json.status && json.data) {
          this.places.unshift(json.data);
          this.serpResults.splice(index, 1);
          this.viewDetail(json.data);
        }
      } catch (e) {
        alert('Lỗi khi tải địa điểm: ' + e.message);
      } finally {
        this.importingId = null;
      }
    },

    async fetchData() {
      this.loading = true; this.error = null;
      try {
        const res = await fetch(`${BASE}/dia-diems/check-in`);
        if (!res.ok) throw new Error('Lỗi kết nối server (' + res.status + ')');
        const json = await res.json();
        const fallbacks = {
          'Cầu nổi tiếng': 'https://images.unsplash.com/photo-1552751753-0fc84ae5b05e?w=800&h=600&fit=crop',
          'Bãi biển':      'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800&h=600&fit=crop',
          'Ngắm cảnh':     'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=800&h=600&fit=crop',
          'Lịch sử':       'https://images.unsplash.com/photo-1548013146-72479768bbaa?w=800&h=600&fit=crop',
          'Phố cổ':        'https://images.unsplash.com/photo-1559592413-7cec4d0cae2b?w=800&h=600&fit=crop',
          'Tự nhiên':      'https://images.unsplash.com/photo-1504701954957-2010ec3bcec1?w=800&h=600&fit=crop',
        };
        this.places = (json.data || []).map(p => ({
          ...p,
          image: p.image || fallbacks[p.loai_dia_diem] || 'https://images.unsplash.com/photo-1559592481-74488ece15f1?w=800&h=600&fit=crop'
        }));
      } catch (e) { this.error = e.message; this.places = []; }
      finally { this.loading = false; }
    },
    setFilter(f) { this.activeFilter = f; },
    categoryClass(c) {
      const m = { 'Cầu nổi tiếng':'bridge','Bãi biển':'beach','Ngắm cảnh':'scenic','Lịch sử':'history','Phố cổ':'oldtown','Tự nhiên':'nature' };
      return m[c] || 'bridge';
    },
    formatPrice(p) { return Number(p).toLocaleString('vi-VN') + 'đ'; },
    viewDetail(p) { this.selectedPlace = p; this.showModal = true; this.$nextTick(() => { this.initModalMap(p); }); },

    initModalMap(place) {
      if (!place.vi_do || !place.kinh_do) return;
      const L = window.L;
      if (!L) return;
      
      const lat = parseFloat(place.vi_do);
      const lng = parseFloat(place.kinh_do);
      if (isNaN(lat) || isNaN(lng)) return;

      if (this.modalMapInstance) {
        this.modalMapInstance.off();
        this.modalMapInstance.remove();
        this.modalMapInstance = null;
      }

      // Initialize map
      this.modalMapInstance = L.map('detail-map', {
        zoomControl: true,
        scrollWheelZoom: false
      }).setView([lat, lng], 16);

      // Google Maps Tile Layer
      L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
        attribution: '&copy; Google Maps'
      }).addTo(this.modalMapInstance);

      // Add a nice marker
      const marker = L.marker([lat, lng]).addTo(this.modalMapInstance)
        .bindPopup(`<div style="font-weight:700;">${place.ten_dia_diem}</div><div style="font-size:12px;">${place.dia_chi}</div>`)
        .openPopup();
      
      // Fix rendering issues in modals
      setTimeout(() => {
        if (this.modalMapInstance) {
          this.modalMapInstance.invalidateSize();
          this.modalMapInstance.setView([lat, lng], 16);
        }
      }, 400);
      
      // Extra check at 800ms for slow transitions
      setTimeout(() => {
        if (this.modalMapInstance) this.modalMapInstance.invalidateSize();
      }, 800);
    },

    closeModal() { this.showModal = false; this.selectedPlace = null; },
    createItinerary() { alert('Chức năng tạo lịch trình đang phát triển'); },
    performSearch() { this.searchQuery = this.tempSearchQuery; },
    clearSearch() { this.searchQuery = ''; this.tempSearchQuery = ''; },
  },
};
</script>

<style scoped>
.hero-section {
  position: relative;
  background: radial-gradient(circle at 10% 20%, rgba(58,134,255,0.14), transparent 28%),
    radial-gradient(circle at 90% 15%, rgba(0,200,150,0.16), transparent 20%),
    linear-gradient(180deg, #f6fbff 0%, #ffffff 48%, #f8fbff 100%);
}
.create-itinerary-btn {
  position: absolute; top: 20px; right: 20px; z-index: 10;
  box-shadow: 0 10px 30px rgba(34,197,94,0.4);
  background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
  border: none; font-weight: 600; padding: 0.7rem 1.5rem; transition: all 0.3s ease;
}
.create-itinerary-btn:hover { transform: translateY(-3px); box-shadow: 0 15px 40px rgba(34,197,94,0.5); }
.page-title { font-size: 2.8rem; font-weight: 800; color: #1f2937; letter-spacing: -0.5px; animation: fadeInDown 0.8s ease-out; }
.page-subtitle { color: #475569; font-size: 1.05rem; margin-top: 1rem; max-width: 760px; margin-left: auto; margin-right: auto; text-align: center; line-height: 1.6; animation: fadeInDown 0.9s ease-out; }
.search-bar { animation: fadeInUp 1s ease-out; }
.search-bar input { border: 2px solid transparent; border-radius: 15px; padding: 0.8rem 1.2rem; font-size: 0.95rem; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
.search-bar input:focus { border-color: #0ea5e9; box-shadow: 0 6px 25px rgba(14,165,233,0.2); outline: none; }
.search-bar .btn { border-radius: 15px; font-weight: 600; transition: all 0.3s ease; border: none; }
.search-bar .btn-primary { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); box-shadow: 0 4px 15px rgba(59,130,246,0.3); padding: 0.8rem 1.5rem; }
.search-bar .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(59,130,246,0.4); }
.search-bar .btn-outline-secondary { border: 2px solid #cbd5e1; color: #64748b; padding: 0.6rem 1.2rem; }
.filter-bar { margin-top: 1.5rem; display: flex; gap: 12px; flex-wrap: wrap; justify-content: center; animation: fadeInUp 1.1s ease-out; padding: 0 1rem; }
.filter-bar .btn { border-radius: 25px; font-weight: 600; padding: 0.65rem 1.4rem; transition: all 0.3s ease; border: 2px solid transparent; font-size: 0.9rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
.filter-bar .btn-outline-primary { border-color: #e0e7ff; color: #4f46e5; background: #f0f4ff; }
.filter-bar .btn-outline-primary:hover { background: #e0e7ff; border-color: #c7d2fe; color: #3730a3; transform: translateY(-2px); }
.filter-bar .btn-primary { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); border-color: transparent; color: #fff; box-shadow: 0 6px 20px rgba(6,182,212,0.3); font-weight: 700; }
.filter-bar .btn-primary:hover { transform: translateY(-3px) scale(1.05); box-shadow: 0 10px 30px rgba(6,182,212,0.4); }
.places-section { padding: 2rem 0; }
.place-card { border-radius: 18px; transition: all 0.4s cubic-bezier(0.175,0.885,0.32,1.275); border: none; background: #fff; overflow: hidden; box-shadow: 0 8px 24px rgba(15,23,42,0.1); animation: fadeInUp 0.6s ease-out; }
.place-card:hover { transform: translateY(-12px); box-shadow: 0 25px 50px rgba(15,23,42,0.2); }
.place-image { height: 220px; background-size: cover; background-position: center; border-top-left-radius: 18px; border-top-right-radius: 18px; position: relative; overflow: hidden; transition: transform 0.4s ease; }
.place-card:hover .place-image { transform: scale(1.1); }
.place-image::after { content: ''; position: absolute; inset: 0; background: linear-gradient(180deg, transparent 50%, rgba(0,0,0,0.3) 100%); }
.card-body { padding: 1.5rem !important; }
.card-title { font-weight: 800; font-size: 1.2rem; color: #0f172a; margin-bottom: 0.5rem; }
.card-body small { font-weight: 600; color: #64748b; display: flex; align-items: center; gap: 0.5rem; }
.badge { font-weight: 700; padding: 0.5rem 0.9rem; border-radius: 8px; font-size: 0.85rem; }
.badge.bg-success { background: linear-gradient(135deg, #34d399 0%, #10b981 100%) !important; }
.badge.bg-danger { background: linear-gradient(135deg, #f87171 0%, #ef4444 100%) !important; }
.place-badge { position: absolute; top: 15px; left: 15px; color: white; padding: 7px 16px; border-radius: 25px; font-size: 12px; font-weight: 700; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3); box-shadow: 0 4px 12px rgba(0,0,0,0.15); text-transform: uppercase; letter-spacing: 0.5px; z-index: 5; }
.place-badge.bridge  { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }
.place-badge.beach   { background: linear-gradient(135deg, #34d399 0%, #059669 100%); }
.place-badge.scenic  { background: linear-gradient(135deg, #f97316 0%, #c2410c 100%); }
.place-badge.history { background: linear-gradient(135deg, #a855f7 0%, #7c3aed 100%); }
.place-badge.oldtown { background: linear-gradient(135deg, #ec4899 0%, #be185d 100%); }
.place-badge.nature  { background: linear-gradient(135deg, #10b981 0%, #047857 100%); }
.price-text { color: #059669; font-weight: 800; font-size: 1.2rem; background: linear-gradient(135deg, #34d399 0%, #059669 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
.modal-overlay { position: fixed; inset: 0; background: rgba(15,23,42,0.7); backdrop-filter: blur(5px); display: flex; justify-content: center; align-items: center; z-index: 9999; animation: fadeIn 0.3s ease; }
.modal-content { width: min(96%, 760px); background: #fff; border-radius: 24px; box-shadow: 0 30px 80px rgba(15,23,42,0.35); position: relative; padding: 2rem; max-height: 85vh; overflow-y: auto; animation: slideUp 0.4s ease; }
.btn-close { position: absolute; top: 1rem; right: 1rem; width: 40px; height: 40px; border: none; border-radius: 50%; background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%); color: #1f2937; font-size: 24px; cursor: pointer; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; }
.btn-close:hover { background: linear-gradient(135deg, #f1f5f9 0%, #e0e7ff 100%); transform: rotate(90deg) scale(1.1); }
.modal-header h4 { margin: 0; font-weight: 800; color: #0f172a; font-size: 1.8rem; margin-bottom: 0.5rem; }
.modal-header small { color: #64748b; font-weight: 600; font-size: 0.95rem; }
.modal-image { width: 100%; height: 280px; background-size: cover; background-position: center; border-radius: 16px; box-shadow: 0 8px 24px rgba(0,0,0,0.15); margin-bottom: 1.5rem; position: relative; overflow: hidden; }
.modal-body p { margin-top: 1rem; color: #475569; font-size: 1rem; line-height: 1.6; }
.modal-body ul { margin-top: 1.5rem; padding-left: 0; color: #334155; list-style: none; }
.modal-body li { margin-bottom: 1rem; padding: 0.75rem 1rem; background: linear-gradient(135deg, #f0f9ff 0%, #f0fdf4 100%); border-left: 4px solid #3b82f6; border-radius: 8px; font-weight: 500; }
.modal-actions { display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem; padding-top: 1.5rem; border-top: 2px solid #e5e7eb; }
.modal-actions .btn { border-radius: 12px; font-weight: 600; padding: 0.8rem 1.8rem; transition: all 0.3s ease; }
.modal-actions .btn-secondary { background: #f1f5f9; color: #475569; border: none; }
.modal-actions .btn-primary { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border: none; color: white; }
.text-truncate-2 { display: -webkit-box; -webkit-line-clamp: 2; line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.glass-panel { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.5); border-radius: 24px; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05); }
.results-title { font-weight: 800; color: #1e293b; font-size: 1.4rem; }
.serp-item { background: #fff; border: 1px solid #f1f5f9; transition: all 0.3s ease; }
.serp-item:hover { transform: scale(1.02); box-shadow: 0 10px 25px rgba(0,0,0,0.05); border-color: #3b82f6; }
.serp-item-img { width: 90px; height: 90px; border-radius: 12px; background-size: cover; background-position: center; border: 1px solid #eee; }
.serp-item-content h6 { font-weight: 700; color: #1e293b; }
@keyframes fadeInDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes slideUp { from { opacity: 0; transform: translateY(30px) scale(0.95); } to { opacity: 1; transform: translateY(0) scale(1); } }
</style>
