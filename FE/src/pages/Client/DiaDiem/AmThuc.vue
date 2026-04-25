<template>
  <div class="giai-tri-page">

    <!-- HERO -->
    <div v-show="!showModal">
      <section class="hero-section position-relative">
        <div class="container text-center">
          <div class="hero-content">
            <h1 class="page-title mb-2">
              Khám Phá Ẩm Thực <span class="text-gradient">Đặc Sắc Đà Thành</span>
            </h1>
            <p class="page-subtitle">
              Hành trình vị giác qua những món ngon nức tiếng, từ quán vỉa hè dân dã đến nhà hàng cao cấp. AI sẽ gợi ý
              thực đơn phù hợp nhất với khẩu vị của bạn.
            </p>

            <div class="search-container">
              <div class="search-glass">
                <input v-model="searchQuery" type="text" class="form-control"
                  placeholder="Tìm địa điểm, món ăn, trải nghiệm...">
              </div>

              <!-- FILTER -->
              <div class="filter-bar d-flex justify-content-center flex-wrap gap-2">
                <button v-for="filter in dynamicFilters" :key="filter"
                  :class="['btn btn-sm', activeFilter === filter ? 'btn-primary' : 'btn-outline-primary']"
                  @click="setFilter(filter)">
                  {{ filter }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>




      <!-- LIST -->
      <section class="places-section py-4">
        <div class="container">

          <!-- Loading -->
          <div v-if="loading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status" style="width:3rem;height:3rem;"></div>
            <p class="mt-3 text-muted">Đang tải dữ liệu...</p>
          </div>

          <!-- Error -->
          <div v-else-if="error" class="alert alert-danger text-center" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ error }}
            <button class="btn btn-sm btn-outline-danger ms-3" @click="fetchAmThuc">Thử lại</button>
          </div>

          <!-- Data -->
          <div v-else class="row g-4">
            <div v-for="place in filteredPlaces" :key="place.id" class="col-12 col-sm-6 col-xl-4">
              <div class="place-card h-100" @click="viewDetail(place)">
                <div class="place-image" :style="{ backgroundImage: `url(${place.image})` }">
                  <div class="place-image-overlay"></div>
                  <span :class="['place-badge', categoryClass(place.loai_dia_diem)]">{{ place.loai_dia_diem }}</span>
                  <button class="favorite-btn" @click.stop="toggleFavorite(place)"
                    :class="{ active: place.is_favorite }">
                    <i :class="place.is_favorite ? 'bi bi-heart-fill' : 'bi bi-heart'"></i>
                  </button>
                  <div class="price-chip">{{ place.gia_ve == 0 ? 'Miễn phí' : formatPrice(place.gia_ve) }}</div>
                </div>
                <div class="place-body">
                  <h5 class="place-title">{{ place.ten_dia_diem }}</h5>
                  <p class="place-addr"><i class="bi bi-geo-alt-fill me-1"></i>{{ place.dia_chi }}</p>
                  <div class="place-meta">
                    <span class="meta-item"><i class="bi bi-clock me-1"></i>{{ place.gio_mo_cua }} – {{
                      place.gio_dong_cua }}</span>
                    <span class="meta-rating"><i class="bi bi-star-fill me-1"></i>{{ place.danh_gia_trung_binh }}</span>
                  </div>
                  <p class="place-desc">{{ place.mo_ta }}</p>
                  <div class="place-footer">
                    <span class="view-btn">Xem chi tiết <i class="bi bi-arrow-right ms-1"></i></span>
                  </div>
                </div>
              </div>
            </div>
            <div v-if="filteredPlaces.length === 0 && !loading" class="text-center py-5 text-muted w-100">
              <i class="bi bi-search fs-1 d-block mb-3 opacity-25"></i>Không tìm thấy địa điểm phù hợp
            </div>
          </div>
        </div>
      </section>
    </div>
    <!-- VIEW CHI TIẾT (Thay thế Modal) -->
    <div v-if="showModal" class="detail-page bg-light py-4 fade-in-content">
      <div class="container">
        <!-- Breadcrumb / Header -->
        <nav aria-label="breadcrumb" class="mb-4 d-flex align-items-center">
          <button
            class="btn btn-sm btn-outline-secondary me-3 rounded-circle shadow-sm d-flex align-items-center justify-content-center"
            @click="closeModal" style="width: 36px; height: 36px; padding: 0;">
            <i class="bi bi-arrow-left"></i>
          </button>
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="#" @click.prevent="closeModal"
                class="text-decoration-none text-muted">Địa điểm</a></li>
            <li class="breadcrumb-item"><a href="#" @click.prevent="closeModal"
                class="text-decoration-none text-muted">{{ activeFilter }}</a></li>
            <li class="breadcrumb-item active fw-bold text-dark" aria-current="page">{{ selectedPlace.ten_dia_diem }}
            </li>
          </ol>
        </nav>

        <div class="row g-4">
          <!-- LEFT COLUMN -->
          <div class="col-lg-8">
            <!-- Gallery Section -->
            <div class="gallery-container bg-white p-3 rounded-4 shadow-sm mb-4">
              <div class="main-image mb-2 rounded-3"
                :style="{ backgroundImage: `url(${currentGalleryImage})`, height: '450px', backgroundSize: 'cover', backgroundPosition: 'center', position: 'relative', overflow: 'hidden' }">
                <button v-if="selectedPlace.gallery && selectedPlace.gallery.length > 1"
                  class="btn btn-light position-absolute top-50 start-0 translate-middle-y ms-3 shadow-sm d-flex align-items-center justify-content-center"
                  @click="prevImage" style="border-radius: 50%; width: 44px; height: 44px; z-index: 10;"><i
                    class="bi bi-chevron-left fs-5"></i></button>
                <button v-if="selectedPlace.gallery && selectedPlace.gallery.length > 1"
                  class="btn btn-light position-absolute top-50 end-0 translate-middle-y me-3 shadow-sm d-flex align-items-center justify-content-center"
                  @click="nextImage" style="border-radius: 50%; width: 44px; height: 44px; z-index: 10;"><i
                    class="bi bi-chevron-right fs-5"></i></button>
              </div>
              <div class="thumbnails d-flex gap-2 py-2 flex-wrap"
                v-if="selectedPlace.gallery && selectedPlace.gallery.length > 1">
                <div v-for="(img, idx) in selectedPlace.gallery" :key="idx"
                  :class="['thumbnail-item rounded-3', { 'border border-3 border-primary': currentImageIdx === idx }]"
                  @click="currentImageIdx = idx"
                  :style="{ backgroundImage: `url(${img.duong_dan_anh || img.image})`, flex: '1 1 0', minWidth: '100px', maxWidth: '140px', height: '80px', backgroundSize: 'cover', backgroundPosition: 'center', cursor: 'pointer', opacity: currentImageIdx === idx ? '1' : '0.6', transition: 'all 0.3s' }">
                </div>
              </div>
            </div>

            <!-- Intro Section -->
            <div class="intro-container bg-white p-4 rounded-4 shadow-sm mb-4">
              <h4 class="mb-3 fw-bold">Giới thiệu</h4>
              <p class="text-secondary" style="line-height: 1.8; font-size: 1.05rem;">{{ selectedPlace.mo_ta }}</p>
            </div>

            <!-- Reviews Section -->
            <div class="reviews-container bg-white p-4 rounded-4 shadow-sm">
              <h4 class="mb-4 fw-bold">Đánh giá ({{ detailReviews.length }})</h4>

              <div class="review-form-card mb-5 p-4 bg-light border-0 rounded-4" v-if="isLoggedIn">
                <h6 class="fw-bold mb-3">Gửi đánh giá của bạn</h6>
                <div class="d-flex align-items-center mb-3">
                  <div class="star-rating-input me-3" style="font-size: 1.5rem; cursor: pointer;">
                    <i v-for="s in 5" :key="s" class="bi"
                      :class="s <= newReview.so_sao ? 'bi-star-fill text-warning' : 'bi-star text-secondary'"
                      @click="newReview.so_sao = s"></i>
                  </div>
                  <span class="text-muted">{{ newReview.so_sao }} sao</span>
                </div>
                <textarea v-model="newReview.noi_dung" class="form-control mb-3 border-0 shadow-sm rounded-3 p-3"
                  rows="3" placeholder="Chia sẻ cảm nhận của bạn về địa điểm này..."></textarea>
                <div class="text-end">
                  <button class="btn btn-primary px-4 py-2 rounded-pill fw-bold shadow-sm" @click="submitReview"
                    :disabled="submittingReview">
                    <span v-if="submittingReview" class="spinner-border spinner-border-sm me-2"></span>
                    Gửi nhận xét
                  </button>
                </div>
              </div>
              <div v-else class="alert alert-info py-3 border-0 shadow-sm rounded-3 mb-5">
                <i class="bi bi-info-circle-fill me-2"></i> Vui lòng <router-link to="/client/dang-nhap"
                  class="fw-bold text-decoration-none">Đăng nhập</router-link> để để lại đánh giá.
              </div>

              <div v-if="loadingReviews" class="text-center py-5">
                <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;"></div>
              </div>
              <div v-else-if="detailReviews.length === 0" class="text-center py-5 text-muted">
                <i class="bi bi-chat-square-text" style="font-size: 3rem; opacity: 0.5;"></i>
                <p class="mt-3 fs-6">Chưa có đánh giá nào cho địa điểm này.</p>
              </div>
              <div v-else class="review-list">
                <div v-for="rv in detailReviews" :key="rv.id" class="review-item border-bottom py-4 last-border-0">
                  <div class="d-flex align-items-center mb-3">
                    <div class="avatar shadow-sm me-3"
                      style="width: 50px; height: 50px; border-radius: 50%; overflow: hidden; background: #e9ecef; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; font-weight: bold; color: #6c757d;">
                      <img v-if="rv.nguoi_dung?.anh_dai_dien" :src="getFullAvatar(rv.nguoi_dung.anh_dai_dien)"
                        alt="avatar" style="width: 100%; height: 100%; object-fit: cover;">
                      <span v-else>{{ (rv.nguoi_dung?.ten || '?').charAt(0).toUpperCase() }}</span>
                    </div>
                    <div>
                      <h6 class="mb-0 fw-bold">{{ rv.nguoi_dung?.ten || rv.ten_nguoi_danh_gia || 'Người dùng' }}</h6>
                      <small class="text-muted">{{ formatDate(rv.created_at) }}</small>
                    </div>
                    <div class="ms-auto text-warning" style="font-size: 1.1rem;">
                      <i v-for="s in rv.so_sao" :key="s" class="bi bi-star-fill me-1"></i>
                    </div>
                  </div>
                  <p class="mb-0 text-secondary" style="padding-left: 66px; line-height: 1.6;">{{ rv.noi_dung }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- RIGHT COLUMN -->
          <div class="col-lg-4">
            <div class="info-card bg-white p-4 rounded-4 shadow-sm position-sticky" style="top: 20px;">
              <h3 class="fw-bold mb-2">{{ selectedPlace.ten_dia_diem }}</h3>
              <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                <div class="stars text-warning me-2">
                  <i class="bi bi-star-fill"></i>
                  <i class="bi bi-star-fill"></i>
                  <i class="bi bi-star-fill"></i>
                  <i class="bi bi-star-fill"></i>
                  <i class="bi bi-star-fill"></i>
                </div>
                <span class="fw-bold me-1">{{ selectedPlace.danh_gia_trung_binh }}</span>
                <span class="text-muted">({{ detailReviews.length }} đánh giá)</span>
              </div>

              <!-- Map Block -->
              <div v-if="selectedPlace.vi_do && selectedPlace.kinh_do"
                class="map-container mb-4 rounded-4 overflow-hidden shadow-sm border"
                style="height: 220px; position: relative;">
                <div id="detail-map" style="width: 100%; height: 100%; z-index: 1;"></div>
              </div>
              <div v-else class="alert alert-warning py-2 mb-4 d-flex align-items-center rounded-3">
                <i class="bi bi-geo-alt-fill me-2"></i> Chưa có tọa độ bản đồ chi tiết.
              </div>

              <!-- Info List -->
              <ul class="list-unstyled mb-4">
                <li class="d-flex align-items-start mb-3">
                  <i class="bi bi-geo-alt text-danger me-3 fs-5 mt-1"></i>
                  <div>
                    <span class="d-block text-muted small">Địa chỉ</span>
                    <span class="fw-medium">{{ selectedPlace.dia_chi }}</span>
                  </div>
                </li>
                <li class="d-flex align-items-start mb-3">
                  <i class="bi bi-clock text-success me-3 fs-5 mt-1"></i>
                  <div>
                    <span class="d-block text-muted small">Giờ hoạt động</span>
                    <span class="fw-medium">{{ selectedPlace.gio_mo_cua }} - {{ selectedPlace.gio_dong_cua }}</span>
                  </div>
                </li>
                <li class="d-flex align-items-start mb-3">
                  <i class="bi bi-telephone text-primary me-3 fs-5 mt-1"></i>
                  <div>
                    <span class="d-block text-muted small">Điện thoại</span>
                    <span class="fw-medium">Liên hệ ban quản lý</span>
                  </div>
                </li>
                <li class="d-flex align-items-start mb-3">
                  <i class="bi bi-tag text-info me-3 fs-5 mt-1"></i>
                  <div>
                    <span class="d-block text-muted small">Loại hình</span>
                    <span class="fw-medium">{{ selectedPlace.loai_dia_diem }}</span>
                  </div>
                </li>
                <li class="d-flex align-items-start py-3 mt-2 border-top">
                  <i class="bi bi-cash-coin text-success me-3 fs-4 mt-1"></i>
                  <div>
                    <span class="d-block text-muted small">Giá vé tham khảo</span>
                    <span class="fw-bold text-success fs-5">{{ selectedPlace.gia_ve == 0 ? 'Miễn phí' :
                      formatPrice(selectedPlace.gia_ve) }}</span>
                  </div>
                </li>
              </ul>

              <button
                class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm d-flex justify-content-center align-items-center mt-4"
                style="background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%); border: none;"
                @click="alert('Chức năng thêm vào lịch trình đang phát triển')">
                <i class="bi bi-calendar-plus me-2 fs-5"></i> Thêm vào lịch trình
              </button>
              <button
                class="btn w-100 py-3 rounded-pill fw-bold shadow-sm d-flex justify-content-center align-items-center mt-3 text-white"
                style="background: linear-gradient(135deg, #10b981 0%, #0ea5e9 100%); border: none;"
                @click="openShareModal">
                <i class="bi bi-send-fill me-2 fs-5"></i> Gửi địa điểm vào nhóm
              </button>

            </div>

          </div>
        </div>
      </div>
    </div>


    <!-- Modal chọn nhóm chia sẻ -->
    <div v-if="showShareModal" class="share-modal-overlay d-flex align-items-center justify-content-center" @click.self="showShareModal = false" style="position: fixed; top:0; left:0; right:0; bottom:0; background: rgba(0,0,0,0.5); z-index: 9999;">
      <div class="share-modal-box bg-white p-5 rounded-4 shadow-lg w-100 animate-in position-relative" style="max-width: 440px;">
        <button class="btn-close position-absolute top-0 end-0 m-4" @click="showShareModal = false"></button>
        <div class="text-center mb-4 mt-2">
          <div class="share-icon-wrap mb-3 mx-auto shadow-sm">
             <i class="bi bi-send-check-fill fs-2 text-primary"></i>
          </div>
          <h4 class="fw-bold mb-1 text-dark">Chia sẻ địa điểm</h4>
          <p class="text-muted mb-0" style="font-size: 0.95rem;">Chọn nhóm để gửi địa điểm này nhé!</p>
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
const BASE = 'http://localhost:8000/api';

export default {
  name: 'AmThuc',

  data() {
    return {
      activeFilter: 'Tất cả',
      showModal: false,
      selectedPlace: null,
      modalMapInstance: null,
      searchQuery: '',
      tempSearchQuery: '',


      // Data từ backend
      places: [],
      loading: false,
      error: null,
      currentImageIdx: 0,
      token: localStorage.getItem('client_token'),
      isLoggedIn: !!localStorage.getItem('client_token'),
      autoplayInterval: null,

      // Reviews
      activeModalTab: 'info',
      loadingReviews: false,
      detailReviews: [],
      newReview: { so_sao: 5, noi_dung: '' },
      submittingReview: false,

      showShareModal: false,
      myJoinedGroups: [],
      selectedGroupToShare: null,
      sendingShare: false,

    };
  },

  mounted() {
    this.fetchAmThuc();
  },

  computed: {
    dynamicFilters() {
      const types = Array.from(new Set(this.places.map(p => p.loai_dia_diem))).filter(Boolean);
      return ['Tất cả', ...types];
    },
    filteredPlaces() {
      let filtered = this.places;
      if (this.activeFilter !== 'Tất cả') {
        filtered = filtered.filter(p => p.loai_dia_diem === this.activeFilter);
      }
      if (this.searchQuery) {
        const q = this.searchQuery.toLowerCase();
        filtered = filtered.filter(p =>
          (p.ten_dia_diem && p.ten_dia_diem.toLowerCase().includes(q)) ||
          (p.dia_chi && p.dia_chi.toLowerCase().includes(q)) ||
          (p.loai_dia_diem && p.loai_dia_diem.toLowerCase().includes(q)) ||
          (p.mo_ta && p.mo_ta.toLowerCase().includes(q))
        );
      }
      return filtered;
    },
    currentGalleryImage() {
      if (!this.selectedPlace || !this.selectedPlace.gallery || this.selectedPlace.gallery.length === 0) {
        return this.selectedPlace ? this.selectedPlace.image : '';
      }
      return this.selectedPlace.gallery[this.currentImageIdx].duong_dan_anh || this.selectedPlace.gallery[this.currentImageIdx].image;
    }
  },

  methods: {

    openShareModal() {
      if (!this.isLoggedIn) {
        this.$toast.warning('Vui lòng đăng nhập để chia sẻ!');
        return;
      }
      this.showShareModal = true;
      if (this.myJoinedGroups.length === 0) {
        this.fetchMyGroups();
      }
    },
    async fetchMyGroups() {
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
    async shareToGroup() {
      if (!this.selectedGroupToShare) return;
      this.sendingShare = true;
      try {
        const payload = {
            id_nhom_du_lich: this.selectedGroupToShare.id,
            id_chi_tiet_nhom: this.selectedGroupToShare.id_chi_tiet_nhom,
            message: JSON.stringify({ type: 'place', id: this.selectedPlace.id, title: this.selectedPlace.ten_dia_diem, image: this.selectedPlace.image })
        };

        const r = await fetch(`${BASE}/nhom-chats`, {
          method: 'POST', 
          headers: { 
            'Content-Type': 'application/json', 
            Authorization: `Bearer ${this.token}` 
          },
          body: JSON.stringify(payload)
        });
        const res = await r.json();
        
        if (res.status) {
          this.$toast.success('Gửi địa điểm thành công!');
          this.showShareModal = false;
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


    async fetchAmThuc() {
      this.loading = true;
      this.error = null;
      try {
        const headers = {};
        if (this.token) {
          headers['Authorization'] = `Bearer ${this.token}`;
        }
        const res = await fetch(`${BASE}/dia-diems/am-thuc`, { headers });
        if (!res.ok) throw new Error('Lỗi kết nối server (' + res.status + ')');
        const json = await res.json();

        const fallbacksByType = {
          'Quán ăn': 'https://images.unsplash.com/photo-1547592180-85f173990554?w=800&h=600&fit=crop',
          'Street food': 'https://images.unsplash.com/photo-1565299585323-38d6b0865b47?w=800&h=600&fit=crop',
          'Hải sản': 'https://images.unsplash.com/photo-1559737022-8a20f87e61a6?w=800&h=600&fit=crop',
          'Quán nhậu': 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=800&h=600&fit=crop',
          'Ăn vặt': 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=800&h=600&fit=crop',
        };
        this.places = (json.data || []).map(p => ({
          ...p,
          image: p.image || fallbacksByType[p.loai_dia_diem] || 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=800&h=600&fit=crop'
        }));
      } catch (e) {
        this.error = e.message || 'Không thể tải dữ liệu. Vui lòng thử lại.';
        this.places = [];
      } finally {
        this.loading = false;
      }
    },

    setFilter(filter) {
      this.activeFilter = filter;
    },

    categoryClass(category) {
      if (!category) return 'all';
      const mapping = {
        'Quán ăn': 'restaurant',
        'Street food': 'streetfood',
        'Hải sản': 'seafood',
        'Quán nhậu': 'bar',
        'Ăn vặt': 'snack',
      };
      return mapping[category] || 'restaurant';
    },

    formatPrice(price) {
      return Number(price).toLocaleString('vi-VN') + 'đ';
    },

    viewDetail(place) {
      this.selectedPlace = place;
      this.currentImageIdx = 0;
      this.showModal = true;
      this.activeModalTab = 'info';
      this.detailReviews = [];
      this.$nextTick(() => {
        this.initModalMap(place);
      });
      this.startAutoplay();
      this.fetchReviews(place.id);
    },

    startAutoplay() {
      this.stopAutoplay();
      if (this.selectedPlace?.gallery?.length > 1) {
        this.autoplayInterval = setInterval(() => {
          this.nextImage();
        }, 4000);
      }
    },
    stopAutoplay() {
      if (this.autoplayInterval) {
        clearInterval(this.autoplayInterval);
        this.autoplayInterval = null;
      }
    },

    nextImage() {
      if (this.selectedPlace.gallery) {
        this.currentImageIdx = (this.currentImageIdx + 1) % this.selectedPlace.gallery.length;
      }
    },
    prevImage() {
      if (this.selectedPlace.gallery) {
        this.currentImageIdx = (this.currentImageIdx - 1 + this.selectedPlace.gallery.length) % this.selectedPlace.gallery.length;
      }
    },

    async toggleFavorite(place) {
      if (!this.token) {
        this.$toast.warning('Vui lòng đăng nhập để thêm vào yêu thích!');
        return;
      }
      try {
        const res = await fetch(`${BASE}/client/yeu-thich/toggle`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${this.token}`
          },
          body: JSON.stringify({ id_dia_diem: place.id })
        });
        const json = await res.json();
        if (json.status) {
          place.is_favorite = json.is_favorite;
          this.$toast.success(json.is_favorite ? `Đã thêm vào yêu thích! ❤️` : `Đã xoá khỏi yêu thích`);
        } else {
          this.$toast.error(json.message || 'Lỗi khi thực hiện thao tác.');
        }
      } catch (e) {
        console.error(e);
      }
    },

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

    closeModal() {
      this.stopAutoplay();
      this.showModal = false;
      this.selectedPlace = null;
    },

    createItinerary() {
      this.$router.push('/client/tao-lich-trinh');
    },

    addToItinerary() { this.$toast.info('Vui lòng sử dụng trang Tạo lịch trình để thêm địa điểm này!'); this.closeModal(); },

    performSearch() {
      this.searchQuery = this.tempSearchQuery;
    },

    clearSearch() {
      this.searchQuery = '';
      this.tempSearchQuery = '';
    },

    async fetchReviews(contentId) {
      this.loadingReviews = true;
      try {
        const res = await fetch(`${BASE}/dia-diems/danh-gia/place/${contentId}`);
        const json = await res.json();
        if (json.status === 'success') {
          this.detailReviews = json.data;
        }
      } catch (e) {
        console.error('Error fetching reviews:', e);
      } finally {
        this.loadingReviews = false;
      }
    },

    async submitReview() {
      if (!this.newReview.noi_dung.trim()) {
        this.$toast.warning('Vui lòng nhập nội dung đánh giá!');
        return;
      }
      this.submittingReview = true;
      try {
        const res = await fetch(`${BASE}/danh-gias`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': `Bearer ${this.token}`
          },
          body: JSON.stringify({
            id_dia_diem: this.selectedPlace.id,
            so_sao: this.newReview.so_sao,
            noi_dung: this.newReview.noi_dung
          })
        });

        const json = await res.json();
        if (res.ok && json.status === 'success') {
          this.newReview.noi_dung = '';
          this.newReview.so_sao = 5;
          await this.fetchReviews(this.selectedPlace.id);
          this.$toast.success('Cảm ơn bạn đã gửi đánh giá! Đánh giá của bạn đang chờ xếp hàng kiểm duyệt. 🌟');
        } else {
          const msg = json.message || (json.errors ? Object.values(json.errors).flat().join('\n') : 'Không thể gửi đánh giá.');
          this.$toast.error('Lỗi: ' + msg);
        }
      } catch (e) {
        console.error('Submit review error:', e);
        this.$toast.error('Không thể kết nối đến máy chủ. Vui lòng kiểm tra lại kết nối mạng.');
      } finally {
        this.submittingReview = false;
      }
    },

    getFullAvatar(path) {
      if (!path) return '';
      if (path.startsWith('http')) return path;
      return `http://localhost:8000${path.startsWith('/') ? '' : '/'}${path}`;
    },

    formatDate(dateStr) {
      if (!dateStr) return '';
      const d = new Date(dateStr);
      return d.toLocaleDateString('vi-VN', {
        year: 'numeric', month: 'long', day: 'numeric',
        hour: '2-digit', minute: '2-digit'
      });
    },
  },
};
</script>

<style scoped>
.check-in-page {
  background: linear-gradient(135deg, #f0f9ff 0%, #f1f5f9 100%);
  min-height: 100vh;
}



.hero-section {
  position: relative;
  background: radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.1) 0px, transparent 50%),
    radial-gradient(at 100% 0%, rgba(16, 185, 129, 0.05) 0px, transparent 50%),
    radial-gradient(at 100% 100%, rgba(244, 63, 94, 0.05) 0px, transparent 50%),
    radial-gradient(at 0% 100%, rgba(59, 130, 246, 0.1) 0px, transparent 50%),
    #ffffff;
  padding: 6rem 0 5rem;
  overflow: hidden;
  border-bottom: 1px solid rgba(0, 0, 0, 0.02);
}

.hero-section::before {
  content: "";
  position: absolute;
  top: -10%;
  right: -5%;
  width: 400px;
  height: 400px;
  background: radial-gradient(circle, rgba(99, 102, 241, 0.08) 0%, transparent 70%);
  border-radius: 50%;
  filter: blur(40px);
}

.page-title {
  font-size: 3.5rem;
  font-weight: 800;
  color: #0f172a;
  letter-spacing: -2px;
  line-height: 1.1;
}

.text-gradient {
  background: linear-gradient(135deg, #6366f1, #2563eb);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.page-subtitle {
  color: #64748b;
  font-size: 1.2rem;
  max-width: 750px;
  margin: 1.5rem auto 3rem;
  line-height: 1.8;
}

.search-container {
  max-width: 800px;
  margin: 0 auto;
  position: relative;
  z-index: 10;
}

.search-glass {
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.5);
  border-radius: 24px;
  padding: 10px;
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.06);
  display: flex;
  transition: all 0.4s;
}

.search-glass:focus-within {
  box-shadow: 0 25px 60px rgba(99, 102, 241, 0.12);
  transform: translateY(-2px);
  border-color: rgba(99, 102, 241, 0.3);
}

.search-glass input {
  border: none !important;
  background: transparent !important;
  padding: 1rem 1.5rem !important;
  font-size: 1.1rem !important;
  color: #1e293b !important;
}

.search-glass input::placeholder {
  color: #94a3b8;
}

.btn-wow {
  background: linear-gradient(135deg, #6366f1, #4f46e5);
  color: white !important;
  border: none !important;
  border-radius: 18px !important;
  padding: 0 2.5rem !important;
  font-weight: 600 !important;
  letter-spacing: 0.5px;
  box-shadow: 0 10px 20px rgba(99, 102, 241, 0.2);
  transition: all 0.3s !important;
}

.btn-wow:hover {
  transform: scale(1.02);
  box-shadow: 0 15px 30px rgba(99, 102, 241, 0.3);
}

.filter-bar {
  margin-top: 3.5rem;
  gap: 12px !important;
}

.filter-bar .btn {
  border-radius: 30px !important;
  padding: 0.6rem 1.5rem !important;
  font-weight: 600 !important;
  font-size: 0.85rem !important;
  border: 1px solid #e2e8f0 !important;
  background: white;
  color: #475569 !important;
  transition: all 0.3s !important;
}

.filter-bar .btn:hover {
  border-color: #6366f1 !important;
  color: #6366f1 !important;
  background: #f5f3ff;
}

.filter-bar .btn-primary {
  background: #0f172a !important;
  color: white !important;
  border: none !important;
  box-shadow: 0 8px 15px rgba(15, 23, 42, 0.2);
}

.places-section {
  padding: 2rem 0;
}

.filter-bar {
  margin-top: 1.5rem;
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
  justify-content: center;
  animation: fadeInUp 1.1s ease-out;
  padding: 0 1rem;
}

.filter-bar .btn {
  border-radius: 25px;
  font-weight: 600;
  padding: 0.65rem 1.4rem;
  transition: all 0.3s ease;
  border: 2px solid transparent;
  font-size: 0.9rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.filter-bar .btn-outline-primary {
  border-color: #e0e7ff;
  color: #4f46e5;
  background: #f0f4ff;
}

.filter-bar .btn-outline-primary:hover {
  background: #e0e7ff;
  border-color: #c7d2fe;
  color: #3730a3;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
}

.filter-bar .btn-primary {
  background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
  border-color: transparent;
  color: #ffffff;
  box-shadow: 0 6px 20px rgba(6, 182, 212, 0.3);
  font-weight: 700;
}

.filter-bar .btn-primary:hover {
  transform: translateY(-3px) scale(1.05);
  box-shadow: 0 10px 30px rgba(6, 182, 212, 0.4);
  background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%);
}


/* ===== PLACE CARDS ===== */
.place-card {
  border-radius: 20px;
  overflow: hidden;
  background: #fff;
  box-shadow: 0 4px 20px rgba(15, 23, 42, 0.08);
  cursor: pointer;
  transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.35s ease;
  border: 1px solid rgba(15, 23, 42, 0.06);
}

.place-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 24px 50px rgba(15, 23, 42, 0.16);
}

.place-image {
  height: 220px;
  background-size: cover;
  background-position: center;
  position: relative;
  overflow: hidden;
}

.place-image-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, transparent 45%, rgba(10, 20, 40, 0.55) 100%);
}

.place-card:hover .place-image {
  transform: scale(1.06);
  transition: transform 0.55s ease;
}

.place-badge {
  position: absolute;
  top: 14px;
  left: 14px;
  color: #fff;
  padding: 5px 14px;
  border-radius: 30px;
  font-size: 11px;
  font-weight: 700;
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.25);
  z-index: 5;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.place-badge.bridge {
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
}

.place-badge.beach {
  background: linear-gradient(135deg, #34d399, #059669);
}

.place-badge.scenic {
  background: linear-gradient(135deg, #f97316, #c2410c);
}

.place-badge.history {
  background: linear-gradient(135deg, #a855f7, #7c3aed);
}

.place-badge.oldtown {
  background: linear-gradient(135deg, #ec4899, #be185d);
}

.place-badge.nature {
  background: linear-gradient(135deg, #10b981, #047857);
}

.place-badge.food {
  background: linear-gradient(135deg, #f59e0b, #d97706);
}

.place-badge.temple {
  background: linear-gradient(135deg, #6366f1, #4338ca);
}

.price-chip {
  position: absolute;
  bottom: 14px;
  right: 14px;
  background: rgba(255, 255, 255, 0.92);
  color: #059669;
  font-weight: 800;
  font-size: 0.82rem;
  padding: 4px 12px;
  border-radius: 20px;
  z-index: 5;
  backdrop-filter: blur(6px);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
}

.favorite-btn {
  position: absolute;
  top: 14px;
  right: 14px;
  width: 38px;
  height: 38px;
  background: rgba(255, 255, 255, 0.9);
  color: #94a3b8;
  border: none;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  cursor: pointer;
  transition: all 0.3s ease;
  z-index: 10;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.favorite-btn:hover,
.favorite-btn.active {
  color: #ef4444;
  background: #fff;
  transform: scale(1.12);
}

.place-body {
  padding: 1.25rem 1.4rem 1rem;
}

.place-title {
  font-size: 1.05rem;
  font-weight: 800;
  color: #0f172a;
  margin-bottom: 0.35rem;
  line-height: 1.3;
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.place-addr {
  font-size: 0.8rem;
  color: #64748b;
  margin-bottom: 0.6rem;
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.place-meta {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 0.7rem;
}

.meta-item {
  font-size: 0.78rem;
  color: #475569;
  font-weight: 500;
}

.meta-rating {
  font-size: 0.82rem;
  font-weight: 700;
  color: #f59e0b;
  background: #fefce8;
  padding: 3px 10px;
  border-radius: 20px;
}

.place-desc {
  font-size: 0.82rem;
  color: #64748b;
  line-height: 1.5;
  margin-bottom: 1rem;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.place-footer {
  border-top: 1px solid #f1f5f9;
  padding-top: 0.85rem;
}

.view-btn {
  font-size: 0.85rem;
  font-weight: 700;
  color: #2563eb;
  cursor: pointer;
  transition: gap 0.2s;
}

.view-btn:hover {
  text-decoration: underline;
}

@keyframes fadeInDown {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }

  to {
    opacity: 1;
  }
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(30px) scale(0.95);
  }

  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

/* Animations */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.place-card {
  animation: fadeInUp 0.6s ease backwards;
}

/* Stagger card animations */
.place-card:nth-child(1) {
  animation-delay: 0.1s;
}

.place-card:nth-child(2) {
  animation-delay: 0.2s;
}

.place-card:nth-child(3) {
  animation-delay: 0.3s;
}

.place-card:nth-child(4) {
  animation-delay: 0.4s;
}

.place-card:nth-child(n+5) {
  animation-delay: 0.5s;
}

.filter-bar .btn {
  border: 1px solid transparent !important;
  box-shadow: none !important;
  background: transparent !important;
  color: #64748b !important;
  font-weight: 600 !important;
  padding: 0.5rem 1.5rem !important;
  position: relative;
}

.filter-bar .btn-primary {
  background: #f1f5f9 !important;
  color: #6366f1 !important;
}

.filter-bar .btn-primary::after {
  content: "";
  position: absolute;
  bottom: 0;
  left: 20%;
  right: 20%;
  height: 3px;
  background: #6366f1;
  border-radius: 10px;
}

.filter-bar .btn-outline-primary:hover {
  background: #f8fafc !important;
  color: #1e293b !important;
}

/* Scrollbar polish */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f5f9;
}

::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style>
