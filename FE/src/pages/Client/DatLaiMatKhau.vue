<template>
    <section class="reset-page">
        <div class="container reset-page__container">
            <div class="row justify-content-center align-items-center g-4">
                <div class="col-12 col-lg-5">
                    <aside class="reset-hero">
                        <span class="reset-hero__badge">
                            <i class="fa-solid fa-lock"></i>
                            Đặt lại mật khẩu
                        </span>

                        <h1 class="reset-hero__title">
                            Tạo mật khẩu mới để tiếp tục sử dụng tài khoản.
                        </h1>

                        <p class="reset-hero__desc">
                            Hãy chọn một mật khẩu đủ mạnh, dễ nhớ với bạn và khó đoán với người khác.
                        </p>
                    </aside>
                </div>

                <div class="col-12 col-lg-5">
                    <div class="reset-card">
                        <div v-if="status !== 'success'">
                            <div class="reset-card__header text-center">
                                <div class="reset-card__logo">
                                    <img
                                        src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR3H7FDGtE7IOwPdIbFilEYiOmUAI9w-x1U6A&s"
                                        alt="DaNang Travel"
                                        class="reset-card__logo-image"
                                    />
                                </div>
                                <h2 class="reset-card__title">Tạo mật khẩu mới</h2>
                                <p class="reset-card__subtitle mb-0">
                                    Nhập mật khẩu mới cho tài khoản của bạn.
                                </p>
                            </div>

                            <form class="reset-form" @submit.prevent="handleSubmit" novalidate>
                                <div class="reset-form__field">
                                    <label for="new-password" class="form-label reset-form__label">Mật khẩu mới</label>
                                    <div class="reset-input" :class="{ 'reset-input--error': errors.mat_khau_moi }">
                                        <span class="reset-input__icon">
                                            <i class="fa-solid fa-lock"></i>
                                        </span>
                                        <input
                                            id="new-password"
                                            ref="passwordInput"
                                            v-model.trim="form.mat_khau_moi"
                                            :type="showPassword ? 'text' : 'password'"
                                            class="reset-input__control"
                                            placeholder="Nhập mật khẩu mới"
                                            autocomplete="new-password"
                                            @input="clearFieldError('mat_khau_moi')"
                                        />
                                        <button
                                            type="button"
                                            class="reset-input__toggle"
                                            @click="showPassword = !showPassword"
                                        >
                                            <i :class="showPassword ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye'"></i>
                                        </button>
                                    </div>
                                    <p v-if="errors.mat_khau_moi" class="reset-form__error">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ errors.mat_khau_moi }}
                                    </p>
                                </div>

                                <div class="reset-form__field">
                                    <label for="confirm-password" class="form-label reset-form__label">Xác nhận mật khẩu</label>
                                    <div class="reset-input" :class="{ 'reset-input--error': errors.xac_nhan_mat_khau }">
                                        <span class="reset-input__icon">
                                            <i class="fa-solid fa-shield-halved"></i>
                                        </span>
                                        <input
                                            id="confirm-password"
                                            v-model.trim="form.xac_nhan_mat_khau"
                                            :type="showConfirmPassword ? 'text' : 'password'"
                                            class="reset-input__control"
                                            placeholder="Nhập lại mật khẩu mới"
                                            autocomplete="new-password"
                                            @input="clearFieldError('xac_nhan_mat_khau')"
                                        />
                                        <button
                                            type="button"
                                            class="reset-input__toggle"
                                            @click="showConfirmPassword = !showConfirmPassword"
                                        >
                                            <i :class="showConfirmPassword ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye'"></i>
                                        </button>
                                    </div>
                                    <p v-if="errors.xac_nhan_mat_khau" class="reset-form__error">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ errors.xac_nhan_mat_khau }}
                                    </p>
                                </div>

                                <button type="submit" class="btn reset-form__submit w-100" :disabled="isSubmitting">
                                    <span v-if="isSubmitting">
                                        <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                        Đang cập nhật...
                                    </span>
                                    <span v-else>
                                        Đặt lại mật khẩu
                                        <i class="fa-solid fa-check ms-2"></i>
                                    </span>
                                </button>
                            </form>
                        </div>

                        <div v-else class="reset-success text-center">
                            <div class="reset-success__icon">
                                <i class="fa-solid fa-circle-check"></i>
                            </div>
                            <h2 class="reset-card__title">Đặt lại mật khẩu thành công</h2>
                            <p class="reset-card__subtitle">
                                Bạn có thể đăng nhập lại ngay với mật khẩu mới vừa tạo.
                            </p>
                            <router-link to="/client/dang-nhap" class="btn reset-form__submit mt-3">
                                Đi đến đăng nhập
                            </router-link>
                        </div>

                        <div v-if="status !== 'success'" class="reset-card__actions">
                            <router-link to="/client/dang-nhap" class="btn reset-card__back">
                                <i class="fa-solid fa-arrow-left me-2"></i>
                                Quay lại đăng nhập
                            </router-link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import axios from 'axios';

