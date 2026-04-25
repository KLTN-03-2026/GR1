<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\ChucVuController;
use App\Http\Controllers\Api\PhanQuyenController;
use App\Http\Controllers\Api\ChucNangController;
use App\Http\Controllers\Api\DanhMucController;
use App\Http\Controllers\Api\ChiTietDanhMucController;
use App\Http\Controllers\Api\ChiPhiPhatSinhController;
use App\Http\Controllers\Api\DiaDiemController;
use App\Http\Controllers\Api\HinhAnhDiaDiemController;
use App\Http\Controllers\Api\DanhGiaController;
use App\Http\Controllers\Api\SoThichNguoiDungController;
use App\Http\Controllers\Api\NhomDuLichController;
use App\Http\Controllers\Api\ChiTietNhomController;
use App\Http\Controllers\Api\NhomChatController;
use App\Http\Controllers\Api\ChuyenDiController;
use App\Http\Controllers\Api\LichTrinhDiaDiemController;
use App\Http\Controllers\Api\NguoiDungController;
use App\Http\Controllers\Api\DanhGiaHeThongController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('client')->group(function () {
    // Auth Routes
    Route::post('/dang-nhap', [NguoiDungController::class, 'dangNhap']);
    Route::post('/dang-ky', [NguoiDungController::class, 'dangKy']);
    Route::post('/quen-mat-khau', [NguoiDungController::class, 'quenMatKhau']);
    Route::post('/dat-lai-mat-khau', [NguoiDungController::class, 'datLaiMatKhau']);
    Route::post('/doi-mat-khau', [NguoiDungController::class, 'doiMatKhau'])->middleware('auth:sanctum');
    Route::post('/kich-hoat', [NguoiDungController::class, 'kichHoat']);
    Route::get('/kich-hoat/{hash_active}', [NguoiDungController::class, 'kichHoat']);
    Route::get('/profile/{id?}', [NguoiDungController::class, 'profile'])->middleware('auth:sanctum');
    Route::post('/cap-nhat-thong-tin', [NguoiDungController::class, 'capNhatThongTin'])->middleware('auth:sanctum');
    Route::post('/cap-nhat-anh-dai-dien', [NguoiDungController::class, 'capNhatAnhDaiDien'])->middleware('auth:sanctum');

    // Planning & Trips
    Route::get('/dia-diem/get-data', [\App\Http\Controllers\Api\ClientApiController::class, 'getDiaDiem']);
    Route::post('/chuyen-di/create', [\App\Http\Controllers\Api\ClientApiController::class, 'createChuyenDi'])->middleware('auth:sanctum');
    Route::get('/chuyen-di/get-data', [\App\Http\Controllers\Api\ClientApiController::class, 'getChuyenDi'])->middleware('auth:sanctum');
    Route::post('/chuyen-di/delete', [\App\Http\Controllers\Api\ClientApiController::class, 'deleteChuyenDi'])->middleware('auth:sanctum');
    Route::post('/chi-tiet-chuyen-di/bulk-create', [\App\Http\Controllers\Api\ClientApiController::class, 'bulkCreateChiTiet'])->middleware('auth:sanctum');
    Route::post('/chuyen-di/{id}/chot-lich-trinh', [\App\Http\Controllers\Api\ClientApiController::class, 'chotLichTrinh'])->middleware('auth:sanctum');
    Route::post('/chuyen-di/{id}/mo-lich-trinh', [\App\Http\Controllers\Api\ClientApiController::class, 'moLichTrinh'])->middleware('auth:sanctum');
    Route::get('/chuyen-di/{id}', [\App\Http\Controllers\Api\ClientApiController::class, 'getChiTietChuyenDi']);

    // API Nhóm du lịch
    Route::prefix('nhom-du-lich')->middleware('auth:sanctum')->group(function () {
        Route::get('/get-joined', [\App\Http\Controllers\Api\ClientNhomDuLichController::class, 'getJoined']);
        Route::get('/get-my-groups', [\App\Http\Controllers\Api\ClientNhomDuLichController::class, 'getMyGroups']);
        Route::get('/get-invites', [\App\Http\Controllers\Api\ClientNhomDuLichController::class, 'getInvites']);
        // Route members phải đặt TRƯỚC /{id} để tránh conflict
        Route::get('/members/{id}', [\App\Http\Controllers\Api\ClientNhomDuLichController::class, 'getMembers']);
        Route::get('/{id}', [\App\Http\Controllers\Api\ClientNhomDuLichController::class, 'getGroup']);
        Route::post('/{id}/set-lich-trinh', [\App\Http\Controllers\Api\ClientNhomDuLichController::class, 'setLichTrinhChinh']);
        Route::post('/create', [\App\Http\Controllers\Api\ClientNhomDuLichController::class, 'createGroup']);
        Route::post('/invite', [\App\Http\Controllers\Api\ClientNhomDuLichController::class, 'inviteMember']);
        Route::post('/accept-invite', [\App\Http\Controllers\Api\ClientNhomDuLichController::class, 'acceptInvite']);
        Route::post('/reject-invite', [\App\Http\Controllers\Api\ClientNhomDuLichController::class, 'rejectInvite']);
        Route::post('/remove-member', [\App\Http\Controllers\Api\ClientNhomDuLichController::class, 'removeMember']);
        Route::post('/leave', [\App\Http\Controllers\Api\ClientNhomDuLichController::class, 'leaveGroup']);
        Route::post('/delete', [\App\Http\Controllers\Api\ClientNhomDuLichController::class, 'deleteGroup']);
    });
    // Favorites
    Route::prefix('yeu-thich')->middleware('auth:sanctum')->group(function () {
        Route::post('/toggle', [\App\Http\Controllers\Api\ClientApiController::class, 'toggleFavorite']);
        Route::get('/', [\App\Http\Controllers\Api\ClientApiController::class, 'getFavorites']);
    });

    // AI Features
    Route::prefix('ai')->middleware('auth:sanctum')->group(function () {
        Route::post('/generate-itinerary', [\App\Http\Controllers\Api\AiController::class, 'generateItinerary']);
        Route::post('/reorder-itinerary/{id}', [\App\Http\Controllers\Api\AiController::class, 'reorderWithAi']);
    });

    // Đánh giá hệ thống (Client gửi sau khi lưu lịch trình – không bắt buộc đăng nhập)
    Route::post('/danh-gia-he-thong', [DanhGiaHeThongController::class, 'store']);
});


