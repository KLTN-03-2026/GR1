<template>
  <div class="favorites-page">
    <!-- HERO -->
    <section class="hero-section py-5 position-relative">
      <div class="container text-center">
        <h1 class="page-title mb-3">
          <i class="bi bi-heart-fill me-3 text-danger"></i>Địa điểm yêu thích
        </h1>
        <p class="page-subtitle mb-4 text-center text-muted">
          Những nới bạn đã "thả tim" sẽ xuất hiện ở đây. Chúng tôi sẽ dựa vào đây để đề xuất những chuyến đi tuyệt vời nhất cho bạn.
        </p>

        <!-- SEARCH -->
        <div class="search-bar d-flex justify-content-center mb-3">
          <input v-model="tempSearchQuery" type="text" class="form-control me-2" placeholder="Tìm kiếm trong yêu thích..."
            style="max-width: 400px;" @keyup.enter="performSearch">
          <button @click="performSearch" class="btn btn-primary me-2 shadow-sm">Tìm kiếm</button>
          <button @click="clearSearch" class="btn btn-outline-secondary" v-if="searchQuery">Xóa</button>
        </div>
      </div>
    </section>

    <!-- LIST -->
    <section class="places-section py-5">
      <div class="container">
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-primary" role="status" style="width:3rem;height:3rem;"></div>
          <p class="mt-3 text-muted fw-500">Đang tải danh sách yêu thích...</p>
        </div>
        
        <div v-else-if="error" class="alert alert-danger text-center shadow-sm rounded-4 py-4">
          <i class="bi bi-exclamation-triangle fs-3 d-block mb-2"></i>
          {{ error }}
          <div class="mt-3">
            <button class="btn btn-outline-danger btn-sm" @click="fetchFavorites">Thử lại</button>
          </div>
        </div>

        <div v-else-if="filteredPlaces.length === 0" class="text-center py-5">
          <div class="empty-state">
            <i class="bi bi-heart fs-1 text-muted opacity-25"></i>
            <h4 class="mt-3 text-secondary">Chưa có địa điểm yêu thích nào</h4>
            <p class="text-muted">Hãy khám phá các địa điểm và nhấn "Thả tim" để lưu lại nhé!</p>
            <router-link to="/check-in" class="btn btn-primary rounded-pill px-4 mt-2">Khám phá ngay</router-link>
          </div>
        </div>

        <div v-else class="row g-4">
          <div v-for="place in filteredPlaces" :key="place.id" class="col-12 col-md-6 col-xl-4">
            <div class="card place-card h-100 shadow-sm border-0">
              <!-- IMAGE -->
              <div class="place-image" :style="{ backgroundImage: `url(${place.image})` }">
                <div class="place-badge">{{ place.loai_dia_diem }}</div>
                <button class="favorite-btn active" @click.stop="toggleFavorite(place)">
                  <i class="bi bi-heart-fill"></i>
                </button>
              </div>

              <!-- BODY -->
              <div class="card-body d-flex flex-column p-4">
                <div class="mb-3">
                  <h5 class="card-title text-dark fw-bold mb-1">{{ place.ten_dia_diem }}</h5>
                  <small class="text-muted d-flex align-items-center gap-1">
                    <i class="bi bi-geo-alt"></i> {{ place.dia_chi }}
                  </small>
                </div>

                <div class="mb-3 d-flex gap-2">
                  <span class="badge rounded-pill bg-success-soft text-success px-3 py-2">
                    <i class="bi bi-clock me-1"></i> {{ place.gio_mo_cua }} - {{ place.gio_dong_cua }}
                  </span>
                </div>

                <p class="text-secondary small line-clamp-2 mb-3">
                  {{ place.mo_ta }}
                </p>

                <div class="d-flex justify-content-between align-items-center mt-auto">
                  <div class="rating fw-bold text-warning">
                    <i class="bi bi-star-fill me-1"></i>{{ place.danh_gia_trung_binh }}
                  </div>
                  <strong class="price-text">
                    {{ place.gia_ve == 0 ? 'Miễn phí' : formatPrice(place.gia_ve) }}
                  </strong>
                </div>

                <button class="btn btn-primary w-100 mt-3 rounded-3" @click="viewDetail(place)">
                  Xem chi tiết
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- MODAL -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal-content overflow-hidden">
        <button class="btn-close shadow" @click="closeModal">×</button>
        <div class="modal-header p-4 pb-0 border-0">
          <div>
            <h4 class="fw-bold mb-0">{{ selectedPlace.ten_dia_diem }}</h4>
            <small class="text-muted">{{ selectedPlace.loai_dia_diem }} • ⭐ {{ selectedPlace.danh_gia_trung_binh }}</small>
          </div>
        </div>
        <div class="modal-body p-4">
          <!-- Gallery -->
          <div class="modal-gallery mb-4" v-if="selectedPlace.gallery && selectedPlace.gallery.length > 0">
            <div class="carousel-container shadow" @mouseenter="stopAutoplay" @mouseleave="startAutoplay">
              <button class="carousel-nav prev" @click="prevImage" v-if="selectedPlace.gallery.length > 1">
                <i class="bi bi-chevron-left"></i>
              </button>
              <div class="carousel-slide" :style="{ backgroundImage: `url(${currentGalleryImage})` }"></div>
              <button class="carousel-nav next" @click="nextImage" v-if="selectedPlace.gallery.length > 1">
                <i class="bi bi-chevron-right"></i>
              </button>
              <div class="carousel-indicators" v-if="selectedPlace.gallery.length > 1">
                <span v-for="(img, idx) in selectedPlace.gallery" :key="idx" 
                      :class="{ active: currentImageIdx === idx }"
                      @click="currentImageIdx = idx"></span>
              </div>
            </div>
            <!-- Thumbnails -->
            <div class="carousel-thumbnails mt-2" v-if="selectedPlace.gallery.length > 1">
              <div v-for="(img, idx) in selectedPlace.gallery" :key="idx"
                   :class="['thumb-item', { active: currentImageIdx === idx }]"
                   @click="currentImageIdx = idx"
                   :style="{ backgroundImage: `url(${img.duong_dan_anh || img.image})` }">
              </div>
            </div>
          </div>
          <div v-else class="modal-image rounded-4 mb-4 shadow" :style="{ backgroundImage: `url(${selectedPlace.image})` }"></div>

          <div class="row g-4">
            <div class="col-12">
              <h6 class="fw-bold text-primary mb-2">Giới thiệu</h6>
              <p class="text-muted mb-0" style="line-height: 1.7;">{{ selectedPlace.mo_ta }}</p>
            </div>
            <div class="col-md-6">
              <h6 class="fw-bold text-primary mb-2">Thông tin liên hệ</h6>
              <ul class="list-unstyled mb-0">
                <li class="mb-2"><i class="bi bi-geo-alt me-2 text-danger"></i> {{ selectedPlace.dia_chi }}</li>
                <li class="mb-2"><i class="bi bi-clock me-2 text-success"></i> {{ selectedPlace.gio_mo_cua }} - {{ selectedPlace.gio_dong_cua }}</li>
                <li><i class="bi bi-cash me-2 text-warning"></i> {{ selectedPlace.gia_ve == 0 ? 'Miễn phí' : formatPrice(selectedPlace.gia_ve) }}</li>
              </ul>
            </div>
          </div>
        </div>
        <div class="modal-footer p-4 pt-0 border-0">
          <button class="btn btn-outline-secondary px-4 me-2" @click="closeModal">Đóng</button>
          <button class="btn btn-primary px-4 shadow" @click="addToItinerary">Thêm vào lịch trình</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
