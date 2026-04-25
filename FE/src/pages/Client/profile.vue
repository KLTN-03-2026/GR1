<template>
  <div class="profile-page py-5">
    <div class="container" style="max-width: 860px;">
      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status"></div>
        <p class="mt-3 text-muted">Đang tải thông tin...</p>
      </div>

      <div v-else>
        <div class="profile-hero-card mb-4">
          <div class="hero-bg"></div>
          <div class="hero-content d-flex align-items-end gap-4 flex-wrap">
            <div class="avatar-wrap">
              <div v-if="hasAvatar" class="profile-avatar avatar-image-frame">
                <img :src="avatarSrc" :alt="user.ten || 'Avatar'" class="profile-avatar-image" @error="handleAvatarError">
              </div>
              <div v-else class="profile-avatar">{{ avatarLetter }}</div>
              <div class="avatar-badge"><i class="bi bi-patch-check-fill text-primary"></i></div>
            </div>

            <div class="flex-grow-1 pb-1">
              <h2 class="profile-name mb-0">{{ user.ten }}</h2>
              <p class="profile-email mb-0">
                <i class="bi bi-envelope me-1"></i>{{ user.email }}
              </p>
            </div>

            <div class="d-flex gap-3 pb-1">
              <div class="hero-stat text-center">
                <div class="hero-stat-value">{{ stats.lichTrinh }}</div>
                <div class="hero-stat-label">Lịch trình</div>
              </div>
              <div class="hero-stat text-center">
                <div class="hero-stat-value">{{ stats.nhom }}</div>
                <div class="hero-stat-label">Nhóm</div>
              </div>
            </div>
          </div>
        </div>

        <ul class="nav profile-tabs mb-4" id="profileTab">
          <li class="nav-item">
            <button class="profile-tab-btn" :class="{ active: tab === 'info' }" @click="tab = 'info'">
              <i class="bi bi-person me-1"></i>Thông tin cá nhân
            </button>
          </li>
          <li class="nav-item">
            <button class="profile-tab-btn" :class="{ active: tab === 'password' }" @click="tab = 'password'">
              <i class="bi bi-shield-lock me-1"></i>Đổi mật khẩu
            </button>
          </li>
        </ul>

        <div v-if="tab === 'info'" class="profile-card fade-in">
          <h5 class="section-title"><i class="bi bi-person-lines-fill me-2 text-primary"></i>Thông tin cá nhân</h5>
          <form @submit.prevent="capNhatThongTin" class="mt-3">
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label fw-semibold small text-secondary">Ảnh đại diện</label>
                <div class="avatar-upload-panel">
                  <div class="avatar-upload-preview">
                    <img v-if="avatarPreviewSrc" :src="avatarPreviewSrc" alt="Avatar preview" class="avatar-upload-image">
                    <div v-else class="avatar-upload-fallback">{{ avatarLetter }}</div>
                  </div>
                  <div class="avatar-upload-actions">
                    <input
                      ref="avatarInput"
                      type="file"
                      class="d-none"
                      accept="image/png,image/jpeg,image/jpg,image/webp"
                      @change="chonAnhDaiDien"
                    >
                    <div class="small text-muted mb-2">
                      Chọn ảnh từ máy tính. Hỗ trợ PNG, JPG, JPEG, WEBP. Tối đa 2MB.
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                      <button type="button" class="btn btn-light rounded-3 px-4" @click="moChonAnh">
                        <i class="bi bi-image me-1"></i>Chọn ảnh
                      </button>
                      <button type="button" class="btn btn-light rounded-3 px-4" :disabled="!avatarFile && !avatarPreview" @click="huyAnhDaiDienTam">
                        <i class="bi bi-x-circle me-1"></i>Hủy
                      </button>
                    </div>
                    <div v-if="avatarFile" class="small text-primary mt-2">
                      Ảnh mới đã được chọn và sẽ được tải lên khi bấm "Lưu thay đổi".
                    </div>
                    <div v-if="avatarError" class="text-danger small mt-2">{{ avatarError }}</div>
                  </div>
                </div>
              </div>

              <div class="col-sm-6">
                <label class="form-label fw-semibold small text-secondary">Họ tên <span class="text-danger">*</span></label>
                <div class="input-group rounded-3 bg-light" :class="{ 'border border-danger': editErrors.ten }">
                  <span class="input-group-text border-0 bg-transparent text-primary"><i class="bi bi-person"></i></span>
                  <input
                    v-model="editForm.ten"
                    type="text"
                    class="form-control border-0 bg-transparent"
                    :class="{ 'is-invalid': editErrors.ten }"
                    placeholder="Nguyễn Văn A"
                  >
                </div>
                <div v-if="editErrors.ten" class="text-danger small mt-1">{{ editErrors.ten }}</div>
              </div>

              <div class="col-sm-6">
                <label class="form-label fw-semibold small text-secondary">Email</label>
                <div class="input-group rounded-3 bg-light opacity-75">
                  <span class="input-group-text border-0 bg-transparent text-secondary"><i class="bi bi-envelope"></i></span>
                  <input :value="user.email" type="email" class="form-control border-0 bg-transparent" disabled>
                </div>
                <div class="small text-muted mt-1"><i class="bi bi-info-circle me-1"></i>Email không thể thay đổi</div>
              </div>

              <div class="col-sm-6">
                <label class="form-label fw-semibold small text-secondary">Số điện thoại</label>
                <div class="input-group rounded-3 bg-light" :class="{ 'border border-danger': editErrors.so_dien_thoai }">
                  <span class="input-group-text border-0 bg-transparent text-primary"><i class="bi bi-phone"></i></span>
                  <input v-model="editForm.so_dien_thoai" type="text" class="form-control border-0 bg-transparent" placeholder="09xx...">
                </div>
                <div v-if="editErrors.so_dien_thoai" class="text-danger small mt-1">{{ editErrors.so_dien_thoai }}</div>
              </div>

              <div class="col-sm-6">
                <label class="form-label fw-semibold small text-secondary">Trạng thái</label>
                <div class="info-field info-field-inline">
                  <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1 fs-7">
                    <i class="bi bi-check-circle me-1"></i>Hoạt động
                  </span>
                </div>
              </div>

              <div class="col-12">
                <label class="form-label fw-semibold small text-secondary">Tham gia từ</label>
                <div class="info-field info-field-inline">
                  <span class="info-value">{{ formatDateTime(user.created_at) }}</span>
                </div>
              </div>

              <div class="col-12 mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-save px-5" :disabled="savingInfo || uploadingAvatar">
                  <span v-if="savingInfo || uploadingAvatar"><span class="spinner-border spinner-border-sm me-2"></span>Đang lưu...</span>
                  <span v-else><i class="bi bi-check-lg me-1"></i>Lưu thay đổi</span>
                </button>
                <button type="button" class="btn btn-light rounded-3 px-4" @click="resetEditForm">
                  <i class="bi bi-arrow-counterclockwise me-1"></i>Khôi phục
                </button>
              </div>
            </div>
          </form>
        </div>

        <div v-if="tab === 'password'" class="profile-card fade-in">
          <h5 class="section-title"><i class="bi bi-shield-lock me-2 text-primary"></i>Đổi mật khẩu</h5>
          <div class="alert-info-box mb-4">
            <i class="bi bi-info-circle me-2"></i>
            Mật khẩu mới phải có tối thiểu 6 ký tự.
          </div>
          <form @submit.prevent="doiMatKhau" style="max-width: 480px;">
            <div class="mb-3">
              <label class="form-label fw-semibold small text-secondary">Mật khẩu hiện tại</label>
              <div class="input-group rounded-3 bg-light" :class="{ 'border border-danger': pwErrors.old }">
                <span class="input-group-text border-0 bg-transparent text-primary"><i class="bi bi-lock"></i></span>
                <input v-model="pwForm.old" :type="showPw.old ? 'text' : 'password'" class="form-control border-0 bg-transparent" placeholder="******">
                <button type="button" class="input-group-text border-0 bg-transparent text-muted" @click="showPw.old = !showPw.old">
                  <i :class="showPw.old ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                </button>
              </div>
              <div v-if="pwErrors.old" class="text-danger small mt-1">{{ pwErrors.old }}</div>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold small text-secondary">Mật khẩu mới</label>
              <div class="input-group rounded-3 bg-light" :class="{ 'border border-danger': pwErrors.new }">
                <span class="input-group-text border-0 bg-transparent text-primary"><i class="bi bi-lock-fill"></i></span>
                <input v-model="pwForm.new" :type="showPw.new ? 'text' : 'password'" class="form-control border-0 bg-transparent" placeholder="******">
                <button type="button" class="input-group-text border-0 bg-transparent text-muted" @click="showPw.new = !showPw.new">
                  <i :class="showPw.new ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                </button>
              </div>
              <div v-if="pwErrors.new" class="text-danger small mt-1">{{ pwErrors.new }}</div>
            </div>

            <div class="mb-4">
              <label class="form-label fw-semibold small text-secondary">Xác nhận mật khẩu mới</label>
              <div class="input-group rounded-3 bg-light" :class="{ 'border border-danger': pwErrors.confirm }">
                <span class="input-group-text border-0 bg-transparent text-primary"><i class="bi bi-check-all"></i></span>
                <input v-model="pwForm.confirm" :type="showPw.confirm ? 'text' : 'password'" class="form-control border-0 bg-transparent" placeholder="******">
                <button type="button" class="input-group-text border-0 bg-transparent text-muted" @click="showPw.confirm = !showPw.confirm">
                  <i :class="showPw.confirm ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                </button>
              </div>
              <div v-if="pwErrors.confirm" class="text-danger small mt-1">{{ pwErrors.confirm }}</div>
            </div>

            <button type="submit" class="btn btn-save px-5" :disabled="savingPw">
              <span v-if="savingPw"><span class="spinner-border spinner-border-sm me-2"></span>Đang đổi...</span>
              <span v-else><i class="bi bi-shield-check me-1"></i>Đổi mật khẩu</span>
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

