<template>
  <aside class="sidebar" :class="{ 'sidebar-collapsed': isCollapsed }">
    <!-- Sidebar Header -->
    <div class="sidebar-header" :class="{ collapsed: isCollapsed }">
      <span class="sidebar-title">Menu</span>
      <button class="btn-collapse" @click="toggleSidebar" :title="isCollapsed ? 'Expand' : 'Collapse'">
        <i class="bi" :class="isCollapsed ? 'bi-chevron-right' : 'bi-chevron-left'"></i>
      </button>
    </div>

    <!-- Navigation Menu -->
    <nav class="sidebar-nav">
      <ul class="nav-menu">
        <!-- Dashboard -->
        <li class="nav-item">
          <router-link to="/admin/dashboard" class="nav-link" :class="{ active: isActive('/dashboard') }">
            <i class="bi bi-house-door"></i>
            <span v-if="!isCollapsed" class="nav-label">Dashboard</span>
          </router-link>
        </li>

        <!-- Users -->
        <li class="nav-item">
          <router-link to="/admin/users" class="nav-link" :class="{ active: isActive('/users') }">
            <i class="bi bi-people"></i>
            <span v-if="!isCollapsed" class="nav-label">Quản lý người dùng</span>
          </router-link>
        </li>

        <!-- Orders with submenu -->
        <li class="nav-item">
          <a class="nav-link" @click="toggleSubmenu" :class="{ active: showSubmenu }">
            <i class="bi bi-person-badge"></i>
            <span v-if="!isCollapsed" class="nav-label">Quản lý nhân viên</span>
            <i v-if="!isCollapsed" class="bi bi-chevron-down ms-auto chevron"></i>
          </a>
          <ul v-if="showSubmenu && !isCollapsed" class="submenu">
            <li>
              <router-link to="/admin/phan-quyen" class="submenu-link">
                <i class="bi bi-shield-lock"></i>
                <span>Phân quyền</span>
              </router-link>
            </li>
            <li>
              <router-link to="/admin/danh-sach-nhan-vien" class="submenu-link">
                <i class="bi bi-people"></i>
                <span>Danh sách nhân viên</span>
              </router-link>
            </li>
          </ul>
        </li>
        <!-- Categories with submenu -->
        <li class="nav-item">
          <a
            class="nav-link"
            @click="toggleCategorySubmenu"
            :class="{ active: showCategorySubmenu || isCategoryMenuActive() }"
          >
            <i class="bi bi-tags"></i>
            <span v-if="!isCollapsed" class="nav-label">Quản lý danh mục</span>
            <i v-if="!isCollapsed" class="bi bi-chevron-down ms-auto chevron"></i>
          </a>
          <ul v-if="showCategorySubmenu && !isCollapsed" class="submenu">
            <li>
              <router-link
                :to="{ path: '/admin/am-thuc', query: { category: 'am-thuc' } }"
                class="submenu-link"
                :class="{ 'router-link-active': isCategoryActive('am-thuc') }"
              >
                <i class="bi bi-cup-hot"></i>
                <span>Ẩm thực</span>
              </router-link>
            </li>
            <li>
              <router-link
                :to="{ path: '/admin/tam-linh', query: { category: 'tam-linh' } }"
                class="submenu-link"
                :class="{ 'router-link-active': isCategoryActive('tam-linh') }"
              >
                <i class="bi bi-bank"></i>
                <span>Tâm linh</span>
              </router-link>
            </li>
            <li>
              <router-link
                :to="{ path: '/admin/giai-tri', query: { category: 'giai-tri' } }"
                class="submenu-link"
                :class="{ 'router-link-active': isCategoryActive('giai-tri') }"
              >
                <i class="bi bi-controller"></i>
                <span>Giải trí</span>
              </router-link>
            </li>
            <li>
              <router-link
                :to="{ path: '/admin/check-in', query: { category: 'check-in' } }"
                class="submenu-link"
                :class="{ 'router-link-active': isCategoryActive('check-in') }"
              >
                <i class="bi bi-camera"></i>
                <span>Check-in</span>
              </router-link>
            </li>
          </ul>
        </li>



        <!-- Reports -->
        <li class="nav-item">
          <router-link to="/admin/quan-ly-danh-gia-phan-hoi" class="nav-link" :class="{ active: isActive('/reports') }">
            <i class="bi bi-chat-square-text"></i>
            <span v-if="!isCollapsed" class="nav-label">Quản lý đánh giá & phản hổi </span>
          </router-link>
        </li>
        <!-- Settings -->
        <li class="nav-item">
          <router-link to="/admin/reports" class="nav-link" :class="{ active: isActive('/settings') }">
            <i class="bi bi-bar-chart-line"></i>
            <span v-if="!isCollapsed" class="nav-label">Báo cáo thống kê</span>
          </router-link>
        </li>
      </ul>
    </nav>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
      <small v-if="!isCollapsed" class="text-secondary">v1.0.0</small>
    </div>
  </aside>
</template>

