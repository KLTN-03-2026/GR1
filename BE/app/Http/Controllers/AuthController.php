<?php

namespace App\Http\Controllers;

use App\Models\NguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
class AuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Normalize email to lowercase để tránh duplicate
            $email = strtolower($googleUser->getEmail());

            // Tìm user theo email
            $user = NguoiDung::where('email', $email)->first();

            if (!$user) {
                // Tạo user mới nếu chưa tồn tại
                $user = NguoiDung::create([
                    'ten' => $googleUser->getName(),
                    'email' => $email,
                    'anh_dai_dien' => $googleUser->getAvatar(),
                    'mat_khau' => Hash::make(uniqid('', true)),
                    'is_active' => 1,
                ]);
            } else {
                // Nếu user đã tồn tại, cập nhật ảnh đại diện từ Google nếu chưa có
                if (!$user->anh_dai_dien) {
                    $user->anh_dai_dien = $googleUser->getAvatar();
                    $user->save();
                }
            }

            $token = $user->createToken('google_auth')->plainTextToken;

            return redirect(env('FRONTEND_URL', 'http://localhost:5173') . '/auth/callback?token=' . $token . '&user=' . urlencode(json_encode($user)));
        } catch (\Exception $e) {
            return redirect(env('FRONTEND_URL', 'http://localhost:5173') . '/auth/error?message=' . urlencode('Đăng nhập Google thất bại'));
        }
    }
}
