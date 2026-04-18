<template>
  <LocationManager
    pageTitle="Quản lý Tâm linh"
    fetchUrl="http://127.0.0.1:8000/api/dia-diems/tam-linh"
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
