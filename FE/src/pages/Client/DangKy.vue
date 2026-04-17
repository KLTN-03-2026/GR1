<template>
  <div class="split-register-layout overflow-hidden vh-100">
    <div class="row g-0 h-100">
      <!-- Left Side: Image -->
      <div class="col-lg-6 d-none d-lg-block register-image-side position-relative h-100">
        <div class="overlay">
          <div class="brand-content position-relative z-index-1">
            <div class="mb-4 slide-in-up">
              <span class="badge bg-white text-primary rounded-pill px-3 py-2 fw-bold text-uppercase shadow-sm">
                <i class="fa-solid fa-plane-departure me-1"></i> DaNang Travel
              </span>
            </div>
            <h1 class="display-4 fw-black text-white mb-4 slide-in-up delay-1 lh-sm">
              Bắt đầu<br>
              <span class="text-info">hành trình mới</span><br>
              của bạn.
            </h1>
            <p class="fs-5 text-white opacity-75 slide-in-up delay-2 fw-light" style="max-width: 450px;">
              Du lịch cùng Travel Planner AI lên kế hoạch thông minh và trải nghiệm du lịch dễ dàng hơn
              bao
              giờ hết.
            </p>
          </div>
        </div>
      </div>

      <!-- Right Side: Form -->
      <div class="col-lg-6 bg-white position-relative h-100 form-side">
        <!-- Inner scroll flex container -->
        <div class="form-shell d-flex flex-column justify-content-center h-100">
          <!-- Nút Home ở góc -->
          <router-link to="/"
            class="btn-home position-absolute top-0 end-0 mt-3 me-3 d-none d-sm-flex align-items-center"
            style="z-index: 10;">
            <div class="icon-box me-2"><i class="fa-solid fa-house"></i></div>
            Trang chủ
          </router-link>

          <form class="form-container w-100 slide-in-left mx-auto" style="max-width: 500px;"
            @submit.prevent="dangKyTaiKhoan">
            <div class="text-center form-header">
              <div class="logo-box mx-auto mb-2 shadow-sm">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR3H7FDGtE7IOwPdIbFilEYiOmUAI9w-x1U6A&s"
                  alt="Logo" class="logo-img" />
              </div>
              <h2 class="fw-bold text-dark mb-1 fs-4">Tạo tài khoản mới</h2>
            </div>

            <div class="row g-3">
              <!-- Họ và tên -->
              <div class="col-12">
                <label class="form-label fw-semibold text-dark small mb-2">Họ và tên <span
                    class="text-danger">*</span></label>
                <div class="custom-input-group" :class="{ 'has-error': errors.ten }">
                  <div class="input-icon"><i class="fa-regular fa-user"></i></div>
                  <input v-model="user.ten" type="text" placeholder="Nguyễn Văn A" @input="errors.ten = ''" />
                </div>
                <span class="error-msg" v-if="errors.ten"><i class="fa-solid fa-circle-exclamation me-1"></i>{{
                  errors.ten }}</span>
              </div>

              <!-- Email -->
              <div class="col-12">
                <label class="form-label fw-semibold text-dark small mb-2">Địa chỉ Email <span
                    class="text-danger">*</span></label>
                <div class="custom-input-group" :class="{ 'has-error': errors.email }">
                  <div class="input-icon"><i class="fa-regular fa-envelope"></i></div>
                  <input v-model="user.email" type="email" placeholder="example@email.com" @input="errors.email = ''" />
                </div>
                <span class="error-msg" v-if="errors.email"><i class="fa-solid fa-circle-exclamation me-1"></i>{{
                  errors.email }}</span>
              </div>

              <!-- Số điện thoại -->
              <div class="col-12">
                <label class="form-label fw-semibold text-dark small mb-2">Số điện thoại <span
                    class="text-danger">*</span></label>
                <div class="custom-input-group" :class="{ 'has-error': errors.so_dien_thoai }">
                  <div class="input-icon"><i class="fa-solid fa-phone"></i></div>
                  <input v-model="user.so_dien_thoai" type="text" placeholder="09xx xxx xxx"
                    @input="errors.so_dien_thoai = ''" />
                </div>
                <span class="error-msg" v-if="errors.so_dien_thoai"><i
                    class="fa-solid fa-circle-exclamation me-1"></i>{{ errors.so_dien_thoai
                    }}</span>
              </div>

              <!-- Mật khẩu -->
              <div class="col-12">
                <label class="form-label fw-semibold text-dark small mb-2">Mật khẩu <span
                    class="text-danger">*</span></label>
                <div class="custom-input-group" :class="{ 'has-error': errors.mat_khau }">
                  <div class="input-icon"><i class="fa-solid fa-lock"></i></div>
                  <input v-model="user.mat_khau" type="password" placeholder="••••••••" @input="errors.mat_khau = ''" />
                </div>
                <span class="error-msg" v-if="errors.mat_khau"><i class="fa-solid fa-circle-exclamation me-1"></i>{{
                  errors.mat_khau }}</span>
              </div>

              <!-- Xác nhận MK -->
              <div class="col-12">
                <label class="form-label fw-semibold text-dark small mb-2">Xác nhận MK <span
                    class="text-danger">*</span></label>
                <div class="custom-input-group" :class="{ 'has-error': errors.re_password }">
                  <div class="input-icon"><i class="fa-solid fa-circle-check"></i></div>
                  <input v-model="user.re_password" type="password" placeholder="••••••••"
                    @input="errors.re_password = ''" />
                </div>
                <span class="error-msg" v-if="errors.re_password"><i class="fa-solid fa-circle-exclamation me-1"></i>{{
                  errors.re_password }}</span>
              </div>
            </div>

            <!-- Điều khoản -->
            <div class="terms-block">
              <div class="form-check custom-check-modern d-flex align-items-center">
                <input v-model="agreeTerms" class="form-check-input mt-0 me-3" type="checkbox" id="terms"
                  @change="errors.terms = ''">
                <label class="form-check-label text-muted small" for="terms" style="cursor: pointer;">
                  Tôi đồng ý với <a href="#" class="text-primary fw-bold text-decoration-none">Điều
                    khoản
                    dịch vụ</a> và <a href="#" class="text-primary fw-bold text-decoration-none">Chính
                    sách bảo mật</a>
                </label>
              </div>
              <span class="error-msg mt-2 block" v-if="errors.terms"><i
                  class="fa-solid fa-circle-exclamation me-1"></i>{{ errors.terms }}</span>
            </div>

            <!-- Nút Đăng ký -->
            <button type="submit" :disabled="loading"
              class="btn btn-primary-gradient w-100 btn-lg action-btn mb-3 shadow-sm hover-lift d-flex align-items-center justify-content-center">
              <span v-if="loading"><span class="spinner-border spinner-border-sm me-2"></span> Đang xử
                lý...</span>
              <span v-else class="fw-bold fs-6"><i class="fa-solid fa-user-plus me-2"></i> Đăng ký tài
                khoản</span>
            </button>

            <!-- OR -->
            <div class="divider d-flex align-items-center divider-compact">
              <div class="line flex-grow-1"></div>
              <span class="px-3 text-muted small fw-medium text-uppercase tracking-wide">Hoặc tham gia
                bằng</span>
              <div class="line flex-grow-1"></div>
            </div>

            <button type="button"
              class="btn btn-social w-100 btn-lg action-btn text-dark d-flex align-items-center justify-content-center hover-lift">
              <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" width="22" class="me-3" />
              <span class="fw-semibold fs-6">Đăng ký bằng Google</span>
            </button>

            <!-- Trở về Đăng nhập -->
            <div class="text-center switch-auth">
              <p class="text-secondary small">Đã có tài khoản?
                <router-link to="/client/dang-nhap"
                  class="text-primary fw-bold text-decoration-none hover-link ms-1">Đăng nhập
                  ngay</router-link>
              </p>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