const API = 'http://127.0.0.1:8000/api/client'
const PROFILE_UPDATED_EVENT = 'client-profile-updated'

export default {
  name: 'ProfilePage',
  data() {
    return {
      loading: true,
      tab: 'info',
      user: {},
      stats: { lichTrinh: 0, nhom: 0 },
      editForm: {},
      editErrors: {},
      savingInfo: false,
      avatarFile: null,
      avatarPreview: '',
      avatarError: '',
      uploadingAvatar: false,
      pwForm: { old: '', new: '', confirm: '' },
      pwErrors: {},
      savingPw: false,
      showPw: { old: false, new: false, confirm: false },
    }
  },
  computed: {
    avatarLetter() {
      return this.user.ten ? this.user.ten.trim().charAt(0).toUpperCase() : '?'
    },
    hasAvatar() {
      return !!(this.avatarPreviewSrc || this.user.anh_dai_dien)
    },
    avatarSrc() {
      return this.resolveAvatarUrl(this.user.anh_dai_dien)
    },
    avatarPreviewSrc() {
      return this.avatarPreview || this.avatarSrc
    },
  },
  mounted() {
    const token = localStorage.getItem('client_token')
    if (!token) {
      this.$router.replace('/client/dang-nhap')
      return
    }

    const userId = localStorage.getItem('client_id')
    if (!userId) {
      this.$toast.error('Không tìm thấy người dùng, vui lòng đăng nhập lại!')
      this.$router.replace('/client/dang-nhap')
      return
    }

    this.fetchProfile()
  },
  beforeUnmount() {
    if (this.avatarPreview) {
      URL.revokeObjectURL(this.avatarPreview)
    }
  },
  methods: {
    authHeader() {
      return { headers: { Authorization: `Bearer ${localStorage.getItem('client_token')}` } }
    },
    emitProfileUpdated() {
      window.dispatchEvent(new CustomEvent(PROFILE_UPDATED_EVENT))
    },
    resolveAvatarUrl(path) {
      if (!path) return ''
      if (/^(https?:)?\/\//i.test(path) || path.startsWith('data:') || path.startsWith('blob:')) {
        return path
      }
      const cleanPath = path.startsWith('/') ? path : `/${path}`
      return `http://127.0.0.1:8000${cleanPath}`
    },
    async fetchProfile() {
      this.loading = true
      try {
        const userId = localStorage.getItem('client_id')
        const res = await axios.get(`${API}/profile/${userId}`, this.authHeader())
        if (res.data.status === 'success' || res.data.status === true) {
          this.user = res.data.data || {}
          if (res.data.stats) {
            this.stats = res.data.stats
          }
          localStorage.setItem('client_ten', this.user.ten || '')
          localStorage.setItem('client_avatar', this.user.anh_dai_dien || '')
          this.emitProfileUpdated()
          this.huyAnhDaiDienTam(false)
          this.resetEditForm()
        }
      } catch {
        this.$toast.error('Không thể tải thông tin người dùng!')
      } finally {
        this.loading = false
      }
    },
    resetEditForm() {
      this.editForm = {
        ten: this.user.ten || '',
        so_dien_thoai: this.user.so_dien_thoai || '',
      }
      this.editErrors = {}
      this.huyAnhDaiDienTam(false)
    },
    validateEdit() {
      this.editErrors = {}
      if (!this.editForm.ten?.trim()) {
        this.editErrors.ten = 'Vui lòng nhập họ tên.'
        return false
      }
      return true
    },
    async capNhatThongTin() {
      if (!this.validateEdit()) return
      this.savingInfo = true
      try {
        const res = await axios.post(`${API}/cap-nhat-thong-tin`, this.editForm, this.authHeader())
        if (res.data.status) {
          localStorage.setItem('client_ten', this.editForm.ten)
          this.user = { ...this.user, ...this.editForm }
          this.emitProfileUpdated()
          if (this.avatarFile) {
            const uploadOk = await this.taiAnhDaiDien({ silentSuccess: true })
            if (!uploadOk) return
          }
          this.$toast.success(res.data.message || 'Cập nhật thông tin thành công!')
        } else {
          this.$toast.error(res.data.message)
        }
      } catch (err) {
        if (err.response?.status === 422) {
          this.editErrors = err.response.data.errors || {}
        }
        this.$toast.error('Cập nhật thất bại!')
      } finally {
        this.savingInfo = false
      }
    },
    moChonAnh() {
      this.$refs.avatarInput?.click()
    },
    chonAnhDaiDien(event) {
      const file = event.target.files?.[0]
      this.avatarError = ''

      if (!file) return

      const validTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/webp']
      if (!validTypes.includes(file.type)) {
        this.avatarError = 'Vui lòng chọn ảnh PNG, JPG, JPEG hoặc WEBP.'
        this.huyAnhDaiDienTam()
        return
      }

      if (file.size > 2 * 1024 * 1024) {
        this.avatarError = 'Kích thước ảnh tối đa là 2MB.'
        this.huyAnhDaiDienTam()
        return
      }

      if (this.avatarPreview) {
        URL.revokeObjectURL(this.avatarPreview)
      }

      this.avatarFile = file
      this.avatarPreview = URL.createObjectURL(file)
    },
    huyAnhDaiDienTam(resetInput = true) {
      if (this.avatarPreview) {
        URL.revokeObjectURL(this.avatarPreview)
      }
      this.avatarPreview = ''
      this.avatarFile = null
      this.avatarError = ''

      if (resetInput && this.$refs.avatarInput) {
        this.$refs.avatarInput.value = ''
      }
    },
    async taiAnhDaiDien(options = {}) {
      const { silentSuccess = false } = options

      if (!this.avatarFile) {
        this.avatarError = 'Vui lòng chọn ảnh trước khi tải lên.'
        return false
      }

      this.uploadingAvatar = true
      this.avatarError = ''

      try {
        const formData = new FormData()
        formData.append('anh_dai_dien', this.avatarFile)

        const res = await axios.post(`${API}/cap-nhat-anh-dai-dien`, formData, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('client_token')}`,
            'Content-Type': 'multipart/form-data',
          },
        })

        if (res.data.status === true || res.data.status === 'success') {
          const updatedData = res.data.data || {}
          const avatarPath = updatedData.anh_dai_dien || res.data.anh_dai_dien || ''

          this.user = {
            ...this.user,
            ...updatedData,
            anh_dai_dien: avatarPath || updatedData.anh_dai_dien || this.user.anh_dai_dien,
          }

          localStorage.setItem('client_avatar', this.user.anh_dai_dien || '')
          this.emitProfileUpdated()
          this.huyAnhDaiDienTam()
          if (!silentSuccess) {
            this.$toast.success(res.data.message || 'Tải ảnh đại diện thành công!')
          }
          return true
        }

        this.avatarError = res.data.message || 'Tải ảnh thất bại.'
        this.$toast.error(this.avatarError)
        return false
      } catch (err) {
        if (err.response?.status === 422) {
          const apiErrors = err.response.data.errors || {}
          this.avatarError = apiErrors.anh_dai_dien?.[0] || err.response.data.message || 'Ảnh tải lên không hợp lệ.'
        } else {
          this.avatarError = err.response?.data?.message || 'Không thể tải ảnh lên máy chủ.'
        }

        this.$toast.error(this.avatarError)
        return false
      } finally {
        this.uploadingAvatar = false
      }
    },
    validatePw() {
      this.pwErrors = {}
      let ok = true
      if (!this.pwForm.old) {
        this.pwErrors.old = 'Vui lòng nhập mật khẩu hiện tại.'
        ok = false
      }
      if (!this.pwForm.new || this.pwForm.new.length < 6) {
        this.pwErrors.new = 'Mật khẩu mới phải có ít nhất 6 ký tự.'
        ok = false
      }
      if (this.pwForm.old && this.pwForm.new && this.pwForm.old === this.pwForm.new) {
        this.pwErrors.new = 'Mật khẩu mới phải khác mật khẩu hiện tại.'
        ok = false
      }
      if (!this.pwForm.confirm) {
        this.pwErrors.confirm = 'Vui lòng xác nhận mật khẩu mới.'
        ok = false
      }
      if (this.pwForm.new !== this.pwForm.confirm) {
        this.pwErrors.confirm = 'Mật khẩu xác nhận không khớp.'
        ok = false
      }
      return ok
    },
    async doiMatKhau() {
      if (!this.validatePw()) return
      this.savingPw = true
      this.pwErrors = {}
      try {
        const payload = {
          mat_khau_cu: this.pwForm.old,
          mat_khau_moi: this.pwForm.new,
        }

        const res = await axios.post(`${API}/doi-mat-khau`, payload, this.authHeader())

        if (res.data.status) {
          if (res.data.message) {
            this.$toast.success(res.data.message)
          }
          this.pwForm = { old: '', new: '', confirm: '' }
          this.pwErrors = {}
        } else {
          const message = res.data.message || ''
          if (message) {
            this.$toast.error(message)
          }
          if ((message || '').toLowerCase().includes('cũ')) {
            this.pwErrors.old = message
          }
        }
      } catch (err) {
        if (err.response?.status === 422) {
          const errors = err.response.data.errors || {}
          this.pwErrors = {
            old: errors.mat_khau_cu?.[0] || '',
            new: errors.mat_khau_moi?.[0] || '',
            confirm: errors.xac_nhan_mat_khau?.[0] || '',
          }
          if (err.response.data?.message) {
            this.$toast.error(err.response.data.message)
          }
        } else if (err.response?.status === 400) {
          const message = err.response.data?.message || ''
          this.pwErrors.old = message
          if (message) {
            this.$toast.error(message)
          }
        } else if (err.response?.status === 401) {
          if (err.response.data?.message) {
            this.$toast.error(err.response.data.message)
          }
          localStorage.removeItem('client_token')
          localStorage.removeItem('access_token')
          localStorage.removeItem('client_id')
          this.$router.replace('/client/dang-nhap')
        } else {
          if (err.response?.data?.message) {
            this.$toast.error(err.response.data.message)
          }
        }
      } finally {
        this.savingPw = false
      }
    },
    handleAvatarError() {
      this.user = { ...this.user, anh_dai_dien: '' }
      localStorage.removeItem('client_avatar')
      this.emitProfileUpdated()
    },
    formatDateTime(val) {
      if (!val) return '-'
      return new Date(val).toLocaleString('vi-VN')
    },
  },
}
</script>

<style scoped>
.profile-page {
  min-height: calc(100vh - 88px);
  background: linear-gradient(160deg, #f0f4ff 0%, #fff9f5 60%, #f7fbff 100%);
}

.profile-hero-card {
  border-radius: 20px;
  overflow: hidden;
  position: relative;
  padding: 0;
  box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
}

.hero-bg {
  height: 120px;
  background: linear-gradient(120deg, #1e3a8a 0%, #0ea5e9 60%, #10b981 100%);
}

.hero-content {
  background: #fff;
  padding: 0 2rem 1.5rem;
  margin-top: -40px;
}

.avatar-wrap {
  position: relative;
}

.profile-avatar {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: linear-gradient(135deg, #ff7b54, #ffb26b);
  color: #fff;
  font-size: 2rem;
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 4px solid #fff;
  box-shadow: 0 4px 16px rgba(255, 123, 84, 0.3);
}

.avatar-image-frame {
  padding: 0;
  overflow: hidden;
  background: #eef4ff;
}

.profile-avatar-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.avatar-badge {
  position: absolute;
  bottom: 2px;
  right: 2px;
  background: #fff;
  border-radius: 50%;
  font-size: 1rem;
  line-height: 1;
  padding: 1px;
}

.avatar-upload-panel {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  border: 1px solid #e5eaf3;
  border-radius: 16px;
  background: linear-gradient(135deg, #f8fbff, #fff);
}

.avatar-upload-preview {
  width: 96px;
  height: 96px;
  border-radius: 50%;
  overflow: hidden;
  border: 3px solid #fff;
  box-shadow: 0 4px 18px rgba(37, 99, 235, 0.12);
  background: #eef4ff;
  flex-shrink: 0;
}

.avatar-upload-image,
.avatar-upload-fallback {
  width: 100%;
  height: 100%;
}

.avatar-upload-image {
  object-fit: cover;
  display: block;
}

.avatar-upload-fallback {
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  font-weight: 800;
  color: #fff;
  background: linear-gradient(135deg, #ff7b54, #ffb26b);
}

.avatar-upload-actions {
  flex: 1;
}

.profile-name {
  font-size: 1.4rem;
  font-weight: 800;
  color: #1e2d44;
}

.profile-email {
  font-size: 0.9rem;
  color: #627289;
}

.hero-stat-value {
  font-size: 1.4rem;
  font-weight: 800;
  color: #1e2d44;
}

.hero-stat-label {
  font-size: 0.72rem;
  color: #9aa9be;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.profile-tabs {
  gap: 8px;
  border: none;
}

.profile-tab-btn {
  background: #fff;
  border: 1.5px solid #e5eaf3;
  border-radius: 30px;
  padding: 0.45rem 1.2rem;
  font-size: 0.88rem;
  font-weight: 600;
  color: #627289;
  cursor: pointer;
  transition: all 0.2s;
}

.profile-tab-btn:hover {
  border-color: #0ea5e9;
  color: #0ea5e9;
}

.profile-tab-btn.active {
  background: linear-gradient(135deg, #0ea5e9, #2563eb);
  color: #fff;
  border-color: transparent;
  box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);
}

.profile-card {
  background: #fff;
  border-radius: 16px;
  padding: 2rem;
  box-shadow: 0 2px 16px rgba(0, 0, 0, 0.06);
}

.section-title {
  font-size: 1.05rem;
  font-weight: 700;
  color: #1e2d44;
  margin-bottom: 0;
  padding-bottom: 1rem;
  border-bottom: 1px solid #f0f4ff;
}

.info-field {
  background: #f8faff;
  border-radius: 10px;
  padding: 0.85rem 1.1rem;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.info-field-inline {
  min-height: 48px;
  justify-content: center;
}

.info-label {
  font-size: 0.75rem;
  font-weight: 600;
  color: #9aa9be;
  text-transform: uppercase;
  letter-spacing: 0.4px;
}

.info-value {
  font-size: 0.95rem;
  font-weight: 600;
  color: #1e2d44;
}

.input-group.bg-light {
  border: 1px solid rgba(0, 0, 0, 0.08);
  transition: all 0.2s;
}

.input-group.bg-light:focus-within {
  background: #fff !important;
  border-color: #0ea5e9 !important;
  box-shadow: 0 0 0 0.2rem rgba(14, 165, 233, 0.12);
}

.input-group .form-control:focus {
  box-shadow: none;
}

.btn-save {
  background: linear-gradient(135deg, #0ea5e9, #2563eb);
  color: #fff;
  border: none;
  border-radius: 30px;
  font-weight: 600;
  padding: 0.55rem 1.5rem;
  transition: all 0.2s;
}

.btn-save:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(37, 99, 235, 0.3);
  color: #fff;
}

.btn-save:disabled {
  opacity: 0.7;
}

.alert-info-box {
  background: #eff6ff;
  border-left: 4px solid #3b82f6;
  border-radius: 8px;
  padding: 0.7rem 1rem;
  font-size: 0.88rem;
  color: #1d4ed8;
}

.fade-in {
  animation: fadeInUp 0.3s ease;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(10px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.fs-7 {
  font-size: 0.78rem;
}

@media (max-width: 576px) {
  .avatar-upload-panel {
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>
