<template>
  <div class="role-page">
    <div class="page-header">
      <div>
        <h1 class="page-title">Quản lý Vai trò và Phân quyền</h1>
        <p class="text-muted mt-1">Cấu hình các chức năng truy cập cho từng chức vụ nhân viên.</p>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-primary action-btn" @click="openCreateRoleModal">
          <i class="bi bi-shield-plus me-2"></i>Thêm chức vụ mới
        </button>
      </div>
    </div>

    <div class="row g-4">
      <!-- Cột trái: Danh sách chức vụ -->
      <div class="col-lg-4">
        <div class="card card-custom h-100">
          <div class="card-header border-bottom-0 pb-0">
            <h5 class="mb-0 fw-bold"><i class="bi bi-person-badge text-primary me-2"></i>Danh sách Vai trò</h5>
          </div>
          <div class="card-body">
            <div v-if="loading" class="text-center py-4">
              <div class="spinner-border text-primary" role="status"></div>
            </div>

            <div v-else class="role-list">
              <div v-for="role in roles" :key="role.id" class="role-item"
                :class="{ 'active': selectedRole && selectedRole.id === role.id }" @click="selectRole(role)">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <strong class="text-dark">{{ role.ten_chuc_vu }}</strong>
                  <span v-if="role.id === 1" class="badge bg-danger rounded-pill custom-badge">Toàn quyền</span>
                  <span v-else class="badge bg-primary rounded-pill custom-badge">{{ role.chuc_nangs?.length || 0 }}
                    quyền</span>
                </div>
                <div class="text-muted small">{{ role.mo_ta || 'Không có mô tả' }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Cột phải: Cấu hình Phân quyền -->
      <div class="col-lg-8">
        <div class="card card-custom h-100">
          <div class="card-header border-bottom-0 pb-0 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">
              <i class="bi bi-ui-checks text-success me-2"></i>Bảng Phân quyền
              <span v-if="selectedRole" class="text-primary">- {{ selectedRole.ten_chuc_vu }}</span>
            </h5>
            <div class="d-flex gap-2">
              <button v-if="selectedRole && selectedRole.id !== 1"
                class="btn btn-sm btn-outline-danger px-3 fw-semibold action-btn-sm" @click="openDeleteRoleModal">
                <i class="bi bi-trash3 me-2"></i>Xóa
              </button>
              <button v-if="selectedRole && selectedRole.id !== 1"
                class="btn btn-sm btn-success px-3 fw-semibold action-btn-sm" :disabled="savingPermissions"
                @click="savePermissions">
                <span v-if="savingPermissions" class="spinner-border spinner-border-sm me-2" role="status"></span>
                <i v-else class="bi bi-save me-2"></i>Lưu phân quyền
              </button>
            </div>
          </div>
          <div class="card-body">
            <div v-if="!selectedRole" class="empty-state py-5 mt-4 text-center">
              <i class="bi bi-shield-lock text-muted" style="font-size: 3rem;"></i>
              <p class="text-muted mt-3">Chọn một chức vụ bên trái để xem và sửa bộ quyền.</p>
            </div>

            <div v-else-if="selectedRole.id === 1" class="empty-state py-5 mt-4 text-center">
              <i class="bi bi-shield-fill-check text-danger" style="font-size: 3rem;"></i>
              <p class="text-muted mt-3">Vai trò <strong>Super Admin</strong> có toàn quyền truy cập toàn bộ hệ
                thống.<br> Không thể điều chỉnh quyền của vai trò này.</p>
            </div>

            <div v-else class="permissions-container mt-3">
              <div class="row g-4">
                <!-- Group Permissions by nhom_chuc_nang -->
                <div v-for="(group, groupName) in groupedPermissions" :key="groupName" class="col-md-6">
                  <div class="permission-group-card">
                    <h6 class="group-title">{{ groupName }}</h6>
                    <div class="permission-list mt-3">
                      <div class="form-check form-switch custom-switch mb-3" v-for="perm in group" :key="perm.id">
                        <input class="form-check-input" type="checkbox" role="switch" :value="perm.ma_chuc_nang"
                          :id="'perm_' + perm.id" v-model="selectedPermissions">
                        <label class="form-check-label ms-2" :for="'perm_' + perm.id">
                          {{ perm.ten_chuc_nang }}
                        </label>
                      </div>
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Form (Reuse logic for create/edit role basic info) -->
    <div id="modalRole" class="modal fade" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header border-0 pb-0">
            <h5 class="modal-title">Thêm chức vụ mới</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form @submit.prevent="createRole">
            <div class="modal-body pt-4">
              <div class="mb-3">
                <label class="form-label">Tên chức vụ</label>
                <input v-model="formRole.ten_chuc_vu" type="text" class="form-control" required
                  placeholder="Ví dụ: Kế toán">
              </div>
              <div class="mb-3">
                <label class="form-label">Mô tả vai trò</label>
                <textarea v-model="formRole.mo_ta" class="form-control" rows="3"
                  placeholder="Mô tả công việc của chức vụ này..."></textarea>
              </div>
            </div>
            <div class="modal-footer border-0 pt-0">
              <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
              <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
                Lưu chức vụ
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal Delete Role -->
    <div id="modalDeleteRole" class="modal fade" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
          <div class="modal-header border-0 pb-0 justify-content-center mt-3">
            <div class="delete-icon-wrapper">
              <i class="bi bi-exclamation-triangle text-danger fs-1"></i>
            </div>
          </div>
          <div class="modal-body text-center pt-3 pb-4">
            <h4 class="fw-bold mb-2">Xóa vai trò?</h4>
            <p class="text-muted mb-0">Bạn có chắc chắn muốn xóa vai trò <strong class="text-dark">{{
              selectedRole?.ten_chuc_vu }}</strong>?</p>
            <p class="text-muted small mt-1">Hành động này không thể hoàn tác.</p>
          </div>
          <div class="modal-footer border-0 pt-0 justify-content-center gap-2 mb-3">
            <button type="button" class="btn btn-light px-4 fw-semibold" data-bs-dismiss="modal">Hủy</button>
            <button type="button" class="btn btn-danger px-4 fw-semibold" :disabled="isDeleting" @click="deleteRole">
              <span v-if="isDeleting" class="spinner-border spinner-border-sm me-2"></span>Xóa vai trò
            </button>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
import axios from 'axios'
import { Modal } from 'bootstrap'

export default {
  name: 'PhanQuyen',
  data() {
    return {
      roles: [],
      permissions: [],
      selectedRole: null,
      selectedPermissions: [], // Mảng chứa ma_chuc_nang
      loading: true,
      savingPermissions: false,
      isSubmitting: false,
      isDeleting: false,
      formRole: {
        ten_chuc_vu: '',
        mo_ta: ''
      }
    }
  },
  computed: {
    groupedPermissions() {
      const groups = {};
      this.permissions.forEach(p => {
        const groupName = p.nhom_chuc_nang || 'Chức năng khác';
        if (!groups[groupName]) groups[groupName] = [];
        groups[groupName].push(p);
      });
      return groups;
    }
  },
  mounted() {
    this.fetchData();
  },
  methods: {
    authHeader() {
      const token = localStorage.getItem('key_admin')
      return token ? { headers: { Authorization: `Bearer ${token}` } } : {}
    },
    async fetchData() {
      this.loading = true;
      try {
        const [rolesRes, permsRes] = await Promise.all([
          axios.get('http://127.0.0.1:8000/api/chuc-vus', this.authHeader()),
          axios.get('http://127.0.0.1:8000/api/chuc-nangs', this.authHeader())
        ]);

        this.roles = rolesRes.data?.data || [];
        this.permissions = permsRes.data?.data || [];

        if (this.roles.length > 0 && !this.selectedRole) {
          this.selectRole(this.roles[0]);
        }
      } catch (error) {
        this.$toast?.error('Lỗi khi tải dữ liệu phân quyền');
      } finally {
        this.loading = false;
      }
    },
    selectRole(role) {
      this.selectedRole = role;
      if (role.id === 1) {
        this.selectedPermissions = []; // View only message
      } else {
        // Lấy danh sách ma_chuc_nang mà role đang có
        this.selectedPermissions = (role.chuc_nangs || []).map(p => p.ma_chuc_nang);
      }
    },
    openCreateRoleModal() {
      this.formRole = { ten_chuc_vu: '', mo_ta: '' };
      const m = Modal.getOrCreateInstance(document.getElementById('modalRole'));
      m.show();
    },
    async createRole() {
      this.isSubmitting = true;
      try {
        const res = await axios.post('http://127.0.0.1:8000/api/chuc-vus', this.formRole, this.authHeader());
        this.$toast?.success('Thêm chức vụ thành công');
        Modal.getInstance(document.getElementById('modalRole'))?.hide();
        await this.fetchData();
      } catch (e) {
        this.$toast?.error('Lỗi khi thêm chức vụ');
      } finally {
        this.isSubmitting = false;
      }
    },
    openDeleteRoleModal() {
      if (!this.selectedRole || this.selectedRole.id === 1) return;
      const m = Modal.getOrCreateInstance(document.getElementById('modalDeleteRole'));
      m.show();
    },
    async deleteRole() {
      if (!this.selectedRole || this.selectedRole.id === 1) return;
      this.isDeleting = true;
      try {
        await axios.delete(`http://127.0.0.1:8000/api/chuc-vus/${this.selectedRole.id}`, this.authHeader());
        this.$toast?.success('Đã xóa chức vụ thành công');
        Modal.getInstance(document.getElementById('modalDeleteRole'))?.hide();
        this.selectedRole = null;
        await this.fetchData();
      } catch (e) {
        this.$toast?.error('Không thể xóa chức vụ. Có thể chức vụ này đang được sử dụng.');
      } finally {
        this.isDeleting = false;
      }
    },
    async savePermissions() {
      if (!this.selectedRole) return;
      this.savingPermissions = true;
      try {
        const payload = {
          permissions: this.selectedPermissions // backend handles array of ma_chuc_nang
        };
        const url = `http://127.0.0.1:8000/api/chuc-vus/${this.selectedRole.id}`;
        await axios.put(url, payload, this.authHeader());

        this.$toast?.success('Cập nhật phân quyền thành công');
        await this.fetchData();
        // Re-select proper role
        const updated = this.roles.find(r => r.id === this.selectedRole.id);
        if (updated) this.selectRole(updated);

      } catch (e) {
        this.$toast?.error('Không thể cập nhật quyền');
      } finally {
        this.savingPermissions = false;
      }
    }
  }
}
</script>

<style scoped>
.role-page {
  animation: fadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 24px;
}

.page-title {
  font-size: 1.75rem;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 4px;
}

.card-custom {
  background: #fff;
  border: 1px solid #e9ecef;
  border-radius: 18px;
  box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
}

.role-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.role-item {
  padding: 18px 20px;
  border-radius: 16px;
  border: 1px solid #e2e8f0;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  background: #ffffff;
  position: relative;
}

.role-item:hover {
  border-color: #cbd5e1;
  background: #f8fafc;
  transform: translateY(-2px);
  box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.05);
}