export default {
  data() {
    return {
      user: {
        ten: '',
        email: '',
        so_dien_thoai: '',
        mat_khau: '',
        re_password: ''
      },
      agreeTerms: false,
      errors: {},
      loading: false,
    }
  },
  methods: {
    clearErrors() {
      this.errors = {};
    },

    validateClient() {
      this.clearErrors();
      let valid = true;

      if (!this.user.ten?.trim()) {
        this.errors.ten = 'Vui lòng nhập họ và tên.';
        valid = false;
      }
      if (!this.user.email?.trim()) {
        this.errors.email = 'Vui lòng nhập địa chỉ email.';
        valid = false;
      } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.user.email)) {
        this.errors.email = 'Email không hợp lệ.';
        valid = false;
      }
      if (!this.user.so_dien_thoai?.trim()) {
        this.errors.so_dien_thoai = 'Vui lòng nhập số điện thoại.';
        valid = false;
      }
      if (!this.user.mat_khau) {
        this.errors.mat_khau = 'Vui lòng nhập mật khẩu.';
        valid = false;
      } else if (this.user.mat_khau.length < 6) {
        this.errors.mat_khau = 'Mật khẩu phải có ít nhất 6 ký tự.';
        valid = false;
      }
      if (!this.user.re_password) {
        this.errors.re_password = 'Vui lòng xác nhận mật khẩu.';
        valid = false;
      } else if (this.user.mat_khau !== this.user.re_password) {
        this.errors.re_password = 'Mật khẩu xác nhận không khớp.';
        valid = false;
      }
      if (!this.agreeTerms) {
        this.errors.terms = 'Bạn cần đồng ý với điều khoản.';
        valid = false;
      }
      return valid;
    },

    dangKyTaiKhoan() {
      if (!this.validateClient()) return;

      this.loading = true;
      const payload = {
        ho_va_ten: this.user.ten,
        email: this.user.email,
        so_dien_thoai: this.user.so_dien_thoai,
        password: this.user.mat_khau,
        password_confirmation: this.user.re_password,
      };

      axios.post('http://localhost:8000/api/client/dang-ky', payload)
        .then((res) => {
          if (res.data.status) {
            this.$toast.success(res.data.message);
            this.$router.push('/client/dang-nhap');
          } else {
            this.$toast.error(res.data.message || 'Đăng ký thất bại!');
          }
        })
        .catch((error) => {
          if (error.response?.status === 422) {
            const apiErrors = error.response.data.validationErrorsRaw || error.response.data.errors || {};
            const responseMessage = error.response.data.message || '';
            const fieldAliasMap = {
              ho_va_ten: 'ten',
              password: 'mat_khau',
              password_confirmation: 're_password',
            };
            const normalizedErrors = Object.entries(apiErrors).reduce((acc, [key, value]) => {
              acc[fieldAliasMap[key] || key] = value;
              return acc;
            }, {});
            const allApiMessages = Object.values(normalizedErrors).flatMap((value) => Array.isArray(value) ? value : [value]).filter(Boolean);
            const duplicateEmailMessages = [
              ...allApiMessages.filter((message) =>
                /email/i.test(message) && /tồn tại|đã được sử dụng|already been taken|already exists|unique|trùng|đã đăng ký/i.test(message)
              ),
              ...(!allApiMessages.length && /email/i.test(responseMessage) && /tồn tại|đã được sử dụng|already been taken|already exists|unique|trùng|đã đăng ký/i.test(responseMessage)
                ? [responseMessage]
                : []),
            ];
            const shouldOnlyShowEmailError = duplicateEmailMessages.length > 0;
            const sourceErrors = shouldOnlyShowEmailError ? { email: duplicateEmailMessages } : normalizedErrors;
            const parsedErrors = {};
            const allMessages = [];

            for (const key in sourceErrors) {
              const fieldMessages = Array.isArray(sourceErrors[key]) ? sourceErrors[key] : [sourceErrors[key]];
              parsedErrors[key] = fieldMessages[0];
              allMessages.push(...fieldMessages.filter(Boolean));
            }

            this.errors = shouldOnlyShowEmailError ? parsedErrors : { ...this.errors, ...parsedErrors };
            if (!shouldOnlyShowEmailError) {
              this.$toast.error(allMessages.join('\n') || responseMessage || 'Dữ liệu không hợp lệ!');
            }
          } else if (error.response?.status === 500) {
            this.$toast.error('Lỗi máy chủ, vui lòng thử lại sau.');
          } else {
            this.$toast.error('Không thể kết nối đến máy chủ!');
          }
        })
        .finally(() => {
          this.loading = false;
        });
    }
  },
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

