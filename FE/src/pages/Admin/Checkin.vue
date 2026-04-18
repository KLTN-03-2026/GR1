<template>
  <LocationManager
    pageTitle="Quản lý Check-in"
    fetchUrl="http://127.0.0.1:8000/api/dia-diems/check-in"
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
