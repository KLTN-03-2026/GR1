<template>
  <LocationManager
    pageTitle="Quản lý Giải trí"
    fetchUrl="/dia-diems/giai-tri"
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
