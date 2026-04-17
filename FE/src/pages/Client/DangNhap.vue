<template>
    <div class="login-wrapper d-flex align-items-center justify-content-center min-vh-100">
        <div class="login-overlay"></div>
        <div class="container z-index-1">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card login-card shadow-lg border-0 rounded-4 overflow-hidden">
                        <div class="card-body p-5">
                            <div class="text-center mb-4">
                                <div class="logo-wrapper mb-3">
                                    <div class="logo-icon bg-gradient-brand text-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm"
                                        style="width: 60px; height: 60px;">
                                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR3H7FDGtE7IOwPdIbFilEYiOmUAI9w-x1U6A&s"
                                            alt="Logo" class="logo-img" />
                                    </div>
                                </div>
                                <h3 class="fw-bold text-uppercase mb-1" style="letter-spacing: 0.5px; color: #2c3e50;">
                                    Chào Mừng Trở Lại
                                </h3>
                                <p class="text-muted small">Du Lịch Đà Nẵng cùng với Đà Nẵng Travel</p>
                            </div>

                            <div class="d-grid mb-4">
                                <button type="button"
                                    class="btn shadow-sm btn-white border-0 bg-light rounded-3 py-2 hover-lift d-flex justify-content-center align-items-center gap-2"
                                    @click="dangNhapGoogle">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c1/Google_%22G%22_logo.svg/3840px-Google_%22G%22_logo.svg.png"
                                        width="18" alt="Google">
                                    <span class="text-dark fw-medium small">Tiếp tục với Google</span>
                                </button>
                            </div>

                            <div class="login-separater text-center mb-4 position-relative">
                                <hr class="border-secondary opacity-25">
                                <span
                                    class="bg-white px-3 fw-bold text-muted small position-absolute top-50 start-50 translate-middle rounded-pill border">
                                    HOẶC
                                </span>
                            </div>

                            <div class="form-body">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-secondary small">Email</label>
                                    <div class="input-group input-group-lg bg-light rounded-3 transition-all"
                                        :class="{ 'border-danger': errors.email }">
                                        <span class="input-group-text border-0 bg-transparent text-primary px-3">
                                            <i class="fa-regular fa-envelope"></i>
                                        </span>
                                        <input v-model="thong_tin_dang_nhap.email" type="email"
                                            class="form-control border-0 bg-transparent fs-6"
                                            :class="{ 'is-invalid': errors.email }"
                                            placeholder="Nhập địa chỉ email của bạn" @input="errors.email = ''">
                                    </div>
                                    <div v-if="errors.email" class="text-danger small mt-1 ms-1">
                                        <i class="bi bi-exclamation-circle me-1"></i>{{ errors.email }}
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-secondary small">Mật khẩu</label>
                                    <div class="input-group input-group-lg bg-light rounded-3 transition-all"
                                        :class="{ 'border-danger': errors.mat_khau }">
                                        <span class="input-group-text border-0 bg-transparent text-primary px-3">
                                            <i class="fa-solid fa-lock-open text-primary opacity-75"></i>
                                        </span>
                                        <input v-model="thong_tin_dang_nhap.mat_khau" @keydown.enter="dangNhap()"
                                            type="password" class="form-control border-0 bg-transparent fs-6"
                                            :class="{ 'is-invalid': errors.mat_khau }" placeholder="Nhập mật khẩu"
                                            @input="errors.mat_khau = ''">
                                    </div>
                                    <div v-if="errors.mat_khau" class="text-danger small mt-1 ms-1">
                                        <i class="bi bi-exclamation-circle me-1"></i>{{ errors.mat_khau }}
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="rememberMe">
                                        <label class="form-check-label text-muted small cursor-pointer"
                                            for="rememberMe">
                                            Nhớ tài khoản
                                        </label>
                                    </div>
                                    <router-link to="/client/quen-mat-khau"
                                        class="text-primary small text-decoration-none fw-medium hover-underline">
                                        Quên mật khẩu?
                                    </router-link>
                                </div>

                                <div class="d-grid mt-4">
                                    <button @click="dangNhap()" :disabled="loading"
                                        class="btn btn-primary btn-lg rounded-3 fw-bold btn-login shadow-sm">
                                        <span v-if="loading">
                                            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                            Đang đăng nhập...
                                        </span>
                                        <span v-else>
                                            Đăng Nhập <i class="fa-solid fa-arrow-right ms-2 fs-6 align-middle"></i>
                                        </span>
                                    </button>
                                </div>

                                <div class="text-center mt-4">
                                    <p class="text-muted small mb-0">
                                        Bạn chưa có tài khoản?
                                        <router-link to="/client/dang-ky"
                                            class="text-primary fw-bold text-decoration-none ms-1 hover-underline">
                                            Đăng ký ngay
                                        </router-link>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4 text-white-50 small" style="position: relative; z-index: 1;">
                        <p>&copy; 2026 KLTN - DaNang Travel.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { saveClientRedirectPath, saveClientSession } from '../../utils/clientAuth';

