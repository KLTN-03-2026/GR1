# Kế hoạch Refactor Project: Loại bỏ Localhost & Chuẩn hoá API / Socket

Dưới đây là kết quả phân tích toàn bộ project theo yêu cầu của bạn, nhằm khắc phục dứt điểm tình trạng lỗi deploy production trên Vercel do hardcode `localhost` / `127.0.0.1`.

---

## 1. Danh sách các file còn chứa lỗi hardcode (localhost / 127.0.0.1)

Hệ thống đã quét toàn bộ source code `src/` và phát hiện các file sau đang chứa URL hardcode:

### 📁 Config & Services
- `src/config/api.js` (Còn fallback `http://localhost:${API_PORT}`)
- `src/services/clientApi.js` (`http://localhost:8000/api`)
- `src/services/chatApi.js` (`http://localhost:8000`)
- `src/composables/useNhomChatSocket.js` (`localhost:8000` và `127.0.0.1`)

### 📁 Client Pages (`src/pages/Client/...`)
- `DiaDiem/TamLinh.vue`
- `DiaDiem/GiaiTri.vue`
- `DiaDiem/CheckIn.vue`
- `DiaDiem/AmThuc.vue`
- `DiaDiem/ChiTietDiaDiem.vue`
- `DangKy.vue`
- `DangNhap.vue`
- `DatLaiMatKhau.vue`
- `QuenMatKhau.vue`
- `kichHoat.vue`
- `profile.vue`
- `NhomDuLich.vue`
- `GroupChatView.vue`
- `ChiTietLichTrinh.vue`

### 📁 Admin Pages (`src/pages/Admin/...`)
- `Users.vue`
- `TamLinh.vue`
- `GiaiTri.vue`
- `Checkin.vue`
- `Amthuc.vue`
- `Reports.vue`
- `PhanQuyen.vue`
- `Dashboard.vue`
- `DanhSachNhanVien.vue`
- `DanhGiaHaiLong.vue` (Sử dụng string `BASE = 'http://127.0.0.1:8000/api/admin'` sai chuẩn)
- `DangNhap.vue`
- `DanhMuc.vue`
- `components/LocationManager.vue`

### 📁 Components & Layouts
- `src/layout/client/components/ClientMenu.vue`
- `src/components/group/GroupInvitesModal.vue`

---

## 2. Code Refactor: Chuẩn hoá Axios Instance

Hiện tại `src/services/api.js` đang dùng biến `BASE`. Cần thống nhất lại bằng cách trực tiếp sử dụng `import.meta.env.VITE_BACKEND_URL`. Dưới đây là code chuẩn cho duy nhất 1 axios instance.

**Đường dẫn file:** `src/services/api.js`

```javascript
import axios from "axios";

// Đảm bảo không bao giờ có dấu slash ở cuối
const BACKEND_URL = (import.meta.env.VITE_BACKEND_URL || "").replace(/\/+$/, "");
const BASE_URL = `${BACKEND_URL}/api`;

const api = axios.create({
  baseURL: BASE_URL,
  headers: {
    "Content-Type": "application/json",
    "Accept": "application/json"
  },
  // Có thể bật withCredentials nếu dùng sanctum cookie-based auth
  // withCredentials: true 
});

// Thêm Interceptor nếu cần gắn Token vào mọi request
api.interceptors.request.use(
  (config) => {
    // Ví dụ lấy token từ localStorage
    // const token = localStorage.getItem('access_token');
    // if (token) {
    //   config.headers.Authorization = `Bearer ${token}`;
    // }
    return config;
  },
  (error) => Promise.reject(error)
);

export default api;
```

**Cách dùng ở các Component:**
```javascript
import api from '@/services/api';

// Thay vì axios.post('http://localhost:8000/api/client/dang-nhap', payload)
api.post('/client/dang-nhap', payload)
  .then(res => {...});
```

---

## 3. Code Refactor: Sửa Socket (Laravel Echo / Reverb)

File socket hiện tại đang hardcode URL trong `DEFAULT_BACKEND_URL`. Cần sửa để lấy triệt để từ `.env`.

**Đường dẫn file:** `src/composables/useNhomChatSocket.js`

```javascript
// Cập nhật cấu hình reverbConfig
const backendUrl = (import.meta.env.VITE_BACKEND_URL || "").replace(/\/+$/, "");

const reverbConfig = {
  key: import.meta.env.VITE_REVERB_APP_KEY || "",
  host: import.meta.env.VITE_REVERB_HOST || "",
  port: Number(import.meta.env.VITE_REVERB_PORT || 443), // Thường production là 443
  scheme: (import.meta.env.VITE_REVERB_SCHEME || "https").toLowerCase(),
  backendUrl: backendUrl,
};

// Cập nhật hàm createEcho
function createEcho(token) {
  const useTLS = reverbConfig.scheme === "https";
  return new Echo({
    broadcaster: "reverb",
    key: reverbConfig.key,
    wsHost: reverbConfig.host,
    wsPort: reverbConfig.port,
    wssPort: reverbConfig.port,
    forceTLS: useTLS,
    enabledTransports: ["ws", "wss"],
    authEndpoint: `${reverbConfig.backendUrl}/broadcasting/auth`,
    auth: {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: "application/json",
      },
    },
  });
}
```

> **Lưu ý:** Nếu dùng Storage (ảnh/avatar), thay vì trả về `http://127.0.0.1:8000/storage/...` thì bạn có thể tạo 1 helper:
```javascript
export const getStorageUrl = (path) => {
  const backendUrl = (import.meta.env.VITE_BACKEND_URL || "").replace(/\/+$/, "");
  const cleanPath = path.startsWith('/') ? path : `/${path}`;
  return `${backendUrl}/storage${cleanPath}`;
}
```

---

## 4. Checklist Đảm Bảo Deploy Vercel (Production) Không Lỗi

Trước khi push code lên deploy, bạn cần kiểm tra checklist sau:

- [ ] **Khai báo Environment Variables trên Vercel:**
  - `VITE_BACKEND_URL`: URL Backend Laravel của bạn (ví dụ: `https://api.yourdomain.com`).
  - `VITE_REVERB_APP_KEY`: Key của websocket.
  - `VITE_REVERB_HOST`: Host websocket production (không được để trống, không phải 127.0.0.1).
  - `VITE_REVERB_PORT`: Thường là `443` cho production.
  - `VITE_REVERB_SCHEME`: `https`.

- [ ] **Xóa hoàn toàn `axios` standalone khỏi các component:** Chỉ import và sử dụng duy nhất biến `api` từ `src/services/api.js`.
- [ ] **Kiểm tra CORS bên Laravel Backend:** File `config/cors.php` của backend phải cho phép domain Vercel truy cập (`allowed_origins` => `['https://your-vercel-app.vercel.app']`).
- [ ] **Kiểm tra file tĩnh (Images):** Đảm bảo link ảnh trả về từ Backend không chứa localhost, có thể chỉnh biến `APP_URL` ở server Laravel `.env` thành domain thực tế.
- [ ] **Test Build Local:** Chạy `npm run build` dưới local trước khi push để xem có lỗi cú pháp hoặc import sai không.

---

**Bạn có muốn tôi tiến hành thực thi sửa tự động toàn bộ 30+ file này ngay bây giờ không? (Dùng axios instance chuẩn và xoá hết các localhost/127.0.0.1)**
