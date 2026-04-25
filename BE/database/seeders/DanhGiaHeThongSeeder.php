<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DanhGiaHeThong;
use App\Models\NguoiDung;
use Carbon\Carbon;

class DanhGiaHeThongSeeder extends Seeder
{
    public function run(): void
    {
        // Xoá dữ liệu cũ (nếu chạy lại)
        DanhGiaHeThong::truncate();

        // Nội dung đóng góp mẫu theo từng mức độ
        $noiDungTheoMuc = [
            1 => [
                'Giao diện quá phức tạp, khó sử dụng.',
                'Hệ thống gặp lỗi khi tôi tạo lịch trình.',
                'AI gợi ý không phù hợp với nhu cầu của tôi.',
                'Tốc độ tải chậm, mất nhiều thời gian chờ.',
                null,
            ],
            2 => [
                'Giao diện tạm được nhưng còn nhiều điểm cần cải thiện.',
                'Một số tính năng chưa hoạt động ổn định.',
                'Cần thêm nhiều địa điểm hơn để lựa chọn.',
                'Kết quả AI đôi khi không khớp với ngân sách đã chọn.',
                null,
            ],
            3 => [
                'Ứng dụng bình thường, dùng được nhưng chưa thực sự ấn tượng.',
                'Tính năng lập lịch tự động cần chính xác hơn.',
                'Giao diện khá ổn, mong có thêm bộ lọc địa điểm.',
                'Cần cải thiện tốc độ tải trang.',
                null,
            ],
            4 => [
                'Rất hài lòng với tính năng lập lịch tự động!',
                'AI gợi ý lịch trình hợp lý, tiết kiệm thời gian lên kế hoạch.',
                'Giao diện đẹp, dễ sử dụng. Sẽ giới thiệu cho bạn bè.',
                'Tính năng chia sẻ nhóm du lịch rất hay!',
                'Hệ thống hoạt động ổn định, trải nghiệm tốt.',
                null,
            ],
            5 => [
                'Tuyệt vời! Hệ thống giúp tôi lên kế hoạch du lịch cực kỳ nhanh chóng.',
                'AI gợi ý rất thông minh, phù hợp đúng sở thích và ngân sách của tôi!',
                'Chưa thấy app nào lập lịch du lịch hay như thế này. 5 sao xứng đáng!',
                'Giao diện hiện đại, mượt mà. Tính năng bản đồ rất tiện lợi!',
                'Ứng dụng du lịch tốt nhất tôi từng dùng. Cảm ơn đội ngũ phát triển!',
                'Sẽ dùng mỗi khi đi du lịch. Rất đáng tin cậy và tiện ích!',
                null,
            ],
        ];

        // Phân bố mức độ thực tế: thiên về 4-5 sao
        $distribution = [
            1 => 5,   //  5 đánh giá mức 1
            2 => 8,   //  8 đánh giá mức 2
            3 => 15,  // 15 đánh giá mức 3
            4 => 30,  // 30 đánh giá mức 4
            5 => 42,  // 42 đánh giá mức 5
        ];

        $ips = [
            '192.168.1.10', '192.168.1.25', '10.0.0.5',
            '172.16.0.3',   '192.168.0.50', '203.113.152.10',
            '14.225.0.80',  '27.72.98.15',  '113.161.44.2',
            '118.69.215.5',
        ];

        foreach ($distribution as $mucDo => $soLuong) {
            for ($i = 0; $i < $soLuong; $i++) {
                // Ngẫu nhiên trong 60 ngày gần đây, trải đều
                $ngayTao = Carbon::now()->subDays(rand(0, 60))->subHours(rand(0, 23))->subMinutes(rand(0, 59));

                // Ngẫu nhiên chọn nội dung, có thể null
                $noiDung = $noiDungTheoMuc[$mucDo][array_rand($noiDungTheoMuc[$mucDo])];

                DanhGiaHeThong::create([
                    'muc_do_hai_long' => $mucDo,
                    'noi_dung'        => $noiDung,
                    'ip_address'      => $ips[array_rand($ips)],
                    'created_at'      => $ngayTao,
                    'updated_at'      => $ngayTao,
                ]);
            }
        }

        $tong = array_sum($distribution);
        $this->command->info("✅ Đã tạo {$tong} đánh giá hệ thống mẫu.");
    }
}
