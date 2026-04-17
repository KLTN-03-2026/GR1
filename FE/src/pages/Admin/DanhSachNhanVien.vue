<template>
  <div class="staff-page">
    <div class="page-header">
      <div>
        <h1 class="page-title">Danh sách nhân viên</h1>
      </div>
    </div>

    <div class="card card-custom table-card">
      <div class="card-header-custom">
        <div class="toolbar d-flex justify-content-between align-items-center w-100 gap-3 flex-wrap">
          <div class="search-box">
            <i class="bi bi-search"></i>
            <input
              v-model.trim="keyword"
              type="text"
              class="form-control search-input"
              placeholder="Tìm kiếm theo tên, email hoặc số điện thoại"
            >
          </div>

          <div class="d-flex align-items-center gap-2 flex-wrap">
            <button class="btn btn-light refresh-btn" :disabled="loading" @click="fetchAdmins">
              <span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status"></span>
              <i v-else class="bi bi-arrow-clockwise me-2"></i>
              Làm mới
            </button>
            <button
              class="btn btn-primary action-btn"
              type="button"
              data-bs-toggle="modal"
              data-bs-target="#modalThemNhanVien"
            >
              <i class="bi bi-person-plus-fill me-2"></i>Thêm nhân viên
            </button>
          </div>
        </div>
      </div>

      <div class="card-body p-0">
        <div v-if="loading" class="state-box">
          <div class="spinner-border text-primary mb-3" role="status"></div>
          <div class="state-title">Đang tải danh sách nhân viên...</div>
        </div>

        <div v-else-if="errorMessage" class="state-box">
          <div class="state-icon state-error">
            <i class="bi bi-exclamation-octagon"></i>
          </div>
          <div class="state-title">Không thể tải danh sách nhân viên</div>
          <div class="state-text">{{ errorMessage }}</div>
        </div>

        <div v-else-if="!filteredAdmins.length" class="state-box">
          <div class="state-icon state-empty">
            <i class="bi bi-inbox"></i>
          </div>
          <div class="state-title">Không có nhân viên nào phù hợp</div>
          <div class="state-text">Thử đổi từ khóa tìm kiếm hoặc kiểm tra lại dữ liệu từ backend.</div>
        </div>

        <div v-else class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead>
              <tr>
                <th class="text-center" style="width: 70px;">#</th>
                <th style="min-width: 260px;">Nhân viên</th>
                <th>Email</th>
                <th style="min-width: 150px;">Số điện thoại</th>
                <th style="min-width: 140px;">Chức vụ</th>
                <th style="min-width: 140px;">Trạng thái</th>
                <th class="text-center" style="width: 150px;">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(admin, index) in filteredAdmins" :key="admin.id || admin.email">
                <td class="text-center fw-semibold">{{ index + 1 }}</td>
                <td>
                  <div class="staff-cell">
                    <div class="avatar-badge">
                      {{ getInitials(admin) }}
                    </div>
                    <div>
                      <div class="staff-name">{{ getDisplayName(admin) }}</div>
                    </div>
                  </div>
                </td>
                <td>
                  <span class="text-body">{{ admin.email || 'Chưa có email' }}</span>
                </td>
                <td>
                  <span class="text-body">{{ getPhone(admin) || 'Chưa cập nhật' }}</span>
                </td>
                <td>
                  <span class="role-badge">
                    {{ getRole(admin) }}
                  </span>
                </td>
                <td>
                  <span class="status-badge" :class="getStatusClass(admin)">
                    {{ getStatusLabel(admin) }}
                  </span>
                </td>
                <td class="text-center">
                  <button
                    class="btn btn-sm btn-outline-primary me-2"
                    type="button"
                    title="Sửa"
                    @click="openEditModal(admin)"
                  >
                    <i class="bi bi-pencil-square"></i>
                  </button>
                  <button
                    class="btn btn-sm btn-outline-danger"
                    type="button"
                    title="Xóa"
                    @click="openDeleteModal(admin)"
                  >
                    <i class="bi bi-trash3"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div
      id="modalThemNhanVien"
      class="modal fade"
      tabindex="-1"
      aria-labelledby="modalThemNhanVienLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content staff-modal">
          <div class="modal-header border-0 pb-0">
            <div>
              <h5 id="modalThemNhanVienLabel" class="modal-title">Thêm nhân viên</h5>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <form @submit.prevent="createAdmin">
            <div class="modal-body pt-3">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Họ và tên</label>
                  <input
                    v-model.trim="create_admin.ten"
                    type="text"
                    class="form-control modal-input"
                    :class="{ 'is-invalid': formErrors.ten }"
                    placeholder="Nhập họ và tên"
                  >
                  <div v-if="formErrors.ten" class="invalid-feedback d-block">{{ formErrors.ten }}</div>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Email</label>
                  <input
                    v-model.trim="create_admin.email"
                    type="email"
                    class="form-control modal-input"
                    :class="{ 'is-invalid': formErrors.email }"
                    placeholder="Nhập địa chỉ email"
                  >
                  <div v-if="formErrors.email" class="invalid-feedback d-block">{{ formErrors.email }}</div>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Số điện thoại</label>
                  <input
                    v-model.trim="create_admin.so_dien_thoai"
                    type="text"
                    class="form-control modal-input"
                    :class="{ 'is-invalid': formErrors.so_dien_thoai }"
                    placeholder="Nhập số điện thoại"
                  >
                  <div v-if="formErrors.so_dien_thoai" class="invalid-feedback d-block">{{ formErrors.so_dien_thoai }}</div>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Chức vụ</label>
                  <select
                    v-model.number="create_admin.chuc_vu"
                    class="form-select modal-input"
                    :class="{ 'is-invalid': formErrors.chuc_vu }"
                  >
                    <option :value="null" disabled>Chọn chức vụ</option>
                    <option :value="1">Quản trị viên</option>
                    <option :value="2">Nhân viên</option>
                  </select>
                  <div v-if="formErrors.chuc_vu" class="invalid-feedback d-block">{{ formErrors.chuc_vu }}</div>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Mật khẩu</label>
                  <input
                    v-model="create_admin.mat_khau"
                    type="password"
                    class="form-control modal-input"
                    :class="{ 'is-invalid': formErrors.mat_khau }"
                    placeholder="Nhập mật khẩu"
                  >
                  <div v-if="formErrors.mat_khau" class="invalid-feedback d-block">{{ formErrors.mat_khau }}</div>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Trạng thái</label>
                  <select
                    v-model.number="create_admin.trang_thai_hoat_dong"
                    class="form-select modal-input"
                    :class="{ 'is-invalid': formErrors.trang_thai_hoat_dong }"
                  >
                    <option :value="1">Đang hoạt động</option>
                    <option :value="0">Tạm khóa</option>
                  </select>
                  <div v-if="formErrors.trang_thai_hoat_dong" class="invalid-feedback d-block">{{ formErrors.trang_thai_hoat_dong }}</div>
                </div>
              </div>
            </div>

            <div class="modal-footer border-0 pt-0">
              <button type="button" class="btn btn-light modal-cancel-btn" data-bs-dismiss="modal">Hủy</button>
              <button type="submit" class="btn btn-primary action-btn" :disabled="isSubmitting">
                <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-2" role="status"></span>
                Lưu nhân viên
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div
      id="modalSuaNhanVien"
      class="modal fade"
      tabindex="-1"
      aria-labelledby="modalSuaNhanVienLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content staff-modal">
          <div class="modal-header border-0 pb-0">
            <div>
              <h5 id="modalSuaNhanVienLabel" class="modal-title">Sửa nhân viên</h5>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <form @submit.prevent="updateAdmin">
            <div class="modal-body pt-3">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Họ và tên</label>
                  <input
                    v-model.trim="edit_admin.ten"
                    type="text"
                    class="form-control modal-input"
                    :class="{ 'is-invalid': editErrors.ten }"
                    placeholder="Tên nhân viên"
                  >
                  <div v-if="editErrors.ten" class="invalid-feedback d-block">{{ editErrors.ten }}</div>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Email</label>
                  <input
                    v-model.trim="edit_admin.email"
                    type="email"
                    class="form-control modal-input"
                    :class="{ 'is-invalid': editErrors.email }"
                    placeholder="Email nhân viên"
                  >
                  <div v-if="editErrors.email" class="invalid-feedback d-block">{{ editErrors.email }}</div>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Số điện thoại</label>
                  <input
                    v-model.trim="edit_admin.so_dien_thoai"
                    type="text"
                    class="form-control modal-input"
                    :class="{ 'is-invalid': editErrors.so_dien_thoai }"
                    placeholder="Số điện thoại"
                  >
                  <div v-if="editErrors.so_dien_thoai" class="invalid-feedback d-block">{{ editErrors.so_dien_thoai }}</div>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Chức vụ</label>
                  <select
                    v-model.number="edit_admin.chuc_vu"
                    class="form-select modal-input"
                    :class="{ 'is-invalid': editErrors.chuc_vu }"
                  >
                    <option :value="1">Quản trị viên</option>
                    <option :value="2">Nhân viên</option>
                  </select>
                  <div v-if="editErrors.chuc_vu" class="invalid-feedback d-block">{{ editErrors.chuc_vu }}</div>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Trạng thái</label>
                  <select
                    v-model.number="edit_admin.trang_thai"
                    class="form-select modal-input"
                    :class="{ 'is-invalid': editErrors.trang_thai }"
                  >
                    <option :value="1">Đang hoạt động</option>
                    <option :value="0">Tạm khóa</option>
                  </select>
                  <div v-if="editErrors.trang_thai" class="invalid-feedback d-block">{{ editErrors.trang_thai }}</div>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Mật khẩu mới</label>
                  <input
                    v-model="edit_admin.mat_khau"
                    type="password"
                    class="form-control modal-input"
                    :class="{ 'is-invalid': editErrors.mat_khau }"
                    placeholder="Bỏ trống nếu không đổi"
                  >
                  <div v-if="editErrors.mat_khau" class="invalid-feedback d-block">{{ editErrors.mat_khau }}</div>
                </div>
              </div>
            </div>

            <div class="modal-footer border-0 pt-0">
              <button type="button" class="btn btn-light modal-cancel-btn" data-bs-dismiss="modal">Hủy</button>
              <button type="submit" class="btn btn-primary action-btn" :disabled="isUpdating">
                <span v-if="isUpdating" class="spinner-border spinner-border-sm me-2" role="status"></span>
                Cập nhật
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div
      id="modalXoaNhanVien"
      class="modal fade"
      tabindex="-1"
      aria-labelledby="modalXoaNhanVienLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content staff-modal">
          <div class="modal-body delete-modal-body">
            <div class="delete-icon">
              <i class="bi bi-trash3"></i>
            </div>
            <h5 id="modalXoaNhanVienLabel" class="modal-title text-center">Xóa nhân viên</h5>
            <p class="delete-text mb-0">
              Bạn có chắc chắn muốn xóa nhân viên
              <strong>{{ delete_admin.ten || delete_admin.email || 'này' }}</strong>
              không?
            </p>
          </div>
          <div class="modal-footer border-0 justify-content-center pt-0">
            <button type="button" class="btn btn-light modal-cancel-btn" data-bs-dismiss="modal">Hủy</button>
            <button type="button" class="btn btn-danger modal-delete-btn" :disabled="isDeleting" @click="deleteAdmin">
              <span v-if="isDeleting" class="spinner-border spinner-border-sm me-2" role="status"></span>
              Xác nhận xóa
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

