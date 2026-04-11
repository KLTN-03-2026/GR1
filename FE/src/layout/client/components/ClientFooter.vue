<template>
  <footer class="client-footer pt-5 pb-4">
    <div class="container px-xl-4">
      <div class="row g-4 mb-4">
        <!-- BRANDING -->
        <div class="col-lg-4 pe-lg-5">
          <div class="footer-brand d-flex align-items-center gap-2 mb-3">
            <div class="logo-wrapper">
              <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR3H7FDGtE7IOwPdIbFilEYiOmUAI9w-x1U6A&s"
                alt="Logo" class="logo-img" />
            </div>
            <span class="brand-text">DaNang <span class="fw-light">Travel</span></span>
          </div>
          <p class="footer-text mb-4 pe-lg-4">
            Khám phá trọn vẹn vẻ đẹp của Đà Nẵng qua lăng kính thông minh. DaNang Travel giúp bạn lên kế hoạch chuyến đi, tìm kiếm địa điểm check-in, ẩm thực và tâm linh nhanh chóng, dễ dàng.
          </p>
          <div class="social-links d-flex gap-3">
            <a href="#" class="social-btn px-2 rounded-circle d-flex align-items-center justify-content-center" title="Facebook">
              <i class="bi bi-facebook"></i>
            </a>
            <a href="#" class="social-btn px-2 rounded-circle d-flex align-items-center justify-content-center" title="Instagram">
              <i class="bi bi-instagram"></i>
            </a>
            <a href="#" class="social-btn px-2 rounded-circle d-flex align-items-center justify-content-center" title="Twitter">
              <i class="bi bi-twitter-x"></i>
            </a>
          </div>
        </div>

        <!-- LINKS -->
        <div class="col-sm-6 col-lg-2 ms-lg-auto mt-5 mt-lg-0">
          <h6 class="footer-heading">Khám Phá</h6>
          <ul class="list-unstyled footer-links">
            <li><router-link to="/check-in">Điểm Check In</router-link></li>
            <li><router-link to="/giai-tri">Khu Vui Chơi</router-link></li>
            <li><router-link to="/am-thuc">Đặc Sản Ẩm Thực</router-link></li>
            <li><router-link to="/tam-linh">Văn Hóa Tâm Linh</router-link></li>
          </ul>
        </div>

        <div class="col-sm-6 col-lg-2 mt-5 mt-lg-0">
          <h6 class="footer-heading">Nền Tảng</h6>
          <ul class="list-unstyled footer-links">
            <li><router-link to="/tao-lich-trinh">Tạo lịch trình</router-link></li>
            <li><router-link to="/nhom-du-lich">Hội nhóm du lịch</router-link></li>
            <li><router-link to="/blog">Blog & Cẩm nang</router-link></li>
            <li><router-link to="/support">Trung tâm hỗ trợ</router-link></li>
          </ul>
        </div>

        <div class="col-lg-3 mt-5 mt-lg-0">
          <h6 class="footer-heading">Liên Hệ</h6>
          <ul class="list-unstyled contact-info mt-3">
            <li class="d-flex gap-3 mb-3">
              <div class="contact-icon d-flex align-items-center justify-content-center text-primary">
                <i class="bi bi-geo-alt-fill"></i>
              </div>
              <div>
                <span class="d-block fw-semibold text-white">Trụ sở chính</span>
                <span class="footer-text">123 Trần Phú, Hải Châu, TP. Đà Nẵng</span>
              </div>
            </li>
            <li class="d-flex gap-3 mb-3">
              <div class="contact-icon d-flex align-items-center justify-content-center text-success">
                <i class="bi bi-envelope-fill"></i>
              </div>
              <div>
                <span class="d-block fw-semibold text-white">Email trợ giúp</span>
                <span class="footer-text">support@danangtravel.com</span>
              </div>
            </li>
            <li class="d-flex gap-3">
              <div class="contact-icon d-flex align-items-center justify-content-center text-warning">
                <i class="bi bi-telephone-fill"></i>
              </div>
              <div>
                <span class="d-block fw-semibold text-white">Hotline</span>
                <span class="footer-text">0905 123 456</span>
              </div>
            </li>
          </ul>
        </div>
      </div>

      <div class="footer-bottom mt-5 pt-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
        <span class="footer-copyright">© 2026 <strong>DaNang Travel</strong>. Đồ án KLTN AI Travel.</span>
        <div class="bottom-links d-flex gap-3">
          <a href="#">Điều khoản bảo mật</a>
          <a href="#">Chính sách cung cấp</a>
        </div>
      </div>
    </div>
  </footer>
