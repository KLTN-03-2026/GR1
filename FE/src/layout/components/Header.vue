<template>
  <header class="navbar-custom sticky-top">
    <div class="container-fluid h-100">
      <div class="d-flex justify-content-between align-items-center h-100">
        <!-- Left: Logo & Brand -->
        <div class="d-flex align-items-center gap-3">
          <div class="logo-brand">
            <i class="bi bi-speedometer2 me-2"></i>
            <span class="fw-bold">MatDash</span>
          </div>
        </div>

        <!-- Right: Actions -->
        <div class="d-flex align-items-center gap-3">
          <!-- Search Bar -->
          <div class="search-box d-none d-md-block">
            <i class="bi bi-search"></i>
            <input 
              type="text" 
              placeholder="Search..." 
              class="form-control-custom"
            >
          </div>

          <!-- Icons & Dropdowns -->
          <div class="navbar-actions d-flex align-items-center gap-2">
            <!-- Notifications -->
            <div class="dropdown">
              <button class="btn-icon" data-bs-toggle="dropdown">
                <i class="bi bi-bell"></i>
                <span class="badge-dot">3</span>
              </button>
              <ul class="dropdown-menu dropdown-menu-end dropdown-menu-custom">
                <li class="dropdown-header">Notifications</li>
                <li><a class="dropdown-item" href="#">New order received</a></li>
                <li><a class="dropdown-item" href="#">User registration</a></li>
                <li><a class="dropdown-item" href="#">System update</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-center small" href="#">View all</a></li>
              </ul>
            </div>

            <!-- Messages -->
            <div class="dropdown">
              <button class="btn-icon" data-bs-toggle="dropdown">
                <i class="bi bi-chat-left-text"></i>
                <span class="badge-dot bg-success">2</span>
              </button>
              <ul class="dropdown-menu dropdown-menu-end dropdown-menu-custom">
                <li class="dropdown-header">Messages</li>
                <li><a class="dropdown-item" href="#">John: Hello there!</a></li>
                <li><a class="dropdown-item" href="#">Sarah: Check this out</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-center small" href="#">View all</a></li>
              </ul>
            </div>

            <!-- User Profile -->
            <div class="dropdown ms-3 ps-3 border-start">
              <button class="btn-profile d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                <img 
                  :src="avatarUrl" 
                  alt="User" 
                  class="rounded-circle"
                  width="36"
                  height="36"
                >
                <span class="d-none d-md-inline small fw-500">{{ adminDisplayName }}</span>
                <i class="bi bi-chevron-down"></i>
              </button>
              <ul class="dropdown-menu dropdown-menu-end dropdown-menu-custom">
                <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>My Profile</a></li>
                <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Settings</a></li>
                <li><a class="dropdown-item" href="#"><i class="bi bi-question-circle me-2"></i>Help</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><button class="dropdown-item text-danger" @click="dangXuat"><i class="bi bi-box-arrow-right me-2"></i>Logout</button></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script>
export default {
  name: 'Header',
  data() {
    return {
      admin: {},
    };
  },
  computed: {
    adminDisplayName() {
      return this.admin.ho_ten || this.admin.ten || this.admin.email || 'Admin';
    },
    avatarUrl() {
      const displayName = encodeURIComponent(this.adminDisplayName);
      return `https://ui-avatars.com/api/?name=${displayName}&background=5a67d8&color=fff`;
    },
  },
  mounted() {
    this.loadAdminData();
  },
  methods: {
    loadAdminData() {
      const adminData = localStorage.getItem('admin_data');
      if (!adminData) return;

      try {
        this.admin = JSON.parse(adminData) || {};
      } catch (error) {
        this.admin = {};
      }
    },
    dangXuat() {
      localStorage.removeItem('key_admin');
      localStorage.removeItem('admin_data');
      localStorage.removeItem('admin_id');
      localStorage.removeItem('admin_ten');
      localStorage.removeItem('admin_email');
      this.$router.push('/admin/dang-nhap');
    },
  },
};
</script>

<style scoped>
.navbar-custom {
  background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
  border-bottom: 1px solid #e9ecef;
  height: 70px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
  z-index: 1000;
}

.logo-brand {
  display: flex;
  align-items: center;
  font-size: 1.25rem;
  color: #5a67d8;
  font-weight: 700;
  cursor: pointer;
  letter-spacing: -0.3px;
}

.logo-brand i {
  font-size: 1.75rem;
}

.search-box {
  position: relative;
  width: 280px;
}

.search-box i {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #9ca3af;
  font-size: 0.875rem;
  font-weight: 500;
}

.form-control-custom {
  background: #f0f2f5;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 8px 12px 8px 36px !important;
  font-size: 0.875rem;
  color: #495057;
  transition: all 0.3s ease;
}

.form-control-custom:focus {
  background: #fff;
  border-color: #5a67d8;
  box-shadow: 0 0 0 3px rgba(90, 103, 216, 0.1);
}

.navbar-actions {
  gap: 1rem;
}

.btn-icon {
  background: none;
  border: none;
  color: #6c757d;
  font-size: 1.25rem;
  cursor: pointer;
  position: relative;
  transition: color 0.2s ease;
  padding: 8px;
  border-radius: 8px;
}

.btn-icon:hover {
  color: #5a67d8;
  background: #f0f2f5;
}

.badge-dot {
  position: absolute;
  top: -2px;
  right: -2px;
  width: 20px;
  height: 20px;
  background: #ef4444;
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.65rem;
  font-weight: 700;
  border: 2px solid white;
}

.badge-dot.bg-success {
  background: #48bb78;
}

.btn-profile {
  background: none;
  border: none;
  color: #6c757d;
  cursor: pointer;
  border-radius: 8px;
  padding: 4px 8px;
  transition: all 0.2s ease;
}

.btn-profile:hover {
  background: #f0f2f5;
}

.border-start {
  border-left: 1px solid #e9ecef;
  padding-left: 1rem;
}

.dropdown-menu-custom {
  border: 1px solid #e9ecef;
  border-radius: 12px;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
  padding: 8px 0;
  margin-top: 8px;
}

.dropdown-menu-custom .dropdown-item {
  padding: 10px 16px;
  font-size: 0.875rem;
  font-weight: 400;
  transition: all 0.2s ease;
  border-radius: 0;
  color: #495057;
}

.dropdown-menu-custom .dropdown-item:hover {
  background: #f8f9fa;
  color: #5a67d8;
}

.dropdown-header {
  padding: 10px 16px;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  color: #6c757d;
  letter-spacing: 0.5px;
}

@media (max-width: 768px) {
  .navbar-custom {
    height: 60px;
  }

  .search-box {
    display: none;
  }

  .btn-profile span {
    display: none;
  }
}
</style>
