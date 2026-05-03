<template>
  <div class="reports-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h2 class="h3 mb-1 text-dark fw-bold">{{ pageTitle }}</h2>
        <p class="text-muted mb-0">{{ pageSubtitle }}</p>
      </div>
      <div class="d-flex align-items-center bg-white border border-secondary-subtle rounded-pill px-3 shadow-sm" style="height:42px;">
        <i class="bi bi-calendar3 text-primary me-2"></i>
        <input type="date" v-model="startDate" class="form-control border-0 bg-transparent p-0 text-secondary" style="width:125px;box-shadow:none;"/>
        <span class="text-muted fw-bold mx-2">→</span>
        <input type="date" v-model="endDate" class="form-control border-0 bg-transparent p-0 text-secondary" style="width:125px;box-shadow:none;"/>
        <div class="vr mx-3 text-secondary" style="height:20px;"></div>
        <button class="btn btn-sm btn-primary rounded-circle d-flex align-items-center justify-content-center p-0" @click="fetchReportsData" style="width:28px;height:28px;">
          <i class="bi bi-search small"></i>
        </button>
      </div>
    </div>

    <!-- ── Spinner ── -->
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary" role="status"></div>
      <p class="text-muted mt-2">Đang tải dữ liệu phân tích...</p>
    </div>

    <template v-else>
      <!-- ══════════════════════════════════════════
           NHÓM 1: TRẠNG THÁI LỊCH TRÌNH
      ══════════════════════════════════════════ -->
      <div class="section-title mb-2 mt-3">
        <i class="bi bi-bar-chart-steps me-2 text-primary"></i>
        <span class="fw-bold">1. Phân tích lịch trình theo trạng thái</span>
        <small class="text-muted ms-2">Đo lường mức độ người dùng thực sự đi theo kế hoạch AI</small>
      </div>
      <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
          <div class="stat-card border-start border-4 border-info">
            <div class="stat-icon bg-info-subtle"><i class="bi bi-pencil-square text-info"></i></div>
            <div class="stat-value text-info">{{ tripStatus.dang_len_ke_hoach || 0 }}</div>
            <div class="stat-label">Đang lên kế hoạch</div>
            <div class="stat-pct">{{ pct(tripStatus.dang_len_ke_hoach, tripStatus.tong_so) }}%</div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="stat-card border-start border-4 border-warning">
            <div class="stat-icon bg-warning-subtle"><i class="bi bi-lock-fill text-warning"></i></div>
            <div class="stat-value text-warning">{{ tripStatus.da_chot || 0 }}</div>
            <div class="stat-label">Đã chốt / Đang đi</div>
            <div class="stat-pct">{{ pct(tripStatus.da_chot, tripStatus.tong_so) }}%</div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="stat-card border-start border-4 border-success">
            <div class="stat-icon bg-success-subtle"><i class="bi bi-check-circle-fill text-success"></i></div>
            <div class="stat-value text-success">{{ tripStatus.da_hoan_thanh || 0 }}</div>
            <div class="stat-label">Đã hoàn thành</div>
            <div class="stat-pct">{{ pct(tripStatus.da_hoan_thanh, tripStatus.tong_so) }}%</div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="stat-card border-start border-4 border-danger">
            <div class="stat-icon bg-danger-subtle"><i class="bi bi-x-circle-fill text-danger"></i></div>
            <div class="stat-value text-danger">{{ tripStatus.da_huy || 0 }}</div>
            <div class="stat-label">Đã hủy</div>
            <div class="stat-pct">{{ pct(tripStatus.da_huy, tripStatus.tong_so) }}%</div>
          </div>
        </div>
      </div>

      <!-- ══════════════════════════════════════════
           NHÓM 2: PHÂN KHÚC NGÂN SÁCH
      ══════════════════════════════════════════ -->
      <div class="section-title mb-2 mt-4">
        <i class="bi bi-wallet2 me-2 text-success"></i>
        <span class="fw-bold">2. Phân tích ngân sách</span>
        <small class="text-muted ms-2">Tỉ lệ nhập ngân sách & phân khúc chi tiêu</small>
      </div>
      <div class="row g-3 mb-4">
        <div class="col-md-4">
          <div class="card card-custom h-100">
            <div class="card-body">
              <h6 class="text-muted fw-semibold mb-3">Tỉ lệ nhập ngân sách</h6>
              <div class="d-flex align-items-center gap-3 mb-2">
                <div class="fs-2 fw-bold text-success">{{ budgetFillRate }}%</div>
                <div class="small text-muted">lịch trình có điền ngân sách cụ thể</div>
              </div>
              <div class="progress" style="height:10px;border-radius:8px;">
                <div class="progress-bar bg-success" :style="{ width: budgetFillRate + '%' }"></div>
              </div>
              <div class="d-flex justify-content-between mt-1">
                <small class="text-success">Có ngân sách: {{ budgetSegments.co_ngan_sach || 0 }}</small>
                <small class="text-muted">Không: {{ budgetSegments.khong_ngan_sach || 0 }}</small>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-8">
          <div class="card card-custom h-100">
            <div class="card-body">
              <h6 class="text-muted fw-semibold mb-3">Phân khúc ngân sách</h6>
              <div class="row g-2">
                <div class="col-6" v-for="seg in budgetSegsDisplay" :key="seg.label">
                  <div class="d-flex justify-content-between align-items-center p-2 rounded-3" :style="{ background: seg.bg }">
                    <span class="small fw-bold" :style="{ color: seg.color }">{{ seg.label }}</span>
                    <span class="badge" :style="{ background: seg.color, color: '#fff' }">{{ seg.count }}</span>
                  </div>
                  <div class="progress mt-1" style="height:5px;border-radius:4px;">
                    <div class="progress-bar" :style="{ width: pct(seg.count, budgetSegments.tong) + '%', background: seg.color }"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ══════════════════════════════════════════
           NHÓM 3: SỞ THÍCH NGƯỜI DÙNG
      ══════════════════════════════════════════ -->
      <div class="section-title mb-2 mt-4">
        <i class="bi bi-heart-fill me-2 text-danger"></i>
        <span class="fw-bold">3. Phân tích sở thích người dùng</span>
        <small class="text-muted ms-2">Căn cứ để AI ưu tiên gợi ý loại hình du lịch</small>
      </div>
      <div class="row g-3 mb-4">
        <div class="col-md-7">
          <div class="card card-custom h-100">
            <div class="card-body">
              <h6 class="text-muted fw-semibold mb-3">Top danh mục được yêu thích</h6>
              <div v-for="(pref, i) in userPreferences" :key="i" class="mb-3">
                <div class="d-flex justify-content-between mb-1">
                  <span class="fw-bold small">{{ pref.ten_danh_muc }}</span>
                  <span class="small text-muted">{{ pref.so_nguoi_thich }} người · ⭐ {{ pref.diem_trung_binh }}</span>
                </div>
                <div class="progress" style="height:8px;border-radius:6px;">
                  <div class="progress-bar" :style="{ width: pct(pref.so_nguoi_thich, maxPrefCount) + '%', background: prefColors[i % prefColors.length] }"></div>
                </div>
              </div>
              <div v-if="!userPreferences.length" class="text-muted small text-center py-3">Chưa có dữ liệu sở thích</div>
            </div>
          </div>
        </div>
        <div class="col-md-5">
          <div class="card card-custom h-100">
            <div class="card-body">
              <h6 class="text-muted fw-semibold mb-3">Phân bổ mức độ yêu thích (1–5⭐)</h6>
              <div v-for="star in 5" :key="star" class="d-flex align-items-center gap-2 mb-2">
                <span class="small fw-bold" style="width:20px;">{{ star }}⭐</span>
                <div class="progress flex-grow-1" style="height:10px;border-radius:6px;">
                  <div class="progress-bar bg-warning" :style="{ width: pct(prefDistMap[star] || 0, totalPrefCount) + '%' }"></div>
                </div>
                <span class="small text-muted" style="width:30px;">{{ prefDistMap[star] || 0 }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ══════════════════════════════════════════
           NHÓM 4: HIỆU QUẢ GỢI Ý ĐỊA ĐIỂM
      ══════════════════════════════════════════ -->
      <div class="section-title mb-2 mt-4">
        <i class="bi bi-robot me-2 text-primary"></i>
        <span class="fw-bold">4. Hiệu quả gợi ý địa điểm của AI</span>
        <small class="text-muted ms-2">Đánh giá thuật toán phân bổ lịch trình</small>
      </div>
      <div class="row g-3 mb-4">
        <div class="col-md-4">
          <div class="stat-card border-start border-4 border-primary">
            <div class="stat-icon bg-primary-subtle"><i class="bi bi-geo-alt-fill text-primary"></i></div>
            <div class="stat-value text-primary">{{ avgPlacesPerTrip.avg_places || 0 }}</div>
            <div class="stat-label">Địa điểm TB / Lịch trình</div>
            <div class="stat-pct">Min {{ avgPlacesPerTrip.min_places || 0 }} · Max {{ avgPlacesPerTrip.max_places || 0 }}</div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="stat-card border-start border-4 border-indigo">
            <div class="stat-icon" style="background:#ede9fe;"><i class="bi bi-clock-history" style="color:#6d28d9;"></i></div>
            <div class="stat-value" style="color:#6d28d9;">{{ avgVisitDuration }}<small class="fs-6"> phút</small></div>
            <div class="stat-label">Thời gian tham quan TB</div>
            <div class="stat-pct">Lấy từ cột thoi_gian_du_kien</div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="stat-card border-start border-4 border-success">
            <div class="stat-icon bg-success-subtle"><i class="bi bi-graph-up-arrow text-success"></i></div>
            <div class="stat-value text-success">{{ totalTrips }}</div>
            <div class="stat-label">Tổng lịch trình đã tạo</div>
            <div class="stat-pct">Toàn thời gian</div>
          </div>
        </div>
        <div class="col-12">
          <div class="card card-custom">
            <div class="card-body">
              <h6 class="text-muted fw-semibold mb-1">📊 Ghi chú AI</h6>
              <ul class="small text-secondary mb-0 ps-3">
                <li>Trung bình <strong>{{ avgPlacesPerTrip.avg_places || '...' }} địa điểm/lịch trình</strong> — nếu &lt; 4 thì AI đang phân bổ quá ít, nếu &gt; 9 thì có thể đang "nhồi nhét".</li>
                <li>Thời gian tham quan TB <strong>{{ avgVisitDuration }} phút</strong> — dùng để AI vạch khung giờ hợp lý cho từng slot.</li>
                <li>Tỉ lệ lịch trình hoàn thành: <strong>{{ pct(tripStatus.da_hoan_thanh, tripStatus.tong_so) }}%</strong> — chứng minh tính thực tế của lịch trình AI.</li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <!-- ══════════════════════════════════════════
           NHÓM 5: ĐÁNH GIÁ THEO NHÓM ĐỊA ĐIỂM
      ══════════════════════════════════════════ -->
      <div class="section-title mb-2 mt-4">
        <i class="bi bi-star-fill me-2 text-warning"></i>
        <span class="fw-bold">5. Đánh giá theo nhóm địa điểm</span>
        <small class="text-muted ms-2">Chất lượng dịch vụ theo từng loại hình du lịch</small>
      </div>
      <div class="row g-3 mb-5">
        <div class="col-md-7">
          <div class="card card-custom h-100">
            <div class="card-body">
              <h6 class="text-muted fw-semibold mb-3">Điểm đánh giá TB theo danh mục</h6>
              <div v-for="(cat, i) in ratingByCategory" :key="i" class="mb-3">
                <div class="d-flex justify-content-between mb-1">
                  <span class="fw-bold small">{{ cat.ten_danh_muc }}</span>
                  <span class="small"><i class="bi bi-star-fill text-warning me-1"></i>{{ cat.avg_rating }} <span class="text-muted">({{ cat.tong_danh_gia }} đánh giá)</span></span>
                </div>
                <div class="progress" style="height:8px;border-radius:6px;">
                  <div class="progress-bar bg-warning" :style="{ width: ((cat.avg_rating / 5) * 100) + '%' }"></div>
                </div>
              </div>
              <div v-if="!ratingByCategory.length" class="text-muted small text-center py-3">Chưa có dữ liệu đánh giá</div>
            </div>
          </div>
        </div>
        <div class="col-md-5">
          <div class="card card-custom h-100">
            <div class="card-body">
              <h6 class="text-muted fw-semibold mb-3">Phân bổ chất lượng (1⭐ → 5⭐)</h6>
              <div v-for="star in 5" :key="star" class="d-flex align-items-center gap-2 mb-2">
                <span class="small fw-bold" style="width:20px;">{{ star }}⭐</span>
                <div class="progress flex-grow-1" style="height:10px;border-radius:6px;">
                  <div class="progress-bar" :style="{ width: pct(ratingDistMap[star] || 0, totalRatingCount) + '%', background: ratingStarColors[star - 1] }"></div>
                </div>
                <span class="small text-muted" style="width:35px;">{{ ratingDistMap[star] || 0 }}</span>
              </div>
              <div class="mt-3 p-2 rounded-3 bg-light text-center">
                <div class="fs-4 fw-bold text-warning">{{ avgRating }}</div>
                <div class="small text-muted">Điểm TB toàn hệ thống / 5.0</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script>
import api from '../../services/api.js';

export default {
  name: 'Reports',
  data() {
    return {
      loading: false,
      startDate: '',
      endDate: '',
      reportType: 'overview',

      // Basic
      totalTrips: 0,
      totalUsers: 0,
      totalGroups: 0,
      avgBudget: 0,
      activeUsers: 0,
      totalReviews: 0,
      avgRating: 0,

      // 1. Trip status
      tripStatus: {},

      // 2. Budget
      budgetSegments: {},
      budgetFillRate: 0,

      // 3. Preferences
      userPreferences: [],
      preferenceDistribution: [],

      // 4. AI efficiency
      avgPlacesPerTrip: {},
      avgVisitDuration: 0,

      // 5. Rating by category
      ratingByCategory: [],
      ratingDistribution: [],

      prefColors: ['#3874ff','#10b981','#f59e0b','#ec4899','#8b5cf6','#0ea5e9'],
      ratingStarColors: ['#ef4444','#f97316','#eab308','#22c55e','#3874ff'],
    };
  },
  computed: {
    pageTitle() { return 'Báo cáo Phân tích AI — Du lịch Đà Nẵng'; },
    pageSubtitle() { return 'Thống kê phục vụ đánh giá hiệu quả hệ thống AI đề xuất lịch trình.'; },
    budgetSegsDisplay() {
      return [
        { label: '< 1 triệu', count: this.budgetSegments.duoi_1tr || 0, color: '#10b981', bg: '#ecfdf5' },
        { label: '1 – 3 triệu', count: this.budgetSegments.tu_1_3tr || 0, color: '#3874ff', bg: '#eff6ff' },
        { label: '3 – 5 triệu', count: this.budgetSegments.tu_3_5tr || 0, color: '#f59e0b', bg: '#fffbeb' },
        { label: '> 5 triệu', count: this.budgetSegments.tren_5tr || 0, color: '#ef4444', bg: '#fef2f2' },
      ];
    },
    maxPrefCount() {
      return Math.max(...this.userPreferences.map(p => p.so_nguoi_thich), 1);
    },
    totalPrefCount() {
      return this.preferenceDistribution.reduce((s, d) => s + (d.so_luong || 0), 0) || 1;
    },
    prefDistMap() {
      const m = {};
      this.preferenceDistribution.forEach(d => { m[d.muc_do_yeu_thich] = d.so_luong; });
      return m;
    },
    totalRatingCount() {
      return this.ratingDistribution.reduce((s, d) => s + (d.so_luong || 0), 0) || 1;
    },
    ratingDistMap() {
      const m = {};
      this.ratingDistribution.forEach(d => { m[d.so_sao] = d.so_luong; });
      return m;
    },
  },
  mounted() { this.fetchReportsData(); },
  methods: {
    pct(val, total) {
      if (!total || total === 0) return 0;
      return Math.round((val / total) * 100);
    },
    async fetchReportsData() {
      this.loading = true;
      try {
        let url = '/admin/statistics?time_filter=year';
        if (this.startDate && this.endDate) url += `&start_date=${this.startDate}&end_date=${this.endDate}`;
        const res = await api.get(url);
        const data = res.data?.data;
        if (!data) return;

        this.totalTrips       = data.total_trips || 0;
        this.totalUsers       = data.total_users || 0;
        this.totalGroups      = data.total_groups || 0;
        this.avgBudget        = data.avg_budget || 0;
        this.activeUsers      = data.active_users || 0;
        this.totalReviews     = data.total_reviews || 0;
        this.avgRating        = data.avg_rating || 0;

        this.tripStatus            = data.trip_status || {};
        this.budgetSegments        = data.budget_segments || {};
        this.budgetFillRate        = data.budget_fill_rate || 0;
        this.userPreferences       = data.user_preferences || [];
        this.preferenceDistribution= data.preference_distribution || [];
        this.avgPlacesPerTrip      = data.avg_places_per_trip || {};
        this.avgVisitDuration      = data.avg_visit_duration || 0;
        this.ratingByCategory      = data.rating_by_category || [];
        this.ratingDistribution    = data.rating_distribution || [];
      } catch (e) {
        console.error('Lỗi tải reports:', e);
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>

<style scoped>
.reports-container { animation: fadeIn 0.4s ease-out forwards; }
@keyframes fadeIn { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }

.section-title { display:flex; align-items:center; padding:0.5rem 0; border-bottom:2px solid #f1f5f9; margin-bottom:1rem; }

.stat-card {
  background:#fff;
  border-radius:14px;
  padding:1.25rem 1rem;
  box-shadow:0 4px 20px rgba(0,0,0,0.04);
  border:1px solid rgba(0,0,0,0.04);
  transition: transform 0.2s, box-shadow 0.2s;
}
.stat-card:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(0,0,0,0.08); }
.stat-icon { width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;margin-bottom:0.75rem; }
.stat-value { font-size:2rem;font-weight:800;line-height:1; }
.stat-label { font-size:0.82rem;color:#64748b;font-weight:500;margin-top:0.25rem; }
.stat-pct { font-size:0.78rem;color:#94a3b8;margin-top:0.15rem; }

.card-custom { background:#fff;border-radius:16px;border:1px solid rgba(0,0,0,0.05);box-shadow:0 4px 20px rgba(0,0,0,0.03);transition:transform 0.2s,box-shadow 0.2s; }
.card-custom:hover { transform:translateY(-2px);box-shadow:0 10px 25px rgba(0,0,0,0.06); }
</style>
