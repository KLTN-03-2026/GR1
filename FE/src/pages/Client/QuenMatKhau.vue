<template>
    <section class="forgot-page">
        <div class="container forgot-page__container">
            <div class="row align-items-center justify-content-center g-4 g-xl-5">
                <div class="col-12 col-lg-5">
                    <aside class="forgot-hero">
                        <span class="forgot-hero__badge">
                            <i class="fa-solid fa-key"></i>
                            Hỗ trợ lấy lại mật khẩu
                        </span>

                        <h1 class="forgot-hero__title">
                            Một bước nhỏ để quay lại tài khoản của bạn.
                        </h1>

                        <p class="forgot-hero__desc">
                            Nhập email đã đăng ký. Nếu tài khoản tồn tại, chúng tôi sẽ gửi hướng dẫn đặt lại mật khẩu về hộp thư của bạn.
                        </p>

                        <div class="forgot-hero__steps">
                            <div v-for="item in steps" :key="item.title" class="hero-step">
                                <div class="hero-step__icon">
                                    <i :class="item.icon"></i>
                                </div>
                                <div>
                                    <h6>{{ item.title }}</h6>
                                    <p>{{ item.description }}</p>
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>

                <div class="col-12 col-lg-6 col-xl-5">
                    <div class="forgot-card">
                        <div class="forgot-card__topbar"></div>

                        <template v-if="status === 'idle' || status === 'error'">
                            <div class="forgot-card__header text-center">
                                <div class="forgot-card__logo">
                                    <img
                                        src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR3H7FDGtE7IOwPdIbFilEYiOmUAI9w-x1U6A&s"
                                        alt="DaNang Travel"
                                        class="forgot-card__logo-image"
                                    />
                                </div>

                                <h2 class="forgot-card__title">Quên mật khẩu</h2>
                                <p class="forgot-card__subtitle mb-0">
                                    Điền email để nhận liên kết khôi phục tài khoản.
                                </p>
                            </div>

                            <div class="forgot-card__tip">
                                <i class="fa-solid fa-circle-info"></i>
                                <span>Kiểm tra thêm mục Spam hoặc Promotions nếu bạn chưa thấy email sau vài phút.</span>
                            </div>

                            <form class="forgot-form" @submit.prevent="handleSubmit" novalidate>
                                <div class="forgot-form__field">
                                    <label for="forgot-email" class="form-label forgot-form__label">Địa chỉ email</label>

                                    <div
                                        class="forgot-input"
                                        :class="{
                                            'forgot-input--error': errors.email,
                                            'forgot-input--filled': form.email
                                        }"
                                    >
                                        <span class="forgot-input__icon">
                                            <i class="fa-regular fa-envelope"></i>
                                        </span>
                                        <input
                                            id="forgot-email"
                                            ref="emailInput"
                                            v-model.trim="form.email"
                                            type="email"
                                            inputmode="email"
                                            autocomplete="email"
                                            class="forgot-input__control"
                                            :aria-invalid="errors.email ? 'true' : 'false'"
                                            :aria-describedby="errors.email ? 'forgot-email-error' : undefined"
                                            placeholder="ban@example.com"
                                            @input="clearFieldError('email')"
                                        />
                                    </div>

                                    <p
                                        v-if="errors.email"
                                        id="forgot-email-error"
                                        class="forgot-form__error"
                                    >
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ errors.email }}
                                    </p>
                                </div>

                                <button
                                    type="submit"
                                    class="btn forgot-form__submit w-100"
                                    :disabled="isSubmitting"
                                >
                                    <span v-if="isSubmitting">
                                        <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                        Đang gửi yêu cầu...
                                    </span>
                                    <span v-else>
                                        Gửi email khôi phục
                                        <i class="fa-solid fa-paper-plane ms-2"></i>
                                    </span>
                                </button>
                            </form>
                        </template>

                        <template v-else>
                            <div class="forgot-success text-center">
                                <div class="forgot-success__icon">
                                    <i class="fa-solid fa-envelope-circle-check"></i>
                                </div>

                                <h2 class="forgot-card__title">Kiểm tra email của bạn</h2>
                                <p class="forgot-card__subtitle">
                                    Nếu địa chỉ <strong>{{ maskedSubmittedEmail }}</strong> tồn tại trong hệ thống, hướng dẫn đặt lại mật khẩu sẽ được gửi ngay.
                                </p>

                                <div class="forgot-success__note">
                                    <i class="fa-regular fa-clock"></i>
                                    <span>Thông thường email sẽ đến trong vòng 1 đến 3 phút.</span>
                                </div>
                            </div>
                        </template>

                        <div class="forgot-card__actions">
                            <router-link to="/client/dang-nhap" class="btn forgot-card__back">
                                <i class="fa-solid fa-arrow-left me-2"></i>
                                Quay lại đăng nhập
                            </router-link>

                            <button
                                v-if="status === 'success'"
                                type="button"
                                class="btn btn-outline-secondary forgot-card__secondary"
                                @click="resetForm"
                            >
                                Gửi cho email khác
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import axios from 'axios';

const API_URL = 'http://127.0.0.1:8000/api/client/quen-mat-khau';
const EMAIL_REGEX = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