.logo-img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  border-radius: 50%;
}

.split-register-layout {
  font-family: 'Inter', sans-serif;
  background-color: #ffffff;
  overflow-x: hidden;
}

/* === LEFT SIDE === */
.register-image-side {
  background: url('https://kyluc.vn/Userfiles/Upload/images/Download/2024/6/4/fa2cdf9ae95e410397fbe7e4fa428bee.png') no-repeat center center;
  background-size: cover;
}

.register-image-side .overlay {
  position: absolute;
  inset: 0;
  /* Soft dark gradient that blends into branding blue */
  background: linear-gradient(180deg, rgba(15, 23, 42, 0.4) 0%, rgba(14, 165, 233, 0.9) 100%);
  display: flex;
  align-items: flex-end;
  padding: 5rem 4rem;
}

.fw-black {
  font-weight: 900;
  letter-spacing: -1px;
}

/* === RIGHT SIDE (FORM) === */
.form-container {
  max-width: 440px;
  margin: 0 auto;
}

.form-side {
  overflow-y: auto;
}

.form-shell {
  padding: 1.25rem 1.5rem;
}

.form-header {
  margin-bottom: 1rem;
}

.logo-box {
  width: 80px;
  height: 80px;
  border-radius: 16px;
  /* background: linear-gradient(135deg, #0ea5e9 0%, #3b82f6 100%); */
  display: flex;
  align-items: center;
  justify-content: center;
  /* transform: rotate(-10deg); */
}

