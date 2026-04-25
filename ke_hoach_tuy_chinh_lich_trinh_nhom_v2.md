# Kế hoạch Tùy chỉnh Lịch trình Nhóm (Phase 2)

Dưới đây là kế hoạch chi tiết để thực hiện 3 yêu cầu nâng cấp trải nghiệm người dùng đối với Lịch trình nhóm:

---

## 1. Nút "Lưu & kết thúc lịch trình" và Khóa tab Quản lý chi phí
**Vị trí:** File `ChiTietLichTrinh.vue`.
- **Giao diện:** 
  - Bổ sung nút **"Lưu & kết thúc lịch trình"** (màu đỏ hoặc gradient nổi bật) trên Header, đặt nằm giữa nút Quay lại (`<`) và nút Xuất PDF.
  - Nút này chỉ hiển thị khi lịch trình chưa chốt (`trip.trang_thai != 2`) và user là người tạo chuyến đi hoặc là trưởng nhóm.
- **Logic:**
  - Khi bấm, gọi API `/chot-lich-trinh`.
  - Thành công: Gán `trip.trang_thai = 2` và tự động bật **Modal Đánh giá hệ thống** (đã có sẵn code trong file này, chỉ cần trigger `showRatingModal = true`).
- **Khóa Tab Chi phí:**
  - Ở Component hiển thị Chi phí phát sinh, thêm điều kiện kiểm tra `trip.trang_thai === 2`.
  - Nếu đã chốt: Ẩn Form thêm chi phí, ẩn các nút Sửa/Xóa. Chỉ để lại danh sách hiển thị (Chế độ Read-only) và hiển thị thêm dòng chữ: *"Lịch trình đã chốt, tính năng quản lý chi phí đã bị khóa."*

---

## 2. Làm đẹp Modal xác nhận "Lịch trình chính thức"
**Vị trí:** File `GroupChatView.vue`.
- **Giao diện Modal:** 
  - Thay thế hàm `confirm(...)` mặc định xấu xí của trình duyệt bằng một thẻ `div` modal tùy chỉnh, có hiệu ứng `fade-in`, icon cảnh báo màu vàng/xanh, nội dung rõ ràng và 2 nút "Hủy" - "Xác nhận".
- **Toast Thông báo:** 
  - Sau khi chốt nhóm thành công, xóa bỏ hàm `alert(...)` và thay bằng thư viện `useToast()` để hiển thị thông báo góc phải màn hình thật chuyên nghiệp: `toast.success("Đã cập nhật lịch trình nhóm thành công!")`.

---

## 3. Mở rộng quyền Kéo Thả (Drag & Drop)
**Vị trí:** File `ChiTietLichTrinh.vue`.
- **Hiện trạng:** Việc kéo thả thay đổi thứ tự địa điểm thường bị giới hạn chỉ cho chủ sở hữu (người tạo) chuyến đi.
- **Giải pháp:** 
  - Sửa lại điều kiện `disabled` trong component draggable. 
  - Điều kiện mới: Chỉ cần chuyến đi chưa chốt (`trip.trang_thai !== 2`), mọi người dùng đang truy cập vào xem (người tạo, người gửi, thành viên được chia sẻ) đều có thể tương tác kéo thả các card địa điểm.
  - Khi thả, hệ thống vẫn sẽ gọi API `updateOrder` như bình thường để lưu thứ tự mới vào database.
- *Lưu ý:* Việc này sẽ giúp các thành viên cùng đóng góp sắp xếp lịch trình thoải mái hơn sau khi click từ link trong nhóm chat.
