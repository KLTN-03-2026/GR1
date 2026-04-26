<?php

namespace App\Http\Controllers\Api;

use App\Events\NhomChatMessageSent;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNhomChatRequest;
use App\Http\Requests\UpdateNhomChatRequest;
use Illuminate\Support\Facades\Log;
use App\Models\ChiTietNhom;
use App\Models\NhomChat;
use Illuminate\Http\Request;

class NhomChatController extends Controller
{
    public function index(Request $request)

    {
        $query = NhomChat::query()
            ->with('chiTietNhom.nguoiDung')
            ->orderBy('created_at');

        if ($request->filled('id_nhom_du_lich')) {
            $query->where('id_nhom_du_lich', $request->integer('id_nhom_du_lich'));
        }

        $chats = $query->get()->map(fn (NhomChat $chat) => $this->transformChat($chat));

        return response()->json([
            'status' => true,
            'data' => $chats,
        ], 200);

    }

    public function store(StoreNhomChatRequest $request)
    {
        $user = $request->user();

        $chiTietNhom = ChiTietNhom::query()
            ->with('nguoiDung')
            ->where('id', $request->integer('id_chi_tiet_nhom'))
            ->where('id_nhom_du_lich', $request->integer('id_nhom_du_lich'))
            ->first();

        if (! $chiTietNhom) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy chi tiết nhóm hợp lệ.',
            ], 404);
        }

        if ((int) $chiTietNhom->id_nguoi_dung !== (int) $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn không có quyền gửi tin nhắn.',
            ], 403);
        }

        if ((int) $chiTietNhom->trang_thai !== 1) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn chưa tham gia nhóm này.',

            ], 403);
        }

        $message = (string) $request->input('message');
        
        // Tự động gắn id_nhom_du_lich cho chuyến đi nếu đây là tin nhắn share lịch trình
        try {
            $msgData = json_decode($message, true);
            if ($msgData && isset($msgData['type']) && $msgData['type'] === 'itinerary' && isset($msgData['id'])) {
                $chuyenDi = \App\Models\ChuyenDi::find($msgData['id']);
                if ($chuyenDi && !$chuyenDi->id_nhom_du_lich) {
                    $chuyenDi->id_nhom_du_lich = $request->integer('id_nhom_du_lich');
                    $chuyenDi->save();
                }
            }
        } catch (\Exception $e) {
            // Bỏ qua lỗi parse JSON nếu tin nhắn là text thường
        }

        $nhomChat = NhomChat::create([
            'id_nhom_du_lich' => $request->integer('id_nhom_du_lich'),
            'id_chi_tiet_nhom' => $request->integer('id_chi_tiet_nhom'),
            'message' => $message,
        ]);

        $nhomChat->setRelation('chiTietNhom', $chiTietNhom);
        $chat = $this->transformChat($nhomChat);

        Log::info('Broadcasting chat message', [
            'chat_id' => $chat['id'] ?? $chat['id_tin_nhan'],
            'group_id' => $chat['id_nhom_du_lich'],
            'user_id' => $chat['id_chi_tiet_nhom'],
            'message' => $chat['message']
        ]);

        broadcast(new NhomChatMessageSent($chat))->toOthers();

        return response()->json([
            'status' => true,
            'chat' => $chat,
        ], 200);
    }

    public function show($id)
    {
        $nhomChat = NhomChat::with('chiTietNhom.nguoiDung')->find($id);

        if (! $nhomChat) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy tin nhắn.',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'chat' => $this->transformChat($nhomChat),
        ], 200);
    }

    public function update(UpdateNhomChatRequest $request, $id)
    {
        $nhomChat = NhomChat::with('chiTietNhom.nguoiDung')->find($id);

        if (! $nhomChat) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy tin nhắn.',
            ], 404);
        }

        $nhomChat->fill($request->only(['message']))->save();

        return response()->json([
            'status' => true,
            'chat' => $this->transformChat($nhomChat),
        ], 200);
    }

    public function destroy($id)
    {
        $nhomChat = NhomChat::find($id);

        if (! $nhomChat) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy tin nhắn.',
            ], 404);
        }

        $nhomChat->delete();

        return response()->json([
            'status' => true,
        ], 200);
    }

    private function transformChat(NhomChat $nhomChat): array
    {
        $nhomChat->loadMissing('chiTietNhom.nguoiDung');

        $chiTietNhom = $nhomChat->chiTietNhom;
        $nguoiDung = optional($chiTietNhom)->nguoiDung;

        return [
            'id' => (int) $nhomChat->id,
            'id_nhom_du_lich' => (int) $nhomChat->id_nhom_du_lich,
            'id_chi_tiet_nhom' => (int) $nhomChat->id_chi_tiet_nhom,
            'message' => (string) $nhomChat->message,
            'id_nguoi_gui' => (int) optional($chiTietNhom)->id_nguoi_dung,
            'ten_nguoi_gui' => (string) ($nguoiDung?->ten ?? ''),
            'anh_dai_dien' => (string) ($nguoiDung?->anh_dai_dien ?? ''),
            'created_at' => optional($nhomChat->created_at)?->toISOString() ?? '',
        ];
    }
}
