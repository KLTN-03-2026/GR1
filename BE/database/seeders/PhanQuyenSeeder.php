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

        // 2. Các chức năng có THẬT trong hệ thống hiện tại
        $permissions = [
            // Hệ thống
            ['nhom_chuc_nang' => 'Hệ thống', 'ma_chuc_nang' => 'dashboard_view', 'ten_chuc_nang' => 'Xem Dashboard'],
            ['nhom_chuc_nang' => 'Hệ thống', 'ma_chuc_nang' => 'admin_manage', 'ten_chuc_nang' => 'Quản lý nhân viên'],
            ['nhom_chuc_nang' => 'Hệ thống', 'ma_chuc_nang' => 'report_view', 'ten_chuc_nang' => 'Xem Báo cáo thống kê'],
            
            // Người dùng
            ['nhom_chuc_nang' => 'Khách hàng', 'ma_chuc_nang' => 'user_manage', 'ten_chuc_nang' => 'Quản lý người dùng'],
            
            // Danh mục
            ['nhom_chuc_nang' => 'Nội dung', 'ma_chuc_nang' => 'category_manage', 'ten_chuc_nang' => 'Quản lý danh mục'],
            
            // Địa điểm
            ['nhom_chuc_nang' => 'Nội dung', 'ma_chuc_nang' => 'place_amthuc_manage', 'ten_chuc_nang' => 'Quản lý Ẩm thực'],
            ['nhom_chuc_nang' => 'Nội dung', 'ma_chuc_nang' => 'place_tamlinh_manage', 'ten_chuc_nang' => 'Quản lý Tâm linh'],
            ['nhom_chuc_nang' => 'Nội dung', 'ma_chuc_nang' => 'place_giaitri_manage', 'ten_chuc_nang' => 'Quản lý Giải trí'],
            ['nhom_chuc_nang' => 'Nội dung', 'ma_chuc_nang' => 'place_checkin_manage', 'ten_chuc_nang' => 'Quản lý Check-in'],
            
            // Đánh giá
            ['nhom_chuc_nang' => 'Kiểm duyệt', 'ma_chuc_nang' => 'review_manage', 'ten_chuc_nang' => 'Quản lý đánh giá'],
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

        // 4. Gán quyền mặc định cho Nhân viên bình thường (Tất cả trừ admin_manage)
        $nhanVienPermIds = ChucNang::where('ma_chuc_nang', '!=', 'admin_manage')->pluck('id');
        $nhanVien->chucNangs()->sync($nhanVienPermIds);
        
        // Disable foreign key checks tạm thời nếu muốn xóa các Role ảo
        // Xóa những chức vụ rác ngoài id 1 và 2 nếu có
        ChucVu::whereNotIn('id', [1, 2])->delete();
    }
}
