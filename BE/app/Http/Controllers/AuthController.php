<?php

namespace App\Http\Controllers;

use App\Models\NguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
    
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

        $email = strtolower($googleUser->getEmail());

        $user = NguoiDung::where('email', $email)->first();

        if (!$user) {
            $user = NguoiDung::create([
                'ten' => $googleUser->getName(),
                'email' => $email,
                'anh_dai_dien' => $googleUser->getAvatar(),
                'mat_khau' => Hash::make(uniqid('', true)),
                'is_active' => 1,
            ]);
        } else {
            if (!$user->anh_dai_dien) {
                $user->anh_dai_dien = $googleUser->getAvatar();
                $user->save();
            }
        }

        $token = $user->createToken('google_auth')->plainTextToken;

        $frontend = env('FRONTEND_URL', 'http://localhost:5173');

        return redirect(
            $frontend . '/auth/callback?token=' . $token .
            '&user=' . urlencode(json_encode([
                'id' => $user->id,
                'ten' => $user->ten,
                'email' => $user->email,
                'anh_dai_dien' => $user->anh_dai_dien,
            ]))
        );

    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Google Login Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        $frontend = env('FRONTEND_URL', 'http://localhost:5173');

        return redirect(
            $frontend . '/auth/error?message=' . urlencode('Đăng nhập Google thất bại: ' . $e->getMessage())
        );
    }
}
}
