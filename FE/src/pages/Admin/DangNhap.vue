<template>
    <div class="login-wrapper d-flex align-items-center justify-content-center min-vh-100">
        <div class="login-overlay"></div>
        <div class="container z-index-1">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card login-card shadow-lg border-0 rounded-4 overflow-hidden">
                        <div class="card-body p-5">
                            <div class="text-center mb-5">
                                <div class="logo-wrapper mb-3">
                                    <div
                                        class="logo-icon bg-primary text-white bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center shadow"
                                        style="width: 65px; height: 65px;"
                                    >
                                        <img
                                            src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR3H7FDGtE7IOwPdIbFilEYiOmUAI9w-x1U6A&s"
                                            alt="Logo"
                                            class="logo-img"
                                            style="width: 95px; height: 95px;"
                                        />
                                    </div>
                                </div>
                                <h3 class="fw-bold text-uppercase mb-1" style="letter-spacing: 1px; color: #2c3e50;">
                                    Login Admin
                                </h3>
                            </div>

                            <div class="form-body">
                                <div class="mb-4">
                                    <label class="form-label fw-semibold text-secondary small text-uppercase">Email Dang Nhap</label>
                                    <div class="input-group input-group-lg bg-light rounded-3" :class="{ 'border-danger': errors.email }">
                                        <span class="input-group-text border-0 bg-transparent text-primary px-3">
                                            <i class="fa-solid fa-envelope"></i>
                                        </span>
                                        <input
                                            v-model="thong_tin_dang_nhap.email"
                                            @keydown.enter="dangNhap"
                                            type="email"
                                            class="form-control border-0 bg-transparent"
                                            :class="{ 'is-invalid': errors.email }"
                                            placeholder="admin@travel.com"
                                            @input="errors.email = ''"
                                        >
                                    </div>
                                    <div v-if="errors.email" class="text-danger small mt-1 ms-1">{{ errors.email }}</div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold text-secondary small text-uppercase">Mat Khau</label>
                                    <div class="input-group input-group-lg bg-light rounded-3" :class="{ 'border-danger': errors.mat_khau }">
                                        <span class="input-group-text border-0 bg-transparent text-primary px-3">
                                            <i class="fa-solid fa-lock"></i>
                                        </span>
                                        <input
                                            v-model="thong_tin_dang_nhap.mat_khau"
                                            @keydown.enter="dangNhap"
                                            type="password"
                                            class="form-control border-0 bg-transparent"
                                            :class="{ 'is-invalid': errors.mat_khau }"
                                            placeholder="Nhap mat khau"
                                            @input="errors.mat_khau = ''"
                                        >
                                    </div>
                                    <div v-if="errors.mat_khau" class="text-danger small mt-1 ms-1">{{ errors.mat_khau }}</div>
                                </div>

                                <div class="d-grid mt-5">
                                    <button
                                        @click="dangNhap"
                                        :disabled="loading"
                                        class="btn btn-primary btn-lg rounded-3 fw-bold btn-login position-relative overflow-hidden"
                                    >
                                        <span v-if="loading" class="position-relative z-index-1 d-flex align-items-center justify-content-center">
                                            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                            Dang dang nhap...
                                        </span>
                                        <span v-else class="position-relative z-index-1 d-flex align-items-center justify-content-center">
                                            <i class="fa-solid fa-right-to-bracket me-2"></i> Dang Nhap
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4 text-white-50 small" style="position: relative; z-index: 1;">
                        <p>&copy; 2026 AI Travel System. He thong phuc vu Khoa Luan Tot Nghiep.</p>
                    </div>
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
            thong_tin_dang_nhap: {
                email: '',
                mat_khau: '',
            },
            errors: {},
            loading: false,
        }
    },
    methods: {
        validateAdmin() {
            this.errors = {};
            let isValid = true;

            if (!this.thong_tin_dang_nhap.email?.trim()) {
                this.errors.email = 'Email la bat buoc.';
                isValid = false;
            }

            if (!this.thong_tin_dang_nhap.mat_khau) {
                this.errors.mat_khau = 'Mat khau la bat buoc.';
                isValid = false;
            }

            return isValid;
        },
        dangNhap() {
            if (!this.validateAdmin()) return;

            this.loading = true;
            axios.post('http://127.0.0.1:8000/api/admin/dang-nhap', this.thong_tin_dang_nhap)
                .then((res) => {
                    if (res.data.status) {
                        const adminData = res.data.data || {};

                        localStorage.setItem('key_admin', res.data.key_admin || res.data.token || '');
                        localStorage.setItem('admin_data', JSON.stringify(adminData));
                        localStorage.setItem('admin_ten', adminData.ho_ten || adminData.ten || adminData.email || 'Admin');
                        this.$toast.success(res.data.message);
                        this.$router.push('/admin/dashboard');
                        return;
                    }

                    this.$toast.error(res.data.message);
                })
                .catch((error) => {
                    if (error.response?.status === 401 || error.response?.status === 403) {
                        this.$toast.error(error.response.data.message || 'Dang nhap that bai.');
                        return;
                    }

                    if (error.response?.status === 422) {
                        const apiErrors = error.response.data.errors || {};
                        const parsedErrors = {};

                        for (const key in apiErrors) {
                            parsedErrors[key] = Array.isArray(apiErrors[key]) ? apiErrors[key][0] : apiErrors[key];
                        }

                        this.errors = { ...this.errors, ...parsedErrors };
                        this.$toast.error(error.response.data.message || 'Du lieu khong hop le.');
                        return;
                    }

                    this.$toast.error('Khong the ket noi den may chu!');
                })
                .finally(() => {
                    this.loading = false;
                });
        }
    }
}
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
    backdrop-filter: blur(10px);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.login-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.2) !important;
}

.input-group {
    transition: all 0.3s ease;
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.input-group:focus-within {
    background: #fff !important;
    border-color: #3b82f6;
    box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.15);
}

.form-control:focus {
    box-shadow: none;
}

.btn-login {
    background: linear-gradient(45deg, #2563eb, #3b82f6);
    border: none;
    transition: all 0.3s ease;
}

.btn-login:hover {
    background: linear-gradient(45deg, #1d4ed8, #2563eb);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(37, 99, 235, 0.4);
}

.btn-login:disabled {
    opacity: 0.8;
    cursor: not-allowed;
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

@keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-8px); }
    100% { transform: translateY(0px); }
}
</style>
