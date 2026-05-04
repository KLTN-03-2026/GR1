<template>
  <div class="favorites-page">
    <!-- HERO -->
    <section class="hero-section position-relative">
      <div class="container text-center">
        <div class="hero-content">
          <h1 class="page-title mb-2">
            Địa Điểm <span class="text-gradient">Yêu Thích</span>
          </h1>
          <p class="page-subtitle">
            Những nơi bạn đã "thả tim" sẽ xuất hiện ở đây. Chúng tôi sẽ dựa vào đây để đề xuất những chuyến đi tuyệt vời nhất cho bạn.
          </p>

          <!-- SEARCH -->
          <div class="search-container">
            <div class="search-glass">
              <input v-model="searchQuery" type="text" class="form-control"
                placeholder="Tìm kiếm trong danh sách yêu thích...">
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- LIST -->
    <section class="places-section py-4">
      <div class="container">
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-primary" role="status" style="width:3rem;height:3rem;"></div>
          <p class="mt-3 text-muted">Đang tải danh sách yêu thích...</p>
        </div>

        <div v-else-if="error" class="alert alert-danger text-center shadow-sm rounded-4 py-4">
          <i class="bi bi-exclamation-triangle fs-3 d-block mb-2"></i>
          {{ error }}
          <div class="mt-3">
            <button class="btn btn-outline-danger btn-sm" @click="fetchFavorites">Thử lại</button>
          </div>
        </div>

        <div v-else-if="filteredPlaces.length === 0" class="text-center py-5">
          <i class="bi bi-heart fs-1 d-block mb-3 opacity-25 text-danger" style="font-size:4rem!important;"></i>
          <h4 class="text-secondary">Chưa có địa điểm yêu thích nào</h4>
          <p class="text-muted">Hãy khám phá các địa điểm và nhấn "Thả tim" để lưu lại nhé!</p>
          <router-link to="/check-in" class="btn btn-primary rounded-pill px-4 mt-2">Khám phá ngay</router-link>
        </div>

        <div v-else class="row g-4">
          <div v-for="place in filteredPlaces" :key="place.id" class="col-12 col-sm-6 col-xl-4">
            <div class="place-card h-100" @click="$router.push('/chi-tiet-dia-diem/' + place.id)">
              <div class="place-image" :style="{ backgroundImage: `url(${place.image})` }">
                <div class="place-image-overlay"></div>
                <span :class="['place-badge', categoryClass(place.loai_dia_diem)]">{{ place.loai_dia_diem }}</span>
                <button class="favorite-btn active" @click.stop="toggleFavorite(place)">
                  <i class="bi bi-heart-fill"></i>
                </button>
                <div class="price-chip">{{ place.gia_ve == 0 ? 'Miễn phí' : formatPrice(place.gia_ve) }}</div>
              </div>
              <div class="place-body">
                <h5 class="place-title">{{ place.ten_dia_diem }}</h5>
                <p class="place-addr"><i class="bi bi-geo-alt-fill me-1"></i>{{ place.dia_chi }}</p>
                <div class="place-meta">
                  <span class="meta-item"><i class="bi bi-clock me-1"></i>{{ place.gio_mo_cua }} – {{ place.gio_dong_cua }}</span>
                  <span class="meta-rating"><i class="bi bi-star-fill me-1"></i>{{ place.danh_gia_trung_binh }}</span>
                </div>
                <p class="place-desc">{{ place.mo_ta }}</p>
                <div class="place-footer">
                  <span class="view-btn">Xem chi tiết <i class="bi bi-arrow-right ms-1"></i></span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import api from '../../services/api';