export default {
    name: 'QuenMatKhau',
    data() {
        return {
            form: {
                email: '',
            },
            errors: {},
            status: 'idle',
            isSubmitting: false,
            submittedEmail: '',
            steps: [
                {
                    icon: 'fa-solid fa-at',
                    title: 'Nhập email bạn đang sử dụng',
                    description: 'Hãy nhập đúng email đã dùng để đăng ký tài khoản để chúng tôi kiểm tra và gửi hướng dẫn phù hợp.',
                },
                {
                    icon: 'fa-solid fa-paper-plane',
                    title: 'Kiểm tra hộp thư đến',
                    description: 'Sau khi gửi yêu cầu, hãy mở email và làm theo hướng dẫn để đặt lại mật khẩu mới.',
                },
            ],
        };
    },
    computed: {
        maskedSubmittedEmail() {
            return this.maskEmail(this.submittedEmail);
        },
    },
    mounted() {
        this.$nextTick(() => {
            this.$refs.emailInput?.focus();
        });
    },
    methods: {
        clearFieldError(field) {
            if (this.errors[field]) {
                this.errors = { ...this.errors, [field]: '' };
            }
            if (this.status === 'error') {
                this.status = 'idle';
            }
        },

        validateForm() {
            const errors = {};
            const email = this.form.email?.trim() || '';

            if (!email) {
                errors.email = 'Vui lòng nhập địa chỉ email.';
            } else if (!EMAIL_REGEX.test(email)) {
                errors.email = 'Email không đúng định dạng.';
            }

            this.errors = errors;
            return Object.keys(errors).length === 0;
        },

        async handleSubmit() {
            if (this.isSubmitting) return;
            if (!this.validateForm()) return;

            this.isSubmitting = true;
            this.status = 'idle';

            try {
                const payload = {
                    email: this.form.email.trim().toLowerCase(),
                };

                const res = await axios.post(API_URL, payload);
                if (!res.data?.status) {
                    this.status = 'error';
                    this.$toast.error(res.data?.message || 'Tài khoản chưa được đăng ký.');
                    return;
                }

                this.submittedEmail = payload.email;
                this.status = 'success';
                this.$toast.success(
                    res.data?.message || 'Nếu email tồn tại, hướng dẫn khôi phục sẽ sớm được gửi tới bạn.'
                );
            } catch (error) {
                if (error.response?.status === 422) {
                    this.errors = { ...this.errors, ...(error.response.data.errors || {}) };
                    this.$toast.error(error.response.data.message || 'Thông tin chưa hợp lệ.');
                } else if (error.response?.data?.message) {
                    this.$toast.error(error.response.data.message);
                } else {
                    this.$toast.error('Không thể gửi yêu cầu lúc này, vui lòng thử lại sau.');
                }

                this.status = 'error';
            } finally {
                this.isSubmitting = false;
            }
        },

        resetForm() {
            this.form.email = '';
            this.errors = {};
            this.status = 'idle';
            this.submittedEmail = '';

            this.$nextTick(() => {
                this.$refs.emailInput?.focus();
            });
        },

        maskEmail(email) {
            if (!email || !email.includes('@')) return email;

            const [localPart, domain] = email.split('@');
            if (localPart.length <= 2) {
                return `${localPart[0] || ''}***@${domain}`;
            }

            return `${localPart.slice(0, 2)}***${localPart.slice(-1)}@${domain}`;
        },
    },
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

.forgot-page {
    position: relative;
    min-height: 100vh;
    display: flex;
    align-items: center;
    overflow: hidden;
    font-family: 'Plus Jakarta Sans', sans-serif;
}

.forgot-page::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
        radial-gradient(circle at 8% 18%, rgba(14, 165, 233, 0.2), transparent 26%),
        radial-gradient(circle at 85% 12%, rgba(249, 115, 22, 0.14), transparent 20%),
        radial-gradient(circle at 78% 84%, rgba(34, 197, 94, 0.12), transparent 24%);
    pointer-events: none;
}

.forgot-page__container {
    position: relative;
    z-index: 1;
    padding-top: 2rem;
    padding-bottom: 2rem;
}

.forgot-hero {
    padding: 1rem 0;
}

.forgot-hero__badge {
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
    box-shadow: 0 18px 36px rgba(14, 165, 233, 0.1);
}

.forgot-hero__title {
    margin: 1.4rem 0 1rem;
    max-width: 520px;
    font-size: clamp(2.1rem, 3vw, 3.3rem);
    line-height: 1.08;
    letter-spacing: -0.04em;
    color: #0f172a;
}

.forgot-hero__desc {
    max-width: 500px;
    margin-bottom: 2rem;
    color: #475569;
    font-size: 1rem;
}

.forgot-hero__steps {
    display: grid;
    gap: 1rem;
}

.hero-step {
    display: flex;
    gap: 1rem;
    align-items: flex-start;
    padding: 1rem 1.1rem;
    border-radius: 24px;
    background: rgba(255, 255, 255, 0.76);
    border: 1px solid rgba(226, 232, 240, 0.92);
    box-shadow: 0 20px 40px rgba(15, 23, 42, 0.06);
}

