# Kế Hoạch Xử Lý Triệt Để Lỗi Nhân Đôi "/api/api" Trên Production

## 1. Nguyên nhân gốc rễ (Root Cause)
Lỗi request gọi đến `http://136.115.68.59/api/api/dia-diems/check-in` khi đẩy lên production thường xuất phát từ sự bất đồng bộ giữa biến môi trường và bản build hiện tại:

1. **Biến môi trường trên Production (Server/Vercel)**: Khả năng cao trên server, biến `VITE_BACKEND_URL` đang được thiết lập thừa chữ `/api` ở cuối (tức là `http://136.115.68.59/api`).
2. **Code bị Cache hoặc Chưa Cập Nhật**: Mặc dù ở local chúng ta đã sửa file `src/services/api.js` để KHÔNG tự động thêm `/api` nữa, nhưng bản build cũ đẩy lên production (hoặc Vercel cache lại bản build cũ) vẫn chứa logic nối chuỗi `+ "/api"`.
   => Kết quả: Trình duyệt ghép nối `http://136.115.68.59/api` (từ env) + `/api/dia-diems/check-in` (từ code cũ) = `/api/api/...`.

---

## 2. Các Bước Khắc Phục (Action Plan)

### Bước 1: Sửa lại Biến Môi Trường (Environment Variables) trên Server
- Truy cập vào Server (nếu dùng VPS) hoặc Dashboard của Vercel/Netlify.
- Tìm đến mục quản lý biến môi trường.
- Sửa giá trị `VITE_BACKEND_URL` thành domain gốc, **tuyệt đối không có `/api`**.
  - ✅ **ĐÚNG**: `VITE_BACKEND_URL=http://136.115.68.59` (hoặc `https://domain.com`)
  - ❌ **SAI**: `VITE_BACKEND_URL=http://136.115.68.59/api`

### Bước 2: Dọn dẹp code rác để tránh nhầm lẫn về sau
Dự án của bạn hiện đang có file cấu hình cũ không còn dùng tới nhưng nội dung bên trong vẫn chứa việc nối `/api` cứng.
- Cần xóa file `src/config/api.js`. File này đang có dòng `export const BASE = \`${BACKEND_URL}/api\`;`. Tuy hiện tại project không import file này, nhưng việc để lại dễ gây lỗi nếu có ai đó import nhầm trong tương lai. (Toàn bộ project giờ chỉ dùng `src/services/api.js`). Tôi sẽ xóa file này cho bạn.

### Bước 3: Build và Xóa Cache (QUAN TRỌNG NHẤT)
Vue 3/Vite sẽ "đóng băng" (hardcode) giá trị của `import.meta.env` trực tiếp vào file `.js` lúc bạn chạy lệnh build. Do đó:
1. Nếu bạn build ở local rồi copy thư mục `dist` lên server:
   - Hãy chắc chắn bạn đã chạy lệnh `npm run build` với phiên bản code mới nhất (bản đã sửa xong vụ lỗi import khi nãy).
   - Xóa thư mục `dist` cũ trên server và copy thư mục `dist` mới lên.
2. Nếu bạn dùng Vercel tự động build:
   - Vào Vercel, chọn tab **Deployments**.
   - Bấm vào dấu 3 chấm của bản deploy gần nhất -> Chọn **Redeploy**.
   - (Bắt buộc) Bỏ tick ô "Use Build Cache" nếu có, để Vercel lấy biến môi trường và code mới nhất compile lại từ đầu.

### Bước 4: Hard Refresh Trình Duyệt
- Trình duyệt thường cache các file JS cũ rất lâu. Bạn hãy mở tab ẩn danh (Incognito) hoặc nhấn `Ctrl + F5` (Windows) / `Cmd + Shift + R` (Mac) ở trang web trên production để test lại. Lúc này lỗi `api/api` chắc chắn sẽ biến mất.