// Admin Auth Routes
Route::post('/admin/dang-nhap', [AdminController::class, 'dangNhap']);


// Admins Quản Lý Nhân Viên Routes
Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
    // Phân quyền: Cấm "Nhân viên" vào quản lý tài khoản Quản trị
    Route::middleware('check_permission:admin_manage')->group(function () {
        Route::get('/danh-sach-nhan-vien', [AdminController::class, 'getData']);
        Route::post('/danh-sach-nhan-vien/them-nhan-vien', [AdminController::class, 'create']);
        Route::get('/danh-sach-nhan-vien/{id}', [AdminController::class, 'show']);
        Route::post('/danh-sach-nhan-vien/{id}', [AdminController::class, 'update']);
        Route::delete('/danh-sach-nhan-vien/{id}', [AdminController::class, 'destroy']);
        Route::post('/{id}', [AdminController::class, 'update']);
        Route::delete('/{id}', [AdminController::class, 'destroy']);
    });

    // Quyền Quản lý người dùng
    Route::middleware('check_permission:user_manage')->group(function () {
        Route::get('/nguoi-dungs/get-data', [AdminController::class, 'getNguoiDung']);
    });

    // Quyền Xem Báo cáo thống kê HOẶC Xem Dashboard
    Route::middleware('check_permission:report_view,dashboard_view')->group(function () {
        Route::get('/statistics', [AdminController::class, 'getStatistics']);
    });

    // Quản lý đánh giá hệ thống (Admin)
    Route::middleware('check_permission:report_view,dashboard_view')->group(function () {
        Route::get('/danh-gia-he-thong', [DanhGiaHeThongController::class, 'index']);
        Route::delete('/danh-gia-he-thong/{id}', [DanhGiaHeThongController::class, 'destroy']);
    });
});

// Chuc Vus Routes
Route::prefix('chuc-vus')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ChucVuController::class, 'index']);
    Route::post('/', [ChucVuController::class, 'store']);
    Route::get('/{chuc_vu}', [ChucVuController::class, 'show']);
    Route::put('/{chuc_vu}', [ChucVuController::class, 'update']);
    Route::delete('/{chuc_vu}', [ChucVuController::class, 'destroy']);
});

// Chuc Nangs Routes
Route::prefix('chuc-nangs')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ChucNangController::class, 'index']);
    Route::post('/', [ChucNangController::class, 'store']);
    Route::get('/{chuc_nang}', [ChucNangController::class, 'show']);
    Route::put('/{chuc_nang}', [ChucNangController::class, 'update']);
    Route::delete('/{chuc_nang}', [ChucNangController::class, 'destroy']);
});

