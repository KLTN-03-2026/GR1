<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChucNang;
use App\Models\ChucVu;

class PhanQuyenSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Dọn dẹp dữ liệu cũ (Xóa cẩn thận để tạo lại)
        DB::table('phan_quyens')->truncate();
        DB::table('chuc_nangs')->delete();
        // Không truncate chuc_vus vì có thể dính foreign keys bảng khác, chỉ update/tạo mới

        // 2. Các chức năng chi tiết theo từng thao tác (CRUD)
        $permissions = [
            // Hệ thống (Chỉ xem)
            ['nhom_chuc_nang' => 'Hệ thống', 'ma_chuc_nang' => 'dashboard_view', 'ten_chuc_nang' => 'Xem Dashboard'],
            ['nhom_chuc_nang' => 'Hệ thống', 'ma_chuc_nang' => 'report_view', 'ten_chuc_nang' => 'Xem Báo cáo thống kê'],
            
            // Quản lý Nhân viên (Admin)
            ['nhom_chuc_nang' => 'Quản lý Nhân viên', 'ma_chuc_nang' => 'admin_view', 'ten_chuc_nang' => 'Xem danh sách Nhân viên'],
            ['nhom_chuc_nang' => 'Quản lý Nhân viên', 'ma_chuc_nang' => 'admin_create', 'ten_chuc_nang' => 'Thêm Nhân viên'],
            ['nhom_chuc_nang' => 'Quản lý Nhân viên', 'ma_chuc_nang' => 'admin_update', 'ten_chuc_nang' => 'Sửa Nhân viên'],
            ['nhom_chuc_nang' => 'Quản lý Nhân viên', 'ma_chuc_nang' => 'admin_delete', 'ten_chuc_nang' => 'Xóa Nhân viên'],
            
            // Quản lý Người dùng
            ['nhom_chuc_nang' => 'Quản lý Người dùng', 'ma_chuc_nang' => 'user_view', 'ten_chuc_nang' => 'Xem danh sách Người dùng'],
            ['nhom_chuc_nang' => 'Quản lý Người dùng', 'ma_chuc_nang' => 'user_create', 'ten_chuc_nang' => 'Thêm Người dùng'],
            ['nhom_chuc_nang' => 'Quản lý Người dùng', 'ma_chuc_nang' => 'user_update', 'ten_chuc_nang' => 'Sửa Người dùng'],
            ['nhom_chuc_nang' => 'Quản lý Người dùng', 'ma_chuc_nang' => 'user_status', 'ten_chuc_nang' => 'Khóa/Mở Khóa Người dùng'],
            ['nhom_chuc_nang' => 'Quản lý Người dùng', 'ma_chuc_nang' => 'user_delete', 'ten_chuc_nang' => 'Xóa Người dùng'],
            
            // Danh mục
            ['nhom_chuc_nang' => 'Quản lý Danh mục', 'ma_chuc_nang' => 'category_view', 'ten_chuc_nang' => 'Xem Danh mục'],
            ['nhom_chuc_nang' => 'Quản lý Danh mục', 'ma_chuc_nang' => 'category_create', 'ten_chuc_nang' => 'Thêm Danh mục'],
            ['nhom_chuc_nang' => 'Quản lý Danh mục', 'ma_chuc_nang' => 'category_update', 'ten_chuc_nang' => 'Sửa Danh mục'],
            ['nhom_chuc_nang' => 'Quản lý Danh mục', 'ma_chuc_nang' => 'category_delete', 'ten_chuc_nang' => 'Xóa Danh mục'],
            
            // Địa điểm - Ẩm thực
            ['nhom_chuc_nang' => 'Quản lý Địa điểm', 'ma_chuc_nang' => 'place_amthuc_view', 'ten_chuc_nang' => 'Xem Ẩm thực'],
            ['nhom_chuc_nang' => 'Quản lý Địa điểm', 'ma_chuc_nang' => 'place_amthuc_create', 'ten_chuc_nang' => 'Thêm Ẩm thực'],
            ['nhom_chuc_nang' => 'Quản lý Địa điểm', 'ma_chuc_nang' => 'place_amthuc_update', 'ten_chuc_nang' => 'Sửa Ẩm thực'],
            ['nhom_chuc_nang' => 'Quản lý Địa điểm', 'ma_chuc_nang' => 'place_amthuc_delete', 'ten_chuc_nang' => 'Xóa Ẩm thực'],
            
            // Địa điểm - Tâm linh
            ['nhom_chuc_nang' => 'Quản lý Địa điểm', 'ma_chuc_nang' => 'place_tamlinh_view', 'ten_chuc_nang' => 'Xem Tâm linh'],
            ['nhom_chuc_nang' => 'Quản lý Địa điểm', 'ma_chuc_nang' => 'place_tamlinh_create', 'ten_chuc_nang' => 'Thêm Tâm linh'],
            ['nhom_chuc_nang' => 'Quản lý Địa điểm', 'ma_chuc_nang' => 'place_tamlinh_update', 'ten_chuc_nang' => 'Sửa Tâm linh'],
            ['nhom_chuc_nang' => 'Quản lý Địa điểm', 'ma_chuc_nang' => 'place_tamlinh_delete', 'ten_chuc_nang' => 'Xóa Tâm linh'],
            
            // Địa điểm - Giải trí
            ['nhom_chuc_nang' => 'Quản lý Địa điểm', 'ma_chuc_nang' => 'place_giaitri_view', 'ten_chuc_nang' => 'Xem Giải trí'],
            ['nhom_chuc_nang' => 'Quản lý Địa điểm', 'ma_chuc_nang' => 'place_giaitri_create', 'ten_chuc_nang' => 'Thêm Giải trí'],
            ['nhom_chuc_nang' => 'Quản lý Địa điểm', 'ma_chuc_nang' => 'place_giaitri_update', 'ten_chuc_nang' => 'Sửa Giải trí'],
            ['nhom_chuc_nang' => 'Quản lý Địa điểm', 'ma_chuc_nang' => 'place_giaitri_delete', 'ten_chuc_nang' => 'Xóa Giải trí'],
            
            // Địa điểm - Check-in
            ['nhom_chuc_nang' => 'Quản lý Địa điểm', 'ma_chuc_nang' => 'place_checkin_view', 'ten_chuc_nang' => 'Xem Check-in'],
            ['nhom_chuc_nang' => 'Quản lý Địa điểm', 'ma_chuc_nang' => 'place_checkin_create', 'ten_chuc_nang' => 'Thêm Check-in'],
            ['nhom_chuc_nang' => 'Quản lý Địa điểm', 'ma_chuc_nang' => 'place_checkin_update', 'ten_chuc_nang' => 'Sửa Check-in'],
            ['nhom_chuc_nang' => 'Quản lý Địa điểm', 'ma_chuc_nang' => 'place_checkin_delete', 'ten_chuc_nang' => 'Xóa Check-in'],
            
            // Đánh giá
            ['nhom_chuc_nang' => 'Quản lý Đánh giá', 'ma_chuc_nang' => 'review_view', 'ten_chuc_nang' => 'Xem Đánh giá'],
            ['nhom_chuc_nang' => 'Quản lý Đánh giá', 'ma_chuc_nang' => 'review_delete', 'ten_chuc_nang' => 'Xóa Đánh giá'], // Đánh giá thường chỉ xem và xóa
        ];

        foreach ($permissions as $p) {
            ChucNang::create([
                'ma_chuc_nang' => $p['ma_chuc_nang'],
                'nhom_chuc_nang' => $p['nhom_chuc_nang'],
                'ten_chuc_nang' => $p['ten_chuc_nang']
            ]);
        }

        // 3. Khởi tạo 2 chức vụ mặc định
        $superAdmin = ChucVu::updateOrCreate(
            ['id' => 1], // ID 1 luôn là Quản trị viên
            ['ten_chuc_vu' => 'Quản trị viên', 'slug_chuc_vu' => 'quan-tri-vien', 'mo_ta' => 'Có toàn quyền hệ thống']
        );

        $nhanVien = ChucVu::updateOrCreate(
            ['id' => 2],
            ['ten_chuc_vu' => 'Nhân viên', 'slug_chuc_vu' => 'nhan-vien', 'mo_ta' => 'Nhân viên quản lý nội dung và kiểm duyệt']
        );

        // 4. Gán quyền cho Quản trị viên (Full Quyền)
        $allPermIds = ChucNang::pluck('id');
        $superAdmin->chucNangs()->sync($allPermIds);

        // 5. Gán quyền mặc định cho Nhân viên bình thường
        // Nhân viên ĐƯỢC XEM danh sách nhân viên (admin_view), nhưng KHÔNG được thêm/sửa/xóa nhân viên
        // Các quyền khác được phép full (hoặc tùy chỉnh)
        $restrictedPerms = ['admin_create', 'admin_update', 'admin_delete'];
        $nhanVienPermIds = ChucNang::whereNotIn('ma_chuc_nang', $restrictedPerms)->pluck('id');
        $nhanVien->chucNangs()->sync($nhanVienPermIds);
        
        // Lưu ý: KHÔNG xóa các chức vụ khác (id > 2) vì người dùng có thể đã tạo thêm (Kế toán, Giám sát,...)
    }
}
