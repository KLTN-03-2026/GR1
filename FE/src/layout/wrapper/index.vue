<template>
  <div
    class="layout-wrapper"
    :class="{ 'layout-collapsed': isSidebarCollapsed }"
    :style="{ '--sidebar-width': sidebarWidth }"
  >
    <!-- Header -->
    <Header />

    <!-- Main Container -->
    <div class="layout-container">
      <!-- Sidebar -->
      <Sidebar
        :is-collapsed="isSidebarCollapsed"
        @toggle-collapse="toggleSidebar"
      />

      <!-- Main Content -->
      <main class="main-content">
        <div class="page-wrapper">
          <slot />
        </div>

        <!-- Footer -->
        <Footer />
      </main>
    </div>
  </div>
</template>

<script>
import Header from '../components/Header.vue';
import Sidebar from '../components/Sidebar.vue';
import Footer from '../components/Footer.vue';

export default {
  name: 'DefaultLayout',
  components: {
    Header,
    Sidebar,
    Footer,
  },
  data() {
    return {
      isSidebarCollapsed: false,
    };
  },
  computed: {
    sidebarWidth() {
      return this.isSidebarCollapsed ? '80px' : '260px';
    },
  },
  methods: {
    toggleSidebar() {
      this.isSidebarCollapsed = !this.isSidebarCollapsed;
    },
  },
};
</script>

<style scoped>
.layout-wrapper {
  --sidebar-width: 260px;
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  background: #f0f4f8;
}

.layout-container {
  display: flex;
  flex-grow: 1;
}

.main-content {
  flex-grow: 1;
  margin-left: var(--sidebar-width);
  display: flex;
  flex-direction: column;
  min-height: calc(100vh - 70px);
  transition: margin-left 0.3s ease;
}

.page-wrapper {
  flex-grow: 1;
  padding: 18px 28px 28px;
  overflow-y: auto;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .main-content {
    margin-left: 80px;
  }

  .page-wrapper {
    padding: 12px 16px 16px;
  }
}

/* Scrollbar styling for main content */
.page-wrapper::-webkit-scrollbar {
  width: 6px;
}

.page-wrapper::-webkit-scrollbar-track {
  background: transparent;
}

.page-wrapper::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

.page-wrapper::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style>