const GOOGLE_AUTH_URL = 'http://127.0.0.1:8000/auth/google';
const LOGIN_API_URL = 'http://127.0.0.1:8000/api/client/dang-nhap';

export default {
    data() {
        return {
            thong_tin_dang_nhap: {},
            errors: {},
            loading: false,
        };
    },
    methods: {
        getRedirectPath() {
            const redirect = this.$route.query.redirect;

            if (typeof redirect !== 'string' || !redirect.startsWith('/')) {
                return '/';
            }

            return redirect;
        },

        luuTrangCanQuayLai() {
            saveClientRedirectPath(this.getRedirectPath());
        },

        dangNhapGoogle() {
            this.luuTrangCanQuayLai();
            window.location.href = GOOGLE_AUTH_URL;
        },

        validateClient() {
            this.errors = {};
            let valid = true;

            if (!this.thong_tin_dang_nhap.email?.trim()) {
                this.errors.email = 'Vui lòng nhập địa chỉ email.';
                valid = false;
            }

            if (!this.thong_tin_dang_nhap.mat_khau) {
                this.errors.mat_khau = 'Vui lòng nhập mật khẩu.';
                valid = false;
            } else if (this.thong_tin_dang_nhap.mat_khau.length < 6) {
                this.errors.mat_khau = 'Mật khẩu phải có ít nhất 6 ký tự.';
                valid = false;
            }

            return valid;
        },

        dangNhap() {
            if (!this.validateClient()) return;

            this.loading = true;
            axios.post(LOGIN_API_URL, this.thong_tin_dang_nhap)
                .then((res) => {
                    if (res.data.status) {
                        this.luuTrangCanQuayLai();
                        saveClientSession(res.data.token, res.data.data || {});

                        if (res.data.message) {
                            this.$toast.success(res.data.message);
                        }

                        const redirectPath = this.getRedirectPath();
                        this.$router.replace(redirectPath).catch((err) => {
                            console.error('Router push error:', err);
                            window.location.href = redirectPath;
                        });
                    } else if (res.data.message) {
                        this.$toast.error(res.data.message);
                    }
                })
                .catch((error) => {
                    const responseMessage = error.response?.data?.message;

                    if (error.response?.status !== 422 && responseMessage) {
                        this.$toast.error(responseMessage);
                        return;
                    }

                    if (error.response?.status === 401) {
                        if (responseMessage) {
                            this.$toast.error(responseMessage);
                        }
                    } else if (error.response?.status === 422) {
                        const apiErrors = error.response.data.errors || {};
                        const parsedErrors = {};

                        for (const key in apiErrors) {
                            parsedErrors[key] = Array.isArray(apiErrors[key]) ? apiErrors[key][0] : apiErrors[key];
                        }

                        this.errors = { ...this.errors, ...parsedErrors };

                        if (error.response.data.message) {
                            this.$toast.error(error.response.data.message);
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
        },
    },
};
</script>

<style scoped>
.login-wrapper {
    position: relative;
    background: url('https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=2070&auto=format&fit=crop') no-repeat center center;
    background-size: cover;
    background-attachment: fixed;
}

.login-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(15, 23, 42, 0.7) 0%, rgba(14, 165, 233, 0.5) 100%);
    backdrop-filter: blur(5px);
}

.z-index-1 {
    z-index: 1;
}

.login-card {
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(20px);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.login-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.15) !important;
}

.bg-gradient-brand {
    background: linear-gradient(135deg, #0ea5e9 0%, #3b82f6 100%);
}

.input-group.transition-all {
    transition: all 0.3s ease;
    border: 1px solid rgba(0, 0, 0, 0.08);
}

.input-group.transition-all:focus-within {
    background: #fff !important;
    border-color: #0ea5e9;
    box-shadow: 0 0 0 0.25rem rgba(14, 165, 233, 0.15);
}

.input-group .form-control:focus {
    box-shadow: none;
}

.btn-login {
    background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);
    border: none;
    transition: all 0.3s ease;
    padding: 12px 20px;
}

.btn-login:hover {
    background: linear-gradient(135deg, #0284c7 0%, #1d4ed8 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 15px rgba(37, 99, 235, 0.3) !important;
}

.hover-lift {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05) !important;
}

.hover-underline:hover {
    text-decoration: underline !important;
}

.cursor-pointer {
    cursor: pointer;
}

.logo-wrapper {
    animation: bounce-soft 3s ease-in-out infinite;
}

.logo-img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    border-radius: 50%;
}

@keyframes bounce-soft {

    0%,
    100% {
        transform: translateY(0);
    }

    50% {
        transform: translateY(-5px);
    }
}
</style>
