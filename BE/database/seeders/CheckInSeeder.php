<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DiaDiem;
use App\Models\DanhMuc;
use App\Models\ChiTietDanhMuc;

class CheckInSeeder extends Seeder
{
    public function run(): void
    {
        $danhMucCheckin = DanhMuc::firstOrCreate(
            ['ten_danh_muc' => 'Check-in'],
            ['mo_ta' => 'Các địa điểm check-in nổi bật tại Đà Nẵng và vùng lân cận']
        );

        $subCategories = [
            'Cầu nổi tiếng' => DanhMuc::firstOrCreate(['ten_danh_muc' => 'Cầu nổi tiếng'], ['mo_ta' => 'Các cây cầu nổi tiếng']),
            'Bãi biển'      => DanhMuc::firstOrCreate(['ten_danh_muc' => 'Bãi biển'],      ['mo_ta' => 'Bãi biển']),
            'Ngắm cảnh'     => DanhMuc::firstOrCreate(['ten_danh_muc' => 'Ngắm cảnh'],     ['mo_ta' => 'Điểm ngắm cảnh']),
            'Lịch sử'       => DanhMuc::firstOrCreate(['ten_danh_muc' => 'Lịch sử'],       ['mo_ta' => 'Di tích lịch sử']),
            'Phố cổ'        => DanhMuc::firstOrCreate(['ten_danh_muc' => 'Phố cổ'],        ['mo_ta' => 'Phố cổ']),
            'Tự nhiên'      => DanhMuc::firstOrCreate(['ten_danh_muc' => 'Tự nhiên'],      ['mo_ta' => 'Thiên nhiên hoang sơ']),
        ];

        $places = [
            [
                'ten_dia_diem' => 'Cầu Vàng – Bà Nà Hills',
                'mo_ta'        => 'Cây cầu nổi tiếng thế giới với đôi bàn tay khổng lồ nâng đỡ giữa không trung, view núi và mây mù huyền ảo.',
                'dia_chi'      => 'Bà Nà Hills, Hòa Ninh, Hòa Vang, Đà Nẵng',
                'kinh_do'      => 107.9963, 'vi_do' => 15.9952000,
                'gia_ve'       => 900000, 'gio_mo_cua' => '08:00', 'gio_dong_cua' => '18:00',
                'danh_gia_trung_binh' => 4.9, 'loai_dia_diem' => 'Cầu nổi tiếng',
                'image'        => 'https://top7vietnam.sgtiepthi.vn/wp-content/uploads/2021/03/Cau-vang-banahill_minhtu-1.jpg',
            ],
            [
                'ten_dia_diem' => 'Cầu Rồng',
                'mo_ta'        => 'Biểu tượng của Đà Nẵng hiện đại, phun lửa và nước vào cuối tuần, view sông Hàn tuyệt đẹp.',
                'dia_chi'      => 'Đường Nguyễn Văn Linh, Quận Hải Châu, Đà Nẵng',
                'kinh_do'      => 108.2271, 'vi_do' => 16.0610000,
                'gia_ve'       => 0, 'gio_mo_cua' => '00:00', 'gio_dong_cua' => '23:59',
                'danh_gia_trung_binh' => 4.8, 'loai_dia_diem' => 'Cầu nổi tiếng',
                'image'        => 'https://sgtourism.vn/wp-content/uploads/2016/08/cau-rong-da-nang-1.jpg',
            ],
            [
                'ten_dia_diem' => 'Biển Mỹ Khê',
                'mo_ta'        => 'Bãi biển thuộc top đẹp nhất châu Á, cát trắng mịn, nước trong xanh, sóng lớn thích surf.',
                'dia_chi'      => 'Phường Phước Mỹ, Sơn Trà, Đà Nẵng',
                'kinh_do'      => 108.2451, 'vi_do' => 16.0540000,
                'gia_ve'       => 0, 'gio_mo_cua' => '00:00', 'gio_dong_cua' => '23:59',
                'danh_gia_trung_binh' => 4.9, 'loai_dia_diem' => 'Bãi biển',
                'image'        => 'https://bieudienthuccanh.com/userfiles/image/da-nang/2019/bai-bien-my-khe/bai-bien-da-nang-hang-dua.jpg',
            ],
            [
                'ten_dia_diem' => 'Đỉnh Bàn Cờ – Sơn Trà',
                'mo_ta'        => 'Điểm cao nhất bán đảo Sơn Trà, view 360° toàn cảnh thành phố, biển và núi.',
                'dia_chi'      => 'Bán đảo Sơn Trà, Đà Nẵng',
                'kinh_do'      => 108.2721, 'vi_do' => 16.1192000,
                'gia_ve'       => 0, 'gio_mo_cua' => '06:00', 'gio_dong_cua' => '18:00',
                'danh_gia_trung_binh' => 4.8, 'loai_dia_diem' => 'Ngắm cảnh',
                'image'        => 'https://static.vinwonders.com/2022/06/dinh-Ban-Co-1-1.jpg',
            ],
            [
                'ten_dia_diem' => 'Cầu Tình Yêu',
                'mo_ta'        => 'Cây cầu lãng mạn bên bờ sông Hàn, nơi các đôi tình nhân khóa khóa tình yêu.',
                'dia_chi'      => 'Trần Hưng Đạo, Sơn Trà, Đà Nẵng',
                'kinh_do'      => 108.2275, 'vi_do' => 16.0610000,
                'gia_ve'       => 0, 'gio_mo_cua' => '00:00', 'gio_dong_cua' => '23:59',
                'danh_gia_trung_binh' => 4.7, 'loai_dia_diem' => 'Cầu nổi tiếng',
                'image'        => 'https://ik.imagekit.io/tvlk/blog/2023/08/cau-tinh-yeu-da-nang-3.jpg?tr=q-70,c-at_max,w-1000,h-600',
            ],
            [
                'ten_dia_diem' => 'Bãi Rạng – Sơn Trà',
                'mo_ta'        => 'Bãi biển hoang sơ, bí ẩn, nước trong vắt, ít người biết – thiên đường của dân snorkel.',
                'dia_chi'      => 'Bán đảo Sơn Trà, Đà Nẵng',
                'kinh_do'      => 108.2702, 'vi_do' => 16.0895000,
                'gia_ve'       => 0, 'gio_mo_cua' => '06:00', 'gio_dong_cua' => '18:00',
                'danh_gia_trung_binh' => 4.7, 'loai_dia_diem' => 'Bãi biển',
                'image'        => 'https://danangbest.com/upload_content/bai-rang-da-nang-1.webp',
            ],
            [
                'ten_dia_diem' => 'Hải đăng Tiên Sa',
                'mo_ta'        => 'Ngọn hải đăng 100+ tuổi trên bán đảo Sơn Trà, view biển rộng lớn.',
                'dia_chi'      => 'Bán đảo Sơn Trà, Đà Nẵng',
                'kinh_do'      => 108.2751, 'vi_do' => 16.1200000,
                'gia_ve'       => 0, 'gio_mo_cua' => '06:00', 'gio_dong_cua' => '18:00',
                'danh_gia_trung_binh' => 4.8, 'loai_dia_diem' => 'Lịch sử',
                'image'        => 'https://dulichdanang24h.vn/wp-content/uploads/2024/06/hai-dang-tien-sa-26.jpg',
            ],
            [
                'ten_dia_diem' => 'Ngũ Hành Sơn',
                'mo_ta'        => 'Danh thắng 5 ngọn núi đá vôi cổ, hang động và chùa chiền linh thiêng, di tích quốc gia.',
                'dia_chi'      => '81 Huyền Trân Công Chúa, Ngũ Hành Sơn, Đà Nẵng',
                'kinh_do'      => 108.2640, 'vi_do' => 16.0045000,
                'gia_ve'       => 40000, 'gio_mo_cua' => '07:00', 'gio_dong_cua' => '17:30',
                'danh_gia_trung_binh' => 4.7, 'loai_dia_diem' => 'Lịch sử',
                'image'        => 'https://lookaside.fbsbx.com/lookaside/crawler/media/?media_id=476108831226951',
            ],
            [
                'ten_dia_diem' => 'Phố đi bộ Bạch Đằng',
                'mo_ta'        => 'Con phố đi bộ dọc sông Hàn, sầm uất về đêm với ánh đèn lung linh và nhiều dịch vụ.',
                'dia_chi'      => 'Bạch Đằng, Hải Châu, Đà Nẵng',
                'kinh_do'      => 108.2232, 'vi_do' => 16.0551930,
                'gia_ve'       => 0, 'gio_mo_cua' => '00:00', 'gio_dong_cua' => '23:59',
                'danh_gia_trung_binh' => 4.6, 'loai_dia_diem' => 'Ngắm cảnh',
                'image'        => 'https://nhahangcham.com/wp-content/uploads/2024/07/04.-Pho-di-bo-Bach-Dang.jpg',
            ],
            [
                'ten_dia_diem' => 'Phố cổ Hội An',
                'mo_ta'        => 'Phố cổ đèn lồng rực rỡ, di sản văn hóa thế giới UNESCO, con phố cổ kính nhất miền Trung.',
                'dia_chi'      => 'Minh An, Hội An, Quảng Nam',
                'kinh_do'      => 108.3262, 'vi_do' => 15.8770873,
                'gia_ve'       => 120000, 'gio_mo_cua' => '08:00', 'gio_dong_cua' => '21:30',
                'danh_gia_trung_binh' => 4.9, 'loai_dia_diem' => 'Phố cổ',
                'image'        => 'https://static.vinwonders.com/2023/01/check-in-hoi-an-01.jpg',
            ],
            [
                'ten_dia_diem' => 'Thánh địa Mỹ Sơn',
                'mo_ta'        => 'Quần thể đền tháp Chăm Pa huyền bí, di sản thế giới UNESCO, hơn 1000 năm tuổi.',
                'dia_chi'      => 'Duy Xuyên, Quảng Nam',
                'kinh_do'      => 108.1232, 'vi_do' => 15.7730000,
                'gia_ve'       => 150000, 'gio_mo_cua' => '06:00', 'gio_dong_cua' => '17:00',
                'danh_gia_trung_binh' => 4.8, 'loai_dia_diem' => 'Lịch sử',
                'image'        => 'https://buulong.com.vn/wp-content/uploads/2026/03/thuyet-minh-ve-thanh-dia-my-son-5.jpg',
            ],
            [
                'ten_dia_diem' => 'Bán đảo Sơn Trà',
                'mo_ta'        => 'Lá phổi xanh của Đà Nẵng, rừng nguyên sinh bảo tồn với nhiều loài động thực vật quý hiếm.',
                'dia_chi'      => 'Sơn Trà, Đà Nẵng',
                'kinh_do'      => 108.2623, 'vi_do' => 16.0993000,
                'gia_ve'       => 0, 'gio_mo_cua' => '06:00', 'gio_dong_cua' => '18:00',
                'danh_gia_trung_binh' => 4.8, 'loai_dia_diem' => 'Tự nhiên',
                'image'        => 'https://danangfantasticity.com/wp-content/uploads/2025/08/ban-dao-son-tra-thanh-pho-da-nang.jpg',
            ],
            [
                'ten_dia_diem' => 'Bãi biển Non Nước',
                'mo_ta'        => 'Bãi biển bình yên chân núi Ngũ Hành Sơn, cát trắng mịn dài, ít người đông.',
                'dia_chi'      => 'Ngũ Hành Sơn, Đà Nẵng',
                'kinh_do'      => 108.2530, 'vi_do' => 16.0032000,
                'gia_ve'       => 0, 'gio_mo_cua' => '00:00', 'gio_dong_cua' => '23:59',
                'danh_gia_trung_binh' => 4.7, 'loai_dia_diem' => 'Bãi biển',
                'image'        => 'https://bazantravel.com/cdn/medias/uploads/85/85697-bai-bien-non-nuoc-da-nang-700x403.jpg',
            ],
            [
                'ten_dia_diem' => 'Biển An Bàng – Hội An',
                'mo_ta'        => 'Bãi biển yên tĩnh, trong xanh, nhiều resort và quán cà phê bờ biển tuyệt đẹp.',
                'dia_chi'      => 'Cẩm An, Hội An, Quảng Nam',
                'kinh_do'      => 108.3438, 'vi_do' => 15.9118000,
                'gia_ve'       => 0, 'gio_mo_cua' => '00:00', 'gio_dong_cua' => '23:59',
                'danh_gia_trung_binh' => 4.8, 'loai_dia_diem' => 'Bãi biển',
                'image'        => 'https://www.vietnamairlines.com/content/dam/legacy-site-assets/SEO-images/2025%20SEO/Traffic%20TA/MN/an%20bang%20beach%20hoi%20an/an%20bang%20beach%20hoi%20an/soft-golden-sand-gentle-waves-crystal-clear-water.jpg',
            ],
            [
                'ten_dia_diem' => 'Cầu sông Hàn',
                'mo_ta'        => 'Cây cầu quay độc đáo trên thế giới, quay 90° vào ban đêm để tàu thuyền qua lại.',
                'dia_chi'      => 'Sông Hàn, Hải Châu, Đà Nẵng',
                'kinh_do'      => 108.2231, 'vi_do' => 16.0652,
                'gia_ve'       => 0, 'gio_mo_cua' => '00:00', 'gio_dong_cua' => '23:59',
                'danh_gia_trung_binh' => 4.6, 'loai_dia_diem' => 'Cầu nổi tiếng',
                'image'        => 'https://images.unsplash.com/photo-1519451241324-20b4ea2c4220?w=800&h=600&fit=crop',
            ],
        ];

        foreach ($places as $placeData) {
            $loai = $placeData['loai_dia_diem'];
            $image = $placeData['image'] ?? null;
            unset($placeData['image']);

            $diaDiem = DiaDiem::firstOrCreate(
                ['ten_dia_diem' => $placeData['ten_dia_diem']],
                $placeData
            );

            if ($image) {
                \App\Models\HinhAnhDiaDiem::firstOrCreate(
                    ['id_dia_diem' => $diaDiem->id, 'is_main' => true],
                    ['duong_dan_anh' => $image, 'sort_order' => 1]
                );
            }
            ChiTietDanhMuc::firstOrCreate(['id_danh_muc' => $danhMucCheckin->id, 'id_dia_diem' => $diaDiem->id]);
            if (isset($subCategories[$loai])) {
                ChiTietDanhMuc::firstOrCreate(['id_danh_muc' => $subCategories[$loai]->id, 'id_dia_diem' => $diaDiem->id]);
            }
        }
    }
}
