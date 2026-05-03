<template>
  <div class="view-report-page">
    <!-- Hero Section -->
    <section class="report-hero">
      <div class="hero-shape hero-shape-one"></div>
      <div class="hero-shape hero-shape-two"></div>
      <div class="container position-relative py-5">
        <div class="text-center" data-reveal>
          <span class="hero-tag">Đánh giá & Phản hồi</span>
          <h1 class="hero-title">Báo cáo trải nghiệm du lịch Đà Nẵng</h1>
          <p class="hero-description mx-auto">
            Tổng hợp đánh giá từ cộng đồng du khách về các địa điểm, lịch trình và trải nghiệm
            tại Đà Nẵng. Giúp bạn chọn được điểm đến phù hợp nhất cho chuyến đi.
          </p>

          <!-- Search Bar -->
          <div class="search-bar mx-auto">
            <i class="bi bi-search"></i>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Tìm kiếm địa điểm, nhà hàng, điểm check-in..."
              @input="filterPlaces"
            />
          </div>

          <!-- Filter Chips -->
          <div class="filter-chips">
            <button
              v-for="cat in categories"
              :key="cat.key"
              class="chip"
              :class="{ active: activeCategory === cat.key }"
              @click="setCategory(cat.key)"
            >
              <i class="bi me-1" :class="cat.icon"></i>{{ cat.label }}
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Stats Strip -->
    <section class="stats-strip py-4">
      <div class="container">
        <div class="row g-3 justify-content-center">
          <div class="col-6 col-md-3" v-for="stat in stats" :key="stat.label">
            <div class="stat-chip text-center">
              <i class="bi mb-1" :class="stat.icon" :style="{ color: stat.color }"></i>
              <strong>{{ stat.value }}</strong>
              <span>{{ stat.label }}</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Place Cards -->
    <section class="places-section py-5">
      <div class="container">
        <div class="section-title mb-4" data-reveal>
          <span class="section-kicker">Địa điểm nổi bật</span>
          <h2>{{ filteredPlaces.length > 0 ? `${filteredPlaces.length} địa điểm` : 'Không tìm thấy kết quả' }}</h2>
        </div>

        <!-- Loading -->
        <div v-if="isLoading" class="loading-state text-center py-5">
          <div class="spinner"></div>
          <p class="mt-3 text-muted">Đang tải dữ liệu...</p>
        </div>

        <!-- No result -->
        <div v-else-if="filteredPlaces.length === 0" class="empty-state text-center py-5">
          <i class="bi bi-search fs-1 text-muted"></i>
          <p class="mt-3 text-muted">Không tìm thấy địa điểm phù hợp. Thử từ khóa khác nhé!</p>
        </div>

        <!-- Cards Grid -->
        <div v-else class="row g-4">
          <div
            v-for="place in filteredPlaces"
            :key="place.id"
            class="col-md-6 col-xl-4"
            data-reveal
          >
            <div class="place-card h-100">
              <!-- Category Badge -->
              <div class="place-header" :style="{ background: place.gradient }">
                <div class="place-category-badge">
                  <i class="bi" :class="place.icon"></i>
                  {{ place.category }}
                </div>
                <div class="place-rating">
                  <i class="bi bi-star-fill"></i>
                  {{ place.rating }}
                </div>
              </div>

              <div class="place-body">
                <h5 class="place-name">{{ place.name }}</h5>
                <p class="place-desc">{{ place.description }}</p>

                <!-- Tags -->
                <div class="place-tags">
                  <span v-for="tag in place.tags" :key="tag">{{ tag }}</span>
                </div>

                <!-- Stats Row -->
                <div class="place-stats">
                  <div class="pstat">
                    <i class="bi bi-chat-square-text"></i>
                    <span>{{ place.reviews }} đánh giá</span>
                  </div>
                  <div class="pstat">
                    <i class="bi bi-heart"></i>
                    <span>{{ place.likes }} yêu thích</span>
                  </div>
                  <div class="pstat">
                    <i class="bi bi-wallet2"></i>
                    <span>{{ place.price }}</span>
                  </div>
                </div>

                <!-- Review Snippets -->
                <div class="review-snippet">
                  <div v-for="review in place.recentReviews" :key="review.user" class="review-item">
                    <img :src="review.avatar" :alt="review.user" class="review-avatar" />
                    <div>
                      <strong>{{ review.user }}</strong>
                      <p class="mb-0">{{ review.text }}</p>
                    </div>
                  </div>
                </div>

                <router-link to="/tao-lich-trinh" class="btn-add-trip">
                  <i class="bi bi-plus-circle me-2"></i>Thêm vào lịch trình
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section py-5">
      <div class="container" data-reveal>
        <div class="cta-panel text-center">
          <span class="section-kicker text-white-50">Lên kế hoạch ngay</span>
          <h2 class="text-white">Tìm được nơi muốn đến? Lên lịch trình ngay thôi!</h2>
          <p class="text-white-50 mb-4">
            Sử dụng những đánh giá trên để chọn địa điểm phù hợp và tạo lịch trình cá nhân hoá cho chuyến đi Đà Nẵng.
          </p>
          <router-link to="/tao-lich-trinh" class="btn btn-light btn-lg px-4">
            Tạo lịch trình ngay
          </router-link>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  name: 'ViewReport',
  data() {
    return {
      searchQuery: '',
      activeCategory: 'all',
      isLoading: false,
      categories: [
        { key: 'all',      label: 'Tất cả',   icon: 'bi-grid' },
        { key: 'am-thuc',  label: 'Ẩm thực',  icon: 'bi-cup-hot' },
        { key: 'tam-linh', label: 'Tâm linh', icon: 'bi-bank' },
        { key: 'giai-tri', label: 'Giải trí', icon: 'bi-controller' },
        { key: 'check-in', label: 'Check-in', icon: 'bi-camera' },
      ],
      stats: [
        { label: 'Địa điểm',    value: '120+',  icon: 'bi-geo-alt-fill',    color: '#10b981' },
        { label: 'Đánh giá',    value: '4.8★',  icon: 'bi-star-fill',       color: '#f59e0b' },
        { label: 'Lượt xem',    value: '18K+',  icon: 'bi-eye-fill',        color: '#3b82f6' },
        { label: 'Du khách',    value: '5K+',   icon: 'bi-people-fill',     color: '#8b5cf6' },
      ],
      places: [
        {
          id: 1, categoryKey: 'am-thuc',
          name: 'Mỳ Quảng Bà Mua', category: 'Ẩm thực',
          icon: 'bi-cup-hot', rating: 4.9,
          gradient: 'linear-gradient(135deg,#fef3c7,#fde68a)',
          description: 'Quán mỳ Quảng lâu đời nhất Đà Nẵng với hương vị đậm đà, nước dùng ngọt thanh và topping phong phú.',
          tags: ['Mỳ Quảng', 'Đặc sản', 'Bình dân'],
          price: '30.000đ - 60.000đ',
          reviews: 842, likes: 1200,
          recentReviews: [
            { user: 'Minh Tú', avatar: 'https://i.pravatar.cc/40?img=5',  text: 'Ngon tuyệt, phải ghé mỗi lần ra Đà Nẵng!' },
            { user: 'Lan Anh', avatar: 'https://i.pravatar.cc/40?img=8',  text: 'Tô mỳ vừa miệng, giá cả phải chăng.' },
          ],
        },
        {
          id: 2, categoryKey: 'tam-linh',
          name: 'Ngũ Hành Sơn', category: 'Tâm linh',
          icon: 'bi-bank', rating: 4.8,
          gradient: 'linear-gradient(135deg,#ede9fe,#c4b5fd)',
          description: 'Quần thể núi đá vôi huyền bí với nhiều hang động, chùa chiền và tác phẩm điêu khắc đá tuyệt đẹp.',
          tags: ['Hang động', 'Di sản', 'Chụp ảnh'],
          price: '40.000đ/người',
          reviews: 1530, likes: 2800,
          recentReviews: [
            { user: 'Hoàng Nam', avatar: 'https://i.pravatar.cc/40?img=12', text: 'View từ đỉnh núi đẹp không kém Hội An.' },
            { user: 'Thu Hà',    avatar: 'https://i.pravatar.cc/40?img=20', text: 'Rất linh thiêng và yên tĩnh, nên đi vào sáng sớm.' },
          ],
        },
        {
          id: 3, categoryKey: 'check-in',
          name: 'Cầu Rồng', category: 'Check-in',
          icon: 'bi-camera', rating: 4.7,
          gradient: 'linear-gradient(135deg,#dbeafe,#bfdbfe)',
          description: 'Biểu tượng của Đà Nẵng hiện đại với màn phun lửa, phun nước đặc sắc vào tối cuối tuần.',
          tags: ['Biểu tượng', 'Phun lửa', 'Miễn phí'],
          price: 'Miễn phí',
          reviews: 3200, likes: 5600,
          recentReviews: [
            { user: 'Bảo Long', avatar: 'https://i.pravatar.cc/40?img=25', text: 'Xem phun lửa tối T7 cực đỉnh!' },
            { user: 'Mỹ Duyên', avatar: 'https://i.pravatar.cc/40?img=31', text: 'Ban đêm lung linh hơn ban ngày rất nhiều.' },
          ],
        },
        {
          id: 4, categoryKey: 'giai-tri',
          name: 'Bà Nà Hills', category: 'Giải trí',
          icon: 'bi-controller', rating: 4.6,
          gradient: 'linear-gradient(135deg,#d1fae5,#a7f3d0)',
          description: 'Khu vui chơi giải trí trên đỉnh núi với Cầu Vàng, làng Pháp cổ kính và không khí mát mẻ quanh năm.',
          tags: ['Cầu Vàng', 'Cáp treo', 'Trẻ em'],
          price: '500.000đ - 750.000đ',
          reviews: 4100, likes: 7200,
          recentReviews: [
            { user: 'Gia Huy',  avatar: 'https://i.pravatar.cc/40?img=33', text: 'Nhất định phải đến một lần trong đời!' },
            { user: 'Kim Ngân', avatar: 'https://i.pravatar.cc/40?img=44', text: 'Cầu Vàng đẹp hơn hình mạng rất nhiều.' },
          ],
        },
        {
          id: 5, categoryKey: 'am-thuc',
          name: 'Bánh Mì Bà Lan', category: 'Ẩm thực',
          icon: 'bi-cup-hot', rating: 4.8,
          gradient: 'linear-gradient(135deg,#fef3c7,#fed7aa)',
          description: 'Tiệm bánh mì nổi tiếng nhất Đà Nẵng với nhân phong phú, vỏ bánh giòn tan và giá cực kỳ hợp lý.',
          tags: ['Bánh mì', 'Bình dân', 'Đặc sản'],
          price: '15.000đ - 30.000đ',
          reviews: 960, likes: 1850,
          recentReviews: [
            { user: 'Hải Đăng', avatar: 'https://i.pravatar.cc/40?img=50', text: 'Xếp hàng 20 phút nhưng xứng đáng từng giây!' },
            { user: 'Yến Nhi',  avatar: 'https://i.pravatar.cc/40?img=55', text: 'Vỏ bánh giòn, nhân đầy, giá hợp lý.' },
          ],
        },
        {
          id: 6, categoryKey: 'check-in',
          name: 'Biển Mỹ Khê', category: 'Check-in',
          icon: 'bi-camera', rating: 4.7,
          gradient: 'linear-gradient(135deg,#e0f2fe,#bae6fd)',
          description: 'Một trong những bãi biển đẹp nhất hành tinh với cát trắng mịn, sóng êm và không khí trong lành.',
          tags: ['Bãi biển', 'Bình minh', 'Tắm biển'],
          price: 'Miễn phí',
          reviews: 5200, likes: 9400,
          recentReviews: [
            { user: 'Trúc Ly', avatar: 'https://i.pravatar.cc/40?img=60', text: 'Bình minh ở đây đẹp đến mức không muốn về.' },
            { user: 'Tuấn Anh', avatar: 'https://i.pravatar.cc/40?img=65', text: 'Nước trong xanh, cát mịn, rất sạch.' },
          ],
        },
      ],
    }
  },
  computed: {
    filteredPlaces() {
      let list = this.places;
      if (this.activeCategory !== 'all') {
        list = list.filter(p => p.categoryKey === this.activeCategory);
      }
      if (this.searchQuery.trim()) {
        const q = this.searchQuery.toLowerCase();
        list = list.filter(p =>
          p.name.toLowerCase().includes(q) ||
          p.description.toLowerCase().includes(q) ||
          p.tags.some(t => t.toLowerCase().includes(q))
        );
      }
      return list;
    },
  },
  mounted() {
    document.title = 'Báo cáo trải nghiệm du lịch Đà Nẵng';
    this.$nextTick(() => {
      const els = this.$el?.querySelectorAll('[data-reveal]') || [];
      if (!els.length || typeof IntersectionObserver === 'undefined') {
        els.forEach(el => el.classList.add('is-visible'));
        return;
      }
      const obs = new IntersectionObserver(
        entries => entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('is-visible'); obs.unobserve(e.target); } }),
        { threshold: 0.12 }
      );
      els.forEach(el => {
        const r = el.getBoundingClientRect();
        if (r.top < window.innerHeight && r.bottom > 0) { el.classList.add('is-visible'); return; }
        obs.observe(el);
      });
    });
  },
  methods: {
    setCategory(key) { this.activeCategory = key; },
    filterPlaces() { /* handled by computed */ },
  },
}
</script>