const API_URL = 'http://127.0.0.1:8000/api/client/dat-lai-mat-khau';

export default {
    name: 'DatLaiMatKhau',
    data() {
        return {
            form: {
                mat_khau_moi: '',
                xac_nhan_mat_khau: '',
            },
            errors: {},
            isSubmitting: false,
            status: 'idle',
            showPassword: false,
            showConfirmPassword: false,
        };
    },
    computed: {
        resetKey() {
            return this.$route.params.key || '';
        },
    },
    mounted() {
        this.$nextTick(() => {
            this.$refs.passwordInput?.focus();
        });
    },
    methods: {
        clearFieldError(field) {
            if (this.errors[field]) {
                this.errors = { ...this.errors, [field]: '' };
            }
        },

        validateForm() {
            const errors = {};

            if (!this.resetKey) {
                this.$toast.error('Liên kết đặt lại mật khẩu không hợp lệ.');
                return false;
            }

            if (!this.form.mat_khau_moi) {
                errors.mat_khau_moi = 'Vui lòng nhập mật khẩu mới.';
            } else if (this.form.mat_khau_moi.length < 6) {
                errors.mat_khau_moi = 'Mật khẩu mới phải có ít nhất 6 ký tự.';
            }

            if (!this.form.xac_nhan_mat_khau) {
                errors.xac_nhan_mat_khau = 'Vui lòng xác nhận mật khẩu.';
            } else if (this.form.xac_nhan_mat_khau !== this.form.mat_khau_moi) {
                errors.xac_nhan_mat_khau = 'Mật khẩu xác nhận không khớp.';
            }

            this.errors = errors;
            return Object.keys(errors).length === 0;
        },

        async handleSubmit() {
            if (this.isSubmitting) return;
            if (!this.validateForm()) return;

            this.isSubmitting = true;

            try {
                const payload = {
                    key: this.resetKey,
                    mat_khau_moi: this.form.mat_khau_moi,
                };

                const res = await axios.post(API_URL, payload);

                if (!res.data?.status) {
                    this.$toast.error(res.data?.message || 'Không thể đặt lại mật khẩu.');
                    return;
                }

                this.status = 'success';
                this.$toast.success(res.data?.message || 'Mật khẩu đã được đặt lại thành công.');
            } catch (error) {
                if (error.response?.status === 422) {
                    this.errors = { ...this.errors, ...(error.response.data.errors || {}) };
                    this.$toast.error(error.response.data.message || 'Thông tin chưa hợp lệ.');
                } else if (error.response?.data?.message) {
                    this.$toast.error(error.response.data.message);
                } else {
                    this.$toast.error('Không thể đặt lại mật khẩu lúc này, vui lòng thử lại sau.');
                }
            } finally {
                this.isSubmitting = false;
            }
        },
    },
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

.reset-page {
    position: relative;
    min-height: 100vh;
    display: flex;
    align-items: center;
    overflow: hidden;
    font-family: 'Plus Jakarta Sans', sans-serif;
}

.reset-page::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
        radial-gradient(circle at 10% 18%, rgba(14, 165, 233, 0.18), transparent 24%),
        radial-gradient(circle at 82% 14%, rgba(59, 130, 246, 0.12), transparent 20%),
        radial-gradient(circle at 76% 82%, rgba(34, 197, 94, 0.12), transparent 22%);
    pointer-events: none;
}

.reset-page__container {
    position: relative;
    z-index: 1;
    padding-top: 2rem;
    padding-bottom: 2rem;
}

.reset-hero__badge {
    display: inline-flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.75rem 1rem;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.84);
    border: 1px solid rgba(125, 211, 252, 0.7);
    color: #075985;
    font-size: 0.86rem;
    font-weight: 700;
    box-shadow: 0 18px 36px rgba(14, 165, 233, 0.08);
}

.reset-hero__title {
    margin: 1.4rem 0 1rem;
    max-width: 520px;
    font-size: clamp(2rem, 3vw, 3.1rem);
    line-height: 1.08;
    letter-spacing: -0.04em;
    color: #0f172a;
}

