<template>
  <div class="activation-page d-flex align-items-center justify-content-center min-vh-100 bg-light">
    <div class="text-center p-5 bg-white rounded-4 shadow-sm" style="max-width: 400px;">
      <div class="spinner-border text-primary mb-4" role="status" style="width: 3rem; height: 3rem;">
        <span class="visually-hidden">Loading...</span>
      </div>
      <h4 class="fw-bold text-dark">Đang kích hoạt tài khoản</h4>
      <p class="text-secondary">Vui lòng chờ trong giây lát, chúng tôi đang xác thực thông tin của bạn...</p>
    </div>
  </div>
</template>
<script>
import axios from 'axios';
export default {
    props: ["hash_active"],
    data() {
        return {
            hash_active: this.$route.params.hash_active,
        }
    },
    mounted() {
        this.kichHoat();
    },
    methods: {
        kichHoat() {
            var payload = {
                hash_active: this.hash_active
            };
            axios.post('http://127.0.0.1:8000/api/client/kich-hoat', payload)
                .then((res) => {
                    if (res.data.status) {
                        this.$toast.success(res.data.message);
                        this.$router.push('/client/dang-nhap');
                    } else {
                        this.$toast.error(res.data.message);
                    }
                });

        }
    },
}
</script>
<style></style>