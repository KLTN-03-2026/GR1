<template>
  <div class="users-page">
    <div class="page-header">
      <div>
        <h1 class="page-title">Danh Sách Người Dùng</h1>
      </div>
    </div>

    <div class="card users-card">
      <div class="card-header-custom">
        <div class="toolbar d-flex justify-content-between align-items-center w-100 gap-3 flex-wrap">
          <div class="search-box">
            <i class="bi bi-search"></i>
            <input v-model.trim="search" type="text" class="form-control" placeholder="Tìm kiếm người dùng...">
          </div>
          <button class="btn btn-primary action-btn" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="bi bi-person-plus-fill me-2"></i>Thêm Tài Khoản
          </button>
        </div>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead>
              <tr>
                <th class="text-center" style="width: 60px">#</th>
                <th>Tên</th>
                <th>Email</th>
                <th>Số điện thoại</th>
                <th class="text-center" style="width: 150px">Thao tác</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="isLoading">
                <td colspan="5" class="empty-state">
                  <i class="bi bi-arrow-repeat spin"></i>
                  <p>Đang tải dữ liệu người dùng...</p>
                </td>
              </tr>

              <tr v-for="(item, index) in filteredUsers" :key="item.id" class="table-row">
                <td class="text-center">{{ index + 1 }}</td>
                <td>
                  <div class="user-cell">
                    <div class="avatar-wrapper mini-avatar">
                      <img v-if="item.anh_dai_dien" :src="resolveAvatarUrl(item.anh_dai_dien)" :alt="item.ten" class="avatar-image">
                      <span v-else class="avatar-fallback">
                        {{ getInitials(item.ten) }}
                      </span>
                    </div>
                    <div>
                      <p class="user-name">{{ item.ten }}</p>
                    </div>
                  </div>
                </td>
                <td>
                  <span class="info-text">{{ item.email }}</span>
                </td>
                <td>
                  <span class="info-text">{{ item.so_dien_thoai || 'Chưa cập nhật' }}</span>
                </td>
                <td class="text-center">
                  <button
                    class="btn btn-sm btn-outline-primary me-2"
                    type="button"
                    title="Sửa"
                    @click="moModalSua(item)"
                  >
                    <i class="bi bi-pencil-square"></i>
                  </button>
                  <button class="btn btn-sm btn-outline-danger" type="button" title="Xóa" @click="moModalXoa(item)">
                    <i class="bi bi-trash3"></i>
                  </button>
                </td>
              </tr>

              <tr v-if="!isLoading && !filteredUsers.length">
                <td colspan="5" class="empty-state">
                  <i class="bi bi-inboxes"></i>
                  <p>Không tìm thấy người dùng phù hợp</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 user-modal">
          <div class="modal-header modal-header-primary">
            <div>
              <h5 class="modal-title">Thêm người dùng mới</h5>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body p-4">
            <form @submit.prevent="themNguoiDung">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label modal-label" for="addTen">Tên người dùng</label>
                  <div class="input-group modal-input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input
                      v-model="create_nguoi_dung.ten"
                      type="text"
                      class="form-control"
                      :class="{ 'is-invalid': formErrors.ten }"
                      id="addTen"
                      placeholder="Nhập tên người dùng"
                    >
                  </div>
                  <div v-if="formErrors.ten" class="invalid-feedback d-block">{{ formErrors.ten }}</div>
                </div>

                <div class="col-md-6">
                  <label class="form-label modal-label" for="addEmail">Email</label>
                  <div class="input-group modal-input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input
                      v-model="create_nguoi_dung.email"
                      type="email"
                      class="form-control"
                      :class="{ 'is-invalid': formErrors.email }"
                      id="addEmail"
                      placeholder="Nhập email"
                    >
                  </div>
                  <div v-if="formErrors.email" class="invalid-feedback d-block">{{ formErrors.email }}</div>
                </div>

                <div class="col-md-6">
                  <label class="form-label modal-label" for="addPassword">Mật khẩu</label>
                  <div class="input-group modal-input-group">
                    <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                    <input
                      v-model="create_nguoi_dung.mat_khau"
                      type="password"
                      class="form-control"
                      :class="{ 'is-invalid': formErrors.mat_khau }"
                      id="addPassword"
                      placeholder="Nhập mật khẩu"
                    >
                  </div>
                  <div v-if="formErrors.mat_khau" class="invalid-feedback d-block">{{ formErrors.mat_khau }}</div>
                </div>

                <div class="col-md-6">
                  <label class="form-label modal-label" for="addPhone">Số điện thoại</label>
                  <div class="input-group modal-input-group">
                    <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                    <input
                      v-model="create_nguoi_dung.so_dien_thoai"
                      type="text"
                      class="form-control"
                      :class="{ 'is-invalid': formErrors.so_dien_thoai }"
                      id="addPhone"
                      placeholder="Nhập số điện thoại"
                    >
                  </div>
                  <div v-if="formErrors.so_dien_thoai" class="invalid-feedback d-block">{{ formErrors.so_dien_thoai }}</div>
                </div>

                <div class="col-12">
                  <label class="form-label modal-label" for="addAvatar">Avatar</label>
                  <div class="input-group modal-input-group">
                    <span class="input-group-text"><i class="bi bi-image"></i></span>
                    <input
                      v-model="create_nguoi_dung.anh_dai_dien"
                      type="text"
                      class="form-control"
                      :class="{ 'is-invalid': formErrors.anh_dai_dien }"
                      id="addAvatar"
                    >
                  </div>
                  <div v-if="formErrors.anh_dai_dien" class="invalid-feedback d-block">{{ formErrors.anh_dai_dien }}</div>
                </div>
              </div>

              <div class="modal-footer mt-3 border-0 px-0 pb-0">
                <button type="button" class="btn btn-outline-secondary modal-btn" data-bs-dismiss="modal">Đóng</button>
                <button type="submit" class="btn btn-primary modal-btn" :disabled="isSubmitting">
                  <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-2"></span>Thêm mới
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 user-modal">
          <div class="modal-header modal-header-primary">
            <div>
              <h5 class="modal-title">Cập nhật người dùng</h5>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body p-4">
            <form @submit.prevent="capNhatNguoiDung">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label modal-label" for="editTen">Tên người dùng</label>
                  <div class="input-group modal-input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input
                      v-model="edit_nguoi_dung.ten"
                      type="text"
                      class="form-control"
                      :class="{ 'is-invalid': editErrors.ten }"
                      id="editTen"
                      placeholder="Nhập tên người dùng"
                    >
                  </div>
                  <div v-if="editErrors.ten" class="invalid-feedback d-block">{{ editErrors.ten }}</div>
                </div>

                <div class="col-md-6">
                  <label class="form-label modal-label" for="editEmail">Email</label>
                  <div class="input-group modal-input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input
                      v-model="edit_nguoi_dung.email"
                      type="email"
                      class="form-control"
                      :class="{ 'is-invalid': editErrors.email }"
                      id="editEmail"
                      placeholder="Nhập email"
                      disabled
                    >
                  </div>
                  <div class="small text-muted mt-1">Email không thể thay đổi</div>
                  <div v-if="editErrors.email" class="invalid-feedback d-block">{{ editErrors.email }}</div>
                </div>

                <div class="col-md-6">
                  <label class="form-label modal-label" for="editPassword">Mật khẩu mới</label>
                  <div class="input-group modal-input-group">
                    <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                    <input
                      v-model="edit_nguoi_dung.mat_khau"
                      type="password"
                      class="form-control"
                      :class="{ 'is-invalid': editErrors.mat_khau }"
                      id="editPassword"
                      placeholder="Bỏ trống nếu không đổi"
                    >
                  </div>
                  <div v-if="editErrors.mat_khau" class="invalid-feedback d-block">{{ editErrors.mat_khau }}</div>
                </div>

                <div class="col-md-6">
                  <label class="form-label modal-label" for="editPhone">Số điện thoại</label>
                  <div class="input-group modal-input-group">
                    <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                    <input
                      v-model="edit_nguoi_dung.so_dien_thoai"
                      type="text"
                      class="form-control"
                      :class="{ 'is-invalid': editErrors.so_dien_thoai }"
                      id="editPhone"
                      placeholder="Nhập số điện thoại"
                    >
                  </div>
                  <div v-if="editErrors.so_dien_thoai" class="invalid-feedback d-block">{{ editErrors.so_dien_thoai }}</div>
                </div>

                <div class="col-12">
                  <label class="form-label modal-label" for="editAvatar">Avatar</label>
                  <div class="input-group modal-input-group">
                    <span class="input-group-text"><i class="bi bi-image"></i></span>
                    <input
                      v-model="edit_nguoi_dung.anh_dai_dien"
                      type="text"
                      class="form-control"
                      :class="{ 'is-invalid': editErrors.anh_dai_dien }"
                      id="editAvatar"
                    >
                  </div>
                  <div v-if="editErrors.anh_dai_dien" class="invalid-feedback d-block">{{ editErrors.anh_dai_dien }}</div>
                </div>
              </div>

              <div class="modal-footer mt-3 border-0 px-0 pb-0">
                <button type="button" class="btn btn-outline-secondary modal-btn" data-bs-dismiss="modal">Đóng</button>
                <button type="submit" class="btn btn-primary modal-btn" :disabled="isUpdating">
                  <span v-if="isUpdating" class="spinner-border spinner-border-sm me-2"></span>Lưu thay đổi
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 user-modal">
          <div class="modal-header modal-header-primary modal-header-danger">
            <div>
              <h5 class="modal-title">Xóa người dùng</h5>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body p-4">
            <div class="delete-modal-icon">
              <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
            <p class="delete-modal-text mb-0">
              Bạn có chắc muốn xóa người dùng <strong>{{ delete_nguoi_dung.ten || 'này' }}</strong> này không?
            </p>
          </div>

          <div class="modal-footer border-0 px-4 pb-4 pt-0">
            <button type="button" class="btn btn-outline-secondary modal-btn" data-bs-dismiss="modal">Đóng</button>
            <button type="button" class="btn btn-danger modal-btn" :disabled="isDeleting" @click="xoaNguoiDung">
              <span v-if="isDeleting" class="spinner-border spinner-border-sm me-2"></span>Xóa
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { Modal } from 'bootstrap';