const BASE = 'http://localhost:8000/api';

export default {
  name: 'YeuThich',
  data() {
    return {
      places: [],
      loading: false,
      error: null,
      searchQuery: '',
      tempSearchQuery: '',
      showModal: false,
      selectedPlace: null,
      currentImageIdx: 0,
      token: localStorage.getItem('client_token'),
      autoplayInterval: null,
    };
  },
  computed: {
    filteredPlaces() {
      if (!this.searchQuery) return this.places;
      const q = this.searchQuery.toLowerCase();
      return this.places.filter(p => 
        p.ten_dia_diem.toLowerCase().includes(q) || 
        (p.mo_ta && p.mo_ta.toLowerCase().includes(q))
      );
    },
    currentGalleryImage() {
      if (!this.selectedPlace || !this.selectedPlace.gallery || this.selectedPlace.gallery.length === 0) {
        return this.selectedPlace ? this.selectedPlace.image : '';
      }
      return this.selectedPlace.gallery[this.currentImageIdx].duong_dan_anh || this.selectedPlace.gallery[this.currentImageIdx].image;
    }
  },
  mounted() {
    if (!this.token) {
      this.$router.push('/client/dang-nhap');
      return;
    }
    this.fetchFavorites();
  },
  methods: {
    async fetchFavorites() {
      this.loading = true;
      this.error = null;
      try {
        const res = await fetch(`${BASE}/client/yeu-thich`, {
          headers: {
            'Authorization': `Bearer ${this.token}`
          }
        });
        const json = await res.json();
        if (json.status === 'success') {
          this.places = json.data;
        } else {
          throw new Error(json.message || 'Lỗi khi tải dữ liệu');
        }
      } catch (e) {
        this.error = e.message;
      } finally {
        this.loading = false;
      }
    },

    async toggleFavorite(place) {
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
          // Xóa khỏi danh sách hiển thị nếu bỏ yêu thích
          if (!json.is_favorite) {
            this.places = this.places.filter(p => p.id !== place.id);
          }
        }
      } catch (e) {
        console.error(e);
      }
    },

    formatPrice(price) {
      return Number(price).toLocaleString('vi-VN') + 'đ';
    },

    viewDetail(place) {
      this.selectedPlace = place;
      this.currentImageIdx = 0;
      this.showModal = true;
      this.startAutoplay();
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

    closeModal() {
      this.stopAutoplay();
      this.showModal = false;
      this.selectedPlace = null;
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

    performSearch() {
      this.searchQuery = this.tempSearchQuery;
    },
    clearSearch() {
      this.searchQuery = '';
      this.tempSearchQuery = '';
    },
    addToItinerary() {
      this.$toast.info('Vui lòng sử dụng trang Tạo lịch trình để thêm địa điểm này!');
      this.closeModal();
    },
  }
};
</script>

