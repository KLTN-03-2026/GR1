<template>
  <div class="category-management-page p-4 fade-in-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h2 class="h3 mb-0 text-gray-800 fw-bold">
          <i class="bi bi-folder2-open text-primary me-2"></i>Quản lý danh mục
        </h2>
        <p class="text-muted mt-1 mb-0">Quản lý và thiết lập danh mục địa điểm hệ thống</p>
      </div>
      <div>
        <button class="btn btn-primary d-flex align-items-center gap-2 shadow-sm rounded-pill px-4 py-2 fw-medium" @click="openAddModal">
          <i class="bi bi-plus-circle"></i> Thêm danh mục mới
        </button>
      </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
      <div class="card-body p-4">
        <div class="row g-3">
          <div class="col-md-5">
            <label class="form-label text-muted small fw-bold">Tìm kiếm danh mục</label>
            <div class="input-group">
              <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
              <input type="text" class="form-control bg-light border-start-0 ps-0" placeholder="Nhập tên danh mục..." v-model="searchQuery">
            </div>
          </div>
          <div class="col-md-4">
            <label class="form-label text-muted small fw-bold">Trạng thái</label>
            <select class="form-select bg-light" v-model="filterStatus">
              <option value="all">Tất cả trạng thái</option>
              <option value="active">Đang hoạt động</option>
              <option value="inactive">Tạm ẩn</option>
            </select>
          </div>
          <div class="col-md-3 d-flex align-items-end">
            <button class="btn btn-primary w-100 fw-medium rounded-3" @click="search">
              Lọc kết quả
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Data Table Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 custom-table">
          <thead class="bg-light text-muted">
            <tr>
              <th class="ps-4 fw-semibold py-3" style="width: 60px;">ID</th>
              <th class="fw-semibold py-3">Tên danh mục</th>
              <th class="fw-semibold py-3">Mô tả</th>
              <th class="text-center fw-semibold py-3">Số lượng địa điểm</th>
              <th class="text-center fw-semibold py-3">Trạng thái</th>
              <th class="text-end pe-4 fw-semibold py-3">Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="category in paginatedCategories" :key="category.id">
              <td class="ps-4"><span class="text-muted fw-medium">#{{ category.id }}</span></td>
              <td>
                <div class="d-flex align-items-center">
                  <div class="category-icon me-3 text-primary bg-primary bg-opacity-10 d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px;">
                    <i :class="category.icon || 'bi bi-folder'"></i>
                  </div>
                  <div>
                    <h6 class="mb-0 fw-bold">{{ category.name }}</h6>
                    <small class="text-muted">{{ category.slug }}</small>
                  </div>
                </div>
              </td>
              <td>
                <p class="mb-0 text-muted small text-truncate" style="max-width: 250px;">{{ category.description }}</p>
              </td>
              <td class="text-center">
                <span class="badge bg-info bg-opacity-10 text-info fw-bold fs-6 rounded-pill px-3">{{ category.placesCount }}</span>
              </td>
              <td class="text-center">
                <span :class="['badge rounded-pill px-3 py-2 fw-medium', category.status === 'active' ? 'bg-success bg-opacity-10 text-success' : 'bg-secondary bg-opacity-10 text-secondary']">
                  {{ category.status === 'active' ? 'Hoạt động' : 'Tạm ẩn' }}
                </span>
              </td>
              <td class="text-end pe-4">
                <button class="btn btn-sm btn-action me-2" title="Chỉnh sửa" @click="openEditModal(category)">
                  <i class="bi bi-pencil-square text-primary"></i>
                </button>
                <button class="btn btn-sm btn-action" title="Xóa" @click="confirmDelete(category)">
                  <i class="bi bi-trash text-danger"></i>
                </button>
              </td>
            </tr>
            <tr v-if="paginatedCategories.length === 0">
              <td colspan="6" class="text-center py-5">
                <div class="empty-state text-muted">
                  <i class="bi bi-inbox fs-1 d-block mb-3 opacity-50"></i>
                  <p class="mb-0">Không tìm thấy danh mục nào.</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Pagination -->
      <div class="card-footer bg-white border-top-0 p-4 d-flex justify-content-between align-items-center" v-if="categories.length > 0">
        <span class="text-muted small">
          Hiển thị <b>{{ (currentPage - 1) * itemsPerPage + 1 }}</b> đến <b>{{ Math.min(currentPage * itemsPerPage, filteredCategories.length) }}</b> trong tổng số <b>{{ filteredCategories.length }}</b> danh mục
        </span>
        <ul class="pagination pagination-sm mb-0">
          <li class="page-item" :class="{ disabled: currentPage === 1 }">
            <a class="page-link" href="#" @click.prevent="currentPage--"><i class="bi bi-chevron-left"></i></a>
          </li>
          <li class="page-item" v-for="page in totalPages" :key="page" :class="{ active: currentPage === page }">
            <a class="page-link" href="#" @click.prevent="currentPage = page">{{ page }}</a>
          </li>
          <li class="page-item" :class="{ disabled: currentPage === totalPages }">
            <a class="page-link" href="#" @click.prevent="currentPage++"><i class="bi bi-chevron-right"></i></a>
          </li>
        </ul>
      </div>
    </div>

    <!-- Modal Form (Mockup) -->
    <div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
          <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
            <h5 class="modal-title fw-bold">{{ isEditing ? 'Chỉnh sửa danh mục' : 'Thêm mới danh mục' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4">
            <form @submit.prevent="saveCategory">
              <div class="mb-3">
                <label class="form-label fw-medium text-muted small">Tên danh mục <span class="text-danger">*</span></label>
                <input type="text" class="form-control bg-light" v-model="formData.name" placeholder="VD: Ẩm thực" required>
              </div>
              <div class="mb-3">
                <label class="form-label fw-medium text-muted small">Bí danh (Slug)</label>
                <input type="text" class="form-control bg-light" v-model="formData.slug" placeholder="am-thuc" disabled title="Tự động tạo từ tên danh mục">
              </div>
              <div class="mb-3">
                <label class="form-label fw-medium text-muted small">Mô tả</label>
                <textarea class="form-control bg-light" rows="3" v-model="formData.description" placeholder="Mô tả ngắn gọn về danh mục này..."></textarea>
              </div>
              <div class="mb-4">
                <label class="form-label fw-medium text-muted small">Trạng thái</label>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" role="switch" id="statusSwitch" v-model="formData.isActive">
                  <label class="form-check-label" for="statusSwitch">{{ formData.isActive ? 'Đang hoạt động' : 'Tạm ẩn' }}</label>
                </div>
              </div>
              <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-medium" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-medium shadow-sm">
                  <i class="bi bi-check2-circle me-1"></i> Lưu thông tin
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'DanhMucManagement',
  data() {
    return {
      searchQuery: '',
      filterStatus: 'all',
      currentPage: 1,
      itemsPerPage: 5,
      isEditing: false,
      formData: {
        id: null,
        name: '',
        slug: '',
        description: '',
        isActive: true
      },
      // Mock data cho giao diện
      categories: [
        { id: 1, name: 'Ẩm thực', slug: 'am-thuc', description: 'Các nhà hàng, quán ăn, đặc sản địa phương', placesCount: 145, status: 'active', icon: 'bi-cup-hot' },
        { id: 2, name: 'Check-in', slug: 'check-in', description: 'Địa điểm sống ảo, cảnh đẹp, biểu tượng thành phố', placesCount: 89, status: 'active', icon: 'bi-camera' },
        { id: 3, name: 'Giải trí', slug: 'giai-tri', description: 'Khu vui chơi, chợ đêm, các hoạt động ngoài trời', placesCount: 56, status: 'active', icon: 'bi-controller' },
        { id: 4, name: 'Tâm linh', slug: 'tam-linh', description: 'Chùa chiền, đền thờ, các di tích tôn giáo', placesCount: 32, status: 'active', icon: 'bi-bank' },
        { id: 5, name: 'Nghỉ dưỡng', slug: 'nghi-duong', description: 'Khách sạn, resort, homestay', placesCount: 204, status: 'inactive', icon: 'bi-house-heart' }
      ]
    }
  },
  computed: {
    filteredCategories() {
      return this.categories.filter(cat => {
        const matchQuery = cat.name.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                           cat.description.toLowerCase().includes(this.searchQuery.toLowerCase());
        const matchStatus = this.filterStatus === 'all' || cat.status === this.filterStatus;
        return matchQuery && matchStatus;
      });
    },
    totalPages() {
      return Math.ceil(this.filteredCategories.length / this.itemsPerPage) || 1;
    },
    paginatedCategories() {
      const start = (this.currentPage - 1) * this.itemsPerPage;
      const end = start + this.itemsPerPage;
      return this.filteredCategories.slice(start, end);
    }
  },
  methods: {
    search() {
      this.currentPage = 1;
    },
    openAddModal() {
      this.isEditing = false;
      this.formData = { id: null, name: '', slug: '', description: '', isActive: true };
      this.showModal();
    },
    openEditModal(category) {
      this.isEditing = true;
      this.formData = {
        id: category.id,
        name: category.name,
        slug: category.slug,
        description: category.description,
        isActive: category.status === 'active'
      };
      this.showModal();
    },
    showModal() {
      // Dùng import bootstrap linh động để tránh lỗi nếu không cài đặt toàn cầu
      const myModal = new window.bootstrap.Modal(document.getElementById('categoryModal'));
      myModal.show();
    },
    saveCategory() {
      alert("Tính năng đang phát triển. (Lưu danh mục: " + this.formData.name + ")");
      const modalEl = document.getElementById('categoryModal');
      const modal = window.bootstrap.Modal.getInstance(modalEl);
      if(modal) modal.hide();
    },
    confirmDelete(category) {
      if(confirm('Bạn có chắc chắn muốn xóa danh mục: ' + category.name + '?')) {
        alert("Chức năng xóa đang được phát triển.");
      }
    }
  },
  watch: {
    'formData.name'(newVal) {
      if(!this.isEditing) {
        // Tự tạo slug đơn giản cho giao diện mock
        this.formData.slug = newVal.toLowerCase().replace(/ /g, '-').normalize('NFD').replace(/[\u0300-\u036f]/g, '');
      }
    }
  }
}
</script>

<style scoped>
.category-management-page {
  background-color: #f8fafc;
  min-height: calc(100vh - 70px);
}

.fade-in-content {
  animation: fadeIn 0.4s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.custom-table th {
  font-size: 0.85rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  border-bottom: 2px solid #e2e8f0;
}

.custom-table td {
  padding: 1rem 0.5rem;
  vertical-align: middle;
}

.btn-action {
  background: transparent;
  border: none;
  border-radius: 8px;
  width: 36px;
  height: 36px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}

.btn-action:hover {
  background: #f1f5f9;
  transform: translateY(-2px);
}

.pagination .page-link {
  border: none;
  color: #64748b;
  border-radius: 8px;
  margin: 0 4px;
  font-weight: 500;
  transition: all 0.2s;
}

.pagination .page-item.active .page-link {
  background-color: #3b82f6;
  color: white;
  box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);
}

.pagination .page-link:hover:not(.active) {
  background-color: #f1f5f9;
  color: #0f172a;
}

/* Form Styles */
.form-control, .form-select {
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 0.6rem 1rem;
}

.form-control:focus, .form-select:focus {
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.input-group-text {
  border-top-left-radius: 8px;
  border-bottom-left-radius: 8px;
}
</style>
