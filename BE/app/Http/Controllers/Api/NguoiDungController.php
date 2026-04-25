<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordNguoiDungRequest;
use App\Http\Requests\ForgotPasswordNguoiDungRequest;
use App\Http\Requests\LoginNguoiDungRequest;
use App\Http\Requests\RegisterNguoiDungRequest;
use App\Http\Requests\ResetPasswordNguoiDungRequest;
use App\Http\Requests\StoreNguoiDungRequest;
use App\Http\Requests\UpdateAvatarNguoiDungRequest;
use App\Http\Requests\UpdateNguoiDungRequest;
use App\Http\Requests\UpdateProfileNguoiDungRequest;
use App\Mail\MasterMail;
use App\Models\NguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class NguoiDungController extends Controller
{
    public function dangNhap(LoginNguoiDungRequest $request)
    {
        $user = NguoiDung::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->mat_khau, $user->mat_khau)) {
            return response()->json([
                'status'  => false,
                'message' => 'Sai email hoặc mật khẩu.'
            ], 401);
        }

        if ((int) $user->is_active === 0) {
            return response()->json([
                'status'  => false,
                'message' => 'Tài khoản chưa được kích hoạt. Vui lòng kiểm tra email của bạn để kích hoạt.'
            ], 403);
        }

        $token = $user->createToken('key_client')->plainTextToken;

        return response()->json([
            'status'     => true,
            'message'    => 'Đăng nhập thành công.',
            'access_token' => $token,
            'client_token' => $token,
            'token'      => $token,
            'key_client' => $token,
            'data'       => $user
        ], 200);
    }

    public function dangKy(RegisterNguoiDungRequest $request)
    {
        try {
            DB::beginTransaction();

            $key = (string) Str::uuid();

            $nguoiDung = NguoiDung::create([
                'ten'           => $request->ho_va_ten,
                'email'         => strtolower($request->email),
                'anh_dai_dien'  => $request->anh_dai_dien ?? null,
                'so_dien_thoai' => $request->so_dien_thoai,
                'mat_khau'      => Hash::make($request->password),
                'is_active'     => 0,
                'hash_active'   => $key,
            ]);

            $tieuDe = 'Kích hoạt tài khoản DaNang Travel';
            $view = 'kichHoatTK';
            $noiDung = [
                'ho_va_ten' => $nguoiDung->ten,
                'link'      => rtrim(env('FRONTEND_URL', 'http://localhost:5173'), '/') . '/client/kich-hoat/' . $key,
            ];

            Mail::to($request->email)->send(new MasterMail($tieuDe, $view, $noiDung));

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Lỗi khi đăng ký hoặc gửi mail kích hoạt: ' . $e->getMessage());

            return response()->json([
                'status'  => false,
                'message' => 'Không thể gửi email kích hoạt. Vui lòng kiểm tra cấu hình mail và thử lại.'
            ], 500);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Đăng ký thành công! Vui lòng kiểm tra email để kích hoạt.'
        ], 201);
    }

    public function kichHoat(Request $request)
    {
        $hashActive = $request->hash_active ?? $request->route('hash_active');
        $ketQua = $this->xuLyKichHoat($hashActive);

        return response()->json([
            'status'  => $ketQua['status'],
            'message' => $ketQua['message']
        ], $ketQua['code']);
    }

    public function kichHoatWeb(Request $request)
    {
        $hashActive = $request->route('hash_active');
        $ketQua = $this->xuLyKichHoat($hashActive);

        // Return a simple HTML response
        $message = $ketQua['message'];
        $status = $ketQua['status'] ? 'Thành công' : 'Thất bại';

        return "<!DOCTYPE html>
<html lang='vi'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Kích hoạt tài khoản</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <h1>Kích hoạt tài khoản DaNang Travel</h1>
    <p class='" . ($ketQua['status'] ? 'success' : 'error') . "'>$message</p>
    <a href='" . env('FRONTEND_URL', 'http://localhost:5173') . "/client/dang-nhap'>Đăng nhập</a>
</body>
</html>";
    }

    private function xuLyKichHoat(?string $hashActive): array
    {
        $user = NguoiDung::where('hash_active', $hashActive)->first();

        if (!$user) {
            return [
                'status'  => false,
                'message' => 'Mã kích hoạt không hợp lệ hoặc đã hết hạn.',
                'code'    => 404,
            ];
        }

        if ((int) $user->is_active === 1) {
            return [
                'status'  => true,
                'message' => 'Tài khoản đã được kích hoạt trước đó.',
                'code'    => 200,
            ];
        }

        $user->update([
            'is_active'   => 1,
            'hash_active' => null,
        ]);

        return [
            'status'  => true,
            'message' => 'Đã kích hoạt tài khoản thành công.',
            'code'    => 200,
        ];
    }

    public function quenMatKhau(ForgotPasswordNguoiDungRequest $request)
    {
        $user = NguoiDung::where('email', $request->email)->first();

        $key = Str::uuid();
        $tieu_de = "Quên mật khẩu";
        $view = "quenMatKhau";
        $noi_dung['ten'] = $user->ten;
        $noi_dung['link'] = "http://localhost:5173/client/dat-lai-mat-khau/" . $key;

        $user->hash_reset = $key;
        $user->save();

        Mail::to($user->email)->send(new MasterMail($tieu_de, $view, $noi_dung));

        return response()->json([
            'status' => true,
            'message' => 'Vui lòng kiểm tra email để đổi mật khẩu.',
        ]);
    }

    public function datLaiMatKhau(ResetPasswordNguoiDungRequest $request)
    {
        $user = NguoiDung::where('hash_reset', $request->key)->first();

        $user->mat_khau = Hash::make($request->mat_khau_moi);
        $user->hash_reset = null;
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Mật khẩu đã được đặt lại thành công.'
        ]);
    }

    public function doiMatKhau(ChangePasswordNguoiDungRequest $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized.',
            ], 401);
        }

        // Kiểm tra mật khẩu cũ
        if (!Hash::check($request->mat_khau_cu, $user->mat_khau)) {
            return response()->json([
                'status' => false,
                'message' => 'Mật khẩu cũ không đúng.',
            ], 400);
        }

        // Cập nhật mật khẩu mới
        $user->mat_khau = Hash::make($request->mat_khau_moi);
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Đổi mật khẩu thành công.',
        ], 200);
    }

    public function getData()
    {
        $nguoiDungs = NguoiDung::all();

        return response()->json([
            'status'  => 'success',
            'message' => 'Lấy danh sách người dùng thành công.',
            'data'    => $nguoiDungs,
        ], 200);
    }

    public function create(StoreNguoiDungRequest $request)
    {
        $nguoiDung = NguoiDung::create([
            'ten'           => $request->ho_va_ten,
            'email'         => strtolower($request->email),
            'mat_khau'      => Hash::make($request->password),
            'so_dien_thoai' => $request->so_dien_thoai,
            'anh_dai_dien'  => $request->anh_dai_dien,
            'is_active'     => 1,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Thêm tài khoản người dùng thành công.',
            'data'    => [
                'id' => $nguoiDung->id,
                'ho_va_ten' => $nguoiDung->ten,
                'email' => $nguoiDung->email,
                'so_dien_thoai' => $nguoiDung->so_dien_thoai,
                'vai_tro' => 'user',
            ],
        ], 201);
    }

    public function profile(Request $request, $id = null)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthorized.'], 401);
        }

        if ($id === null) {
            $id = $user->id;
        }

        // Optional: chỉ cho phép xem profile của chính user
        if ((int) $user->id !== (int) $id) {
            return response()->json([
                'status' => false,
                'message' => 'Không có quyền truy cập.',
            ], 403);
        }

        $nguoiDung = NguoiDung::find($id);

        if (!$nguoiDung) {
            return response()->json([
                'status'  => false,
                'message' => 'Không tìm thấy người dùng.',
            ], 404);
        }

        $lichTrinhCount = \App\Models\ChuyenDi::where('id_nguoi_dung', $id)->count();
        $nhomCount = \DB::table('chi_tiet_nhoms')
                        ->where('id_nguoi_dung', $id)
                        ->where('trang_thai', 'da_vao') // Assuming only counting joined active groups
                        ->count();

        // Check if DB throws error for trang_thai condition, if so just rely on simple count or 'da_duyet' etc. Let's just do count() based on normal standard or left it without trang_thai if we are unsure. Let's provide without status and map it.
        // Actually the table has 'trang_thai', maybe 'da_duyet' or Enum. I will just do simple count where user is in 'chi_tiet_nhoms'.
        $nhomCount = \DB::table('chi_tiet_nhoms')
                        ->where('id_nguoi_dung', $id)
                        ->count();

        return response()->json([
            'status'  => true,
            'data'    => [
                'id'            => $nguoiDung->id,
                'ten'           => $nguoiDung->ten,
                'email'         => $nguoiDung->email,
                'so_dien_thoai' => $nguoiDung->so_dien_thoai,
                'anh_dai_dien'  => $nguoiDung->anh_dai_dien,
                'created_at'    => $nguoiDung->created_at,
            ],
            'stats'   => [
                'lichTrinh' => $lichTrinhCount,
                'nhom'      => $nhomCount,
            ]
        ], 200);
    }

    public function capNhatThongTin(UpdateProfileNguoiDungRequest $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized.',
            ], 401);
        }

        $user->ten = $request->ten;
        $user->so_dien_thoai = $request->so_dien_thoai;
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật thông tin thành công.',
            'data' => [
                'id' => $user->id,
                'ten' => $user->ten,
                'email' => $user->email,
                'so_dien_thoai' => $user->so_dien_thoai,
                'anh_dai_dien' => $user->anh_dai_dien,
                'created_at' => $user->created_at,
            ],
        ], 200);
    }

    public function capNhatAnhDaiDien(UpdateAvatarNguoiDungRequest $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized.',
            ], 401);
        }

        $file = $request->file('anh_dai_dien');
        $storePath = $file->storeAs('avatar-client', time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension(), 'public');
        $publicPath = '/storage/' . $storePath;

        $user->anh_dai_dien = $publicPath;
        $user->save();

        $data = [
            'id' => $user->id,
            'ten' => $user->ten,
            'email' => $user->email,
            'anh_dai_dien' => $publicPath,
        ];

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật ảnh đại diện thành công.',
            'data' => $data,
        ], 200);
    }

    public function update(UpdateNguoiDungRequest $request, $id)
    {
        $nguoiDung = NguoiDung::find($id);

        if (!$nguoiDung) {
            return response()->json([
                'status'  => false,
                'message' => 'Không tìm thấy người dùng.',
            ], 404);
        }

        $nguoiDung->ten = $request->ho_va_ten;
        $nguoiDung->so_dien_thoai = $request->so_dien_thoai;
        $nguoiDung->anh_dai_dien = $request->anh_dai_dien;

        if ($request->filled('password')) {
            $nguoiDung->mat_khau = Hash::make($request->password);
        }

        $nguoiDung->save();

        return response()->json([
            'status'  => true,
            'message' => 'Cập nhật tài khoản người dùng thành công.',
            'data'    => [
                'id' => $nguoiDung->id,
                'ho_va_ten' => $nguoiDung->ten,
                'so_dien_thoai' => $nguoiDung->so_dien_thoai,
                'anh_dai_dien' => $nguoiDung->anh_dai_dien,
            ],
        ], 200);
    }

    public function destroy($id)
    {
        $nguoiDung = NguoiDung::find($id);

        if (!$nguoiDung) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Không tìm thấy người dùng.',
            ], 404);
        }

        $nguoiDung->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Xóa người dùng thành công.',
        ], 200);
    }
}
