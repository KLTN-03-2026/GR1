<template>
  <LocationManager
    pageTitle="Quản lý Tâm linh"
    fetchUrl="/dia-diems/tam-linh"
    categoryId="4"
    categoryDefaultType="Chùa"
  />
</template>

<script>
import LocationManager from './components/LocationManager.vue';

export default {
  name: 'TamLinhAdmin',
  components: {
    LocationManager
  }
}
</script>