.hero-step__icon {
    width: 48px;
    height: 48px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    color: #0284c7;
    background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 100%);
}

.hero-step h6 {
    margin-bottom: 0.3rem;
    font-size: 0.95rem;
    color: #0f172a;
}

.hero-step p {
    margin-bottom: 0;
    font-size: 0.9rem;
    color: #64748b;
}

.forgot-card {
    position: relative;
    overflow: hidden;
    background: rgba(255, 255, 255, 0.94);
    border: 1px solid rgba(226, 232, 240, 0.95);
    border-radius: 30px;
    padding: 2rem;
    box-shadow: 0 28px 60px rgba(15, 23, 42, 0.14);
    backdrop-filter: blur(16px);
}

.forgot-card__topbar {
    display: none;
}

.forgot-card__header {
    margin-bottom: 1.5rem;
}

.forgot-card__logo {
    width: 88px;
    height: 88px;
    margin: 0 auto 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 28px;
    background: linear-gradient(135deg, rgba(14, 165, 233, 0.12) 0%, rgba(59, 130, 246, 0.18) 100%);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.8);
}

.forgot-card__logo-image {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    object-fit: contain;
}

.forgot-card__title {
    margin-bottom: 0.5rem;
    color: #0f172a;
    font-size: 1.75rem;
    font-weight: 800;
    letter-spacing: -0.03em;
}

.forgot-card__subtitle {
    color: #64748b;
    font-size: 0.97rem;
}

.forgot-card__tip,
.forgot-success__note {
    display: flex;
    gap: 0.75rem;
    align-items: center;
    padding: 0.95rem 1rem;
    border-radius: 18px;
    font-size: 0.92rem;
}

.forgot-card__tip {
    margin-bottom: 1.4rem;
    background: #eff6ff;
    color: #1d4ed8;
}

.forgot-form__field {
    margin-bottom: 1.2rem;
}

.forgot-form__label {
    margin-bottom: 0.55rem;
    color: #0f172a;
    font-weight: 700;
}

.forgot-input {
    display: flex;
    align-items: center;
    border-radius: 18px;
    border: 1.5px solid #dbe4f0;
    background: #f8fbff;
    padding: 0.25rem;
    transition: border-color 0.25s ease, box-shadow 0.25s ease, background-color 0.25s ease;
}

.forgot-input:focus-within {
    border-color: #0ea5e9;
    background: #ffffff;
    box-shadow: 0 0 0 6px rgba(14, 165, 233, 0.08);
}

.forgot-input--error {
    border-color: #ef4444;
    background: #fff5f5;
}

.forgot-input--filled .forgot-input__icon {
    color: #0284c7;
}

.forgot-input__icon {
    width: 48px;
    display: flex;
    justify-content: center;
    color: #64748b;
    font-size: 1rem;
    transition: color 0.2s ease;
}

.forgot-input__control {
    flex: 1;
    border: none;
    outline: none;
    background: transparent;
    padding: 0.95rem 1rem 0.95rem 0;
    color: #0f172a;
    font-size: 0.98rem;
    font-weight: 500;
}

.forgot-input__control::placeholder {
    color: #94a3b8;
    font-weight: 400;
}

.forgot-form__error {
    margin: 0.5rem 0 0;
    font-size: 0.85rem;
    color: #dc2626;
    font-weight: 600;
}

.forgot-form__submit {
    min-height: 56px;
    border: none;
    border-radius: 18px;
    color: #ffffff;
    font-weight: 700;
    background: linear-gradient(135deg, #0284c7 0%, #2563eb 55%, #0f766e 100%);
    box-shadow: 0 20px 30px rgba(37, 99, 235, 0.2);
}

.forgot-form__submit:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 24px 34px rgba(37, 99, 235, 0.25);
}

.forgot-form__submit:disabled {
    opacity: 0.86;
    cursor: not-allowed;
}

.forgot-success {
    padding-top: 0.5rem;
}

.forgot-success__icon {
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
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.8);
}

.forgot-success__note {
    justify-content: center;
    margin-top: 1rem;
    background: #ecfdf5;
    color: #047857;
}

.forgot-card__actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-top: 1.6rem;
}

.forgot-card__back,
.forgot-card__secondary {
    min-height: 48px;
    border-radius: 14px;
    font-weight: 700;
    padding: 0.75rem 1.1rem;
}

.forgot-card__back {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #334155;
}

.forgot-card__secondary {
    flex: 1;
}

@media (max-width: 991.98px) {
    .forgot-page {
        align-items: flex-start;
    }

    .forgot-hero {
        text-align: center;
    }

    .forgot-hero__desc {
        margin-left: auto;
        margin-right: auto;
    }

    .hero-step {
        text-align: left;
    }
}

@media (max-width: 575.98px) {
    .forgot-page__container {
        padding-top: 1rem;
        padding-bottom: 1rem;
    }

    .forgot-card {
        padding: 1.35rem;
        border-radius: 24px;
    }

    .forgot-hero__title {
        font-size: 1.9rem;
    }

    .forgot-card__actions > * {
        width: 100%;
    }
}
</style>
