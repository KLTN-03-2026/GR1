<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$permissionCodes): Response
    {
        $admin = $request->user();

        if (!$admin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vui lòng đăng nhập để tiếp tục.'
            ], 401);
        }

        // Tự động cho phép nếu là Super Admin (id_chuc_vu = 1)
        if ($admin->id_chuc_vu === 1) {
            return $next($request);
        }

        // Kiểm tra xem user có ít nhất 1 trong các quyền yêu cầu không
        $hasAccess = false;
        foreach ($permissionCodes as $code) {
            if ($admin->hasPermission($code)) {
                $hasAccess = true;
                break;
            }
        }

        // Nếu k có quyền -> Báo lỗi 403
        if (!$hasAccess) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bạn không có quyền thực hiện chức năng này.'
            ], 403);
        }

        return $next($request);
    }
}
