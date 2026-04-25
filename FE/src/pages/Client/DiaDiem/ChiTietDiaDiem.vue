<template>
  <div class="chi-tiet-dia-diem-page py-4 bg-light min-vh-100">
    <div class="container" v-if="loading">
      <div class="text-center py-5">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></div>
        <p class="mt-3 text-muted">Đang tải thông tin địa điểm...</p>
      </div>
    </div>
    <div class="container" v-else-if="error">
      <div class="alert alert-danger text-center">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ error }}
        <div class="mt-3">
          <button class="btn btn-outline-danger" @click="$router.push('/')">Về trang chủ</button>
        </div>
      </div>
    </div>
    <div class="container" v-else-if="selectedPlace">
      <!-- Breadcrumb / Header -->
      <nav aria-label="breadcrumb" class="mb-4 d-flex align-items-center">
        <button
          class="btn btn-sm btn-outline-secondary me-3 rounded-circle shadow-sm d-flex align-items-center justify-content-center"
          @click="goBack" style="width: 36px; height: 36px; padding: 0;">
          <i class="bi bi-arrow-left"></i>
        </button>
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><router-link to="/" class="text-decoration-none text-muted">Trang chủ</router-link></li>
          <li class="breadcrumb-item"><span class="text-muted">{{ selectedPlace.loai_dia_diem || 'Địa điểm' }}</span></li>
          <li class="breadcrumb-item active fw-bold text-dark" aria-current="page">{{ selectedPlace.ten_dia_diem }}</li>
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
            <p class="text-secondary" style="line-height: 1.8; font-size: 1.05rem;">{{ selectedPlace.mo_ta || 'Đang cập nhật thông tin giới thiệu.' }}</p>
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
                    <img v-else-if="rv.avatar_nguoi_danh_gia" :src="rv.avatar_nguoi_danh_gia" alt="avatar"
                      style="width: 100%; height: 100%; object-fit: cover;" referrerpolicy="no-referrer">
                    <span v-else>{{ (rv.nguoi_dung?.ten || rv.ten_nguoi_danh_gia || '?').charAt(0).toUpperCase()
                    }}</span>
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
              <div ref="detailMap" style="width: 100%; height: 100%; z-index: 1;"></div>
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
              @click="addToItinerary">
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
  name: 'ChiTietDiaDiem',
  data() {
    return {
      placeId: this.$route.params.id,
      loading: true,
      error: null,
      selectedPlace: null,
      
      currentImageIdx: 0,
      autoplayInterval: null,
      modalMapInstance: null,
      
      token: localStorage.getItem('client_token'),
      isLoggedIn: !!localStorage.getItem('client_token'),
      
      // Reviews
      loadingReviews: false,
      detailReviews: [],
      newReview: { so_sao: 5, noi_dung: '' },
      submittingReview: false,
      
      // Share
      showShareModal: false,
      myJoinedGroups: [],
      selectedGroupToShare: null,
      sendingShare: false,
    };
  },
  computed: {
    currentGalleryImage() {
      if (!this.selectedPlace || !this.selectedPlace.gallery || this.selectedPlace.gallery.length === 0) {
        return this.selectedPlace ? this.selectedPlace.image : '';
      }
      return this.selectedPlace.gallery[this.currentImageIdx].duong_dan_anh || this.selectedPlace.gallery[this.currentImageIdx].image;
    }
  },
  mounted() {
    this.fetchPlaceDetail();
  },
  unmounted() {
    this.stopAutoplay();
    if (this.modalMapInstance) {
      this.modalMapInstance.off();
      this.modalMapInstance.remove();
    }
  },
  methods: {
    async fetchPlaceDetail() {
      this.loading = true;
      this.error = null;
      try {
        const res = await fetch(`${BASE}/dia-diems/get-detail/${this.placeId}`);
        const json = await res.json();
        if (json.status && json.data) {
          this.selectedPlace = json.data;
          // Fallback image if empty
          if (!this.selectedPlace.image) {
            this.selectedPlace.image = 'https://images.unsplash.com/photo-1559592481-74488ece15f1?w=800&h=600&fit=crop';
          }
          setTimeout(() => { this.initModalMap(this.selectedPlace); }, 300);
          this.startAutoplay();
          this.fetchReviews(this.placeId);
        } else {
          this.error = json.message || 'Không tìm thấy địa điểm này.';
        }
      } catch (e) {
        this.error = 'Lỗi kết nối đến máy chủ.';
        console.error(e);
      } finally {
        this.loading = false;
      }
    },
    goBack() {
      if (window.history.length > 2) {
        this.$router.go(-1);
      } else {
        this.$router.push('/');
      }
    },
    formatPrice(p) { return Number(p).toLocaleString('vi-VN') + 'đ'; },
    nextImage() {
      if (!this.selectedPlace.gallery) return;
      this.currentImageIdx = (this.currentImageIdx + 1) % this.selectedPlace.gallery.length;
    },
    prevImage() {
      if (!this.selectedPlace.gallery) return;
      this.currentImageIdx = (this.currentImageIdx - 1 + this.selectedPlace.gallery.length) % this.selectedPlace.gallery.length;
    },
    startAutoplay() {
      this.stopAutoplay();
      if (this.selectedPlace?.gallery?.length > 1) {
        this.autoplayInterval = setInterval(this.nextImage, 4000);
      }
    },
    stopAutoplay() {
      if (this.autoplayInterval) {
        clearInterval(this.autoplayInterval);
        this.autoplayInterval = null;
      }
    },
    async fetchReviews(contentId) {
      this.loadingReviews = true;
      try {
        const res = await fetch(`${BASE}/dia-diems/danh-gia/place/${contentId}`);
        const json = await res.json();
        if (json.status === 'success') this.detailReviews = json.data;
      } catch (e) {
        console.error(e);
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
    formatDate(d) {
      if (!d) return '';
      return new Date(d).toLocaleDateString('vi-VN', {
        year: 'numeric', month: 'long', day: 'numeric',
        hour: '2-digit', minute: '2-digit'
      });
    },
    initModalMap(place) {
      const el = this.$refs.detailMap;
      if (!el || !place.vi_do) return;
      const L = window.L;
      const lat = parseFloat(place.vi_do);
      const lng = parseFloat(place.kinh_do);

      if (this.modalMapInstance) {
        this.modalMapInstance.off();
        this.modalMapInstance.remove();
        this.modalMapInstance = null;
      }

      this.modalMapInstance = L.map(el, {
        zoomControl: true,
        scrollWheelZoom: false
      }).setView([lat, lng], 16);

      L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
        attribution: '&copy; Google Maps'
      }).addTo(this.modalMapInstance);

      L.marker([lat, lng]).addTo(this.modalMapInstance)
        .bindPopup(`<strong>${place.ten_dia_diem}</strong>`)
        .openPopup();

      setTimeout(() => {
        if (this.modalMapInstance) this.modalMapInstance.invalidateSize();
      }, 800);
    },
    addToItinerary() {
        this.$toast.info('Vui lòng sử dụng trang Tạo lịch trình để thêm địa điểm này!'); 
    },
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
  }
};
</script>

<style scoped>
/* Scoped styles re-used from category pages */
</style>
