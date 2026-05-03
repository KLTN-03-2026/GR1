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
              
              <!-- Alert thành công -->
              <div v-if="successMsg" class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center mb-4 animate__animated animate__fadeInDown">
                <i class="bi bi-check-circle-fill me-3 text-success" style="font-size: 1.5rem;"></i>
                <div>{{ successMsg }}</div>
              </div>
              
              <!-- Alert lỗi -->
              <div v-if="errorMsg" class="alert alert-danger border-0 shadow-sm rounded-3 d-flex align-items-center mb-4">
                <i class="bi bi-exclamation-circle-fill me-3 text-danger" style="font-size: 1.5rem;"></i>
                <div>{{ errorMsg }}</div>
              </div>

              <form @submit.prevent="saveProfile">
                <div class="row g-3 mb-4">
                  <div class="col-md-6">
                    <label class="form-label fw-bold text-muted small">Họ và tên <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" v-model="form.name" :class="{ 'is-invalid': formErrors.name }" placeholder="Nhập họ và tên">
                    <div v-if="formErrors.name" class="invalid-feedback">{{ formErrors.name }}</div>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-bold text-muted small">Email</label>
                    <input type="email" class="form-control" v-model="form.email" disabled>
                    <div class="small text-muted mt-1"><i class="bi bi-info-circle me-1"></i>Email không thể thay đổi</div>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-bold text-muted small">Số điện thoại</label>
                    <input type="text" class="form-control" v-model="form.phone" :class="{ 'is-invalid': formErrors.phone }" placeholder="09xx...">
                    <div v-if="formErrors.phone" class="invalid-feedback">{{ formErrors.phone }}</div>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-bold text-muted small">Chức vụ</label>
                    <input type="text" class="form-control" :value="admin.ten_chuc_vu || (isSuperAdmin ? 'Quản trị viên' : 'Nhân viên')" disabled>
                    <div class="small text-muted mt-1"><i class="bi bi-lock me-1"></i>Chức vụ không thể tự thay đổi</div>
                  </div>
                  
                  <!-- Đổi mật khẩu section -->
                  <div class="col-12 mt-3">
                    <div class="d-flex align-items-center gap-2 mb-3">
                      <hr class="flex-grow-1">
                      <span class="text-muted small fw-semibold">Đổi mật khẩu (tùy chọn)</span>
                      <hr class="flex-grow-1">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-bold text-muted small">Mật khẩu hiện tại</label>
                    <div class="input-group">
                      <input :type="showPw.old ? 'text' : 'password'" class="form-control" v-model="form.mat_khau_cu" :class="{ 'is-invalid': formErrors.mat_khau_cu }" placeholder="Bỏ trống nếu không đổi">
                      <button type="button" class="btn btn-outline-secondary" @click="showPw.old = !showPw.old">
                        <i :class="showPw.old ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                      </button>
                    </div>
                    <div v-if="formErrors.mat_khau_cu" class="text-danger small mt-1">{{ formErrors.mat_khau_cu }}</div>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-bold text-muted small">Mật khẩu mới</label>
                    <div class="input-group">
                      <input :type="showPw.new ? 'text' : 'password'" class="form-control" v-model="form.mat_khau_moi" :class="{ 'is-invalid': formErrors.mat_khau_moi }" placeholder="Tối thiểu 6 ký tự">
                      <button type="button" class="btn btn-outline-secondary" @click="showPw.new = !showPw.new">
                        <i :class="showPw.new ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                      </button>
                    </div>
                    <div v-if="formErrors.mat_khau_moi" class="text-danger small mt-1">{{ formErrors.mat_khau_moi }}</div>
                  </div>
                </div>
                
                <div class="d-flex gap-2">
                  <button type="submit" class="btn btn-primary px-4 rounded-pill" :disabled="saving">
                    <span v-if="saving"><span class="spinner-border spinner-border-sm me-2"></span>Đang lưu...</span>
                    <span v-else><i class="bi bi-check-lg me-1"></i>Lưu thay đổi</span>
                  </button>
                  <button type="button" class="btn btn-light rounded-pill px-4" @click="resetForm">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Khôi phục
                  </button>
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
import api from '../../services/api.js';