<style scoped>
/* ── Base ── */
.view-report-page { color: #4f5d75; }

[data-reveal] { opacity: 0; transform: translateY(28px); transition: opacity 0.7s ease, transform 0.7s ease; }
[data-reveal].is-visible { opacity: 1; transform: translateY(0); }

/* ── Hero ── */
.report-hero {
  position: relative;
  background:
    radial-gradient(circle at 10% 20%, rgba(58, 134, 255, 0.12), transparent 28%),
    radial-gradient(circle at 90% 10%, rgba(16, 185, 129, 0.14), transparent 22%),
    linear-gradient(180deg, #f6fbff 0%, #ffffff 60%);
  overflow: hidden;
}

.hero-shape {
  position: absolute;
  border-radius: 50%;
  filter: blur(8px);
  opacity: 0.4;
}

.hero-shape-one {
  width: 320px; height: 320px;
  top: -60px; right: -80px;
  background: rgba(16, 185, 129, 0.2);
  animation: floatY 8s ease-in-out infinite;
}

.hero-shape-two {
  width: 240px; height: 240px;
  bottom: -40px; left: -80px;
  background: rgba(58, 134, 255, 0.15);
  animation: floatY 10s ease-in-out infinite reverse;
}

@keyframes floatY {
  0%, 100% { transform: translateY(0); }
  50%       { transform: translateY(-18px); }
}

.hero-tag, .section-kicker {
  display: inline-block;
  padding: 0.45rem 0.9rem;
  border-radius: 999px;
  background: rgba(29, 111, 214, 0.08);
  color: #1d6fd6;
  font-size: 0.76rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  margin-bottom: 1.1rem;
}

.hero-title {
  font-size: clamp(2rem, 4.5vw, 3.2rem);
  line-height: 1.1;
  color: #22304a;
  margin-bottom: 1rem;
  font-weight: 800;
}

.hero-description {
  font-size: 1.05rem;
  line-height: 1.8;
  color: #4f5d75;
  max-width: 600px;
  margin-bottom: 2rem;
}

/* ── Search ── */
.search-bar {
  display: flex;
  align-items: center;
  gap: 12px;
  max-width: 580px;
  background: #fff;
  border: 1px solid rgba(34, 48, 74, 0.1);
  border-radius: 999px;
  padding: 12px 20px;
  box-shadow: 0 12px 30px rgba(31, 41, 55, 0.08);
  margin-bottom: 1.5rem;
}

.search-bar i { color: #94a3b8; font-size: 1.1rem; }

.search-bar input {
  border: none;
  outline: none;
  flex: 1;
  font-size: 0.96rem;
  color: #22304a;
  background: transparent;
}

.search-bar input::placeholder { color: #94a3b8; }

/* ── Filter Chips ── */
.filter-chips {
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: 1rem;
}

.chip {
  display: inline-flex;
  align-items: center;
  padding: 7px 18px;
  border-radius: 999px;
  background: #fff;
  border: 1px solid rgba(34, 48, 74, 0.12);
  color: #4f5d75;
  font-size: 0.875rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.chip:hover { background: #f0f9f6; border-color: #10b981; color: #10b981; }
.chip.active { background: linear-gradient(135deg,#10b981,#0ea5a4); border-color: transparent; color: #fff; box-shadow: 0 8px 20px rgba(16,185,129,0.28); }

/* ── Stats Strip ── */
.stats-strip { background: #f8fbff; border-top: 1px solid rgba(34,48,74,0.06); border-bottom: 1px solid rgba(34,48,74,0.06); }

.stat-chip {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 2px;
  background: #fff;
  border: 1px solid rgba(34,48,74,0.08);
  border-radius: 1rem;
  padding: 1rem;
  box-shadow: 0 6px 18px rgba(31,41,55,0.05);
}

.stat-chip i     { font-size: 1.4rem; }
.stat-chip strong { font-size: 1.3rem; color: #22304a; }
.stat-chip span   { font-size: 0.8rem; color: #64748b; }

/* ── Section Heading ── */
.section-title h2 { font-size: 1.5rem; font-weight: 700; color: #22304a; }

/* ── Place Cards ── */
.place-card {
  background: #fff;
  border-radius: 1.5rem;
  border: 1px solid rgba(34,48,74,0.08);
  box-shadow: 0 16px 40px rgba(31,41,55,0.07);
  overflow: hidden;
  transition: transform 0.28s ease, box-shadow 0.28s ease;
}

.place-card:hover { transform: translateY(-4px); box-shadow: 0 24px 50px rgba(31,41,55,0.12); }

.place-header {
  position: relative;
  height: 110px;
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  padding: 0 16px 14px;
}

.place-category-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: rgba(255,255,255,0.9);
  backdrop-filter: blur(6px);
  border-radius: 999px;
  padding: 5px 12px;
  font-size: 0.8rem;
  font-weight: 700;
  color: #22304a;
}

.place-rating {
  background: rgba(255,255,255,0.9);
  backdrop-filter: blur(6px);
  border-radius: 999px;
  padding: 5px 10px;
  font-size: 0.85rem;
  font-weight: 700;
  color: #22304a;
}

.place-rating i { color: #f59e0b; margin-right: 3px; }

.place-body { padding: 16px 18px 18px; }

.place-name { font-size: 1.1rem; font-weight: 700; color: #22304a; margin-bottom: 6px; }
.place-desc { font-size: 0.875rem; color: #4f5d75; line-height: 1.6; margin-bottom: 12px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

.place-tags { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 12px; }
.place-tags span {
  padding: 3px 10px;
  background: #f1f5f9;
  border-radius: 999px;
  font-size: 0.75rem;
  font-weight: 600;
  color: #475569;
}

.place-stats { display: flex; gap: 14px; margin-bottom: 14px; }

.pstat {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 0.78rem;
  color: #64748b;
}

.pstat i { color: #10b981; }

/* ── Review Snippets ── */
.review-snippet { display: flex; flex-direction: column; gap: 10px; margin-bottom: 14px; background: #f8fafc; border-radius: 10px; padding: 10px 12px; }

.review-item { display: flex; gap: 10px; align-items: flex-start; }

.review-avatar {
  width: 32px; height: 32px;
  border-radius: 50%;
  object-fit: cover;
  flex-shrink: 0;
  border: 2px solid #e2e8f0;
}

.review-item strong { font-size: 0.8rem; color: #22304a; display: block; }
.review-item p { font-size: 0.8rem; color: #64748b; line-height: 1.4; }

/* ── CTA Button ── */
.btn-add-trip {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  padding: 10px;
  border-radius: 12px;
  background: linear-gradient(135deg, #10b981, #0ea5a4);
  color: #fff;
  font-size: 0.875rem;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.2s;
  border: none;
}

.btn-add-trip:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(16,185,129,0.3); color: #fff; }

/* ── Loading ── */
.spinner {
  width: 44px; height: 44px;
  border: 4px solid #e2e8f0;
  border-top-color: #10b981;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin: 0 auto;
}

@keyframes spin { to { transform: rotate(360deg); } }

/* ── CTA Section ── */
.cta-section { padding: 0 0 3rem; }

.cta-panel {
  background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 60%, #164e63 100%);
  border-radius: 2rem;
  padding: 3.5rem 2rem;
  box-shadow: 0 30px 60px rgba(15,23,42,0.15);
}

.cta-panel h2 { font-size: clamp(1.5rem, 3vw, 2.2rem); font-weight: 800; margin-bottom: 1rem; }

.btn-light { border-radius: 999px; font-weight: 600; }

/* ── Responsive ── */
@media (max-width: 767.98px) {
  .hero-title { font-size: 1.8rem; }
  .search-bar { padding: 10px 14px; }
  .place-stats { flex-wrap: wrap; gap: 8px; }
}
</style>