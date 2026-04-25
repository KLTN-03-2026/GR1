<template>
  <div class="staff-page">
    <div class="page-header">
      <div>
        <h1 class="page-title">{{ pageTitle }}</h1>
      </div>
    </div>

    <div class="card card-custom table-card">
      <div class="card-header-custom">
        <div class="toolbar d-flex justify-content-between align-items-center w-100 gap-3 flex-wrap">
          <div class="d-flex align-items-center gap-3">

            <div class="search-box">
              <i class="bi bi-search"></i>
              <input v-model.trim="keyword" type="text" class="form-control search-input"
                placeholder="Tìm kiếm địa điểm nội bộ...">
            </div>
          </div>

          <div class="d-flex align-items-center gap-2 flex-wrap">
            <button class="btn btn-light refresh-btn" :disabled="loading" @click="fetchPlaces">
              <span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status"></span>
              <i v-else class="bi bi-arrow-clockwise me-2"></i>
              Làm mới
            </button>

            <button class="btn btn-primary action-btn" type="button" @click="openAddModal">
              <i class="bi bi-plus-circle-fill me-2"></i>Thêm địa điểm
            </button>
          </div>
        </div>
      </div>

      <div class="card-body p-0">
        <div v-if="loading" class="state-box">
          <div class="spinner-border text-primary mb-3" role="status"></div>
          <div class="state-title">Đang tải danh sách địa điểm...</div>
        </div>

        <div v-else-if="errorMessage" class="state-box">
          <div class="state-icon state-error">
            <i class="bi bi-exclamation-octagon"></i>
          </div>
          <div class="state-title">Không thể tải dữ liệu</div>
          <div class="state-text">{{ errorMessage }}</div>
        </div>

        <div v-else-if="!filteredPlaces.length" class="state-box">
          <div class="state-icon state-empty">
            <i class="bi bi-inbox"></i>
          </div>
          <div class="state-title">Không tìm thấy địa điểm nào</div>
        </div>

        <div v-else class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead>
              <tr>
                <th class="text-center" style="width: 60px;">#</th>
                <th style="width: 80px;">Ảnh</th>
                <th style="min-width: 250px;">Tên địa điểm & Địa chỉ</th>
                <th style="min-width: 140px;">Loại / Danh mục</th>
                <th class="text-center" style="min-width: 100px;">Đánh giá</th>
                <th class="text-center" style="width: 150px;">Hành động</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(place, index) in filteredPlaces" :key="place.id">
                <td class="text-center fw-semibold">{{ index + 1 }}</td>
                <td>
                  <img :src="place.image || 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=150'"
                    class="place-thumbnail" alt="Thumb">
                </td>
                <td>
                  <div class="staff-cell">
                    <div>
                      <div class="staff-name">{{ place.ten_dia_diem }}</div>
                      <small class="text-muted"><i class="bi bi-geo-alt"></i> {{ place.dia_chi || 'Chưa cập nhật'
                        }}</small>
                    </div>
                  </div>
                </td>
                <td>
                  <span class="role-badge">{{ place.loai_dia_diem || 'Khác' }}</span>
                </td>
                <td class="text-center">
                  <span class="status-badge status-active">
                    <i class="bi bi-star-fill text-warning me-1"></i> {{ place.danh_gia_trung_binh || '0.0' }}
                  </span>
                </td>
                <td class="text-center">

                  <button class="btn btn-sm btn-outline-info me-2" title="Quản lý ảnh" @click="openImageModal(place)">
                    <i class="bi bi-images"></i>
                  </button>
                  <button class="btn btn-sm btn-outline-primary me-2" type="button" title="Sửa"
                    @click="openEditModal(place)">
                    <i class="bi bi-pencil-square"></i>
                  </button>
                  <button class="btn btn-sm btn-outline-danger" type="button" title="Xóa"
                    @click="openDeleteModal(place)">
                    <i class="bi bi-trash3"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- MODAL FORM TO ADD / EDIT -->
    <div id="modalFormDiaDiem" class="modal fade" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content staff-modal">
          <div class="modal-header border-0 pb-0">
            <div>
              <h5 class="modal-title">{{ isEditing ? 'Cập nhật địa điểm' : 'Thêm địa điểm mới' }}</h5>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body pt-3">
            <div class="row g-3">
              <!-- Cột Trái -->
              <div class="col-md-6">
                <label class="form-label">Tên địa điểm <span class="text-danger">*</span></label>
                <input v-model="form.ten_dia_diem" type="text" class="form-control modal-input"
                  placeholder="Tên địa điểm">
              </div>
              <div class="col-md-6">
                <label class="form-label">Loại địa điểm <span class="text-danger">*</span></label>
                <select v-model="form.loai_dia_diem" class="form-select modal-input">
                  <option value="" disabled>-- Chọn loại địa điểm --</option>
                  <option v-for="cat in categories" :key="cat.id" :value="cat.ten_danh_muc">
                    {{ cat.ten_danh_muc }}
                  </option>
                  <option value="Khác">Khác</option>
                </select>
              </div>

              <div class="col-md-12">
                <label class="form-label">Địa chỉ</label>
                <input v-model="form.dia_chi" type="text" class="form-control modal-input" placeholder="Địa chỉ đầy đủ">
              </div>

              <div class="col-md-6">
                <label class="form-label">Tọa độ trên bản đồ</label>
                <div id="admin-map"
                  style="height: 200px; width: 100%; border-radius: 14px; border: 1px solid #dbe4f0; background: #f8fafc;">
                </div>
                <small class="text-muted mt-1 d-block"><i class="bi bi-info-circle me-1"></i>Thay đổi số bên dưới hoặc
                  click lên bản đồ để chọn vị trí.</small>
              </div>

              <div class="col-md-6">
                <div class="row g-3">
                  <div class="col-12">
                    <label class="form-label">Vĩ độ (Latitude) <span class="text-danger">*</span></label>
                    <input v-model="form.vi_do" type="number" step="0.000001" class="form-control modal-input"
                      placeholder="Ví dụ: 16.0613" @input="updateAdminMarker">
                  </div>
                  <div class="col-12">
                    <label class="form-label">Kinh độ (Longitude) <span class="text-danger">*</span></label>
                    <input v-model="form.kinh_do" type="number" step="0.000001" class="form-control modal-input"
                      placeholder="Ví dụ: 108.2271" @input="updateAdminMarker">
                  </div>
                </div>
              </div>

              <div class="col-md-4">
                <label class="form-label">Giá vé (VNĐ)</label>
                <input v-model="form.gia_ve" type="number" class="form-control modal-input"
                  placeholder="Bỏ trống nếu miễn phí">
              </div>
              <div class="col-md-4">
                <label class="form-label">Giờ mở cửa</label>
                <input v-model="form.gio_mo_cua" type="time" class="form-control modal-input">
              </div>
              <div class="col-md-4">
                <label class="form-label">Giờ đóng cửa</label>
                <input v-model="form.gio_dong_cua" type="time" class="form-control modal-input">
              </div>

              <div class="col-12">
                <label class="form-label">Hình ảnh (URL)</label>
                <input v-model="form.image" type="text" class="form-control modal-input" style="min-height: auto;"
                  placeholder="Dán link ảnh vào đây">
                <div v-if="form.image" class="mt-2">
                  <img :src="form.image" style="height: 120px; border-radius: 8px; object-fit: cover;" alt="Preview">
                </div>
              </div>

              <div class="col-12">
                <label class="form-label">Mô tả</label>
                <textarea v-model="form.mo_ta" class="form-control modal-input modal-textarea" rows="3"
                  placeholder="Nhập mô tả"></textarea>
              </div>
            </div>
          </div>

          <div class="modal-footer border-0 pt-0">
            <button type="button" class="btn btn-light modal-cancel-btn" data-bs-dismiss="modal">Hủy</button>
            <button type="button" class="btn btn-primary action-btn" @click="savePlace">
              <span v-if="saving" class="spinner-border spinner-border-sm me-2" role="status"></span>
              {{ isEditing ? 'Cập nhật' : 'Lưu địa điểm' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL XÓA -->
    <div id="modalXoaDiaDiem" class="modal fade" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content staff-modal">
          <div class="modal-body delete-modal-body">
            <div class="delete-icon">
              <i class="bi bi-trash3"></i>
            </div>
            <h5 class="modal-title text-center">Xóa địa điểm</h5>
            <p class="delete-text mb-0">
              Bạn có chắc chắn muốn xóa địa điểm <strong>{{ selectedPlace?.ten_dia_diem }}</strong> không? Dữ liệu không
              thể khôi phục.
            </p>
          </div>
          <div class="modal-footer border-0 justify-content-center pt-0">
            <button type="button" class="btn btn-light modal-cancel-btn" data-bs-dismiss="modal">Hủy</button>
            <button type="button" class="btn btn-danger modal-delete-btn" @click="confirmDelete">Xác nhận xóa</button>
          </div>
        </div>
      </div>
    </div>
    <!-- MODAL QUẢN LÝ ẢNH -->
    <div id="modalQuanLyAnh" class="modal fade" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content staff-modal">
          <div class="modal-header border-0 pb-0">
            <div>
              <h5 class="modal-title">Quản lý hình ảnh - <span class="text-primary">{{
                selectedPlaceForImages?.ten_dia_diem }}</span></h5>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body pt-3">
            <div class="mb-4 d-flex gap-2 p-3 bg-light rounded shadow-sm">
              <input v-model="newImageUrl" type="text" class="form-control"
                placeholder="Dán link URL ảnh mới vào đây...">
              <button class="btn btn-primary text-nowrap px-4" @click="addImage"
                :disabled="!newImageUrl || addingImage">
                <span v-if="addingImage" class="spinner-border spinner-border-sm me-1"></span>
                <i v-else class="bi bi-plus-lg me-1"></i> Thêm ảnh
              </button>
            </div>
            <div v-if="loadingImages" class="text-center py-5">
              <div class="spinner-border text-primary" role="status"></div>
            </div>
            <div v-else-if="placeImages.length === 0" class="text-center py-5 text-muted bg-light rounded">
              <i class="bi bi-image" style="font-size: 2rem; opacity: 0.5;"></i>
              <p class="mt-2">Chưa có hình ảnh nào cho địa điểm này.</p>
            </div>
            <div v-else class="row g-4">
              <div class="col-xl-3 col-lg-4 col-md-6" v-for="img in placeImages" :key="img.id">
                <div class="card h-100 position-relative border-0"
                  style="border-radius: 16px; overflow: hidden; box-shadow: 0 10px 20px -5px rgba(0,0,0,0.1); transition: transform 0.2s;">
                  <span v-if="img.is_main" class="badge bg-success position-absolute"
                    style="top: 12px; left: 12px; z-index: 2; font-size: 0.8rem; padding: 6px 10px;">
                    <i class="bi bi-star-fill text-warning me-1"></i> Ảnh chính
                  </span>
                  <img :src="img.duong_dan_anh" class="card-img-top" style="height: 200px; object-fit: cover;"
                    alt="Image">
                  <div class="card-body p-3 bg-white d-flex justify-content-between align-items-center">
                    <button class="btn btn-sm fw-semibold"
                      :class="img.is_main ? 'btn-light text-success cursor-default' : 'btn-outline-primary'"
                      title="Biến thành ảnh chính" @click="setMainImage(img)" :disabled="img.is_main"
                      style="border-radius: 8px;">
                      {{ img.is_main ? 'Đang là ảnh chính' : 'Đặt làm ảnh chính' }}
                    </button>
                    <button class="btn btn-sm btn-outline-danger" style="border-radius: 8px;" title="Xóa ảnh"
                      @click="deleteImage(img)">
                      <i class="bi bi-trash3"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer border-0 pt-0">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import * as bootstrap from 'bootstrap'

const API_URL = 'http://127.0.0.1:8000/api/dia-diems';

export default {
  name: 'LocationManager',
  props: {
    pageTitle: { type: String, default: 'Quản lý địa điểm' },
    fetchUrl: { type: String, required: true },
    categoryId: { type: [Number, String], required: true },
    categoryDefaultType: { type: String, default: 'Khác' },
  },
  data() {
    return {
      isEditing: false,
      places: [],
      categories: [],
      keyword: '',
      filterCategory: 'Tất cả',
      loading: false,
      errorMessage: '',
      selectedPlace: null,
      form: {
        id: null,
        ten_dia_diem: '',
        loai_dia_diem: '',
        dia_chi: '',
        vi_do: '',
        kinh_do: '',
        gia_ve: '',
        gio_mo_cua: '',
        gio_dong_cua: '',
        image: '',
        mo_ta: '',
      },
      adminMapInstance: null,
      adminMarker: null,
      modalInstance: null,
      deleteModalInstance: null,

      imageModalInstance: null,
      selectedPlaceForImages: null,
      placeImages: [],
      loadingImages: false,
      newImageUrl: '',
      addingImage: false,
    }
  },
  computed: {
    filteredPlaces() {
      let filtered = this.places;

      // Filter by keyword
      const keyword = this.keyword.toLowerCase()
      if (keyword) {
        filtered = filtered.filter((p) => {
          return (p.ten_dia_diem && p.ten_dia_diem.toLowerCase().includes(keyword)) ||
            (p.dia_chi && p.dia_chi.toLowerCase().includes(keyword)) ||
            (p.loai_dia_diem && p.loai_dia_diem.toLowerCase().includes(keyword));
        });
      }

      return filtered;
    },
  },
  mounted() {
    this.fetchPlaces();
    this.fetchCategories();
  },
  methods: {
    authHeader() {
      const token = localStorage.getItem('key_admin')
      return token ? { headers: { Authorization: `Bearer ${token}` } } : {}
    },
    async fetchCategories() {
      try {
        const res = await axios.get('http://127.0.0.1:8000/api/danh-mucs', this.authHeader());
        this.categories = res.data.data || [];
      } catch (error) {
        console.error("Lỗi khi tải danh mục:", error);
      }
    },
    async fetchPlaces() {
      this.loading = true
      this.errorMessage = ''

      try {
        const res = await axios.get(this.fetchUrl, this.authHeader())
        this.places = Array.isArray(res.data.data) ? res.data.data : []
      } catch (error) {
        this.places = []
        this.errorMessage = error.response?.data?.message || 'Không thể tải danh sách.'
      } finally {
        this.loading = false
      }
    },
    openAddModal() {
      this.isEditing = false;
      this.form = {
        id: null, ten_dia_diem: '', loai_dia_diem: '', dia_chi: '',
        vi_do: '', kinh_do: '', gia_ve: '', gio_mo_cua: '', gio_dong_cua: '', image: '', mo_ta: ''
      };
      if (!this.modalInstance) this.modalInstance = new bootstrap.Modal(document.getElementById('modalFormDiaDiem'));
      this.modalInstance.show();
      this.$nextTick(() => { this.initAdminMap(); });
    },
    openEditModal(place) {
      this.isEditing = true;
      this.form = { ...place };
      // Strip seconds if backend returns HH:MM:SS
      if (this.form.gio_mo_cua) {
        this.form.gio_mo_cua = this.form.gio_mo_cua.substring(0, 5);
      }
      if (this.form.gio_dong_cua) {
        this.form.gio_dong_cua = this.form.gio_dong_cua.substring(0, 5);
      }
      if (!this.modalInstance) this.modalInstance = new bootstrap.Modal(document.getElementById('modalFormDiaDiem'));
      this.modalInstance.show();
      this.$nextTick(() => { this.initAdminMap(); });
    },
    openDeleteModal(place) {
      this.selectedPlace = place;
      if (!this.deleteModalInstance) this.deleteModalInstance = new bootstrap.Modal(document.getElementById('modalXoaDiaDiem'));
      this.deleteModalInstance.show();
    },
    openImageModal(place) {
      this.selectedPlaceForImages = place;
      this.placeImages = [];
      this.newImageUrl = '';
      if (!this.imageModalInstance) this.imageModalInstance = new bootstrap.Modal(document.getElementById('modalQuanLyAnh'));
      this.imageModalInstance.show();
      this.fetchImages();
    },
    async fetchImages() {
      if (!this.selectedPlaceForImages) return;
      this.loadingImages = true;
      try {
        const res = await axios.get(`http://127.0.0.1:8000/api/hinh-anh-dia-diems/dia-diem/${this.selectedPlaceForImages.id}`, this.authHeader());
        this.placeImages = res.data.data;
      } catch (e) {
        this.$toast.error('Không thể tải hình ảnh.');
      } finally {
        this.loadingImages = false;
      }
    },
    async addImage() {
      if (!this.newImageUrl) return;
      this.addingImage = true;
      try {
        await axios.post('http://127.0.0.1:8000/api/hinh-anh-dia-diems', {
          id_dia_diem: this.selectedPlaceForImages.id,
          duong_dan_anh: this.newImageUrl,
          is_main: this.placeImages.length === 0, // Nếu chưa có ảnh nào thì tự động thành ảnh chính
          sort_order: 1
        }, this.authHeader());
        this.newImageUrl = '';
        this.$toast.success('Đã thêm ảnh thành công!');
        await this.fetchImages();
        this.fetchPlaces(); // Cập nhật lại thumbnail bên ngoài nếu đây là ảnh đầu tiên
      } catch (e) {
        this.$toast.error('Không thể thêm ảnh: ' + (e.response?.data?.message || e.message));
      } finally {
        this.addingImage = false;
      }
    },
    async setMainImage(img) {
      try {
        await axios.post(`http://127.0.0.1:8000/api/hinh-anh-dia-diems/${img.id}/set-main`, {}, this.authHeader());
        this.$toast.success('Đã thay đổi ảnh chính thành công!');
        await this.fetchImages();
        // Cập nhật thủ công ảnh thumbnail ngoài danh sách
        const place = this.places.find(p => p.id === this.selectedPlaceForImages.id);
        if (place) place.image = img.duong_dan_anh;
      } catch (e) {
        this.$toast.error('Lỗi khi đổi ảnh chính.');
      }
    },
    async deleteImage(img) {
      if (!confirm('Bạn có chắc chắn muốn xóa ảnh này không?')) return;
      try {
        await axios.delete(`http://127.0.0.1:8000/api/hinh-anh-dia-diems/${img.id}`, this.authHeader());
        this.$toast.success('Đã xóa ảnh.');
        await this.fetchImages();
        // Nếu vừa xóa ảnh chính và còn ảnh khác, thì load lại danh sách ngoài kia
        if (img.is_main) {
          this.fetchPlaces();
        }
      } catch (e) {
        this.$toast.error('Lỗi khi xóa ảnh.');
      }
    },
    async savePlace() {
      if (!this.form.ten_dia_diem || !this.form.loai_dia_diem) {
        this.$toast.warning('Vui lòng nhập tên và loại địa điểm!');
        return;
      }
      this.saving = true;
      const payload = { ...this.form };
      if (!payload.gio_mo_cua) payload.gio_mo_cua = null;
      else payload.gio_mo_cua = payload.gio_mo_cua.substring(0, 5);

      if (!payload.gio_dong_cua) payload.gio_dong_cua = null;
      else payload.gio_dong_cua = payload.gio_dong_cua.substring(0, 5);

      try {
        if (this.isEditing) {
          await axios.put(`${API_URL}/${this.form.id}`, payload, this.authHeader());
        } else {
          await axios.post(API_URL, payload, this.authHeader());
        }
        await this.fetchPlaces();
        this.modalInstance.hide();
        this.$toast.success(this.isEditing ? 'Đã cập nhật địa điểm thành công!' : 'Đã thêm địa điểm mới thành công!');
      } catch (e) {
        this.$toast.error('Có lỗi xảy ra: ' + (e.response?.data?.message || e.message));
      } finally {
        this.saving = false;
      }
    },

    initAdminMap() {
      const L = window.L;
      if (!L) return;

      if (this.adminMapInstance) {
        this.adminMapInstance.remove();
        this.adminMapInstance = null;
      }

      const defaultLat = parseFloat(this.form.vi_do) || 16.0544;
      const defaultLng = parseFloat(this.form.kinh_do) || 108.2022;

      this.adminMapInstance = L.map('admin-map').setView([defaultLat, defaultLng], 14);

      L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
        attribution: '&copy; Google Maps'
      }).addTo(this.adminMapInstance);

      this.adminMarker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(this.adminMapInstance);

      // Map click to pick location
      this.adminMapInstance.on('click', (e) => {
        const { lat, lng } = e.latlng;
        this.form.vi_do = lat.toFixed(7);
        this.form.kinh_do = lng.toFixed(7);
        this.updateAdminMarker();
      });

      // Marker drag to update form
      this.adminMarker.on('dragend', () => {
        const pos = this.adminMarker.getLatLng();
        this.form.vi_do = pos.lat.toFixed(7);
        this.form.kinh_do = pos.lng.toFixed(7);
      });

      setTimeout(() => {
        if (this.adminMapInstance) this.adminMapInstance.invalidateSize();
      }, 400);
    },

    updateAdminMarker() {
      if (!this.adminMapInstance || !this.adminMarker) return;
      const lat = parseFloat(this.form.vi_do);
      const lng = parseFloat(this.form.kinh_do);
      if (!isNaN(lat) && !isNaN(lng)) {
        this.adminMarker.setLatLng([lat, lng]);
        this.adminMapInstance.panTo([lat, lng]);
      }
    },

    async confirmDelete() {
      if (!this.selectedPlace) return;
      try {
        await axios.delete(`${API_URL}/${this.selectedPlace.id}`, this.authHeader());
        await this.fetchPlaces();
        this.deleteModalInstance.hide();
        this.$toast.success(`Đã xóa địa điểm "${this.selectedPlace.ten_dia_diem}" thành công!`);
      } catch (e) {
        this.$toast.error('Xoá thất bại: ' + (e.response?.data?.message || e.message));
      }
    }
  }
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

.place-thumbnail {
  width: 56px;
  height: 56px;
  border-radius: 12px;
  object-fit: cover;
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

.search-box-container {
  min-width: 450px;
}

.search-box {
  position: relative;
  flex-grow: 1;
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
  width: 100%;
}

.search-input:focus {
  border-color: #a5b4fc;
  box-shadow: 0 0 0 4px rgba(90, 103, 216, 0.12);
  background: #fff;
}

.filter-select {
  min-height: 44px;
  border-radius: 12px;
  border: 1px solid #dbe4f0;
  background: #fff;
  width: 180px;
}

.action-btn {
  white-space: nowrap;
  border: none;
  border-radius: 10px;
  padding: 10px 18px;
  font-weight: 600;
}

.bg-gradient-serp {
  background: linear-gradient(90deg, #10b981 0%, #059669 100%);
  color: white;
}

.refresh-btn {
  border-radius: 10px;
  padding: 10px 16px;
  font-weight: 600;
  border: 1px solid #dbe4f0;
  color: #475569;
  background: #fff;
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

.staff-cell {
  display: flex;
  align-items: center;
  gap: 14px;
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
  background: rgba(251, 191, 36, 0.15);
  color: #b45309;
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

.google-search-toggle {
  display: flex;
  align-items: center;
  white-space: nowrap;
}

.text-google {
  color: #4285F4;
  font-weight: 600;
  cursor: pointer;
}

.search-box {
  display: flex;
  align-items: center;
  position: relative;
  flex-grow: 1;
}

.search-btn-google {
  position: absolute;
  right: 6px;
  border-radius: 8px;
  z-index: 5;
}

.action-btn-import {
  background: #10b981;
  color: #fff;
  border: none;
  font-weight: 600;
  padding: 6px 12px;
  border-radius: 8px;
  transition: all 0.2s;
}

.action-btn-import:hover {
  transform: scale(1.05);
  background: #059669;
}
</style>