export default {
  name: 'YeuThich',
  data() {
    return {
      places: [],
      loading: false,
      error: null,
      searchQuery: '',
      token: localStorage.getItem('client_token'),
    };
  },
  computed: {
    filteredPlaces() {
      if (!this.searchQuery) return this.places;
      const q = this.searchQuery.toLowerCase();
      return this.places.filter(p =>
        (p.ten_dia_diem && p.ten_dia_diem.toLowerCase().includes(q)) ||
        (p.dia_chi && p.dia_chi.toLowerCase().includes(q)) ||
        (p.loai_dia_diem && p.loai_dia_diem.toLowerCase().includes(q)) ||
        (p.mo_ta && p.mo_ta.toLowerCase().includes(q))
      );
    },
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
        const res = await api.get('/client/yeu-thich');
        const json = res.data;
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
        const res = await api.post('/client/yeu-thich/toggle', {
          id_dia_diem: place.id
        });
        const json = res.data;
        if (json.status) {
          if (!json.is_favorite) {
            this.places = this.places.filter(p => p.id !== place.id);
            this.$toast && this.$toast.success(`Đã xoá "${place.ten_dia_diem}" khỏi yêu thích`);
          }
        }
      } catch (e) {
        console.error(e);
      }
    },

    formatPrice(price) {
      return Number(price).toLocaleString('vi-VN') + 'đ';
    },

    categoryClass(c) {
      const m = {
        'Cầu nổi tiếng': 'bridge',
        'Bãi biển': 'beach',
        'Ngắm cảnh': 'scenic',
        'Lịch sử': 'history',
        'Phố cổ': 'oldtown',
        'Tự nhiên': 'nature',
        'Ẩm thực': 'food',
        'Tâm linh': 'temple',
        'Giải trí': 'entertainment',
      };
      return m[c] || 'bridge';
    },
  }
};
</script>

<style scoped>
/* ===== HERO ===== */
.hero-section {
  background: radial-gradient(at 0% 0%, rgba(239, 68, 68, 0.08) 0px, transparent 50%),
    radial-gradient(at 100% 0%, rgba(251, 113, 133, 0.06) 0px, transparent 50%),
    radial-gradient(at 100% 100%, rgba(244, 63, 94, 0.05) 0px, transparent 50%),
    radial-gradient(at 0% 100%, rgba(239, 68, 68, 0.08) 0px, transparent 50%),
    #ffffff;
  padding: 6rem 0 5rem;
  overflow: hidden;
  border-bottom: 1px solid rgba(0, 0, 0, 0.02);
}

.page-title {
  font-size: 3.5rem;
  font-weight: 800;
  color: #0f172a;
  letter-spacing: -2px;
  line-height: 1.1;
}

.text-gradient {
  background: linear-gradient(135deg, #ef4444, #f43f5e);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.page-subtitle {
  color: #64748b;
  font-size: 1.15rem;
  max-width: 700px;
  margin: 1.5rem auto 3rem;
  line-height: 1.8;
}

.search-container {
  max-width: 600px;
  margin: 0 auto;
  position: relative;
  z-index: 10;
}

.search-glass {
  background: rgba(255, 255, 255, 0.85);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.5);
  border-radius: 24px;
  padding: 10px;
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.06);
  display: flex;
  transition: all 0.4s;
}

.search-glass:focus-within {
  box-shadow: 0 25px 60px rgba(239, 68, 68, 0.12);
  transform: translateY(-2px);
  border-color: rgba(239, 68, 68, 0.3);
}

.search-glass input {
  border: none !important;
  background: transparent !important;
  padding: 1rem 1.5rem !important;
  font-size: 1.05rem !important;
  color: #1e293b !important;
}

.search-glass input::placeholder {
  color: #94a3b8;
}

.search-glass input:focus {
  box-shadow: none !important;
  outline: none !important;
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
  animation: fadeInUp 0.6s ease backwards;
}

.place-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 24px 50px rgba(15, 23, 42, 0.16);
}

.place-card:nth-child(1) { animation-delay: 0.1s; }
.place-card:nth-child(2) { animation-delay: 0.2s; }
.place-card:nth-child(3) { animation-delay: 0.3s; }
.place-card:nth-child(4) { animation-delay: 0.4s; }
.place-card:nth-child(n+5) { animation-delay: 0.5s; }

.place-image {
  height: 220px;
  background-size: cover;
  background-position: center;
  position: relative;
  overflow: hidden;
  transition: transform 0.55s ease;
}

.place-card:hover .place-image {
  transform: scale(1.06);
}

.place-image-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, transparent 45%, rgba(10, 20, 40, 0.55) 100%);
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

.place-badge.bridge  { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.place-badge.beach   { background: linear-gradient(135deg, #34d399, #059669); }
.place-badge.scenic  { background: linear-gradient(135deg, #f97316, #c2410c); }
.place-badge.history { background: linear-gradient(135deg, #a855f7, #7c3aed); }
.place-badge.oldtown { background: linear-gradient(135deg, #ec4899, #be185d); }
.place-badge.nature  { background: linear-gradient(135deg, #10b981, #047857); }
.place-badge.food    { background: linear-gradient(135deg, #f59e0b, #d97706); }
.place-badge.temple  { background: linear-gradient(135deg, #6366f1, #4338ca); }
.place-badge.entertainment { background: linear-gradient(135deg, #ef4444, #dc2626); }

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
</style>
