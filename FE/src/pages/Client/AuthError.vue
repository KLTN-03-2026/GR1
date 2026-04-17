<template>
    <section class="auth-error-page">
        <div class="auth-error-card text-center">
            <div class="auth-error-icon">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
            <h1 class="auth-error-title">Không thể đăng nhập bằng Google</h1>
            <p class="auth-error-desc">
                {{ errorMessage }}
            </p>
            <router-link to="/client/dang-nhap" class="btn auth-error-button">
                Quay lại đăng nhập
            </router-link>
        </div>
    </section>
</template>

<script>
import { clearClientRedirectPath } from '../../utils/clientAuth';

export default {
    name: 'AuthErrorPage',
    computed: {
        errorMessage() {
            const message = this.$route.query.message;
            return typeof message === 'string' && message
                ? message
                : 'Đăng nhập Google thất bại. Vui lòng thử lại.';
        },
    },
    mounted() {
        clearClientRedirectPath();

        if (this.errorMessage) {
            this.$toast.error(this.errorMessage);
        }
    },
};
</script>

<style scoped>
.auth-error-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px;
    background:
        radial-gradient(circle at top right, rgba(248, 113, 113, 0.18), transparent 26%),
        radial-gradient(circle at bottom left, rgba(251, 191, 36, 0.12), transparent 22%),
        linear-gradient(160deg, #fff7ed 0%, #ffffff 50%, #fff1f2 100%);
}

.auth-error-card {
    width: min(100%, 520px);
    padding: 40px 32px;
    border-radius: 28px;
    background: rgba(255, 255, 255, 0.94);
    box-shadow: 0 28px 60px rgba(15, 23, 42, 0.12);
    border: 1px solid rgba(254, 215, 170, 0.8);
}

.auth-error-icon {
    width: 84px;
    height: 84px;
    margin: 0 auto 18px;
    border-radius: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #ffedd5 0%, #fee2e2 100%);
    color: #ea580c;
    font-size: 2rem;
}

.auth-error-title {
    font-size: 1.65rem;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 0.75rem;
}

.auth-error-desc {
    color: #64748b;
    margin-bottom: 1.5rem;
}

.auth-error-button {
    min-height: 50px;
    border: none;
    border-radius: 16px;
    padding: 0.85rem 1.4rem;
    color: #fff;
    font-weight: 700;
    background: linear-gradient(135deg, #ea580c 0%, #dc2626 100%);
}
</style>
