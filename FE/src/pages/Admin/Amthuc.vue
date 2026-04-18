<template>
  <LocationManager
    pageTitle="Quản lý Ẩm thực"
    fetchUrl="http://127.0.0.1:8000/api/dia-diems/am-thuc"
    categoryId="1"
    categoryDefaultType="Quán ăn"
  />
</template>

<script>
import LocationManager from './components/LocationManager.vue';

export default {
  name: 'AmThucAdmin',
  components: {
    LocationManager
  }
}
</script>