export default {
  name: 'Users',
  data() {
    return {
      search: '',
      list_users: [],
      isLoading: false,
      isSubmitting: false,
      isUpdating: false,
      isDeleting: false,
      formErrors: {},
      editErrors: {},
      create_nguoi_dung: {
        ten: '',
        email: '',
        mat_khau: '',
        so_dien_thoai: '',
        anh_dai_dien: '',
      },
      edit_nguoi_dung: {
        id: null,
        ten: '',
        email: '',
        mat_khau: '',
        so_dien_thoai: '',
        anh_dai_dien: '',
      },
      delete_nguoi_dung: {
        id: null,
        ten: '',
        email: '',
      },
    };
  },
  computed: {
    filteredUsers() {
      const keyword = this.search.toLowerCase();

      if (!keyword) {
        return this.list_users;
      }

      return this.list_users.filter((user) => {
        return [user.ten, user.email, user.so_dien_thoai || '']
          .some((value) => String(value).toLowerCase().includes(keyword));
      });
    },
  },
  mounted() {
    this.layDataUser();
  },
  methods: {
    resetCreateForm() {
      this.formErrors = {};
      this.create_nguoi_dung = {
        ten: '',
        email: '',
        mat_khau: '',
        so_dien_thoai: '',
        anh_dai_dien: '',
      };
    },
    resetEditForm() {
      this.editErrors = {};
      this.edit_nguoi_dung = {
        id: null,
        ten: '',
        email: '',
        mat_khau: '',
        so_dien_thoai: '',
        anh_dai_dien: '',
      };
    },
    resetDeleteForm() {
      this.delete_nguoi_dung = {
        id: null,
        ten: '',
        email: '',
      };
    },
    layDataUser() {
      this.isLoading = true;
      axios
        .get('http://127.0.0.1:8000/api/admin/nguoi-dungs/get-data')
        .then((res) => {
          if (res.data && res.data.data) {
            this.list_users = res.data.data;
          } else {
            this.list_users = [];
          }
        })
        .catch((err) => {
          console.error(err);
          this.$toast?.error('Không tìm thấy dữ liệu người dùng');
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    getInitials(name) {
      return String(name || 'ND')
        .split(' ')
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part.charAt(0).toUpperCase())
        .join('');
    },
    resolveAvatarUrl(path) {
      if (!path) return '';
      if (/^(https?:)?\/\//i.test(path) || path.startsWith('data:') || path.startsWith('blob:')) {
        return path;
      }
      const cleanPath = path.startsWith('/') ? path : `/${path}`;
      return `http://127.0.0.1:8000${cleanPath}`;
    },
    moModalSua(item) {
      this.resetEditForm();
      this.edit_nguoi_dung = {
        id: item.id,
        ten: item.ten || '',
        email: item.email || '',
        mat_khau: '',
        so_dien_thoai: item.so_dien_thoai || '',
        anh_dai_dien: item.anh_dai_dien || '',
      };

      const modalElement = document.getElementById('editModal');
      const modalInstance = Modal.getOrCreateInstance(modalElement);
      modalInstance?.show();
    },
    moModalXoa(item) {
      this.resetDeleteForm();
      this.delete_nguoi_dung = {
        id: item.id,
        ten: item.ten || '',
        email: item.email || '',
      };

      const modalElement = document.getElementById('deleteModal');
      const modalInstance = Modal.getOrCreateInstance(modalElement);
      modalInstance?.show();
    },
    themNguoiDung() {
      this.formErrors = {};

      const payload = {
        ho_va_ten: this.create_nguoi_dung.ten,
        email: this.create_nguoi_dung.email,
        password: this.create_nguoi_dung.mat_khau,
        password_confirmation: this.create_nguoi_dung.mat_khau,
        so_dien_thoai: this.create_nguoi_dung.so_dien_thoai,
        anh_dai_dien: this.create_nguoi_dung.anh_dai_dien || null,
      };

      this.isSubmitting = true;
      axios.post('http://127.0.0.1:8000/api/admin/nguoi-dungs/create', payload)
        .then((res) => {
          this.$toast?.success(res.data?.message || 'Thêm tài khoản người dùng thành công');
          this.layDataUser();
          this.resetCreateForm();
          const modalElement = document.getElementById('addModal');
          const modalInstance = Modal.getInstance(modalElement);
          modalInstance?.hide();
        })
        .catch((err) => {
          if (err.response?.status === 422) {
            const apiErrors = err.response.data.errors || {};
            this.formErrors = {
              ten: apiErrors.ho_va_ten?.[0] || '',
              email: apiErrors.email?.[0] || '',
              mat_khau: apiErrors.password?.[0] || '',
              so_dien_thoai: apiErrors.so_dien_thoai?.[0] || '',
              anh_dai_dien: apiErrors.anh_dai_dien?.[0] || '',
            };

            if (!this.formErrors.email) {
              this.$toast?.error(err.response.data.message || 'Dữ liệu không hợp lệ');
            }
            return;
          }

          this.$toast?.error(err.response?.data?.message || 'Không thể thêm người dùng lúc này');
        })
        .finally(() => {
          this.isSubmitting = false;
        });
    },
    capNhatNguoiDung() {
      this.editErrors = {};

      const payload = {
        ho_va_ten: this.edit_nguoi_dung.ten,
        email: this.edit_nguoi_dung.email,
        so_dien_thoai: this.edit_nguoi_dung.so_dien_thoai,
        anh_dai_dien: this.edit_nguoi_dung.anh_dai_dien || null,
      };

      if (this.edit_nguoi_dung.mat_khau) {
        payload.password = this.edit_nguoi_dung.mat_khau;
        payload.password_confirmation = this.edit_nguoi_dung.mat_khau;
      }

      this.isUpdating = true;
      axios.post(`http://127.0.0.1:8000/api/admin/nguoi-dungs/${this.edit_nguoi_dung.id}`, payload)
        .then((res) => {
          this.$toast?.success(res.data?.message || 'Cập nhật người dùng thành công');
          this.layDataUser();
          this.resetEditForm();
          const modalElement = document.getElementById('editModal');
          const modalInstance = Modal.getInstance(modalElement);
          modalInstance?.hide();
        })
        .catch((err) => {
          if (err.response?.status === 422) {
            const apiErrors = err.response.data.errors || {};
            this.editErrors = {
              ten: apiErrors.ho_va_ten?.[0] || '',
              email: apiErrors.email?.[0] || '',
              mat_khau: apiErrors.password?.[0] || '',
              so_dien_thoai: apiErrors.so_dien_thoai?.[0] || '',
              anh_dai_dien: apiErrors.anh_dai_dien?.[0] || '',
            };
            this.$toast?.error(err.response.data.message || 'Dữ liệu không hợp lệ');
            return;
          }

          this.$toast?.error(err.response?.data?.message || 'Không thể cập nhật người dùng lúc này');
        })
        .finally(() => {
          this.isUpdating = false;
        });
    },
    xoaNguoiDung() {
      if (!this.delete_nguoi_dung.id) return;

      this.isDeleting = true;
      axios.delete(`http://127.0.0.1:8000/api/admin/nguoi-dungs/${this.delete_nguoi_dung.id}`)
        .then((res) => {
          this.$toast?.success(res.data?.message || 'Xóa người dùng thành công');
          this.layDataUser();
          this.resetDeleteForm();
          const modalElement = document.getElementById('deleteModal');
          const modalInstance = Modal.getInstance(modalElement);
          modalInstance?.hide();
        })
        .catch((err) => {
          this.$toast?.error(err.response?.data?.message || 'Không thể xóa người dùng lúc này');
        })
        .finally(() => {
          this.isDeleting = false;
        });
    },
  },
};
</script>

<style scoped>
.users-page {
  animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }

  to {
    opacity: 1;
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
  margin-bottom: 6px;
  font-size: 1.85rem;
  font-weight: 700;
  color: #1e293b;
  letter-spacing: -0.03em;
}

.action-btn {
  white-space: nowrap;
  background: linear-gradient(90deg, #5a67d8 0%, #7c3aed 100%);
  border: none;
  border-radius: 10px;
  padding: 10px 18px;
  font-weight: 600;
}

.users-card {
  border: 1px solid #e9ecef;
  border-radius: 16px;
  background: #ffffff;
  box-shadow: 0 12px 30px rgba(15, 23, 42, 0.05);
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
  min-width: 280px;
}

.search-box i {
  position: absolute;
  top: 50%;
  left: 14px;
  transform: translateY(-50%);
  color: #94a3b8;
}

.search-box .form-control {
  border-radius: 12px;
  border: 1px solid #dbe4f0;
  min-height: 44px;
  padding-left: 42px;
  box-shadow: none;
}

.search-box .form-control:focus {
  border-color: #a5b4fc;
  box-shadow: 0 0 0 4px rgba(90, 103, 216, 0.12);
}

.table thead th {
  background: #f8fafc;
  color: #64748b;
  font-size: 0.8rem;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  font-weight: 700;
  padding: 16px 18px;
  border-bottom: 1px solid #edf2f7;
  white-space: nowrap;
}

.table tbody td {
  padding: 18px;
  border-bottom: 1px solid #f1f5f9;
  color: #334155;
}

.table-row:hover {
  background: #f8fafc;
}

.table tbody tr:last-child td {
  border-bottom: none;
}

.user-cell {
  display: flex;
  align-items: center;
  gap: 14px;
}

.avatar-wrapper {
  width: 48px;
  height: 48px;
  border-radius: 14px;
  overflow: hidden;
  background: linear-gradient(135deg, #5a67d8, #7c3aed);
  color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  font-weight: 700;
}

.mini-avatar {
  width: 42px;
  height: 42px;
}

.avatar-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.avatar-fallback {
  font-size: 0.95rem;
  letter-spacing: 0.04em;
}

.user-name {
  margin-bottom: 4px;
  font-weight: 700;
  color: #1e293b;
}

.user-id {
  margin: 0;
  font-size: 0.82rem;
  color: #94a3b8;
}

.info-text {
  color: #334155;
  font-weight: 500;
}

.empty-state {
  padding: 36px 20px !important;
  text-align: center;
  color: #94a3b8 !important;
}

.empty-state i {
  display: block;
  font-size: 2rem;
  margin-bottom: 10px;
}

.empty-state p {
  margin: 0;
}

.user-modal {
  border: none;
  overflow: hidden;
  box-shadow: 0 30px 80px rgba(15, 23, 42, 0.18);
}

.modal-header-primary {
  background: linear-gradient(135deg, #2563eb, #7c3aed);
  color: #fff;
  border-bottom: none;
  padding: 20px 24px;
}

.modal-header-danger {
  background: linear-gradient(135deg, #dc2626, #f97316);
}

.modal-title {
  font-size: 1.2rem;
  font-weight: 700;
  margin-bottom: 4px;
}

.modal-subtitle {
  color: rgba(255, 255, 255, 0.8);
  font-size: 0.9rem;
}

.modal-label {
  font-size: 0.85rem;
  font-weight: 600;
  color: #334155;
  margin-bottom: 8px;
}

.modal-input-group {
  border: 1px solid #dbe4f0;
  border-radius: 14px;
  overflow: hidden;
  background: #f8fafc;
}

.modal-input-group .input-group-text {
  border: none;
  background: transparent;
  color: #64748b;
}

.modal-input-group .form-control {
  border: none;
  background: transparent;
  min-height: 48px;
  box-shadow: none;
}

.modal-input-group:focus-within {
  border-color: #a5b4fc;
  box-shadow: 0 0 0 4px rgba(90, 103, 216, 0.12);
}

.modal-btn {
  min-width: 120px;
  border-radius: 12px;
  font-weight: 600;
  padding: 10px 18px;
}

.delete-modal-icon {
  width: 76px;
  height: 76px;
  margin: 0 auto 18px;
  border-radius: 999px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, rgba(239, 68, 68, 0.14), rgba(248, 113, 113, 0.24));
  color: #dc2626;
  font-size: 2rem;
  box-shadow: inset 0 0 0 1px rgba(239, 68, 68, 0.08);
}

.delete-modal-text {
  max-width: 360px;
  margin: 0 auto;
  text-align: center;
  font-size: 1rem;
  line-height: 1.7;
  color: #334155;
}

.spin {
  animation: spin 0.9s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }

  to {
    transform: rotate(360deg);
  }
}

@media (max-width: 991.98px) {
  .page-header,
  .card-header-custom {
    flex-direction: column;
    align-items: stretch;
  }

  .toolbar {
    min-width: 100%;
  }

  .search-box {
    width: 100%;
  }
}

@media (max-width: 767.98px) {
  .page-title {
    font-size: 1.55rem;
  }

  .table thead th,
  .table tbody td {
    padding: 14px;
  }

  .delete-modal-text {
    font-size: 0.95rem;
  }
}
</style>
