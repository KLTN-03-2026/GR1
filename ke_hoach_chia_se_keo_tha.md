# Kế hoạch Triển khai: Chia sẻ nhóm khi tạo & Kéo thả địa điểm

Dưới đây là kế hoạch chi tiết để triển khai 2 yêu cầu mới nhằm tăng tính tương tác và trải nghiệm người dùng (UX) cho đồ án:

---

## 1. Chia sẻ lịch trình vào nhóm khi tạo & Cho phép thành viên đổi địa điểm

**Mục tiêu:** 
Ngay tại lúc tạo lịch trình (form nhập liệu AI), người dùng có thể chỉ định luôn một nhóm để hệ thống tự động share. Các thành viên trong nhóm đó khi xem chi tiết có quyền sử dụng nút "Đổi địa điểm" (Swap).

### Bước 1: Frontend - Form Tạo Lịch Trình (`TaoLichTrinh.vue`)
- **Tải danh sách nhóm:** Gọi API lấy các nhóm mà người dùng đang tham gia (API đã có sẵn).
- **Thêm field UI:** Bổ sung một Dropdown/Select mang tên *"Gắn với nhóm du lịch (Tùy chọn)"* ở bước nhập thông tin chuyến đi.
- **Gửi dữ liệu:** Khi submit form tạo lịch trình AI, truyền thêm tham số `id_nhom_du_lich` lên Backend.

### Bước 2: Backend - API Tạo chuyến đi & Tự động share
- **Cập nhật dữ liệu chuyến đi:** Trong `ClientApiController::createChuyenDi` (hoặc Controller gọi AI), khi tạo `ChuyenDi` sẽ gán luôn trường `id_nhom_du_lich = request->id_nhom_du_lich`.
- **Gửi tin nhắn tự động:** Sau khi AI tạo xong lịch trình, nếu có `id_nhom_du_lich`, Backend tự động tạo một dòng dữ liệu vào bảng `nhom_chats` chứa nội dung định dạng JSON của chuyến đi (tương tự như logic nút "Chia sẻ" hiện tại) để cả nhóm thấy thông báo.

### Bước 3: Frontend & Backend - Quyền đổi địa điểm cho thành viên
- **Giao diện (`ChiTietLichTrinh.vue`):** Đảm bảo nút "Đổi" không bị khóa bởi phân quyền (hiện tại logic đã mở cho bất cứ ai truy cập được link chuyến đi).
- **Trải nghiệm Realtime (Tùy chọn):** Sau khi thành viên A bấm "Đổi địa điểm", có thể sử dụng WebSocket/Pusher hoặc đơn giản là yêu cầu thành viên B reload lại trang để thấy dữ liệu mới.

---

## 2. Tính năng: Kéo thả để thay đổi thứ tự địa điểm (Drag & Drop)

**Mục tiêu:** 
Tại trang Chi tiết lịch trình, người dùng có thể giữ chuột vào một địa điểm và kéo lên/xuống để đổi thứ tự tham quan trong ngày hôm đó.

### Bước 1: Backend - API Cập nhật thứ tự hàng loạt
- **Route:** Thêm route mới trong `routes/api.php`:
  ```php
  Route::post('/lich-trinh-dia-diems/reorder', [LichTrinhDiaDiemController::class, 'reorder']);
  ```
- **Xử lý Controller (`LichTrinhDiaDiemController.php`):**
  Nhận vào mảng dữ liệu gồm ID lịch trình và Thứ tự mới, ví dụ: 
  `items: [{ id: 10, thu_tu: 1 }, { id: 12, thu_tu: 2 }, ...]`
  - Lặp qua mảng và cập nhật field `thu_tu_tham_quan` tương ứng.
  - Sửa lại trường `gio_bat_dau`, `gio_ket_thuc` nếu cần thiết (tính toán lại theo thứ tự mới) hoặc chỉ cần đổi vị trí.

### Bước 2: Frontend - Thư viện Kéo thả (`ChiTietLichTrinh.vue`)
- **Tích hợp:** Sử dụng thư viện `VueDraggablePlus` hoặc thư viện kéo thả mặc định của HTML5 để bao bọc danh sách `lichTrinhTheoNgay`.
- **UI:** Thêm biểu tượng "Dấu 6 chấm" (Drag handle) ở góc của mỗi `timeline-card` để báo hiệu cho người dùng biết có thể kéo thả.
- **Xử lý sự kiện `@end`:**
  - Khi thả chuột ra, mảng dữ liệu local đã bị thay đổi vị trí.
  - Vòng lặp map mảng mới để gán lại `thu_tu_tham_quan` (ví dụ vị trí 0 thì thứ tự 1...).
  - Đóng gói mảng mới gửi lên API `/lich-trinh-dia-diems/reorder`.
  - Hiển thị Toast thông báo "Đã lưu thứ tự mới!".
  - (Khuyến nghị: Tính toán lại giờ bắt đầu/kết thúc cho hợp lý để các địa điểm không bị dồn giờ).

---

## Đánh giá
Hai tính năng này hoàn toàn khả thi và là những điểm nhấn UX cực kỳ ấn tượng khi Demo báo cáo đồ án, đặc biệt tính năng Kéo Thả (Drag & Drop) luôn được hội đồng đánh giá cao về độ đầu tư kỹ thuật Frontend. Bạn muốn tiến hành code ngay tính năng nào trước thì báo cho tôi nhé!