.role-item.active {
  background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
  border-color: transparent;
  box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.4);
}

.role-item.active .text-dark {
  color: #ffffff !important;
}

.role-item.active .text-muted {
  color: rgba(255, 255, 255, 0.8) !important;
}

.role-item.active .badge.bg-primary {
  background-color: rgba(255, 255, 255, 0.25) !important;
  color: #ffffff !important;
  border: 1px solid rgba(255, 255, 255, 0.3);
}

.custom-badge {
  font-size: 0.72rem;
  padding: 5px 12px;
  font-weight: 700;
  letter-spacing: 0.02em;
}

.action-btn-sm {
  border-radius: 8px;
  padding: 6px 14px;
  font-size: 0.85rem;
  display: inline-flex;
  align-items: center;
  transition: all 0.2s;
}

.action-btn-sm:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
}

.permission-group-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  padding: 24px;
  height: 100%;
  transition: all 0.3s ease;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
}

.permission-group-card:hover {
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.025);
  border-color: #cbd5e1;
  transform: translateY(-2px);
}

.group-title {
  font-weight: 800;
  color: #1e293b;
  margin-bottom: 20px;
  padding-bottom: 12px;
  font-size: 1.1rem;
  display: flex;
  align-items: center;
  position: relative;
}

.group-title::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 40px;
  height: 3px;
  background: #6366f1;
  border-radius: 3px;
}

.custom-switch .form-check-input {
  width: 2.8em;
  height: 1.4em;
  margin-top: 0;
  margin-left: -3rem;
  cursor: pointer;
  background-color: #cbd5e1;
  border: none;
  transition: background-color 0.25s ease, box-shadow 0.25s ease;
}

.custom-switch .form-check-input:focus {
  box-shadow: none;
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='rgba%28255, 255, 255, 1%29'/%3e%3c/svg%3e");
}

.custom-switch .form-check-input:checked {
  background-color: #10b981;
  /* Emerald green */
  box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
}

.custom-switch .form-check-label {
  font-weight: 600;
  color: #334155;
  cursor: pointer;
  padding-top: 0;
  transition: color 0.2s;
  user-select: none;
}

.custom-switch:hover .form-check-label {
  color: #0f172a;
}

.custom-switch {
  padding: 8px 12px;
  padding-left: 3.5rem;
  border-radius: 10px;
  transition: background 0.2s;
  display: flex;
  align-items: center;
  margin-bottom: 6px;
}

.custom-switch:hover {
  background: #f8fafc;
}

.delete-icon-wrapper {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: #fef2f2;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto;
}
</style>