const API_URL = 'http://127.0.0.1:8000/api/admin/danh-sach-nhan-vien'
const CREATE_API_URL = 'http://127.0.0.1:8000/api/admin/danh-sach-nhan-vien/them-nhan-vien'

const defaultCreateAdmin = () => ({
  ten: '',
  email: '',
  so_dien_thoai: '',
  chuc_vu: null,
  mat_khau: '',
  trang_thai_hoat_dong: 1,
})

const defaultEditAdmin = () => ({
  id: null,
  ten: '',
  email: '',
  so_dien_thoai: '',
  chuc_vu: 2,
  mat_khau: '',
  trang_thai: 1,
})

const defaultDeleteAdmin = () => ({
  id: null,
  ten: '',
  email: '',
})

export default {
  name: 'DanhSachNhanVien',
  data() {
    return {
      admins: [],
      keyword: '',
      loading: false,
      isSubmitting: false,
      isUpdating: false,
      isDeleting: false,
      errorMessage: '',
      formErrors: {},
      editErrors: {},
      create_admin: defaultCreateAdmin(),
      edit_admin: defaultEditAdmin(),
      delete_admin: defaultDeleteAdmin(),
    }
  },
  computed: {
    filteredAdmins() {
      const keyword = this.keyword.toLowerCase()
      if (!keyword) return this.admins

      return this.admins.filter((admin) => {
        const values = [
          admin.id,
          admin.email,
          admin.ten,
          admin.ho_ten,
          admin.so_dien_thoai,
          admin.id_chuc_vu,
          admin.chuc_vu,
          admin.ten_chuc_vu,
          admin.trang_thai,
          admin.trang_thai_hoat_dong,
          admin.vai_tro,
          admin.role,
        ]

        return values.some((value) => String(value || '').toLowerCase().includes(keyword))
      })
    },
  },
  mounted() {
    this.fetchAdmins()
  },
  methods: {
    resetCreateForm() {
      this.formErrors = {}
      this.create_admin = defaultCreateAdmin()
    },
    resetEditForm() {
      this.editErrors = {}
      this.edit_admin = defaultEditAdmin()
    },
    resetDeleteForm() {
      this.delete_admin = defaultDeleteAdmin()
    },
    authHeader() {
      const token = localStorage.getItem('key_admin')
      return token
        ? {
            headers: {
              Authorization: `Bearer ${token}`,
            },
          }
        : {}
    },
    async createAdmin() {
      this.formErrors = {}
      this.isSubmitting = true

      try {
        const payload = {
          ho_ten: this.create_admin.ten,
          email: this.create_admin.email,
          so_dien_thoai: this.create_admin.so_dien_thoai,
          id_chuc_vu: this.create_admin.chuc_vu,
          mat_khau: this.create_admin.mat_khau,
          trang_thai: this.create_admin.trang_thai_hoat_dong,
        }

        const res = await axios.post(CREATE_API_URL, payload, this.authHeader())
        this.$toast?.success(res.data?.message || 'Thêm nhân viên thành công')
        this.resetCreateForm()
        await this.fetchAdmins()

        const modalElement = document.getElementById('modalThemNhanVien')
        const modalInstance = Modal.getInstance(modalElement) || Modal.getOrCreateInstance(modalElement)
        modalInstance?.hide()
      } catch (error) {
        if (error.response?.status === 422) {
          const apiErrors = error.response.data?.errors || {}
          this.formErrors = {
            ten: apiErrors.ho_ten?.[0] || '',
            email: apiErrors.email?.[0] || '',
            so_dien_thoai: apiErrors.so_dien_thoai?.[0] || '',
            chuc_vu: apiErrors.id_chuc_vu?.[0] || '',
            mat_khau: apiErrors.mat_khau?.[0] || '',
            trang_thai_hoat_dong: apiErrors.trang_thai?.[0] || '',
          }
          this.$toast?.error(error.response.data?.message || 'Dữ liệu không hợp lệ')
          return
        }

        this.$toast?.error(error.response?.data?.message || 'Không thể thêm nhân viên lúc này')
      } finally {
        this.isSubmitting = false
      }
    },
    openEditModal(admin) {
      this.resetEditForm()
      this.edit_admin = {
        id: admin.id,
        ten: admin.ho_ten || admin.ten || '',
        email: admin.email || '',
        so_dien_thoai: admin.so_dien_thoai || admin.sdt || '',
        chuc_vu: Number(admin.id_chuc_vu || admin.chuc_vu || 2),
        mat_khau: '',
        trang_thai: Number(admin.trang_thai ?? admin.trang_thai_hoat_dong ?? 1),
      }

      const modalElement = document.getElementById('modalSuaNhanVien')
      const modalInstance = Modal.getOrCreateInstance(modalElement)
      modalInstance?.show()
    },
    async updateAdmin() {
      if (!this.edit_admin.id) return

      this.editErrors = {}
      this.isUpdating = true

      try {
        const payload = {
          ho_ten: this.edit_admin.ten,
          email: this.edit_admin.email,
          so_dien_thoai: this.edit_admin.so_dien_thoai,
          id_chuc_vu: this.edit_admin.chuc_vu,
          trang_thai: this.edit_admin.trang_thai,
        }

        if (this.edit_admin.mat_khau) {
          payload.mat_khau = this.edit_admin.mat_khau
        }

        const res = await axios.post(`${API_URL}/${this.edit_admin.id}`, payload, this.authHeader())
        this.$toast?.success(res.data?.message || 'Cập nhật nhân viên thành công')
        this.resetEditForm()
        await this.fetchAdmins()

        const modalElement = document.getElementById('modalSuaNhanVien')
        const modalInstance = Modal.getInstance(modalElement) || Modal.getOrCreateInstance(modalElement)
        modalInstance?.hide()
      } catch (error) {
        if (error.response?.status === 422) {
          const apiErrors = error.response.data?.errors || {}
          this.editErrors = {
            ten: apiErrors.ho_ten?.[0] || '',
            email: apiErrors.email?.[0] || '',
            so_dien_thoai: apiErrors.so_dien_thoai?.[0] || '',
            chuc_vu: apiErrors.id_chuc_vu?.[0] || '',
            mat_khau: apiErrors.mat_khau?.[0] || '',
            trang_thai: apiErrors.trang_thai?.[0] || '',
          }
          this.$toast?.error(error.response.data?.message || 'Dữ liệu không hợp lệ')
          return
        }

        this.$toast?.error(error.response?.data?.message || 'Không thể cập nhật nhân viên lúc này')
      } finally {
        this.isUpdating = false
      }
    },
    openDeleteModal(admin) {
      this.resetDeleteForm()
      this.delete_admin = {
        id: admin.id,
        ten: admin.ho_ten || admin.ten || '',
        email: admin.email || '',
      }

      const modalElement = document.getElementById('modalXoaNhanVien')
      const modalInstance = Modal.getOrCreateInstance(modalElement)
      modalInstance?.show()
    },
    async deleteAdmin() {
      if (!this.delete_admin.id) return

      this.isDeleting = true

      try {
        const res = await axios.delete(`${API_URL}/${this.delete_admin.id}`, this.authHeader())
        this.$toast?.success(res.data?.message || 'Xóa nhân viên thành công')
        this.resetDeleteForm()
        await this.fetchAdmins()

        const modalElement = document.getElementById('modalXoaNhanVien')
        const modalInstance = Modal.getInstance(modalElement) || Modal.getOrCreateInstance(modalElement)
        modalInstance?.hide()
      } catch (error) {
        this.$toast?.error(error.response?.data?.message || 'Không thể xóa nhân viên lúc này')
      } finally {
        this.isDeleting = false
      }
    },
    async fetchAdmins() {
      this.loading = true
      this.errorMessage = ''

      try {
        const res = await axios.get(API_URL, this.authHeader())
        this.admins = Array.isArray(res.data.data) ? res.data.data : []
      } catch (error) {
        this.admins = []
        this.errorMessage = error.response?.data?.message || 'Không thể tải danh sách nhân viên.'
      } finally {
        this.loading = false
      }
    },
    getDisplayName(admin) {
      return admin.ho_ten || admin.ten || admin.email || `Nhân viên #${admin.id ?? ''}`
    },
    getInitials(admin) {
      const name = this.getDisplayName(admin).trim()
      const parts = name.split(/\s+/).filter(Boolean)
      if (!parts.length) return 'NV'
      if (parts.length === 1) return parts[0].slice(0, 2).toUpperCase()
      return `${parts[0][0] || ''}${parts[parts.length - 1][0] || ''}`.toUpperCase()
    },
    getRole(admin) {
      if (admin.ten_chuc_vu) {
        return admin.ten_chuc_vu
      }

      if (admin.vai_tro || admin.role) {
        return admin.vai_tro || admin.role
      }

      const roleMap = {
        1: 'Quản trị viên',
        2: 'Nhân viên',
      }

      return roleMap[Number(admin.id_chuc_vu || admin.chuc_vu)] || `Chức vụ #${admin.id_chuc_vu ?? admin.chuc_vu ?? '--'}`
    },
    getPhone(admin) {
      return admin.so_dien_thoai || admin.sdt || ''
    },
    getStatusLabel(admin) {
      return Number(admin.trang_thai ?? admin.trang_thai_hoat_dong) === 1 ? 'Đang hoạt động' : 'Tạm khóa'
    },
    getStatusClass(admin) {
      return Number(admin.trang_thai ?? admin.trang_thai_hoat_dong) === 1 ? 'status-active' : 'status-inactive'
    },
  },
}
</script>

