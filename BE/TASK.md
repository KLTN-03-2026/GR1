# Danh sách Tác vụ (Task List) - Project Backend KLTN

Dựa trên thiết kế Cơ sở dữ liệu và phân tích logic nghiệp vụ, dưới đây là danh sách các tác vụ (tasks) cần thực hiện cho phần Backend của dự án. File này đóng vai trò như một checklist chuẩn để theo dõi tiến độ công việc.

## 1. Thiết lập Dự án & Cơ sở dữ liệu (Project Setup & Database)

- [ ] Khởi tạo project Backend (Cấu hình framework, thư viện cơ bản).
- [ ] Thiết lập kết nối Cơ sở dữ liệu (Cấu hình `.env`).
- [ ] Tạo các file Database Migration cho toàn bộ các bảng trong CSDL (Khởi tạo Schema, đảm bảo đúng thứ tự khoá ngoại rào buộc để không bị lỗi).
- [ ] Tạo các file Seeder và Factory để sinh dữ liệu mẫu (Fake data) cho mục đích test UI/API.

## 2. Module Quản trị viên & Phân quyền (Admin & RBAC)

- [ ] Xây dựng API Đăng nhập/Đăng xuất cho hệ thống tài khoản Admin.
- [x] Xây dựng CRUD cho bảng `chuc_vu` (Roles).
- [x] Xây dựng CRUD cho bảng `chuc_nangs` (Permissions).
- [x] Xây dựng API gán quyền cho chức vụ (Lưu vào bảng trung gian `phan_quyens`).
- [ ] Cấu hình Middleware kiểm tra quyền truy cập (RBAC Middleware - Chặn truy cập api nếu admin không có quyền).
- [x] Xây dựng CRUD quản lý tài khoản `admin`.

## 3. Module Quản lý Địa điểm (Locations)

- [x] Xây dựng CRUD cho bảng `danh_mucs` (Categories).
- [ ] Xây dựng CRUD tổng thể cho `dia_diem` (Bao gồm thông tin chung, toạ độ, giá vé, giờ mở cửa...).
- [ ] Xây dựng API Upload và quản lý Hình ảnh địa điểm (Bảng `hinh_anh_dia_diem`).
- [x] Xây dựng API Gán các danh mục (Tags/Categories) cho một địa điểm cụ thể (`chi_tiet_danh_mucs`).
- [ ] Xây dựng API/Logic: Tìm kiếm, Lọc (Filter) địa điểm theo tên, danh mục, khoảng giá, và bán kính toạ độ (Kinh độ / Vĩ độ).

## 4. Module Tương tác Nhóm du lịch (Social/Group)

- [ ] Xây dựng API Cho phép người dùng tạo nhóm du lịch mới (`nhom_du_lich` - Mặc định gán người tạo làm vai trò Admin nhóm).
- [ ] Xây dựng API Mời/Thêm thành viên vào nhóm, và API cập nhật vai trò trong nhóm (`chi_tiet_nhom`).
- [ ] Xây dựng API Xóa thành viên khỏi nhóm / Chủ động Rời khỏi nhóm.
- [ ] Xây dựng API Quản lý box chat giao tiếp (`nhom_chat`): Gửi tin nhắn, load lại lịch sử chat trước đó (Xem xét tích hợp WebSocket/Socket.io để real-time).

## 5. Module Lập kế hoạch & Lịch trình (Trips & Routing)

- [ ] Xây dựng API Tạo chuyến đi (`chuyen_di`) có đính kèm thông số ngân sách, số ngày, ngày đi.
- [ ] Xây dựng Logic xác thực mức 1: Rào buộc chuyến đi chỉ có thể có `id_nguoi_dung` (Cá nhân) HOẶC `id_nhom_du_lich` (Nhóm) - Không bao giờ được phép có và tồn tại cả 2 hoặc null cả 2.
- [ ] Xây dựng API Add địa điểm vào chuyến đi (`lich_trinh_dia_diem`) bao gồm set chi phí cắm mốc dự kiến và thời gian dự kiến.
- [ ] Xây dựng API Đổi chỗ/Cập nhật thứ tự đi tới (`thu_tu_tham_quan`).
- [x] Xây dựng API Quản lý chi phí lặt vặt/phát sinh bên ngoài lịch trình (`chi_phi_phat_sinhs`).
- [ ] Xây dựng API/Logic: Hàm xuất Báo cáo Thống kê & Tính toán tổng chi phí thực tiễn dựa trên dữ liệu các địa điểm + chi phí lặt vặt; Đưa ra Response cảnh báo (Warning) nếu số tiền này vượt mức `ngan_sach`.

## 6. Chuẩn hoá Đầu ra & API Documentation

- [ ] Cài đặt framework làm Document API (Swagger, Postman collection file, hoặc Scribe).
- [ ] Bổ sung Description log, format Response JSON chuẩn chỉnh thành 1 khung thống nhất cho 100% Endpoints.
- [ ] Test toàn bộ quá trình luồng (End-to-End Test sơ bộ).