.reset-hero__desc {
    max-width: 480px;
    color: #475569;
    margin-bottom: 1.8rem;
}

.reset-hero__tips {
    display: grid;
    gap: 0.9rem;
}

.reset-tip {
    display: flex;
    gap: 0.9rem;
    align-items: center;
    padding: 1rem 1.1rem;
    border-radius: 20px;
    background: rgba(255, 255, 255, 0.76);
    border: 1px solid rgba(226, 232, 240, 0.92);
    box-shadow: 0 18px 34px rgba(15, 23, 42, 0.05);
    color: #475569;
}

.reset-tip i {
    color: #0284c7;
}

.reset-card {
    background: rgba(255, 255, 255, 0.94);
    border: 1px solid rgba(226, 232, 240, 0.95);
    border-radius: 30px;
    padding: 2rem;
    box-shadow: 0 28px 60px rgba(15, 23, 42, 0.14);
    backdrop-filter: blur(16px);
}

.reset-card__header {
    margin-bottom: 1.5rem;
}

.reset-card__logo {
    width: 88px;
    height: 88px;
    margin: 0 auto 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 28px;
    background: linear-gradient(135deg, rgba(14, 165, 233, 0.12) 0%, rgba(59, 130, 246, 0.18) 100%);
}

.reset-card__logo-image {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    object-fit: contain;
}

.reset-card__title {
    margin-bottom: 0.5rem;
    color: #0f172a;
    font-size: 1.72rem;
    font-weight: 800;
    letter-spacing: -0.03em;
}

.reset-card__subtitle {
    color: #64748b;
    font-size: 0.97rem;
}

.reset-form__field {
    margin-bottom: 1.2rem;
}

.reset-form__label {
    margin-bottom: 0.55rem;
    color: #0f172a;
    font-weight: 700;
}

.reset-input {
    display: flex;
    align-items: center;
    border-radius: 18px;
    border: 1.5px solid #dbe4f0;
    background: #f8fbff;
    padding: 0.25rem 0.25rem 0.25rem 0;
    transition: border-color 0.25s ease, box-shadow 0.25s ease, background-color 0.25s ease;
}

.reset-input:focus-within {
    border-color: #0ea5e9;
    background: #ffffff;
    box-shadow: 0 0 0 6px rgba(14, 165, 233, 0.08);
}

.reset-input--error {
    border-color: #ef4444;
    background: #fff5f5;
}

.reset-input__icon {
    width: 48px;
    display: flex;
    justify-content: center;
    color: #64748b;
}

.reset-input__control {
    flex: 1;
    border: none;
    outline: none;
    background: transparent;
    padding: 0.95rem 0.5rem 0.95rem 0;
    color: #0f172a;
    font-size: 0.98rem;
    font-weight: 500;
}

.reset-input__toggle {
    width: 44px;
    height: 44px;
    border: none;
    background: transparent;
    color: #64748b;
    border-radius: 12px;
}

.reset-form__error {
    margin: 0.5rem 0 0;
    font-size: 0.85rem;
    color: #dc2626;
    font-weight: 600;
}

.reset-form__submit {
    min-height: 56px;
    border: none;
    border-radius: 18px;
    color: #ffffff;
    font-weight: 700;
    background: linear-gradient(135deg, #0284c7 0%, #2563eb 55%, #0f766e 100%);
    box-shadow: 0 20px 30px rgba(37, 99, 235, 0.2);
}

.reset-form__submit:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 24px 34px rgba(37, 99, 235, 0.25);
}

.reset-card__actions {
    display: flex;
    margin-top: 1.4rem;
}

.reset-card__back {
    min-height: 48px;
    border-radius: 14px;
    font-weight: 700;
    padding: 0.75rem 1.1rem;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #334155;
}

.reset-success__icon {
    width: 92px;
    height: 92px;
    margin: 0 auto 1.2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 30px;
    background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
    color: #15803d;
    font-size: 2.2rem;
}

@media (max-width: 991.98px) {
    .reset-page {
        align-items: flex-start;
    }

    .reset-hero {
        text-align: center;
    }

    .reset-hero__desc {
        margin-left: auto;
        margin-right: auto;
    }
}

@media (max-width: 575.98px) {
    .reset-page__container {
        padding-top: 1rem;
        padding-bottom: 1rem;
    }

    .reset-card {
        padding: 1.35rem;
        border-radius: 24px;
    }
}
</style>
