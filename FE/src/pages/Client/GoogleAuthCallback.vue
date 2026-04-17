<template>
    <section class="google-callback-page">
        <div class="google-callback-card text-center">
            <div class="google-callback-spinner mb-4">
                <span class="spinner-border text-primary" role="status"></span>
            </div>
            <h1 class="google-callback-title">Đang xử lý đăng nhập Google</h1>
            <p class="google-callback-desc mb-0">
                Hệ thống đang xác thực tài khoản và chuyển bạn đến trang phù hợp.
            </p>
        </div>
    </section>
</template>

<script>
import {
    clearClientRedirectPath,
    getClientRedirectPath,
    saveClientSession,
} from '../../utils/clientAuth';

export default {
    name: 'GoogleAuthCallback',
    mounted() {
        this.xuLyGoogleCallback();
    },
    methods: {
        layGiaTriURL(key) {
            const queryValue = this.$route.query[key];
            if (typeof queryValue === 'string' && queryValue) {
                return queryValue;
            }

            const hash = window.location.hash.startsWith('#')
                ? window.location.hash.slice(1)
                : window.location.hash;

            const hashParams = new URLSearchParams(hash);
            return hashParams.get(key) || '';
        },

        parseUser(rawUser) {
            if (!rawUser) return {};

            try {
                return JSON.parse(rawUser);
            } catch {
                try {
                    return JSON.parse(decodeURIComponent(rawUser));
                } catch {
                    return {};
                }
            }
        },

        redirectSauDangNhap() {
            const redirectPath = this.layGiaTriURL('redirect') || getClientRedirectPath() || '/';
            clearClientRedirectPath();

            this.$router.replace(redirectPath).catch(() => {
                window.location.href = redirectPath;
            });
        },

        quayVeTrangDangNhap(message = '') {
            clearClientRedirectPath();

            if (message) {
                this.$toast.error(message);
            }

            this.$router.replace('/client/dang-nhap').catch(() => {
                window.location.href = '/client/dang-nhap';
            });
        },

        xuLyGoogleCallback() {
            const token = this.layGiaTriURL('token');
            const rawUser = this.layGiaTriURL('user');
            const message = this.layGiaTriURL('message');
            const error = this.layGiaTriURL('error');

            if (error) {
                this.quayVeTrangDangNhap(message || error);
                return;
            }

            if (!token) {
                this.quayVeTrangDangNhap(message || 'Đăng nhập Google thất bại, thiếu token.');
                return;
            }

            const user = this.parseUser(rawUser);
            saveClientSession(token, user);

            if (message) {
                this.$toast.success(message);
            }

            this.redirectSauDangNhap();
        },
    },
};
</script>

<style scoped>
.google-callback-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px;
    background:
        radial-gradient(circle at top left, rgba(14, 165, 233, 0.18), transparent 28%),
        radial-gradient(circle at bottom right, rgba(249, 115, 22, 0.12), transparent 26%),
        linear-gradient(160deg, #eff6ff 0%, #ffffff 48%, #f8fafc 100%);
}

.google-callback-card {
    width: min(100%, 520px);
    padding: 40px 32px;
    border-radius: 28px;
    background: rgba(255, 255, 255, 0.92);
    box-shadow: 0 28px 60px rgba(15, 23, 42, 0.12);
    border: 1px solid rgba(226, 232, 240, 0.9);
    backdrop-filter: blur(14px);
}

.google-callback-title {
    font-size: 1.65rem;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 0.75rem;
}

.google-callback-desc {
    color: #64748b;
    font-size: 0.97rem;
}
</style>