/* Custom Input Modern */
.custom-input-group {
  display: flex;
  align-items: center;
  background-color: #f8fafc;
  border: 1.5px solid #f1f5f9;
  border-radius: 12px;
  padding: 2px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.custom-input-group:focus-within {
  background-color: #ffffff;
  border-color: #0ea5e9;
  box-shadow: 0 4px 20px rgba(14, 165, 233, 0.1);
  transform: translateY(-1px);
}

.custom-input-group.has-error {
  border-color: #ef4444;
  background-color: #fef2f2;
}

.input-icon {
  width: 40px;
  display: flex;
  justify-content: center;
  align-items: center;
  color: #94a3b8;
  font-size: 1.1rem;
  transition: color 0.3s;
}

.custom-input-group:focus-within .input-icon {
  color: #0ea5e9;
}

.custom-input-group.has-error .input-icon {
  color: #ef4444;
}

.custom-input-group input {
  border: none;
  background: transparent;
  padding: 7px 14px 7px 0;
  width: 100%;
  outline: none;
  font-size: 0.95rem;
  color: #1e293b;
  font-weight: 500;
}

.custom-input-group input::placeholder {
  color: #94a3b8;
  font-weight: 400;
}

.error-msg {
  color: #ef4444;
  font-size: 0.85rem;
  margin-top: 6px;
  display: block;
  font-weight: 500;
}

/* Checkbox */
.custom-check-modern .form-check-input {
  width: 1.4em;
  height: 1.4em;
  border: 2px solid #cbd5e1;
  border-radius: 6px;
  transition: all 0.2s;
}

.custom-check-modern .form-check-input:checked {
  background-color: #0ea5e9;
  border-color: #0ea5e9;
}

.custom-check-modern .form-check-input:focus {
  box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.2);
}