</template>

<script>
export default {
  name: 'ClientFooter',
};
</script>

<style scoped>
/* -------------------------------------
   FOOTER BASE
------------------------------------- */
.client-footer {
  background: #0a192f;
  color: rgba(255, 255, 255, 0.75);
  font-family: 'Inter', sans-serif;
  border-top: 1px solid rgba(255, 255, 255, 0.05);
  position: relative;
  overflow: hidden;
}

/* Optional subtle glowing orb effect in the background */
.client-footer::before {
  content: '';
  position: absolute;
  top: 0;
  left: 10%;
  width: 300px;
  height: 300px;
  background: radial-gradient(circle, rgba(0, 198, 255, 0.03) 0%, rgba(10, 25, 47, 0) 70%);
  border-radius: 50%;
  pointer-events: none;
}

/* -------------------------------------
   BRANDING
------------------------------------- */
.logo-wrapper {
  overflow: hidden;
  border-radius: 50%;
  border: 2px solid rgba(255,255,255,0.7);
  box-shadow: 0 4px 15px rgba(0, 198, 255, 0.2);
  background: #fff;
  width: 46px; 
  height: 46px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.logo-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.brand-text {
  color: #fff;
  font-weight: 800;
  font-size: 1.5rem;
  letter-spacing: -0.5px;
}

.footer-text {
  color: #8892b0;
  line-height: 1.7;
  font-size: 0.95rem;
}

/* -------------------------------------
   SOCIAL LINKS
------------------------------------- */
.social-btn {
  width: 40px;
  height: 40px;
  background: rgba(255, 255, 255, 0.05);
  color: #ccd6f6;
  text-decoration: none;
  border: 1px solid rgba(255, 255, 255, 0.1);
  transition: all 0.3s ease;
  font-size: 1.1rem;
}
.social-btn:hover {
  background: linear-gradient(135deg, #00c6ff, #0072ff);
  color: #fff;
  border-color: transparent;
  transform: translateY(-3px);
  box-shadow: 0 6px 15px rgba(0, 198, 255, 0.3);
}

/* -------------------------------------
   MENUS & LINKS
------------------------------------- */
.footer-heading {
  color: #e2e8f0;
  font-size: 1.1rem;
  font-weight: 700;
  margin-bottom: 1.5rem;
  letter-spacing: 0.5px;
  position: relative;
  display: inline-block;
}
.footer-heading::after {
  content: '';
  position: absolute;
  left: 0;
  bottom: -6px;
  width: 40%;
  height: 2px;
  background: linear-gradient(90deg, #00c6ff, transparent);
  border-radius: 2px;
}

.footer-links li {
  margin-bottom: 0.85rem;
}
.footer-links a {
  color: #8892b0;
  text-decoration: none;
  font-size: 0.95rem;
  transition: all 0.25s ease;
  display: inline-block;
}
.footer-links a:hover {
  color: #00c6ff;
  transform: translateX(4px);
}

/* -------------------------------------
   CONTACT INFO
------------------------------------- */
.contact-info li {
  font-size: 0.95rem;
}
.contact-icon {
  width: 36px;
  height: 36px;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 10px;
  font-size: 1.1rem;
  flex-shrink: 0;
  border: 1px solid rgba(255, 255, 255, 0.08);
}

/* -------------------------------------
   FOOTER BOTTOM
------------------------------------- */
.footer-bottom {
  border-top: 1px solid rgba(255, 255, 255, 0.08);
  font-size: 0.9rem;
}
.footer-copyright {
  color: #8892b0;
}
.footer-copyright strong {
  color: #ccd6f6;
  font-weight: 600;
}
.bottom-links a {
  color: #8892b0;
  text-decoration: none;
  transition: color 0.2s ease;
}
.bottom-links a:hover {
  color: #00c6ff;
}
</style>
