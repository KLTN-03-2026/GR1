<h1 align="center">
  <br>
  ✈️ DaNang Travel Planner AI
  <br>
</h1>

<h4 align="center">Ứng dụng web thông minh giúp tự động lên lịch trình du lịch Đà Nẵng bằng Trí Tuệ Nhân Tạo (AI).</h4>

<p align="center">
  <a href="#-tính-năng-nổi-bật">Tính năng</a> •
  <a href="#-công-nghệ-sử-dụng">Công nghệ</a> •
  <a href="#-cài-đặt--triển-khai">Cài đặt</a> •
  <a href="#-cấu-trúc-dự-án">Cấu trúc</a> •
  <a href="#-nhà-phát-triển">Nhà phát triển</a>
</p>

---

## ✨ Tính năng nổi bật
Dự án mang đến một giải pháp toàn diện cho du khách khi đến Đà Nẵng với các tính năng cốt lõi:

- 🤖 **Lên lịch trình bằng AI**: Tự động tạo lịch trình tham quan chi tiết dựa trên sở thích, số ngày đi và ngân sách của người dùng (Powered by Gemini AI).
- 📍 **Hệ thống địa điểm đa dạng**: Gợi ý điểm đến được phân loại chi tiết (Tâm linh, Giải trí, Ẩm thực, Check-in).
- 🌤️ **Tích hợp thời tiết thực tế**: Cập nhật dự báo thời tiết (Open-Meteo API) đồng bộ với lịch trình tham quan.
- 💬 **Thông báo Real-time**: Hệ thống WebSockets thời gian thực sử dụng Laravel Reverb.
- 🔐 **Xác thực thông minh**: Đăng nhập nhanh chóng và bảo mật qua Google OAuth 2.0.
- 📊 **Quản trị viên (Admin Dashboard)**: Hệ thống quản lý người dùng, kiểm duyệt đánh giá, và thống kê báo cáo mạnh mẽ (Export Excel/PDF).

## 💻 Công nghệ sử dụng

**Backend:**
- Laravel 11.x (PHP 8.2+)
- Cơ sở dữ liệu: MySQL
- WebSockets: Laravel Reverb
- AI Integration: Google Gemini API

**Frontend:**
- Vue.js 3 (Composition API)
- Vite
- State Management: Pinia
- CSS Framework: Tailwind CSS / Vanilla CSS
- Axios

## 🚀 Cài đặt & Triển khai

Dưới đây là hướng dẫn để chạy dự án trên môi trường Localhost.

### 1. Yêu cầu hệ thống
- PHP >= 8.2
- Composer
- Node.js >= 18.x
- MySQL

### 2. Cài đặt Backend (Laravel)

```bash
# Di chuyển vào thư mục Backend
cd BE

# Cài đặt thư viện PHP
composer install

# Sao chép file môi trường và tạo key
cp .env.example .env
php artisan key:generate

# Cấu hình Database trong file .env, sau đó chạy Migration & Seeder
php artisan migrate --seed

# Khởi chạy server Backend
php artisan serve
```

### 3. Cài đặt Frontend (Vue 3)

```bash
# Di chuyển vào thư mục Frontend
cd FE/corevue

# Cài đặt các gói phụ thuộc (Dependencies)
npm install

# Khởi chạy server Frontend
npm run dev
```

### 4. Khởi chạy Real-time Server (WebSockets)
Mở một terminal mới, vào thư mục `BE` và chạy:
```bash
php artisan reverb:start
```

## ⚙️ Biến môi trường (.env)
Bạn cần cấu hình các API Key quan trọng trong file `.env` của Backend để dự án hoạt động đầy đủ:

```env
# AI & Third-party APIs
GEMINI_API_KEY=your_gemini_api_key

# Google OAuth
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback

# WebSocket (Reverb Local)
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=http
```

## 📂 Cấu trúc dự án

```text
Khoá Luận Tốt Nghiệp/
├── BE/                     # Mã nguồn Backend (Laravel 11)
│   ├── app/                # Controllers, Models, Middleware...
│   ├── routes/             # Định nghĩa API endpoints (api.php)
│   └── database/           # Migrations & Seeders
└── FE/
    └── corevue/            # Mã nguồn Frontend (Vue 3 + Vite)
        ├── src/
        │   ├── components/ # Các UI Component dùng chung
        │   ├── pages/      # Views/Pages (Client & Admin)
        │   └── router/     # Vue Router config
        └── package.json
```

## 👨‍💻 Nhà phát triển

- 🎓 **[Nguyễn Anh Hiếu]** - [Vai trò: Backend/Frontend] - 📧 Email: nguyenanhhieu@dtu.edu.vn
- 🎓 **[Bùi Trung Hiếu]** - [Vai trò: Backend/Frontend] - 📧 Email: buitrunghieu3@dtu.edu.vn
- 🎓 **[Nguyễn Việt Hoàng]** - [Vai trò: Frontend] - 📧 Email: nguyenviethoang14@dtu.edu.vn
- 🎓 **[Võ Khải Hoàn]** - [Vai trò: Frontend/UI/UX] - 📧 Email: vokhaihoan@dtu.edu.vn
- 🎓 **[Lê Thị Thanh Lam]** - [Vai trò: Frontend/UI/UX] - 📧 Email: letthanhlam@dtu.edu.vn

**Giảng viên hướng dẫn:** ThS. [Nguyễn Quốc Long]

*"🏫 Khoá Luận Tốt Nghiệp - [Đại Học Duy Tân] ✨"*

## 📞 Liên hệ
- Sinh viên: [Nguyễn Anh Hiếu]
- 📧 Email hỗ trợ: [nguyenanhhieu@dtu.edu.vn] 

## 🤝 Đóng góp (Contributing)
Chúng tôi rất hoan nghênh mọi đóng góp! 

## 🔄 Quy trình phát triển
1. Fork repo này
2. Tạo branch mới (`git checkout -b feature/AmazingFeature`)
3. Commit thay đổi (`git commit -m 'Add some AmazingFeature'`)
4. Push lên branch (`git push origin feature/AmazingFeature`)
5. Tạo Pull Request

## 🐛 Báo lỗi (Issues)
Nếu bạn phát hiện lỗi, vui lòng tạo issue mới với:
- Mô tả chi tiết lỗi
- Các bước để tái hiện lại lỗi (Steps to reproduce)
- Môi trường thử nghiệm (Browser, OS...)

---
<p align="center">Dự án được phát triển với ❤️ cho cộng đồng du lịch Đà Nẵng.</p>