<style scoped>
.staff-page {
  animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(6px);
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
  gap: 16px;
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

.card-header-custom {
  padding: 20px 22px;
  border-bottom: 1px solid #edf2f7;
  display: flex;
  justify-content: flex-end;
  gap: 16px;
  align-items: center;
}

.toolbar {
  min-width: 320px;
}

.search-box {
  position: relative;
  min-width: 300px;
}

.search-box i {
  position: absolute;
  top: 50%;
  left: 16px;
  transform: translateY(-50%);
  color: #94a3b8;
}

.search-input {
  min-height: 44px;
  padding-left: 42px;
  border-radius: 12px;
  border: 1px solid #dbe4f0;
  background: #fff;
}

.search-input:focus {
  border-color: #a5b4fc;
  box-shadow: 0 0 0 4px rgba(90, 103, 216, 0.12);
  background: #fff;
}

.action-btn {
  white-space: nowrap;
  background: linear-gradient(90deg, #5a67d8 0%, #7c3aed 100%);
  border: none;
  border-radius: 10px;
  padding: 10px 18px;
  font-weight: 600;
}

.refresh-btn {
  border-radius: 10px;
  padding: 10px 16px;
  font-weight: 600;
  border: 1px solid #dbe4f0;
  color: #475569;
  background: #fff;
}

.refresh-btn:hover:not(:disabled) {
  background: #f8fafc;
  border-color: #cbd5e1;
}

.table thead th {
  background: #f8fafc;
  color: #64748b;
  font-size: 0.8rem;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  padding: 14px 18px;
  border-bottom: 1px solid #e2e8f0;
}

.table tbody td {
  padding: 16px 18px;
  color: #334155;
  border-bottom: 1px solid #f1f5f9;
}

.table tbody tr:hover {
  background: #fafcff;
}

.staff-cell {
  display: flex;
  align-items: center;
  gap: 14px;
}

.avatar-badge {
  width: 46px;
  height: 46px;
  border-radius: 14px;
  background: linear-gradient(135deg, #5a67d8, #7c3aed);
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  letter-spacing: 0.04em;
  flex-shrink: 0;
}

.staff-name {
  font-weight: 700;
  color: #0f172a;
}

.role-badge {
  display: inline-flex;
  align-items: center;
  padding: 7px 12px;
  border-radius: 999px;
  background: rgba(90, 103, 216, 0.12);
  color: #4c51bf;
  font-size: 0.82rem;
  font-weight: 700;
}

.status-badge {
  display: inline-flex;
  align-items: center;
  padding: 7px 12px;
  border-radius: 999px;
  font-size: 0.82rem;
  font-weight: 700;
}

.status-active {
  background: rgba(16, 185, 129, 0.14);
  color: #047857;
}

.status-inactive {
  background: rgba(239, 68, 68, 0.14);
  color: #b91c1c;
}

.state-box {
  min-height: 320px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  padding: 32px 20px;
  text-align: center;
}

.state-icon {
  width: 72px;
  height: 72px;
  border-radius: 22px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.8rem;
  margin-bottom: 18px;
}

.state-error {
  background: #fff1f2;
  color: #dc2626;
}

.state-empty {
  background: #eff6ff;
  color: #2563eb;
}

.state-title {
  font-size: 1.1rem;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 6px;
}

.state-text {
  max-width: 420px;
  color: #64748b;
}

.staff-modal {
  border: 0;
  border-radius: 24px;
  box-shadow: 0 24px 60px rgba(15, 23, 42, 0.18);
}

.modal-title {
  font-size: 1.25rem;
  font-weight: 700;
  color: #0f172a;
}

.modal-subtitle {
  color: #64748b;
  margin-top: 6px;
}

.form-label {
  font-weight: 600;
  color: #334155;
  margin-bottom: 8px;
}

.modal-input {
  min-height: 46px;
  border-radius: 14px;
  border: 1px solid #dbe4f0;
  padding: 10px 14px;
  color: #0f172a;
}

.modal-input:focus {
  border-color: #a5b4fc;
  box-shadow: 0 0 0 4px rgba(90, 103, 216, 0.12);
}

.modal-textarea {
  min-height: 110px;
  resize: none;
}

.modal-cancel-btn {
  min-width: 110px;
  border-radius: 12px;
  border: 1px solid #dbe4f0;
  color: #475569;
  font-weight: 600;
}

.delete-modal-body {
  padding: 32px 28px 12px;
  text-align: center;
}

.delete-icon {
  width: 72px;
  height: 72px;
  border-radius: 22px;
  margin: 0 auto 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #fff1f2;
  color: #dc2626;
  font-size: 1.6rem;
}

.delete-text {
  max-width: 360px;
  margin: 12px auto 0;
  color: #64748b;
  line-height: 1.6;
}

.modal-delete-btn {
  min-width: 140px;
  border-radius: 12px;
  font-weight: 600;
}

@media (max-width: 768px) {
  .page-header {
    flex-direction: column;
  }

  .card-header-custom,
  .toolbar {
    min-width: 100%;
  }

  .search-box {
    width: 100%;
    min-width: 100%;
  }

  .modal-footer {
    flex-direction: column;
  }

  .modal-cancel-btn,
  .modal-delete-btn,
  .action-btn {
    width: 100%;
  }
}
</style>
