<template>
  <LocationManager
    pageTitle="Quản lý Giải trí"
    fetchUrl="http://127.0.0.1:8000/api/dia-diems/giai-tri"
    categoryId="3"
    categoryDefaultType="Khu vui chơi"
  />
</template>

<script>
import LocationManager from './components/LocationManager.vue';

export default {
  name: 'GiaiTriAdmin',
  components: {
    LocationManager
  }
}
</script>
