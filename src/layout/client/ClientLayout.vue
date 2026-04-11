<template>
  <div class="client-layout d-flex flex-column min-vh-100">
    <ClientMenu />

    <main class="client-main flex-grow-1">
      <slot />
    </main>

    <ClientFooter />
  </div>
</template>

<script>
import ClientMenu from './components/ClientMenu.vue';
import ClientFooter from './components/ClientFooter.vue';

export default {
  name: 'ClientLayout',
  components: {
    ClientMenu,
    ClientFooter,
  },
};
</script>

<style scoped>
.client-layout {
  background:
    radial-gradient(circle at top left, rgba(255, 180, 92, 0.14), transparent 28%),
    linear-gradient(180deg, #fff8ef 0%, #ffffff 38%, #f7fbff 100%);
}

.client-main {
  padding-top: 88px;
}

@media (max-width: 991.98px) {
  .client-main {
    padding-top: 80px;
  }
}
</style>