<style scoped>
.hero-section {
  position: relative;
  background: linear-gradient(135deg, #fff5f5 0%, #fff 100%);
  padding: 4rem 0 3rem;
  border-bottom: 1px solid rgba(220, 38, 38, 0.05);
}
.page-title {
  font-size: 2.4rem;
  font-weight: 900;
  color: #0f172a;
}
.search-bar input {
  border: 2px solid #fee2e2;
  border-radius: 12px;
  padding: 0.75rem 1.2rem;
  box-shadow: 0 2px 10px rgba(0,0,0,0.03);
}
.search-bar input:focus {
  border-color: #ef4444;
  box-shadow: 0 0 0 4px rgba(239,68,68,0.1);
}
.place-card {
  border-radius: 20px;
  overflow: hidden;
  transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
  background: #fff;
  border: 1px solid rgba(0,0,0,0.05);
}
.place-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 40px rgba(0,0,0,0.08) !important;
}
.place-image {
  height: 220px;
  background-size: cover;
  background-position: center;
  position: relative;
}
.place-badge {
  position: absolute;
  top: 15px;
  left: 15px;
  background: rgba(255, 255, 255, 0.95);
  padding: 5px 15px;
  border-radius: 30px;
  font-size: 0.75rem;
  font-weight: 700;
  color: #ef4444;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
.favorite-btn {
  position: absolute;
  top: 15px;
  right: 15px;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  border: none;
  background: #fff;
  color: #ef4444;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
.bg-success-soft {
  background-color: #f0fdf4;
}
.price-text {
  color: #ef4444;
  font-size: 1.1rem;
}
</style>
