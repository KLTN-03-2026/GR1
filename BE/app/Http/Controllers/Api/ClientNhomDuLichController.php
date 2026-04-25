<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChiTietNhom;
use App\Models\NguoiDung;
use App\Models\NhomDuLich;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientNhomDuLichController extends Controller
{
    private function getCurrentUserId(): int
    {
        $userId = auth('sanctum')->id();

        if (! $userId) {
            throw new AuthenticationException('Unauthenticated.');
        }

        return (int) $userId;
    }

    public function getJoined()
    {
        $userId = $this->getCurrentUserId();

        $groups = DB::table('nhom_du_lichs as n')
            ->join('chi_tiet_nhoms as c', 'n.id', '=', 'c.id_nhom_du_lich')
            ->where('c.id_nguoi_dung', $userId)
            ->where('c.trang_thai', 1)
            ->where('c.vai_tro', '!=', 'truong_nhom')
            ->select(
                'n.id',
                'n.ten_nhom',
                'n.mo_ta',
                'n.id_chuyen_di',
                'n.nguoi_tao',
                'c.id as id_chi_tiet_nhom'
            )
            ->get()
            ->map(function ($g) {
                $g->so_thanh_vien = ChiTietNhom::where('id_nhom_du_lich', $g->id)
                    ->where('trang_thai', 1)
                    ->count();
                $g->la_truong_nhom = false;
                return $g;
            });

        return response()->json(['status' => true, 'data' => $groups]);
    }

    public function getMyGroups()
    {
        $userId = $this->getCurrentUserId();

        $groups = DB::table('nhom_du_lichs as n')
            ->join('chi_tiet_nhoms as c', 'n.id', '=', 'c.id_nhom_du_lich')
            ->where('c.id_nguoi_dung', $userId)
            ->where('c.vai_tro', 'truong_nhom')
            ->where('c.trang_thai', 1)
            ->select(
                'n.id',
                'n.ten_nhom',
                'n.mo_ta',
                'n.id_chuyen_di',
                'n.nguoi_tao',
                'c.id as id_chi_tiet_nhom'
            )
            ->get()
            ->map(function ($g) {
                $g->so_thanh_vien = ChiTietNhom::where('id_nhom_du_lich', $g->id)
                    ->where('trang_thai', 1)
                    ->count();
                $g->la_truong_nhom = true;
                return $g;
            });

        return response()->json(['status' => true, 'data' => $groups]);
    }

    public function getInvites()
    {
        $userId = $this->getCurrentUserId();

        $invites = DB::table('chi_tiet_nhoms as c')
            ->join('nhom_du_lichs as n', 'c.id_nhom_du_lich', '=', 'n.id')
            ->where('c.id_nguoi_dung', $userId)
            ->where('c.trang_thai', 0)
            ->select(
                'c.id as id_thanh_vien',
                'n.id as id_nhom',
                'n.ten_nhom',
                'n.mo_ta',
                'n.nguoi_tao'
            )
            ->get();

        return response()->json(['status' => true, 'data' => $invites]);
    }

    public function getMembers($id)
    {
        $members = DB::table('chi_tiet_nhoms as c')
            ->join('nguoi_dungs as u', 'c.id_nguoi_dung', '=', 'u.id')
            ->where('c.id_nhom_du_lich', $id)
            ->where('c.trang_thai', '!=', -1) // Exclude left members
            ->select(
                'c.id as id_thanh_vien',
                'u.id as id_nguoi_dung',
                'u.ten',
                'u.email',
                'u.anh_dai_dien',
                'c.vai_tro',
                'c.trang_thai'
            )
            ->get();

        return response()->json(['status' => true, 'data' => $members]);
    }

    public function getGroup($id)
    {
        $group = NhomDuLich::find($id);

        if (! $group) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy nhóm du lịch.',
            ], 404);
        }

        $group->so_thanh_vien = ChiTietNhom::where('id_nhom_du_lich', $id)
            ->where('trang_thai', 1)
            ->count();

        return response()->json(['status' => true, 'data' => $group]);
    }

    public function createGroup(Request $request)
    {
        $userId = $this->getCurrentUserId();
        $user = NguoiDung::find($userId);

        $nhom = NhomDuLich::create([
            'id_tao_nhom' => $userId,
            'ten_nhom' => $request->ten_nhom,
            'mo_ta' => $request->mo_ta,
            'id_chuyen_di' => $request->id_chuyen_di,
            'nguoi_tao' => $user ? $user->ten : 'Nguoi dung',
        ]);

        $chiTietNhom = ChiTietNhom::create([
            'id_nguoi_dung' => $userId,
            'id_nhom_du_lich' => $nhom->id,
            'vai_tro' => 'truong_nhom',
            'trang_thai' => 1,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Tạo nhóm thành công.',
            'data' => [
                'id' => $nhom->id,
                'ten_nhom' => $nhom->ten_nhom,
                'id_chi_tiet_nhom' => $chiTietNhom->id,
            ],
        ]);
    }

    public function inviteMember(Request $request)
    {
        $idNhom = $request->id_nhom;
        $email = $request->email;

        $nguoiDung = NguoiDung::where('email', $email)->first();

        if (! $nguoiDung) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy người dùng có email này.',
            ]);
        }

        $exists = ChiTietNhom::where('id_nguoi_dung', $nguoiDung->id)
            ->where('id_nhom_du_lich', $idNhom)
            ->first();

        if ($exists) {
            if ($exists->trang_thai == -1) {
                // Was left, re-invite
                $exists->update(['trang_thai' => 0, 'vai_tro' => 'thanh_vien']);
                return response()->json([
                    'status' => true,
                    'message' => 'Đã gửi lại lời mời thành công.',
                ]);
            }
            return response()->json([
                'status' => false,
                'message' => 'Người dùng đã có trong nhóm hoặc đã được mời.',
            ]);
        }

        ChiTietNhom::create([
            'id_nguoi_dung' => $nguoiDung->id,
            'id_nhom_du_lich' => $idNhom,
            'vai_tro' => 'thanh_vien',
            'trang_thai' => 0,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Đã gửi lời mời thành công.',
        ]);
    }

    public function acceptInvite(Request $request)
    {
        $userId = $this->getCurrentUserId();
        $idThanhVien = $request->id_thanh_vien;
        $chapNhan = $request->boolean('chap_nhan');

        $chiTiet = ChiTietNhom::find($idThanhVien);
        if (! $chiTiet) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy lời mời.',
            ]);
        }

        if ((int) $chiTiet->id_nguoi_dung !== $userId) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn không có quyền xử lý lời mời này.',
            ], 403);
        }

        if ((int) $chiTiet->trang_thai !== 0) {
            return response()->json([
                'status' => false,
                'message' => 'Lời mời này đã được xử lý từ trước.',
            ], 422);
        }

        $chiTiet->trang_thai = $chapNhan ? 1 : 2;
        $chiTiet->save();

        return response()->json([
            'status' => true,
            'message' => $chapNhan ? 'Đã tham gia nhóm!' : 'Đã từ chối lời mời.',
        ]);
    }

    public function removeMember(Request $request)
    {
        $idNhom = $request->id_nhom;
        $idNguoiDung = $request->id_nguoi_dung;

        ChiTietNhom::where('id_nhom_du_lich', $idNhom)
            ->where('id_nguoi_dung', $idNguoiDung)
            ->update(['trang_thai' => -1]); // Set status to -1 (left/removed)

        return response()->json([
            'status' => true,
            'message' => 'Đã xoá người dùng khỏi nhóm.',
        ]);
    }

    public function leaveGroup(Request $request)
    {
        $idNhom = $request->id_nhom;
        $userId = $this->getCurrentUserId();

        ChiTietNhom::where('id_nhom_du_lich', $idNhom)
            ->where('id_nguoi_dung', $userId)
            ->update(['trang_thai' => -1]); // Set status to -1 (left/removed)

        return response()->json([
            'status' => true,
            'message' => 'Đã rời nhóm thành công.',
        ]);
    }

    public function deleteGroup(Request $request)
    {
        $idNhom = $request->id;

        \App\Models\NhomChat::where('id_nhom_du_lich', $idNhom)->delete();
        ChiTietNhom::where('id_nhom_du_lich', $idNhom)->delete();
        NhomDuLich::where('id', $idNhom)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Đã giải tán nhóm thành công.',
        ]);
    }
}