/* Buttons */
.btn-primary-gradient {
  background: linear-gradient(135deg, #0ea5e9 0%, #3b82f6 100%);
  color: white;
  border: none;
  padding: 8px 20px;
  border-radius: 12px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.btn-primary-gradient:hover:not(:disabled) {
  background: linear-gradient(135deg, #0284c7 0%, #2563eb 100%);
  box-shadow: 0 10px 25px rgba(14, 165, 233, 0.3) !important;
}

.btn-social {
  background-color: #ffffff;
  border: 1.5px solid #e2e8f0;
  padding: 8px 20px;
  border-radius: 12px;
  transition: all 0.3s ease;
}

.btn-social:hover {
  background-color: #f8fafc;
  border-color: #cbd5e1;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

/* Home Button */
.btn-home {
  background: #ffffff;
  color: #475569;
  padding: 6px 16px 6px 6px;
  border-radius: 40px;
  font-size: 0.85rem;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.3s ease;
  border: 1px solid #e2e8f0;
}

.btn-home .icon-box {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: #f1f5f9;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #64748b;
  transition: all 0.3s ease;
}

.btn-home:hover {
  background: #f8fafc;
  border-color: #cbd5e1;
  color: #0ea5e9;
}

.btn-home:hover .icon-box {
  background: #e0f2fe;
  color: #0ea5e9;
}

.hover-lift {
  transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.hover-lift:hover {
  transform: translateY(-3px);
}

.hover-link {
  position: relative;
  padding-bottom: 2px;
}

.hover-link::after {
  content: '';
  position: absolute;
  width: 100%;
  height: 2px;
  bottom: 0;
  left: 0;
  background-color: #0ea5e9;
  transform: scaleX(0);
  transform-origin: bottom right;
  transition: transform 0.3s ease-out;
}

.hover-link:hover::after {
  transform: scaleX(1);
  transform-origin: bottom left;
}

/* Miscellaneous */
.divider .line {
  height: 1px;
  background-color: #e2e8f0;
}

.tracking-wide {
  letter-spacing: 0.05em;
}

.terms-block {
  margin-top: 0.85rem;
  margin-bottom: 0.85rem;
}

.divider-compact {
  margin: 0.85rem 0;
}

.switch-auth {
  margin-top: 1rem;
}

.action-btn {
  min-height: 40px;
}

/* Animations */
.slide-in-up {
  animation: slideInUp 0.8s cubic-bezier(0.25, 1, 0.5, 1) forwards;
  opacity: 0;
  transform: translateY(40px);
}

.slide-in-left {
  animation: slideInLeft 0.8s cubic-bezier(0.25, 1, 0.5, 1) forwards;
  opacity: 0;
  transform: translateX(40px);
}

.delay-1 {
  animation-delay: 0.2s;
}

.delay-2 {
  animation-delay: 0.4s;
}

@keyframes slideInUp {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes slideInLeft {
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@media (max-width: 991.98px) {
  .form-shell {
    height: auto;
    min-height: 100vh;
    padding: 1.5rem 1.25rem 2rem;
  }

  .form-container {
    padding-top: 1.5rem;
    padding-bottom: 1.5rem;
  }
}

@media (min-width: 992px) and (max-height: 900px) {
  .register-image-side .overlay {
    padding: 3rem;
  }

  .brand-content .badge {
    font-size: 0.75rem;
  }

  .brand-content h1 {
    font-size: 2.6rem;
    margin-bottom: 1rem !important;
  }

  .brand-content p {
    font-size: 1rem !important;
  }

  .form-shell {
    padding: 1rem 1.5rem;
  }

  .form-container {
    max-width: 460px;
  }

  .logo-box {
    width: 72px;
    height: 72px;
  }

  .form-header {
    margin-bottom: 0.75rem;
  }

  .row.g-3 {
    --bs-gutter-y: 0.85rem;
  }

  .form-label {
    margin-bottom: 0.35rem !important;
    font-size: 0.82rem;
  }

  .custom-input-group {
    border-radius: 10px;
  }

  .input-icon {
    width: 36px;
    font-size: 1rem;
  }

  .custom-input-group input {
    padding: 6px 12px 6px 0;
    font-size: 0.92rem;
  }

  .error-msg {
    margin-top: 4px;
    font-size: 0.78rem;
  }

  .custom-check-modern .form-check-input {
    width: 1.2em;
    height: 1.2em;
  }

  .custom-check-modern .form-check-label {
    font-size: 0.8rem !important;
    line-height: 1.4;
  }
}
</style>
