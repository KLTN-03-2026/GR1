<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DiaDiem;
use App\Models\HinhAnhDiaDiem;
use App\Models\DanhGia;
use Faker\Factory as Faker;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('vi_VN');
        $diaDiems = DiaDiem::all();

        // Một số nội dung review mẫu chân thật
        $reviewContents = [
            'Chỗ này không gian tuyệt vời, rất đáng để trải nghiệm nhé mọi người!',
            'Đồ ăn ngon, phục vụ nhiệt tình nhanh nhẹn, 10 điểm cho chất lượng.',
            'Mình đi vào cuối tuần khá đông nhưng bù lại view đẹp, đáng tiền.',
            'Trải nghiệm tuyệt vời cùng gia đình, không khí mát mẻ, dễ chịu.',
            'Giá cả hợp lý so với dịch vụ nhận được. Rất hài lòng.',
            'Lên hình sống ảo cực chất, checkin không góc chết luôn!',
            'Quán sạch sẽ, chu đáo, có bãi để xe rộng rãi.',
            'Lần đầu đến Đà Nẵng ghé thử và thấy rất thích nơi này.'
        ];

        foreach ($diaDiems as $diaDiem) {
            // --- 1. Tạo thêm Hình Ảnh cho đủ 5 hình ---
            $imageCount = HinhAnhDiaDiem::where('id_dia_diem', $diaDiem->id)->count();
            
            for ($i = $imageCount; $i < 5; $i++) {
                // Sinh ngẫu nhiên keyword từ tên địa điểm để ảnh liên quan xíu
                HinhAnhDiaDiem::create([
                    'id_dia_diem'   => $diaDiem->id,
                    'duong_dan_anh' => 'https://picsum.photos/seed/' . rand(1, 100000) . '/800/600',
                    'is_main'       => false,
                    'sort_order'    => $i + 1
                ]);
            }

            // --- 2. Tạo thêm Đánh Giá cho đủ 5 người ---
            $reviewCount = DanhGia::where('id_dia_diem', $diaDiem->id)->count();

            for ($j = $reviewCount; $j < 5; $j++) {
                DanhGia::create([
                    'id_nguoi_dung'         => null,
                    'id_dia_diem'           => $diaDiem->id,
                    'so_sao'               => rand(4, 5),
                    'noi_dung'             => $faker->randomElement($reviewContents),
                    'ten_nguoi_danh_gia'   => $faker->name,
                    'avatar_nguoi_danh_gia'=> 'https://i.pravatar.cc/150?img=' . rand(1, 70),
                    'la_danh_gia_google'   => true, // giả vờ là đánh giá google để hiện tên và avatar
                ]);
            }
        }
    }
}