<script>
export default {
  name: 'Sidebar',
  props: {
    isCollapsed: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      showSubmenu: false,
      showCategorySubmenu: false,
    };
  },
  methods: {
    toggleSidebar() {
      this.$emit('toggle-collapse');
    },
    toggleSubmenu() {
      this.showSubmenu = !this.showSubmenu;
    },
    toggleCategorySubmenu() {
      this.showCategorySubmenu = !this.showCategorySubmenu;
    },
    isActive(path) {
      return this.$route.path === path;
    },
    isCategoryActive(category) {
      return this.$route.path === '/products' && this.$route.query.category === category;
    },
    isCategoryMenuActive() {
      return this.$route.path === '/products' && ['am-thuc', 'tam-linh', 'giai-tri', 'check-in'].includes(this.$route.query.category);
    },
  },
};
</script>

<style scoped>
.sidebar {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  width: 260px;
  position: fixed;
  top: 70px;
  left: 0;
  background: linear-gradient(180deg, #ffffff 0%, #f8f9fa 100%);
  transition: all 0.3s ease;
  z-index: 100;
  border-right: 1px solid #e9ecef;
}

.sidebar.sidebar-collapsed {
  width: 80px;
}

.sidebar-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 12px;
  border-bottom: 1px solid #e2e8f0;
  height: 60px;
  color: #495057;
  font-weight: 600;
  font-size: 0.9375rem;
}

.sidebar-header.collapsed {
  justify-content: center;
  padding: 16px 8px;
}

.sidebar-title {
  color: #495057;
  font-weight: 700;
  letter-spacing: 0.2px;
  white-space: nowrap;
  opacity: 1;
  max-width: 120px;
  overflow: hidden;
  transition: opacity 0.2s ease, max-width 0.2s ease, margin 0.2s ease;
}

.sidebar.sidebar-collapsed .sidebar-title {
  opacity: 0;
  max-width: 0;
  margin: 0;
}

.btn-collapse {
  background: #eef2ff;
  border: 1px solid #dbe4ff;
  color: #6c757d;
  font-size: 1rem;
  cursor: pointer;
  border-radius: 10px;
  width: 32px;
  height: 32px;
  padding: 0;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.btn-collapse:hover {
  background: #e0e7ff;
  color: #5a67d8;
  border-color: #c7d2fe;
}

.sidebar.sidebar-collapsed .btn-collapse {
  width: 36px;
  height: 36px;
}

.sidebar-nav {
  flex-grow: 1;
  overflow-y: auto;
  padding: 12px 8px;
}

.nav-menu {
  list-style: none;
  padding: 0;
  margin: 0;
}

.nav-item {
  margin-bottom: 8px;
}

.nav-link {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 12px;
  color: #6c757d;
  text-decoration: none;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
  border-left: 3px solid transparent;
  font-size: 0.9375rem;
  font-weight: 500;
}

.nav-link:hover {
  background: #e7f5ff;
  color: #5a67d8;
  border-left-color: #5a67d8;
}

.nav-link.active {
  background: linear-gradient(90deg, rgba(90, 103, 216, 0.1) 0%, rgba(90, 103, 216, 0.05) 100%);
  color: #5a67d8;
  border-left-color: #5a67d8;
  font-weight: 600;
}

.nav-link i {
  font-size: 1.125rem;
  min-width: 20px;
}

.nav-label {
  flex-grow: 1;
}

.chevron {
  font-size: 0.75rem;
  transition: transform 0.2s ease;
}

.nav-link.active .chevron {
  transform: rotate(180deg);
}

.submenu {
  list-style: none;
  padding: 8px 0 8px 0;
  margin: 0;
  background: #f8f9fa;
  border-radius: 6px;
  margin-top: 4px;
}

.submenu-link {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 8px 16px;
  color: #6c757d;
  text-decoration: none;
  transition: all 0.2s ease;
  font-size: 0.875rem;
  font-weight: 500;
  border-radius: 6px;
  margin: 0 8px;
}

.submenu-link:hover {
  background: #e7f5ff;
  color: #5a67d8;
}

.submenu-link.router-link-active {
  color: #5a67d8;
  background: #e7f5ff;
  font-weight: 600;
}

.submenu-link i {
  font-size: 1rem;
}

.sidebar-footer {
  padding: 12px;
  border-top: 1px solid #e9ecef;
  text-align: center;
  color: #adb5bd;
  font-size: 0.75rem;
}

/* Scrollbar styling */
.sidebar-nav::-webkit-scrollbar {
  width: 6px;
}

.sidebar-nav::-webkit-scrollbar-track {
  background: transparent;
}

.sidebar-nav::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

.sidebar-nav::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

@media (max-width: 768px) {
  .sidebar {
    width: 80px;
  }

  .sidebar.sidebar-collapsed {
    width: 260px;
  }

  .sidebar-header span,
  .nav-label {
    display: none;
  }

  .sidebar.sidebar-collapsed .sidebar-header span,
  .sidebar.sidebar-collapsed .nav-label {
    display: inline;
  }
}
</style>