// Phan Quyens Routes
Route::prefix('phan-quyens')->group(function () {
    Route::get('/', [PhanQuyenController::class, 'index']);
    Route::post('/', [PhanQuyenController::class, 'store']);
    Route::get('/{phan_quyen}', [PhanQuyenController::class, 'show']);
    Route::put('/{phan_quyen}', [PhanQuyenController::class, 'update']);
    Route::delete('/{phan_quyen}', [PhanQuyenController::class, 'destroy']);
});

// Danh Mucs Routes
Route::prefix('danh-mucs')->group(function () {
    Route::get('/', [DanhMucController::class, 'index']);
    Route::post('/', [DanhMucController::class, 'store']);
    Route::get('/{danh_muc}', [DanhMucController::class, 'show']);
    Route::put('/{danh_muc}', [DanhMucController::class, 'update']);
    Route::delete('/{danh_muc}', [DanhMucController::class, 'destroy']);
});

// Chi Tiet Danh Mucs Routes
Route::prefix('chi-tiet-danh-mucs')->group(function () {
    Route::get('/', [ChiTietDanhMucController::class, 'index']);
    Route::post('/', [ChiTietDanhMucController::class, 'store']);
    Route::get('/{chi_tiet_danh_muc}', [ChiTietDanhMucController::class, 'show']);
    Route::put('/{chi_tiet_danh_muc}', [ChiTietDanhMucController::class, 'update']);
    Route::delete('/{chi_tiet_danh_muc}', [ChiTietDanhMucController::class, 'destroy']);
});

// Chi Phi Phat Sinhs Routes
Route::prefix('chi-phi-phat-sinhs')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ChiPhiPhatSinhController::class, 'index']);
    Route::post('/', [ChiPhiPhatSinhController::class, 'store']);
    Route::get('/{chi_phi_phat_sinh}', [ChiPhiPhatSinhController::class, 'show']);
    Route::put('/{chi_phi_phat_sinh}', [ChiPhiPhatSinhController::class, 'update']);
    Route::delete('/{chi_phi_phat_sinh}', [ChiPhiPhatSinhController::class, 'destroy']);
});

// Địa điểm theo danh mục (phải đặt TRƯỚC prefix group)
Route::get('/dia-diems/am-thuc',  [DiaDiemController::class, 'getAmThuc']);
Route::get('/dia-diems/check-in', [DiaDiemController::class, 'getCheckIn']);
Route::get('/dia-diems/giai-tri', [DiaDiemController::class, 'getGiaiTri']);
Route::get('/dia-diems/tam-linh', [DiaDiemController::class, 'getTamLinh']);

Route::prefix('serp')->group(function () {
    Route::get('/account', [\App\Http\Controllers\Api\SerpApiController::class, 'accountInfo']);
    Route::post('/update-images', [\App\Http\Controllers\Api\SerpApiController::class, 'updateImages']);
    Route::get('/search', [\App\Http\Controllers\Api\SerpApiController::class, 'search']);
    Route::post('/import', [\App\Http\Controllers\Api\SerpApiController::class, 'import']);
    Route::post('/crawl-reviews', [\App\Http\Controllers\Api\SerpApiController::class, 'crawlReviews']);
    Route::post('/crawl-images', [\App\Http\Controllers\Api\SerpApiController::class, 'crawlImages']);
});

Route::prefix('dia-diems')->group(function () {
    Route::get('/', [DiaDiemController::class, 'index']);
    Route::post('/', [DiaDiemController::class, 'store']);
    Route::get('/get-detail/{id}', [DiaDiemController::class, 'show']);
    Route::get('/danh-gia/place/{id}', [\App\Http\Controllers\Api\DanhGiaController::class, 'getByPlace']);
    Route::put('/{dia_diem}', [DiaDiemController::class, 'update']);
    Route::delete('/{dia_diem}', [DiaDiemController::class, 'destroy']);
});

// Hinh Anh Dia Diems Routes
Route::prefix('hinh-anh-dia-diems')->group(function () {
    Route::get('/', [HinhAnhDiaDiemController::class, 'index']);
    Route::post('/', [HinhAnhDiaDiemController::class, 'store']);
    Route::get('/dia-diem/{id_dia_diem}', [HinhAnhDiaDiemController::class, 'getByDiaDiem']);
    Route::post('/{id}/set-main', [HinhAnhDiaDiemController::class, 'setMainImage']);
    Route::get('/{hinh_anh_dia_diem}', [HinhAnhDiaDiemController::class, 'show']);
    Route::put('/{hinh_anh_dia_diem}', [HinhAnhDiaDiemController::class, 'update']);
    Route::delete('/{hinh_anh_dia_diem}', [HinhAnhDiaDiemController::class, 'destroy']);
});