export default {
  name: 'AdminProfile',
  data() {
    return {
      admin: {},
      form: {
        name: '',
        email: '',
        phone: '',
        mat_khau_cu: '',
        mat_khau_moi: '',
      },
      formErrors: {},
      saving: false,
      successMsg: '',
      errorMsg: '',
      showPw: { old: false, new: false },
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
          this.resetForm();
        } catch(e) {
          console.error("Lỗi parse admin data", e);
        }
      }
      // Also refresh from API
      this.fetchProfileFromApi();
    },
    async fetchProfileFromApi() {
      try {
        const res = await api.get('/admin/me');
        if (res.data?.status && res.data?.data) {
          this.admin = res.data.data;
          // Update localStorage with fresh data
          const currentRaw = localStorage.getItem('admin_data');
          if (currentRaw) {
            const currentData = JSON.parse(currentRaw);
            const merged = { ...currentData, ...res.data.data };
            localStorage.setItem('admin_data', JSON.stringify(merged));
          }
          this.resetForm();
        }
      } catch (e) {
        // Silent - fallback to localStorage data
      }
    },
    resetForm() {
      this.form = {
        name: this.admin.ho_ten || this.admin.ten || '',
        email: this.admin.email || '',
        phone: this.admin.so_dien_thoai || this.admin.sdt || '',
        mat_khau_cu: '',
        mat_khau_moi: '',
      };
      this.formErrors = {};
      this.successMsg = '';
      this.errorMsg = '';
    },
    validate() {
      this.formErrors = {};
      if (!this.form.name?.trim()) {
        this.formErrors.name = 'Vui lòng nhập họ và tên.';
        return false;
      }
      if (this.form.mat_khau_cu && !this.form.mat_khau_moi) {
        this.formErrors.mat_khau_moi = 'Vui lòng nhập mật khẩu mới.';
        return false;
      }
      if (this.form.mat_khau_moi && this.form.mat_khau_moi.length < 6) {
        this.formErrors.mat_khau_moi = 'Mật khẩu mới phải có ít nhất 6 ký tự.';
        return false;
      }
      return true;
    },
    async saveProfile() {
      if (!this.validate()) return;
      this.saving = true;
      this.successMsg = '';
      this.errorMsg = '';

      try {
        const adminId = this.admin.id;
        if (!adminId) {
          this.errorMsg = 'Không tìm thấy ID tài khoản. Vui lòng đăng nhập lại.';
          return;
        }

        const payload = {
          ho_ten: this.form.name,
          so_dien_thoai: this.form.phone,
        };

        // Chỉ gửi mật khẩu nếu user nhập
        if (this.form.mat_khau_moi) {
          payload.mat_khau = this.form.mat_khau_moi;
        }

        const res = await api.post(`/admin/danh-sach-nhan-vien/${adminId}`, payload);

        if (res.data?.status) {
          this.successMsg = res.data.message || 'Cập nhật thông tin thành công!';
          
          // Update local admin data
          const updatedData = res.data.data || {};
          this.admin = { ...this.admin, ...updatedData, ho_ten: this.form.name, so_dien_thoai: this.form.phone };
          
          // Update localStorage
          const raw = localStorage.getItem('admin_data');
          if (raw) {
            const data = JSON.parse(raw);
            const merged = { ...data, ...this.admin };
            localStorage.setItem('admin_data', JSON.stringify(merged));
          }
          
          // Clear password fields
          this.form.mat_khau_cu = '';
          this.form.mat_khau_moi = '';
          
          // Auto-hide success message
          setTimeout(() => { this.successMsg = ''; }, 4000);
        } else {
          this.errorMsg = res.data?.message || 'Cập nhật thất bại.';
        }
      } catch (err) {
        if (err.response?.status === 422) {
          const apiErrors = err.response.data?.errors || {};
          this.formErrors = {
            name: apiErrors.ho_ten?.[0] || '',
            phone: apiErrors.so_dien_thoai?.[0] || '',
            mat_khau_moi: apiErrors.mat_khau?.[0] || '',
          };
          this.errorMsg = err.response.data?.message || 'Dữ liệu không hợp lệ.';
        } else {
          this.errorMsg = err.response?.data?.message || 'Không thể kết nối đến máy chủ.';
        }
      } finally {
        this.saving = false;
      }
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
.btn-primary {
  background: linear-gradient(135deg, #4f46e5, #7c3aed);
  border: none;
}
.btn-primary:hover:not(:disabled) {
  background: linear-gradient(135deg, #4338ca, #6d28d9);
  transform: translateY(-1px);
  box-shadow: 0 4px 14px rgba(79, 70, 229, 0.35);
}
</style>
