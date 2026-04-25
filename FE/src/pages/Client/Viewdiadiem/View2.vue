<template>
  <div class="view2-page">
    <div class="container">
      <h1 class="page-title">Chi tiết địa điểm</h1>
      <!-- Loading state -->
      <div v-if="loading" class="loading">
        <span>Đang tải...</span>
      </div>
      <!-- Content -->
      <div v-else-if="location" class="location-content">
        <div class="location-header">
          <img :src="location.image" :alt="location.name" class="location-image shadow" />
          <div class="location-info">
            <h2>{{ location.name }}</h2>
            <p class="location-address"><i class="fas fa-map-marker-alt"></i> {{ location.address }}</p>
            <div class="location-rating">
              <span class="stars">⭐⭐⭐⭐⭐</span>
              <span class="rating-text">{{ location.rating }} ({{ location.reviews }} đánh giá)</span>
            </div>
          </div>
        </div>
        <div class="location-description">
          <h3>Mô tả</h3>
          <p>{{ location.description }}</p>
        </div>
        <div class="location-features">
          <h3>Tiện ích</h3>
          <ul class="features-list">
            <li v-for="feature in location.features" :key="feature">
              <i class="fas fa-check-circle" style="color:#1976d2;margin-right:6px;"></i>{{ feature }}
            </li>
          </ul>
        </div>
        <div class="location-gallery">
          <h3>Hình ảnh</h3>
          <div class="gallery-grid">
            <img 
              v-for="(img, index) in location.gallery" 
              :key="index" 
              :src="img" 
              :alt="`Hình ${index + 1}`"
              class="gallery-item"
            />
          </div>
        </div>
        <div class="location-map">
          <h3>Bản đồ</h3>
          <div class="map-container">
            <iframe 
              v-if="location.mapUrl" 
              :src="location.mapUrl" 
              width="100%" 
              height="400" 
              style="border:0;" 
              allowfullscreen 
              loading="lazy"
            ></iframe>
          </div>
        </div>
        <div class="location-reviews">
          <h3>Đánh giá</h3>
          <div class="reviews-list">
            <div v-for="review in location.reviewsList" :key="review.id" class="review-item">
              <div class="review-header">
                <img :src="review.avatar" :alt="review.user" class="review-avatar" />
                <div class="review-info">
                  <span class="review-user">{{ review.user }}</span>
                  <span class="review-date">{{ review.date }}</span>
                </div>
                <div class="review-rating">⭐⭐⭐⭐⭐</div>
              </div>
              <p class="review-content">{{ review.content }}</p>
            </div>
          </div>
        </div>
        <div class="location-actions">
          <button class="btn btn-primary" @click="handleShare"><i class="fas fa-share-alt"></i> Chia sẻ</button>
          <button class="btn btn-secondary" @click="handleSave"><i class="fas fa-heart"></i> Lưu</button>
          <button class="btn btn-outline" @click="handleDirections"><i class="fas fa-location-arrow"></i> Chỉ đường</button>
        </div>
      </div>
      <!-- Not found -->
      <div v-else class="not-found">
        <p>Không tìm thấy địa điểm</p>
        <router-link to="/" class="btn btn-primary">Quay về trang chủ</router-link>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'View2Page',
  data() {
    return {
      loading: true,
      location: null,
      locationId: null
    }
  },
  mounted() {
    this.locationId = this.$route.params.id
    this.fetchLocation()
  },
  methods: {
    async fetchLocation() {
      try {
        // Simulated API call
        await new Promise(resolve => setTimeout(resolve, 500))
        this.location = {
          id: this.locationId || '1',
          name: 'Địa điểm mẫu',
          address: '123 Đường ABC, Quận XYZ, TP.HCM',
          image: 'https://via.placeholder.com/800x400',
          rating: 4.5,
          reviews: 128,
          description: 'Mô tả chi tiết về địa điểm du lịch...',
          features: ['WiFi miễn phí', 'Chỗ đậu xe', 'Nhà hàng', 'Khách sạn', 'Hướng dẫn viên'],
          gallery: [
            'https://via.placeholder.com/300x200',
            'https://via.placeholder.com/300x200',
            'https://via.placeholder.com/300x200',
            'https://via.placeholder.com/300x200'
          ],
          mapUrl: 'https://www.google.com/maps/embed?pb=...',
          reviewsList: [
            {
              id: 1,
              user: 'Nguyễn Văn A',
              avatar: 'https://via.placeholder.com/50',
              date: '20/04/2026',
              content: 'Địa điểm rất đẹp, tôi sẽ quay lại!'
            },
            {
              id: 2,
              user: 'Trần Thị B',
              avatar: 'https://via.placeholder.com/50',
              date: '18/04/2026',
              content: 'Dịch vụ tốt, nhân viên nhiệt tình.'
            }
          ]
        }
      } catch (error) {
        console.error('Error fetching location:', error)
      } finally {
        this.loading = false
      }
    },
    handleShare() {
      if (navigator.share) {
        navigator.share({
          title: this.location.name,
          text: this.location.description,
          url: window.location.href
        })
      } else {
        alert('Sao chép link: ' + window.location.href)
      }
    },
    handleSave() {
      alert('Đã lưu địa điểm!')
    },
    handleDirections() {
      window.open(`https://www.google.com/maps/dir/?api=1&destination=${encodeURIComponent(this.location.address)}`, '_blank')
    }
  }
}
</script>