// Danh Gias Routes
Route::prefix('danh-gias')->group(function () {
    Route::get('/', [DanhGiaController::class, 'index']);
    Route::post('/', [DanhGiaController::class, 'store']);
    Route::get('/{danh_gia}', [DanhGiaController::class, 'show']);
    Route::put('/{danh_gia}', [DanhGiaController::class, 'update']);
    Route::delete('/{danh_gia}', [DanhGiaController::class, 'destroy']);
});

// So Thich Nguoi Dungs Routes
Route::prefix('so-thich-nguoi-dungs')->group(function () {
    Route::get('/', [SoThichNguoiDungController::class, 'index']);
    Route::post('/', [SoThichNguoiDungController::class, 'store']);
    Route::get('/{so_thich}', [SoThichNguoiDungController::class, 'show']);
    Route::put('/{so_thich}', [SoThichNguoiDungController::class, 'update']);
    Route::delete('/{so_thich}', [SoThichNguoiDungController::class, 'destroy']);
});

// Nhom Du Lichs Routes
Route::prefix('nhom-du-lichs')->group(function () {
    Route::get('/', [NhomDuLichController::class, 'index']);
    Route::post('/', [NhomDuLichController::class, 'store']);
    Route::get('/{nhom}', [NhomDuLichController::class, 'show']);
    Route::put('/{nhom}', [NhomDuLichController::class, 'update']);
    Route::delete('/{nhom}', [NhomDuLichController::class, 'destroy']);
});

// Chi Tiet Nhoms Routes
Route::prefix('chi-tiet-nhoms')->group(function () {
    Route::get('/', [ChiTietNhomController::class, 'index']);
    Route::post('/', [ChiTietNhomController::class, 'store']);
    Route::get('/{chi_tiet}', [ChiTietNhomController::class, 'show']);
    Route::put('/{chi_tiet}', [ChiTietNhomController::class, 'update']);
    Route::delete('/{chi_tiet}', [ChiTietNhomController::class, 'destroy']);
});

// Nhom Chats Routes
Route::prefix('nhom-chats')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [NhomChatController::class, 'index']);
    Route::post('/', [NhomChatController::class, 'store']);
    Route::get('/{chat}', [NhomChatController::class, 'show']);
    Route::put('/{chat}', [NhomChatController::class, 'update']);
    Route::delete('/{chat}', [NhomChatController::class, 'destroy']);
});

// Chuyen Dis Routes
Route::prefix('chuyen-dis')->group(function () {
    Route::get('/', [ChuyenDiController::class, 'index']);
    Route::post('/', [ChuyenDiController::class, 'store']);
    Route::get('/{chuyen_di}', [ChuyenDiController::class, 'show']);
    Route::get('/{id}/chi-phis', [ChiPhiPhatSinhController::class, 'getByChuyenDi']);
    Route::put('/{chuyen_di}', [ChuyenDiController::class, 'update']);
    Route::delete('/{chuyen_di}', [ChuyenDiController::class, 'destroy']);
});

// Lich Trinh Dia Diems Routes
Route::prefix('lich-trinh-dia-diems')->group(function () {
    Route::get('/', [LichTrinhDiaDiemController::class, 'index']);
    Route::post('/', [LichTrinhDiaDiemController::class, 'store']);
    Route::post('/reorder', [LichTrinhDiaDiemController::class, 'reorder']);
    Route::post('/{id}/swap', [LichTrinhDiaDiemController::class, 'swapDiaDiem']);
    Route::get('/{lich_trinh}', [LichTrinhDiaDiemController::class, 'show']);
    Route::put('/{lich_trinh}', [LichTrinhDiaDiemController::class, 'update']);
    Route::delete('/{lich_trinh}', [LichTrinhDiaDiemController::class, 'destroy']);
});

// Lấy danh sách địa điểm (kèm tọa độ) theo chuyến đi
Route::get('/chuyen-di/{id}/dia-diems', [LichTrinhDiaDiemController::class, 'getDiaDiemByChuyenDi']);

// Người dùng Routes
Route::prefix('admin/nguoi-dungs')->group(function () {
    Route::get('get-data/', [NguoiDungController::class, 'getData']);
    Route::post('/create', [NguoiDungController::class, 'create']);
    Route::post('/{nguoi_dung}', [NguoiDungController::class, 'update']);
    Route::delete('/{nguoi_dung}', [NguoiDungController::class, 'destroy']);
});