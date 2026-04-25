# Kế hoạch Fix Lỗi & Nâng Cấp Giao Diện Chi Tiết Lịch Trình

Tài liệu này vạch ra hướng xử lý cho 4 vấn đề/tính năng mới theo yêu cầu:

---

## 1. Xử lý lỗi 404 khi gọi API `chot-lich-trinh`
**Nguyên nhân có thể:** 
- Lỗi 404 Not Found có thể xuất phát từ việc Route chưa được định nghĩa đúng chuẩn hoặc nằm ngoài block middleware `auth:sanctum`.
- Hoặc Model `ChuyenDi::find($id)` không tìm thấy dữ liệu, dẫn đến đoạn code `return response()->json(..., 404)` trong Controller.
**Giải pháp:**
- Kiểm tra lại khai báo route trong `routes/api.php` để chắc chắn nó nằm đúng block `Route::prefix('client')`.
- Thay đổi chuẩn trả về của lỗi tìm kiếm thành HTTP 400 hoặc 200 với `status => false` để dễ debug ở Frontend, tránh nhầm lẫn với lỗi 404 Route của Laravel.

---

## 2. Thêm nút "Lưu & Kết thúc" trong trang Chi tiết Lịch trình
**Mục tiêu:** Trưởng nhóm có thể chốt lịch trình ngay tại trang xem chi tiết (`/lich-trinh/:id`), thay vì chỉ có thể chốt trong màn hình Chat nhóm.
**Giải pháp:**
- **Backend:** Trong API get chi tiết chuyến đi (`getChiTietChuyenDi`), trả về thêm một cờ boolean `is_leader`. Backend sẽ query xem `id_nguoi_dung` hiện tại có phải là `truong_nhom` của `id_nhom_du_lich` của chuyến đi hay không.
- **Frontend (`ChiTietLichTrinh.vue`):** 
  - Thêm nút **"🔒 Lưu & kết thúc"** nằm giữa nút "Quay lại" và "Gửi vào nhóm".
  - Nút này được bọc bởi điều kiện `v-if="trip.is_leader && !isFinalized"`.
  - Khi click, gọi cùng một API `chot-lich-trinh`, nếu thành công thì gán `trip.trang_thai = 2`.

---

## 3. Lỗi không cập nhật thứ tự ngay sau khi kéo thả (Sortable.js)
**Nguyên nhân:** Khi bạn kéo thả một DOM Element thông qua `Sortable.js`, thư viện này chỉ thay đổi cấu trúc DOM HTML mà không làm thay đổi biến mảng `chiTietList` trong bộ nhớ của Vue.js. Do đó, khi tính toán xong giờ và cập nhật lên Server, dữ liệu nội bộ của Vue vẫn bị sai lệch cho tới khi F5 lại.
**Giải pháp (Frontend `ChiTietLichTrinh.vue`):**
- Trong sự kiện `onEnd` của Sortable, ta lấy ra vị trí cũ (`evt.oldIndex`) và vị trí mới (`evt.newIndex`).
- Thực hiện cắt và chèn phần tử trong mảng gốc của Vue (`lichTrinhTheoNgay[activeDayTab - 1]`).
- Sau đó mới chạy thuật toán Haversine tính toán lại giờ.
- Điều này giúp Vue.js nhận diện được mảng đã thay đổi và render UI tức thì mà không cần reload trang.

---

## 4. Vẽ đường nối (Polyline) các địa điểm trên Bản đồ Leaflet
**Mục tiêu:** Thay vì chỉ hiển thị các chấm Marker rời rạc, sẽ có đường vẽ nối tiếp nhau theo thứ tự tham quan (A -> B -> C).
**Giải pháp (Frontend `ChiTietLichTrinh.vue`):**
- Trong phương thức `updateMapMarkers()`: Khởi tạo mảng lưu trữ tọa độ: `const latlngs = []`.
- Khi duyệt qua danh sách các địa điểm của ngày hiện tại (`activeDayTab`), push `[lat, lng]` vào mảng `latlngs`.
- Tạo `L.polyline(latlngs, { color: '#0ea5e9', weight: 4, dashArray: '5, 10' })` và thêm vào `map`.
- Cần lưu vết instance của polyline (ví dụ: `this.mapRouteLine`) để xóa (`map.removeLayer()`) mỗi khi chuyển ngày hoặc sau khi thao tác kéo thả đổi thứ tự thành công.
