<template>
  <div class="admin-profile-page py-4">
    <div class="container-fluid">
      <div class="row mb-4">
        <div class="col-12">
          <!-- Banner đổi màu theo chức vụ -->
          <div class="hero-card shadow-sm rounded-4 border-0 p-4" :style="isSuperAdmin ? 'background: linear-gradient(135deg, #4f46e5 0%, #312e81 100%); color: white;' : 'background: linear-gradient(135deg, #0d9488 0%, #115e59 100%); color: white;'">
            <div class="d-flex align-items-center gap-4">
              <div class="avatar-circle shadow-lg d-flex align-items-center justify-content-center fw-bold text-dark" style="width: 100px; height: 100px; background: #fff; border-radius: 50%; font-size: 2.5rem; flex-shrink: 0;">
                {{ adminInitials }}
              </div>
              <div>
                <h2 class="fw-bold mb-1 d-flex align-items-center">
                  {{ admin.ho_ten || admin.ten || 'Admin User' }}
                  <i v-if="isSuperAdmin" class="bi bi-patch-check-fill text-warning ms-2" style="font-size: 1.5rem;" title="Tài khoản Super Admin"></i>
                </h2>
                <div class="d-flex align-items-center gap-3 mt-2">
                  <span class="badge px-3 py-2 rounded-pill fs-6 shadow-sm" :class="isSuperAdmin ? 'bg-warning text-dark' : 'bg-light text-teal'">
                    <i class="bi" :class="isSuperAdmin ? 'bi-shield-shaded' : 'bi-person-badge'"></i>
                    {{ admin.ten_chuc_vu || (isSuperAdmin ? 'Quản trị viên' : 'Nhân viên') }}
                  </span>
                  <span class="text-light opacity-75"><i class="bi bi-envelope-fill me-1"></i> {{ admin.email }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4">
        <!-- Cột thông tin -->
        <div class="col-lg-4">
          <div class="card shadow-sm border-0 rounded-4 h-100 position-relative overflow-hidden">
            <div v-if="isSuperAdmin" class="position-absolute top-0 end-0 p-3 opacity-25">
               <i class="bi bi-star-fill text-warning" style="font-size: 4rem;"></i>
            </div>
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
              <h5 class="fw-bold"><i class="bi bi-person-lines-fill me-2" :class="isSuperAdmin ? 'text-primary' : 'text-teal'"></i> Chi tiết liên hệ</h5>
            </div>
            <div class="card-body">
              <ul class="list-group list-group-flush position-relative z-1">
                <li class="list-group-item px-0 py-3 d-flex align-items-center bg-transparent">
                  <div class="icon-wrap me-3 text-muted" style="width: 32px;"><i class="bi bi-person fs-5"></i></div>
                  <div>
                    <div class="text-muted small">Họ và tên</div>
                    <div class="fw-bold">{{ admin.ho_ten || admin.ten || 'Chưa cập nhật' }}</div>
                  </div>
                </li>
                <li class="list-group-item px-0 py-3 d-flex align-items-center bg-transparent">
                  <div class="icon-wrap me-3 text-muted" style="width: 32px;"><i class="bi bi-envelope fs-5"></i></div>
                  <div>
                    <div class="text-muted small">Email liên hệ</div>
                    <div class="fw-bold">{{ admin.email || 'Chưa cập nhật' }}</div>
                  </div>
                </li>
                <li class="list-group-item px-0 py-3 d-flex align-items-center bg-transparent">
                  <div class="icon-wrap me-3 text-muted" style="width: 32px;"><i class="bi bi-telephone fs-5"></i></div>
                  <div>
                    <div class="text-muted small">Số điện thoại</div>
                    <div class="fw-bold">{{ admin.so_dien_thoai || admin.sdt || 'Chưa cập nhật' }}</div>
                  </div>
                </li>
                <li class="list-group-item px-0 py-3 d-flex align-items-center border-bottom-0 bg-transparent">
                  <div class="icon-wrap me-3" :class="isSuperAdmin ? 'text-warning' : 'text-teal'" style="width: 32px;"><i class="bi bi-shield-check fs-5"></i></div>
                  <div>
                    <div class="text-muted small">Mức độ quyền hạn</div>
                    <div class="fw-bold" :class="isSuperAdmin ? 'text-primary' : 'text-success'">
                      {{ isSuperAdmin ? 'Toàn quyền hệ thống' : 'Theo phân quyền nghiệp vụ' }}
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
        
        <!-- Cột Cài đặt -->
        <div class="col-lg-8">
          <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
              <h5 class="fw-bold"><i class="bi bi-gear-fill me-2" :class="isSuperAdmin ? 'text-primary' : 'text-teal'"></i> Cài đặt tài khoản</h5>
            </div>
            <div class="card-body">
              
              <!-- Message cho Super Admin -->
              <div v-if="isSuperAdmin" class="alert alert-primary border-0 shadow-sm rounded-3 d-flex align-items-center mb-4">
                <i class="bi bi-shield-lock-fill me-3 text-primary" style="font-size: 2rem;"></i>
                <div>
                  <strong class="d-block mb-1">Tài khoản Quản trị cấp cao (Super Admin)</strong>
                  <span class="small">Bạn có toàn quyền truy cập, chỉnh sửa và quản lý mọi tính năng trong hệ thống bao gồm cả nhân sự, người dùng và dữ liệu AI.</span>
                </div>
              </div>
              
              <!-- Message cho Nhân viên -->
              <div v-else class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center mb-4" style="background-color: #f0fdfa; color: #0f766e;">
                <i class="bi bi-info-circle-fill me-3" style="font-size: 2rem;"></i>
                <div>
                  <strong class="d-block mb-1">Tài khoản Nhân viên vận hành</strong>
                  <span class="small">Bạn có quyền xem thống kê và quản lý nội dung danh mục theo sự phân công của Ban quản trị. Tính năng cập nhật cá nhân đang khóa tạm thời. Xin liên hệ Quản trị viên nếu cần thay đổi.</span>
                </div>
              </div>
              
              <form @submit.prevent="saveProfile">
                <div class="row g-3 mb-4">
                  <div class="col-md-6">
                    <label class="form-label fw-bold text-muted small">Họ và tên</label>
                    <input type="text" class="form-control" v-model="form.name" disabled>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-bold text-muted small">Email</label>
                    <input type="email" class="form-control" v-model="form.email" disabled>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-bold text-muted small">Số điện thoại</label>
                    <input type="text" class="form-control" v-model="form.phone" disabled>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-bold text-muted small">Chức vụ</label>
                    <input type="text" class="form-control" :value="admin.ten_chuc_vu || (isSuperAdmin ? 'Quản trị viên' : 'Nhân viên')" disabled>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AdminProfile',
  data() {
    return {
      admin: {},
      form: {
        name: '',
        email: '',
        phone: ''
      }
    };
  },
  computed: {
    adminInitials() {
      const name = this.admin.ho_ten || this.admin.ten || 'A';
      return name.charAt(0).toUpperCase();
    },
    isSuperAdmin() {
      // 1: Quản trị viên (Super Admin), 2: Nhân viên
      const role = Number(this.admin.id_chuc_vu || this.admin.chuc_vu || 0);
      return role === 1;
    }
  },
  mounted() {
    this.loadProfile();
  },
  methods: {
    loadProfile() {
      const raw = localStorage.getItem('admin_data');
      if (raw) {
        try {
          this.admin = JSON.parse(raw);
          this.form.name = this.admin.ho_ten || this.admin.ten || '';
          this.form.email = this.admin.email || '';
          this.form.phone = this.admin.so_dien_thoai || this.admin.sdt || '';
        } catch(e) {
          console.error("Lỗi parse admin data", e);
        }
      }
    },
    saveProfile() {
      this.$toast.info('Tính năng đang phát triển.');
    }
  }
};
</script>

<style scoped>
.admin-profile-page {
  animation: fadeIn 0.3s ease;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
.form-control:disabled {
  background-color: #f8fafc;
  cursor: not-allowed;
  border-color: #e2e8f0;
}
.text-teal {
  color: #0d9488;
}
.text-primary {
  color: #4f46e5 !important;
}
</style>
