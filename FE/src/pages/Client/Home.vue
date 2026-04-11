<template>
  <div class="home-page">
    <section class="hero-section overflow-hidden">
      <div class="hero-shape hero-shape-one"></div>
      <div class="hero-shape hero-shape-two"></div>
      <div class="container position-relative">
        <div class="row align-items-center min-vh-100 py-5">
          <div class="col-lg-6" data-reveal>
            <span class="hero-tag">Khám phá Đà Nẵng theo cách của bạn</span>
            <h1 class="hero-title">
              Lên lịch trình du lịch Đà Nẵng thật nhanh cho một chuyến đi thật đáng nhớ.
            </h1>
            <p class="hero-description">
              Từ biển xanh, ẩm thực địa phương đến những điểm check-in nổi bật, bạn có thể dễ dàng
              chọn hành trình phù hợp với quỹ thời gian, ngân sách và phong cách du lịch của riêng mình.
            </p>

            <div class="d-flex flex-wrap gap-3">
              <router-link to="/tao-lich-trinh" class="btn btn-brand btn-lg px-4">
                Tạo lịch trình ngay
              </router-link>
              <a href="#overview" class="btn btn-light btn-lg px-4 btn-watch">
                <i class="bi bi-play-circle me-2"></i>Xem tổng quan
              </a>
            </div>

            <div class="row g-3 mt-4">
              <div
                v-for="item in quickStats"
                :key="item.label"
                class="col-sm-4"
              >
                <div class="quick-stat">
                  <strong>{{ item.value }}</strong>
                  <span>{{ item.label }}</span>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-6 mt-5 mt-lg-0" data-reveal>
            <div class="hero-visual">
              <div class="floating-card summary-card">
                <span class="mini-label">Lịch trình gợi ý</span>
                <h5>Đà Nẵng 3 ngày 2 đêm</h5>
                <p>Gói hành trình cân bằng giữa nghỉ dưỡng, vui chơi và trải nghiệm địa phương.</p>
                <div class="trip-tags">
                  <span>Biển Mỹ Khê</span>
                  <span>Bà Nà Hills</span>
                  <span>Ẩm thực</span>
                </div>
              </div>

              <div class="dashboard-mockup">
                <div class="mockup-header">
                  <span></span>
                  <span></span>
                  <span></span>
                </div>
                <div class="mockup-content">
                  <div class="mockup-sidebar">
                    <div class="sidebar-pill active"></div>
                    <div class="sidebar-pill"></div>
                    <div class="sidebar-pill"></div>
                    <div class="sidebar-pill"></div>
                  </div>
                  <div class="mockup-main">
                    <div class="row g-3 mb-3">
                      <div class="col-7">
                        <div class="mockup-panel tall plan-panel">
                          <span class="panel-chip">Gợi ý trong ngày</span>
                          <h6>Ngày 1</h6>
                          <ul class="mock-list">
                            <li>07:00 - Biển Mỹ Khê</li>
                            <li>10:00 - Cầu Rồng</li>
                            <li>14:00 - Bảo tàng Chăm</li>
                            <li>18:30 - Chợ đêm Helio</li>
                          </ul>
                        </div>
                      </div>
                      <div class="col-5">
                        <div class="mockup-panel small mb-3 budget-panel">
                          <span class="panel-chip">Chi phí dự kiến</span>
                          <strong>2.850.000đ</strong>
                          <small>cho 2 người / 3N2Đ</small>
                        </div>
                        <div class="mockup-panel small accent score-panel">
                          <span class="panel-chip">Phù hợp</span>
                          <strong>Rất hợp</strong>
                          <small>cho chuyến đi thiên về biển và check-in</small>
                        </div>
                      </div>
                    </div>
                    <div class="mockup-panel chart">
                      <div class="chart-head">
                        <span class="panel-chip">Chi tiêu tham khảo</span>
                      </div>
                      <div class="budget-bars">
                        <div
                          v-for="budget in budgetBreakdown"
                          :key="budget.label"
                          class="budget-row"
                        >
                          <div class="d-flex justify-content-between mb-1">
                            <span>{{ budget.label }}</span>
                            <strong>{{ budget.amount }}</strong>
                          </div>
                          <div class="budget-track">
                            <div class="budget-fill" :style="{ width: budget.width }"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="floating-card users-card">
                <div class="d-flex align-items-center justify-content-between">
                  <div>
                    <span class="mini-label">Nhóm đồng hành</span>
                    <h6 class="mb-1">Tạo nhóm du lịch</h6>
                  </div>
                  <div class="avatar-stack">
                    <span>A</span>
                    <span>B</span>
                    <span>+</span>
                  </div>
                </div>
              </div>

              <div class="floating-card route-card">
                <i class="bi bi-geo-alt-fill"></i>
                <div>
                  <span class="mini-label">Lộ trình đề xuất</span>
                  <p class="mb-0">Sắp xếp điểm đến mượt hơn để bạn tận hưởng chuyến đi thoải mái hơn.</p>
                </div>
              </div>

              <div class="floating-card mini-card interest-card">
                <span class="mini-label">Phong cách du lịch</span>
                <div class="mini-card-list">
                  <span v-for="item in interests" :key="item">{{ item }}</span>
                </div>
              </div>

              <div class="floating-card mini-card destination-card">
                <span class="mini-label">Nên ghé thử</span>
                <ul class="mini-destination-list">
                  <li v-for="place in suggestedPlaces" :key="place.name">
                    <strong>{{ place.name }}</strong>
                    <small>{{ place.type }}</small>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="overview" class="py-5">
      <div class="container">
        <div class="row g-4">
          <div
            v-for="feature in features"
            :key="feature.title"
            class="col-md-6 col-xl-3"
            data-reveal
          >
            <div class="feature-box h-100">
              <div class="icon-badge">
                <i :class="feature.icon"></i>
              </div>
              <h4>{{ feature.title }}</h4>
              <p class="mb-0">{{ feature.description }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="about-section py-5">
      <div class="container">
        <div class="section-title text-center mb-5" data-reveal>
          <span class="section-kicker">Về Chuyến Đi</span>
          <h2>Biến kế hoạch du lịch Đà Nẵng thành một trải nghiệm dễ chịu ngay từ lúc bắt đầu</h2>
          <p>
            Không cần mất hàng giờ để gom thông tin từ nhiều nơi, bạn vẫn có thể nhanh chóng
            hình dung một hành trình hợp lý, rõ ràng và phù hợp với sở thích của mình.
          </p>
        </div>

        <div class="row align-items-center g-4">
          <div class="col-lg-6" data-reveal>
            <div class="about-visual">
              <div class="about-grid">
                <div class="about-tile tile-large tile-photo tile-my-khe">
                  <div class="tile-overlay">
                    <span class="tile-label">Biển Mỹ Khê</span>
                    <h4>Điểm dừng lý tưởng cho một ngày nhẹ nhàng bên biển và thành phố.</h4>
                    <p>Bắt đầu hành trình với bình minh, cà phê ven biển và những điểm tham quan gần trung tâm.</p>
                  </div>
                </div>
                <div class="about-tile tile-accent tile-photo tile-ba-na">
                  <div class="tile-overlay tile-overlay-dark">
                    <span class="tile-label tile-label-light">Bà Nà Hills</span>
                    <h5 class="text-white">Một ngày trọn vẹn để vui chơi và ngắm cảnh</h5>
                    <ul class="tile-list tile-list-light">
                      <li>Cáp treo</li>
                      <li>Cầu Vàng</li>
                      <li>Khu làng Pháp</li>
                    </ul>
                  </div>
                </div>
                <div class="about-tile tile-soft">
                  <span class="tile-label">Chi phí tham khảo</span>
                  <h5>3N2Đ cho 2 người</h5>
                  <p class="mb-0">
                    Một mức chi tiêu vừa phải để bạn có thể tận hưởng chuyến đi thoải mái mà vẫn dễ kiểm soát ngân sách.
                  </p>
                  <div class="budget-mini">
                    <div>
                      <strong>2.850.000đ</strong>
                      <small>Tổng dự kiến</small>
                    </div>
                    <div>
                      <strong>92%</strong>
                      <small>AI Match</small>
                    </div>
                  </div>
                </div>
              </div>
              <div class="about-badge">
                <strong>95%</strong>
                <span>người dùng hoàn tất lịch trình nhanh hơn</span>
              </div>
            </div>
          </div>

          <div class="col-lg-6" data-reveal>
            <div class="about-copy">
              <h3>Lên kế hoạch nhanh hơn để dành nhiều thời gian hơn cho trải nghiệm thật.</h3>
              <p>
                Một chuyến đi đẹp không nên bắt đầu bằng cảm giác quá tải. Trang web này giúp bạn chọn điểm đến,
                hình dung lịch trình và cân đối chi phí theo cách trực quan hơn, để việc chuẩn bị cũng trở thành
                một phần thú vị của chuyến du lịch.
              </p>

              <div
                v-for="point in aboutPoints"
                :key="point"
                class="about-point"
              >
                <i class="bi bi-check-circle-fill"></i>
                <span>{{ point }}</span>
              </div>

              <div class="stats-grid mt-4">
                <div
                  v-for="stat in counters"
                  :key="stat.label"
                  class="stat-box"
                >
                  <strong>{{ stat.value }}</strong>
                  <span>{{ stat.label }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="services-section py-5">
      <div class="container">
        <div class="section-title text-center mb-5" data-reveal>
          <span class="section-kicker">Trải Nghiệm</span>
          <h2>Mọi thứ bạn cần cho một chuyến đi Đà Nẵng gọn gàng và dễ theo dõi</h2>
          <p>
            Từ lúc lên ý tưởng cho đến khi hoàn thành chuyến đi, mọi thông tin đều được sắp xếp
            để bạn dễ chọn, dễ lưu và dễ quay lại khi cần.
          </p>
        </div>

        <div class="row g-4">
          <div
            v-for="service in services"
            :key="service.title"
            class="col-md-6 col-xl-4"
            data-reveal
          >
            <div class="service-card h-100">
              <div class="service-icon">
                <i :class="service.icon"></i>
              </div>
              <h4>{{ service.title }}</h4>
              <p>{{ service.description }}</p>
              <a href="#" class="service-link">
                Xem thêm <i class="bi bi-arrow-right-short"></i>
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="cta-section py-5">
      <div class="container" data-reveal>
        <div class="cta-panel text-center">
          <span class="section-kicker text-white-50">Sẵn Sàng Cho Chuyến Đi</span>
          <h2 class="text-white">Bắt đầu lên lịch trình Đà Nẵng của riêng bạn ngay hôm nay</h2>
          <p class="text-white-50 mb-4">
            Chọn phong cách du lịch bạn yêu thích, tham khảo chi phí và lưu lại hành trình phù hợp nhất
            cho chuyến đi sắp tới.
          </p>
          <div class="d-flex justify-content-center flex-wrap gap-3">
            <router-link to="/tao-lich-trinh" class="btn btn-light btn-lg px-4">
              Tạo lịch trình
            </router-link>
            <router-link to="/dia-diem" class="btn btn-outline-light btn-lg px-4">
              Xem địa điểm
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <section class="testimonial-section py-5">
      <div class="container">
        <div class="section-title text-center mb-5" data-reveal>
          <span class="section-kicker">Cảm Hứng Du Lịch</span>
          <h2>Những điều làm cho việc chuẩn bị chuyến đi trở nên dễ chịu hơn</h2>
          <p>
            Khi thông tin được sắp xếp rõ ràng và hành trình được gợi ý hợp lý, việc lên kế hoạch cũng trở nên nhẹ nhàng hơn nhiều.
          </p>
        </div>

        <div class="row g-4">
          <div
            v-for="review in reviews"
            :key="review.name"
            class="col-md-6 col-xl-4"
            data-reveal
          >
            <div class="review-card h-100">
              <div class="review-stars mb-3">
                <i v-for="star in 5" :key="star" class="bi bi-star-fill"></i>
              </div>
              <p class="review-text">"{{ review.content }}"</p>
              <div class="d-flex align-items-center gap-3 mt-4">
                <div class="review-avatar overflow-hidden">
                  <img
                    :src="review.avatar"
                    :alt="review.name"
                    class="review-avatar-image"
                  >
                </div>
                <div>
                  <h6 class="mb-1">{{ review.name }}</h6>
                  <span class="text-muted">{{ review.role }}</span>
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
export default {
  name: 'Home',
  data() {
    return {
      quickStats: [
        { value: '2N1Đ - 4N3Đ', label: 'gợi ý theo thời gian' },
        { value: 'Biển - Ẩm thực', label: 'chọn theo sở thích' },
        { value: 'Chi phí rõ ràng', label: 'dễ cân đối ngân sách' },
      ],
      interests: ['Biển', 'Ẩm thực', 'Check-in', 'Tâm linh'],
      suggestedPlaces: [
        { name: 'Biển Mỹ Khê', type: 'Nghỉ dưỡng' },
        { name: 'Bà Nà Hills', type: 'Vui chơi' },
        { name: 'Ngũ Hành Sơn', type: 'Tâm linh' },
      ],
      budgetBreakdown: [
        { label: 'Di chuyển', amount: '650.000đ', width: '58%' },
        { label: 'Ăn uống', amount: '900.000đ', width: '78%' },
        { label: 'Vé tham quan', amount: '850.000đ', width: '70%' },
        { label: 'Phát sinh', amount: '450.000đ', width: '36%' },
      ],
      features: [
        {
          icon: 'bi bi-calendar-week',
          title: 'Lịch trình theo từng ngày',
          description: 'Dễ hình dung chuyến đi của bạn sẽ diễn ra như thế nào từ sáng đến tối.',
        },
        {
          icon: 'bi bi-wallet2',
          title: 'Chi phí dễ theo dõi',
          description: 'Tham khảo nhanh mức chi tiêu để chuẩn bị ngân sách thoải mái hơn.',
        },
        {
          icon: 'bi bi-heart',
          title: 'Chọn đúng phong cách',
          description: 'Từ nghỉ dưỡng, ẩm thực đến check-in, mỗi hành trình đều có chất riêng.',
        },
        {
          icon: 'bi bi-cpu',
          title: 'Gợi ý phù hợp hơn',
          description: 'Hành trình được sắp xếp để tiết kiệm thời gian và thuận tiện hơn khi di chuyển.',
        },
      ],
      aboutPoints: [
        'Chọn nhanh những điểm nên ghé để không mất thời gian tìm kiếm quá nhiều nơi.',
        'Dễ lưu lại kế hoạch cho chuyến đi cùng bạn bè, người thân hoặc nhóm đồng hành.',
        'Phù hợp cho cả chuyến đi ngắn cuối tuần lẫn lịch trình nhiều ngày tại Đà Nẵng.',
      ],
      counters: [
        { value: 'Nhanh hơn', label: 'ít thời gian chuẩn bị' },
        { value: 'Rõ ràng hơn', label: 'lịch trình dễ theo dõi' },
        { value: 'Thoải mái hơn', label: 'chi phí dễ cân đối' },
        { value: 'Trọn vẹn hơn', label: 'trải nghiệm hợp sở thích' },
      ],
      services: [
        {
          icon: 'bi bi-pencil-square',
          title: 'Tạo lịch trình',
          description: 'Bắt đầu từ vài lựa chọn cơ bản để nhanh chóng có một kế hoạch du lịch phù hợp.',
        },
        {
          icon: 'bi bi-compass',
          title: 'Địa điểm nổi bật',
          description: 'Khám phá những nơi đáng ghé tại Đà Nẵng theo từng phong cách trải nghiệm khác nhau.',
        },
        {
          icon: 'bi bi-collection',
          title: 'Lịch trình của tôi',
          description: 'Lưu lại các kế hoạch yêu thích để xem lại, chỉnh sửa hoặc dùng cho chuyến đi tiếp theo.',
        },
        {
          icon: 'bi bi-diagram-3',
          title: 'Đi cùng bạn bè',
          description: 'Phù hợp cho những chuyến đi có nhiều người, cần cùng nhau thống nhất kế hoạch.',
        },
        {
          icon: 'bi bi-chat-quote',
          title: 'Đánh giá sau chuyến đi',
          description: 'Lưu lại cảm nhận về địa điểm để những hành trình sau ngày càng phù hợp hơn.',
        },
        {
          icon: 'bi bi-person-circle',
          title: 'Lưu dấu hành trình',
          description: 'Giữ lại lịch sử các chuyến đi để dễ quay lại, tham khảo hoặc chia sẻ với người khác.',
        },
      ],
      reviews: [
        {
          initials: 'TH',
          name: 'Thu Hương',
          avatar: 'https://i.pravatar.cc/120?img=32',
          content: 'Trước đây mình hay mất khá nhiều thời gian để tự gom địa điểm ăn chơi ở Đà Nẵng. Giờ nhìn lịch trình gợi ý sẵn thấy dễ chọn hơn hẳn và đỡ rối hơn rất nhiều.',
        },
        {
          initials: 'MN',
          name: 'Minh Ngọc',
          avatar: 'https://i.pravatar.cc/120?img=47',
          content: 'Đi đông người thì phần khó nhất là thống nhất đi đâu và chi bao nhiêu. Mình thấy phần gợi ý điểm đến kèm chi phí tham khảo rất tiện, cả nhóm chốt kế hoạch nhanh hơn nhiều.',
        },
        {
          initials: 'AL',
          name: 'Anh Linh',
          avatar: 'https://i.pravatar.cc/120?img=12',
          content: 'Mình thích kiểu có sẵn một hành trình rõ ràng để tham khảo rồi chỉnh lại theo nhu cầu của nhà mình. Chuẩn bị cho chuyến đi vì thế cũng nhẹ đầu hơn và cảm giác yên tâm hơn.',
        },
      ],
      observer: null,
    };
  },
  mounted() {
    this.$nextTick(() => {
      const elements = this.$el?.querySelectorAll('[data-reveal]') || [];

      if (!elements.length) {
        return;
      }

      if (typeof IntersectionObserver === 'undefined') {
        elements.forEach((element) => element.classList.add('is-visible'));
        return;
      }

      this.observer = new IntersectionObserver(
        (entries) => {
          entries.forEach((entry) => {
            if (entry.isIntersecting) {
              entry.target.classList.add('is-visible');
              this.observer?.unobserve(entry.target);
            }
          });
        },
        { threshold: 0.15 }
      );

      elements.forEach((element) => {
        const rect = element.getBoundingClientRect();
        const isInViewport = rect.top < window.innerHeight && rect.bottom > 0;

        if (isInViewport) {
          element.classList.add('is-visible');
          return;
        }

        this.observer.observe(element);
      });
    });
  },
  beforeUnmount() {
    if (this.observer) {
      this.observer.disconnect();
    }
  },
};
</script>

<style scoped>
.home-page {
  color: #4f5d75;
}

[data-reveal] {
  opacity: 0;
  transform: translateY(36px);
  transition: opacity 0.8s ease, transform 0.8s ease;
}

[data-reveal].is-visible {
  opacity: 1;
  transform: translateY(0);
}

.hero-section {
  position: relative;
  background:
    radial-gradient(circle at 10% 20%, rgba(58, 134, 255, 0.14), transparent 28%),
    radial-gradient(circle at 90% 15%, rgba(0, 200, 150, 0.16), transparent 20%),
    linear-gradient(180deg, #f6fbff 0%, #ffffff 48%, #f8fbff 100%);
}

.hero-shape {
  position: absolute;
  border-radius: 50%;
  filter: blur(8px);
  opacity: 0.45;
}

.hero-shape-one {
  width: 340px;
  height: 340px;
  top: 90px;
  right: -80px;
  background: rgba(65, 184, 131, 0.18);
  animation: floatY 8s ease-in-out infinite;
}

.hero-shape-two {
  width: 260px;
  height: 260px;
  bottom: 20px;
  left: -100px;
  background: rgba(58, 134, 255, 0.12);
  animation: floatY 10s ease-in-out infinite reverse;
}

.hero-tag,
.section-kicker {
  display: inline-block;
  padding: 0.5rem 0.95rem;
  border-radius: 999px;
  background: rgba(29, 111, 214, 0.08);
  color: #1d6fd6;
  font-size: 0.78rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  margin-bottom: 1.25rem;
}

.hero-title {
  font-size: clamp(2.6rem, 5vw, 4.4rem);
  line-height: 1.08;
  color: #22304a;
  margin-bottom: 1.2rem;
}

.hero-description,
.section-title p,
.about-copy p,
.service-card p,
.review-text {
  line-height: 1.85;
}

.hero-description {
  font-size: 1.06rem;
  max-width: 40rem;
  margin-bottom: 1.75rem;
}

.btn-brand {
  background: linear-gradient(135deg, #10b981, #0ea5a4);
  border: none;
  color: #fff;
  box-shadow: 0 18px 35px rgba(16, 185, 129, 0.22);
}

.btn-brand:hover {
  color: #fff;
  transform: translateY(-2px);
}

.btn-watch {
  border: 1px solid rgba(34, 48, 74, 0.08);
  color: #22304a;
}

.quick-stat,
.feature-box,
.service-card,
.review-card,
.stat-box {
  background: #fff;
  border-radius: 1.25rem;
  border: 1px solid rgba(34, 48, 74, 0.08);
  box-shadow: 0 20px 45px rgba(31, 41, 55, 0.06);
}

.quick-stat {
  padding: 1rem 1.1rem;
}

.quick-stat strong,
.stat-box strong {
  display: block;
  font-size: 1.4rem;
  color: #22304a;
}

.quick-stat span,
.stat-box span {
  font-size: 0.92rem;
  color: #6c7a92;
}

.hero-visual {
  position: relative;
  min-height: 640px;
}

.dashboard-mockup {
  position: relative;
  margin: 3.5rem auto 0;
  max-width: 520px;
  background: linear-gradient(180deg, #ffffff 0%, #f7fbff 100%);
  border-radius: 2rem;
  padding: 1rem;
  border: 1px solid rgba(34, 48, 74, 0.08);
  box-shadow: 0 30px 60px rgba(31, 41, 55, 0.12);
  animation: floatY 6s ease-in-out infinite;
}

.mockup-header {
  display: flex;
  gap: 0.45rem;
  padding: 0.4rem 0.25rem 0.9rem;
}

.mockup-header span {
  width: 0.7rem;
  height: 0.7rem;
  border-radius: 50%;
  background: #d7e2f1;
}

.mockup-content {
  display: grid;
  grid-template-columns: 82px 1fr;
  gap: 1rem;
}

.mockup-sidebar {
  background: #eef5ff;
  border-radius: 1.25rem;
  padding: 1rem 0.9rem;
}

.sidebar-pill {
  height: 0.75rem;
  border-radius: 999px;
  background: #c5d7f2;
  margin-bottom: 1rem;
}

.sidebar-pill.active {
  background: linear-gradient(90deg, #10b981, #0ea5a4);
}

.mockup-panel {
  border-radius: 1.2rem;
  background: #edf4fc;
}

.mockup-panel.tall {
  min-height: 214px;
  background: linear-gradient(180deg, #dff1ee 0%, #eef8ff 100%);
}

.mockup-panel.small {
  min-height: 96px;
  padding: 1rem;
}

.mockup-panel.small.accent {
  background: linear-gradient(135deg, #d8eaff, #e8fdf6);
}

.mockup-panel.chart {
  min-height: 140px;
  background:
    linear-gradient(180deg, rgba(255, 255, 255, 0.3), rgba(255, 255, 255, 0.3)),
    linear-gradient(135deg, #e4efff, #effff8);
  padding: 1rem;
}

.floating-card {
  position: absolute;
  background: rgba(255, 255, 255, 0.92);
  backdrop-filter: blur(12px);
  border-radius: 1.25rem;
  padding: 1rem 1.15rem;
  border: 1px solid rgba(34, 48, 74, 0.08);
  box-shadow: 0 25px 45px rgba(31, 41, 55, 0.1);
}

.summary-card {
  top: 0;
  left: 0;
  max-width: 240px;
  animation: floatY 7s ease-in-out infinite;
}

.users-card {
  right: 0;
  top: 120px;
  width: 240px;
  animation: floatY 9s ease-in-out infinite reverse;
}

.route-card {
  left: 20px;
  bottom: 32px;
  max-width: 270px;
  display: flex;
  gap: 0.85rem;
  align-items: flex-start;
  animation: floatY 8s ease-in-out infinite;
}

.interest-card {
  right: 18px;
  bottom: 175px;
  width: 210px;
  animation: floatY 11s ease-in-out infinite reverse;
}

.destination-card {
  right: 36px;
  bottom: -4px;
  width: 220px;
  animation: floatY 10s ease-in-out infinite;
}

.route-card i {
  color: #10b981;
  font-size: 1.2rem;
}

.mini-label {
  display: block;
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: #7e8ba3;
  margin-bottom: 0.35rem;
}

.avatar-stack {
  display: flex;
}

.avatar-stack span {
  width: 2rem;
  height: 2rem;
  border-radius: 50%;
  background: linear-gradient(135deg, #1d6fd6, #2cc8bb);
  color: #fff;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  margin-left: -0.35rem;
  border: 2px solid #fff;
  font-size: 0.78rem;
}

.trip-tags,
.mini-card-list {
  display: flex;
  flex-wrap: wrap;
  gap: 0.45rem;
  margin-top: 0.85rem;
}

.trip-tags span,
.mini-card-list span,
.panel-chip {
  display: inline-flex;
  align-items: center;
  padding: 0.35rem 0.7rem;
  border-radius: 999px;
  background: rgba(29, 111, 214, 0.08);
  color: #1d6fd6;
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.04em;
}

.plan-panel {
  padding: 1rem;
}

.plan-panel h6,
.budget-panel strong,
.score-panel strong {
  color: #22304a;
  margin-top: 0.75rem;
  margin-bottom: 0.5rem;
}

.mock-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.mock-list li {
  padding: 0.45rem 0;
  border-bottom: 1px solid rgba(34, 48, 74, 0.08);
  font-size: 0.88rem;
  color: #52606d;
}

.mock-list li:last-child {
  border-bottom: none;
}

.budget-panel small,
.score-panel small,
.mini-destination-list small {
  color: #6c7a92;
}

.chart-head {
  margin-bottom: 1rem;
}

.budget-bars {
  display: flex;
  flex-direction: column;
  gap: 0.8rem;
}

.budget-row span,
.budget-row strong {
  font-size: 0.82rem;
}

.budget-track {
  height: 0.55rem;
  border-radius: 999px;
  background: rgba(34, 48, 74, 0.08);
  overflow: hidden;
}

.budget-fill {
  height: 100%;
  border-radius: 999px;
  background: linear-gradient(90deg, #10b981, #1d6fd6);
}

.mini-destination-list {
  list-style: none;
  padding: 0;
  margin: 0.75rem 0 0;
}

.mini-destination-list li {
  display: flex;
  flex-direction: column;
  gap: 0.15rem;
  padding: 0.45rem 0;
  border-bottom: 1px solid rgba(34, 48, 74, 0.08);
}

.mini-destination-list li:last-child {
  border-bottom: none;
}

.section-title {
  max-width: 760px;
  margin-left: auto;
  margin-right: auto;
}

.section-title h2,
.about-copy h3,
.cta-panel h2 {
  color: #22304a;
  margin-bottom: 1rem;
}

.feature-box,
.service-card,
.review-card {
  padding: 1.6rem;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.feature-box:hover,
.service-card:hover,
.review-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 28px 55px rgba(31, 41, 55, 0.12);
}

.icon-badge,
.service-icon,
.review-avatar {
  width: 3.4rem;
  height: 3.4rem;
  border-radius: 1rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.icon-badge {
  background: linear-gradient(135deg, #eaf4ff, #eefcf7);
  color: #1d6fd6;
  font-size: 1.25rem;
  margin-bottom: 1rem;
}

.about-section {
  background: linear-gradient(180deg, #ffffff 0%, #f7fbff 100%);
}

.about-visual {
  position: relative;
  min-height: 420px;
}

.about-grid {
  display: grid;
  grid-template-columns: 1.2fr 0.8fr;
  gap: 1rem;
}

.about-tile {
  border-radius: 1.8rem;
  min-height: 180px;
  box-shadow: 0 28px 55px rgba(31, 41, 55, 0.08);
  padding: 1.4rem;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.tile-large {
  min-height: 380px;
  background: linear-gradient(145deg, #dff4ff, #effff8);
}

.tile-accent {
  background: linear-gradient(145deg, #203a43, #2c5364);
}

.tile-soft {
  background: linear-gradient(145deg, #fff1dc, #fff9f1);
}

.tile-label {
  display: inline-flex;
  align-items: center;
  width: fit-content;
  padding: 0.35rem 0.7rem;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.6);
  color: #1d6fd6;
  font-size: 0.72rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-bottom: 0.9rem;
}

.tile-label-light {
  background: rgba(255, 255, 255, 0.18);
  color: #ffffff;
}

.tile-large h4,
.tile-soft h5,
.tile-accent h5 {
  margin-bottom: 0.8rem;
  color: #22304a;
}

.tile-large p,
.tile-soft small {
  color: #52606d;
}

.tile-points {
  display: flex;
  flex-wrap: wrap;
  gap: 0.45rem;
  margin-top: 1rem;
}

.tile-points span {
  display: inline-flex;
  padding: 0.4rem 0.7rem;
  border-radius: 999px;
  background: rgba(29, 111, 214, 0.08);
  color: #1d6fd6;
  font-size: 0.78rem;
  font-weight: 600;
}

.tile-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.tile-list li {
  padding: 0.45rem 0;
  border-bottom: 1px solid rgba(255, 255, 255, 0.16);
}

.tile-list li:last-child {
  border-bottom: none;
}

.tile-list-light {
  color: rgba(255, 255, 255, 0.88);
}

.tile-photo {
  position: relative;
  overflow: hidden;
  background-size: cover;
  background-position: center;
}

.tile-photo::before {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, rgba(12, 24, 36, 0.12) 0%, rgba(12, 24, 36, 0.7) 100%);
}

.tile-my-khe {
  background-image:
    linear-gradient(145deg, rgba(223, 244, 255, 0.12), rgba(239, 255, 248, 0.08)),
    url('https://static.vinwonders.com/2022/04/bai-bien-my-khe-da-nang-2.jpg');
}

.tile-ba-na {
  background-image:
    linear-gradient(145deg, rgba(32, 58, 67, 0.1), rgba(44, 83, 100, 0.15)),
    url('https://media.vietravel.com/images/Content/nui-chua-ba-na-1.png');
}

.tile-overlay {
  position: relative;
  z-index: 1;
  margin-top: auto;
  color: #ffffff;
}

.tile-overlay h4,
.tile-overlay h5,
.tile-overlay p {
  color: #ffffff;
}

.tile-overlay-dark {
  padding-top: 1.5rem;
}

.budget-mini {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 0.9rem;
  margin-top: 1rem;
}

.budget-mini div {
  background: rgba(255, 255, 255, 0.55);
  border-radius: 1rem;
  padding: 0.9rem;
}

.budget-mini strong {
  display: block;
  color: #22304a;
  font-size: 1.05rem;
  margin-bottom: 0.2rem;
}

.about-badge {
  position: absolute;
  right: 18px;
  bottom: 30px;
  background: #fff;
  border-radius: 1.2rem;
  padding: 1rem 1.1rem;
  max-width: 220px;
  box-shadow: 0 25px 45px rgba(31, 41, 55, 0.12);
}

.about-badge strong {
  display: block;
  font-size: 1.8rem;
  color: #10b981;
}

.about-point {
  display: flex;
  gap: 0.75rem;
  align-items: flex-start;
  margin-top: 1rem;
}

.about-point i {
  color: #10b981;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 1rem;
}

.stat-box {
  padding: 1rem 1.1rem;
}

.services-section {
  background: #fff;
}

.service-icon {
  background: linear-gradient(135deg, #1d6fd6, #2cc8bb);
  color: #fff;
  font-size: 1.25rem;
  margin-bottom: 1rem;
}

.service-link {
  color: #1d6fd6;
  text-decoration: none;
  font-weight: 600;
}

.cta-panel {
  background: linear-gradient(135deg, #1d3557, #2a9d8f);
  border-radius: 2rem;
  padding: 4rem 2rem;
  box-shadow: 0 30px 60px rgba(29, 53, 87, 0.18);
}

.testimonial-section {
  background: linear-gradient(180deg, #f7fbff 0%, #ffffff 100%);
}

.review-stars {
  color: #f4b400;
  display: flex;
  gap: 0.3rem;
}

.review-avatar {
  background: linear-gradient(135deg, #1d6fd6, #2cc8bb);
  color: #fff;
  font-weight: 700;
  overflow: hidden;
  flex-shrink: 0;
}

.review-avatar-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

@keyframes floatY {
  0%,
  100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-16px);
  }
}

@media (max-width: 991.98px) {
  .hero-visual {
    min-height: auto;
    padding-bottom: 2rem;
  }

  .dashboard-mockup {
    margin-top: 7rem;
  }

  .floating-card {
    position: relative;
    inset: auto;
    margin-bottom: 1rem;
    max-width: 100%;
    width: 100%;
  }

  .about-visual {
    min-height: auto;
  }

  .interest-card,
  .destination-card {
    width: 100%;
  }
}

@media (max-width: 767.98px) {
  .hero-title {
    font-size: 2.4rem;
  }

  .stats-grid {
    grid-template-columns: 1fr;
  }

  .about-grid {
    grid-template-columns: 1fr;
  }

  .tile-large {
    min-height: 240px;
  }

  .about-badge {
    position: relative;
    right: auto;
    bottom: auto;
    margin-top: 1rem;
  }

  .cta-panel {
    padding: 2.8rem 1.25rem;
  }
}
</style>