<style scoped>
.view2-page {
  padding: 20px;
  min-height: 100vh;
  background: #f5f5f5;
}
.container {
  max-width: 1200px;
  margin: 0 auto;
  background: white;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}
.page-title {
  font-size: 28px;
  color: #333;
  margin-bottom: 20px;
  text-align: center;
  letter-spacing: 1px;
}
.shadow {
  box-shadow: 0 4px 16px rgba(25,118,210,0.13);
}
.loading {
  text-align: center;
  padding: 40px;
  font-size: 18px;
  color: #666;
}
.location-header {
  display: flex;
  gap: 20px;
  margin-bottom: 30px;
}
.location-image {
  width: 400px;
  height: 250px;
  object-fit: cover;
  border-radius: 8px;
}
.location-info {
  flex: 1;
}
.location-info h2 {
  font-size: 24px;
  margin-bottom: 10px;
}
.location-address {
  color: #1976d2;
  margin-bottom: 10px;
  font-weight: 500;
}
.location-rating {
  display: flex;
  align-items: center;
  gap: 10px;
}
.stars {
  color: #ffc107;
}
.location-description,
.location-features,
.location-gallery,
.location-map,
.location-reviews {
  margin-bottom: 30px;
}
.location-description h3,
.location-features h3,
.location-gallery h3,
.location-map h3,
.location-reviews h3 {
  font-size: 20px;
  margin-bottom: 15px;
  color: #333;
}
.features-list {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  list-style: none;
  padding: 0;
}
.features-list li {
  background: #e3f2fd;
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 14px;
  display: flex;
  align-items: center;
}
.gallery-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 10px;
}
.gallery-item {
  width: 100%;
  height: 150px;
  object-fit: cover;
  border-radius: 8px;
  cursor: pointer;
  transition: transform 0.3s;
}
.gallery-item:hover {
  transform: scale(1.05);
}
.map-container {
  border-radius: 8px;
  overflow: hidden;
}
.reviews-list {
  display: flex;
  flex-direction: column;
  gap: 15px;
}
.review-item {
  border: 1px solid #eee;
  border-radius: 8px;
  padding: 15px;
}
.review-header {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 10px;
}
.review-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
}
.review-info {
  flex: 1;
  display: flex;
  flex-direction: column;
}
.review-user {
  font-weight: bold;
}
.review-date {
  font-size: 12px;
  color: #999;
}
.review-rating {
  color: #ffc107;
}
.review-content {
  color: #555;
  line-height: 1.6;
}
.location-actions {
  display: flex;
  gap: 10px;
  justify-content: center;
  margin-top: 30px;
}
.btn {
  padding: 12px 24px;
  border: none;
  border-radius: 6px;
  font-size: 16px;
  cursor: pointer;
  transition: all 0.3s;
  display: flex;
  align-items: center;
  gap: 6px;
}
.btn-primary {
  background: #1976d2;
  color: white;
}
.btn-primary:hover {
  background: #1565c0;
}
.btn-secondary {
  background: #4caf50;
  color: white;
}
.btn-secondary:hover {
  background: #43a047;
}
.btn-outline {
  background: transparent;
  border: 2px solid #1976d2;
  color: #1976d2;
}
.btn-outline:hover {
  background: #1976d2;
  color: white;
}
.not-found {
  text-align: center;
  padding: 60px 20px;
}
.not-found p {
  color: #888;
  font-size: 18px;
  margin-bottom: 18px;
}
</style>