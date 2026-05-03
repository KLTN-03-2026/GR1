<template>
  <LocationManager
    pageTitle="Quản lý Check-in"
    fetchUrl="/dia-diems/check-in"
    categoryId="2"
    categoryDefaultType="Điểm check-in"
  />
</template>

<script>
import LocationManager from './components/LocationManager.vue';

export default {
  name: 'CheckInAdmin',
  components: {
    LocationManager
  }
}
</script>
