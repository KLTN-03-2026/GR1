<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DiaDiem;
use App\Models\DanhMuc;
use App\Models\ChiTietDanhMuc;

class TamLinhSeeder extends Seeder
{
    public function run(): void
    {
        $danhMucTamLinh = DanhMuc::firstOrCreate(
            ['ten_danh_muc' => 'Tâm linh'],
            ['mo_ta' => 'Các địa điểm tâm linh tại Đà Nẵng và vùng lân cận']
        );

        $subCategories = [
            'Chùa'      => DanhMuc::firstOrCreate(['ten_danh_muc' => 'Chùa'],      ['mo_ta' => 'Chùa Phật giáo']),
            'Đền'       => DanhMuc::firstOrCreate(['ten_danh_muc' => 'Đền'],       ['mo_ta' => 'Đền thờ']),
            'Nhà thờ'   => DanhMuc::firstOrCreate(['ten_danh_muc' => 'Nhà thờ'],  ['mo_ta' => 'Nhà thờ Công giáo']),
            'Thánh địa' => DanhMuc::firstOrCreate(['ten_danh_muc' => 'Thánh địa'],['mo_ta' => 'Thánh địa tôn giáo']),
            'Tu viện'   => DanhMuc::firstOrCreate(['ten_danh_muc' => 'Tu viện'],  ['mo_ta' => 'Tu viện thiền định']),
        ];

        $places = [
            [
                'ten_dia_diem' => 'Chùa Linh Ứng Bãi Bụt',
                'mo_ta'        => 'Ngôi chùa linh thiêng trên bán đảo Sơn Trà với tượng Phật Quan Âm cao 67m, view biển tuyệt đẹp.',
                'dia_chi'      => 'Bãi Bụt, Sơn Trà, Đà Nẵng',
                'kinh_do'      => 108.2471, 'vi_do' => 16.1002606,
                'gia_ve'       => 0, 'gio_mo_cua' => '06:00', 'gio_dong_cua' => '21:00',
                'danh_gia_trung_binh' => 4.9, 'loai_dia_diem' => 'Chùa',
                'image'        => 'https://images.unsplash.com/photo-1580974928064-f0aeef70895a?w=800',
            ],
            [
                'ten_dia_diem' => 'Chùa Linh Ứng Ngũ Hành Sơn',
                'mo_ta'        => 'Ngôi chùa cổ linh thiêng trong quần thể danh thắng Ngũ Hành Sơn, nghệ thuật khắc đá tinh xảo.',
                'dia_chi'      => '81 Huyền Trân Công Chúa, Ngũ Hành Sơn, Đà Nẵng',
                'kinh_do'      => 108.2641, 'vi_do' => 16.0040000,
                'gia_ve'       => 20000, 'gio_mo_cua' => '07:00', 'gio_dong_cua' => '17:30',
                'danh_gia_trung_binh' => 4.7, 'loai_dia_diem' => 'Chùa',
                'image'        => 'https://buulong.com.vn/wp-content/uploads/2026/03/lich-su-chua-linh-ung-ngu-hanh-son.jpg',
            ],
            [
                'ten_dia_diem' => 'Chùa Quán Thế Âm',
                'mo_ta'        => 'Ngôi chùa nổi tiếng với động Huyền Không và lễ hội Bà Quán Thế Âm lớn hàng năm.',
                'dia_chi'      => '509 Ngô Quyền, Ngũ Hành Sơn, Đà Nẵng',
                'kinh_do'      => 108.2621, 'vi_do' => 16.0030000,
                'gia_ve'       => 0, 'gio_mo_cua' => '06:00', 'gio_dong_cua' => '18:00',
                'danh_gia_trung_binh' => 4.7, 'loai_dia_diem' => 'Chùa',
                'image'        => 'https://static.vinwonders.com/2022/04/chua-quan-am-thumb.jpg',
            ],
            [
                'ten_dia_diem' => 'Chùa Pháp Lâm',
                'mo_ta'        => 'Trung tâm Phật giáo lớn nhất Đà Nẵng, không gian trang nghiêm, cây xanh bóng mát.',
                'dia_chi'      => '574 Ông Ích Khiêm, Hải Châu, Đà Nẵng',
                'kinh_do'      => 108.2103, 'vi_do' => 16.0622864,
                'gia_ve'       => 0, 'gio_mo_cua' => '05:00', 'gio_dong_cua' => '21:00',
                'danh_gia_trung_binh' => 4.6, 'loai_dia_diem' => 'Chùa',
                'image'        => 'https://static.vinwonders.com/2022/12/chua-phap-lam-1.jpg',
            ],
            [
                'ten_dia_diem' => 'Chùa Nam Sơn',
                'mo_ta'        => 'Ngôi chùa lớn tại Liên Chiểu, kiến trúc đẹp, không gian yên bình và thiên nhiên trong lành.',
                'dia_chi'      => 'Liên Chiểu, Đà Nẵng',
                'kinh_do'      => 108.1654, 'vi_do' => 16.0700000,
                'gia_ve'       => 0, 'gio_mo_cua' => '05:00', 'gio_dong_cua' => '21:00',
                'danh_gia_trung_binh' => 4.8, 'loai_dia_diem' => 'Chùa',
                'image'        => 'https://hellodanang.vn/wp-content/uploads/2024/11/chua-nam-son-1732119558.jpg',
            ],
            [
                'ten_dia_diem' => 'Nhà thờ Chính toà Đà Nẵng',
                'mo_ta'        => 'Nhà thờ Con Gà biểu tượng, kiến trúc Gothic Pháp màu hồng nổi bật, 100+ tuổi lịch sử.',
                'dia_chi'      => '156 Trần Phú, Hải Châu, Đà Nẵng',
                'kinh_do'      => 108.2229, 'vi_do' => 16.0672,
                'gia_ve'       => 0, 'gio_mo_cua' => '05:00', 'gio_dong_cua' => '20:00',
                'danh_gia_trung_binh' => 4.7, 'loai_dia_diem' => 'Nhà thờ',
                'image'        => 'https://images.unsplash.com/photo-1555993539-1732b0258235?w=800&h=600&fit=crop',
            ],
            [
                'ten_dia_diem' => 'Chùa Cầu – Hội An',
                'mo_ta'        => 'Biểu tượng của Hội An, cây cầu cổ 400 năm là nơi thờ tự và điểm check-in nổi tiếng.',
                'dia_chi'      => 'Nguyễn Thị Minh Khai, Minh An, Hội An, Quảng Nam',
                'kinh_do'      => 108.3268, 'vi_do' => 15.8779000,
                'gia_ve'       => 80000, 'gio_mo_cua' => '08:00', 'gio_dong_cua' => '21:00',
                'danh_gia_trung_binh' => 4.8, 'loai_dia_diem' => 'Đền',
                'image'        => 'https://statics.vinpearl.com/chua-cau-hoi-an-21_1628047454.jpg',
            ],
            [
                'ten_dia_diem' => 'Thánh địa Mỹ Sơn – Tâm linh',
                'mo_ta'        => 'Thánh địa Chăm Pa linh thiêng, nơi vua Chăm tế lễ thần Shiva suốt nhiều thế kỷ, UNESCO.',
                'dia_chi'      => 'Duy Xuyên, Quảng Nam',
                'kinh_do'      => 108.1232, 'vi_do' => 15.7727000,
                'gia_ve'       => 150000, 'gio_mo_cua' => '06:00', 'gio_dong_cua' => '17:00',
                'danh_gia_trung_binh' => 4.8, 'loai_dia_diem' => 'Thánh địa',
                'image'        => 'https://cdn.xanhsm.com/2025/01/3dd34648-thanh-dia-my-son-1a.jpg',
            ],
            [
                'ten_dia_diem' => 'Đền thờ Cá Ông – Sơn Trà',
                'mo_ta'        => 'Ngôi đền linh thiêng thờ cá voi, biểu tượng tâm linh của ngư dân miền biển Sơn Trà.',
                'dia_chi'      => 'Mân Thái, Sơn Trà, Đà Nẵng',
                'kinh_do'      => 108.2451, 'vi_do' => 16.0753000,
                'gia_ve'       => 0, 'gio_mo_cua' => '06:00', 'gio_dong_cua' => '18:00',
                'danh_gia_trung_binh' => 4.6, 'loai_dia_diem' => 'Đền',
                'image'        => 'https://images.unsplash.com/photo-1560185127-6a7f4e9e1e88?w=800&h=600&fit=crop',
            ],
            [
                'ten_dia_diem' => 'Chùa Linh Ứng Bà Nà',
                'mo_ta'        => 'Ngôi chùa trên núi cao 1,487m, mây mù bao phủ, không gian cực kỳ linh thiêng và yên bình.',
                'dia_chi'      => 'Bà Nà Hills, Hòa Ninh, Đà Nẵng',
                'kinh_do'      => 107.9952, 'vi_do' => 15.9950000,
                'gia_ve'       => 700000, 'gio_mo_cua' => '08:00', 'gio_dong_cua' => '17:30',
                'danh_gia_trung_binh' => 4.8, 'loai_dia_diem' => 'Chùa',
                'image'        => 'https://pystravel.vn/_next/image?url=https%3A%2F%2Fbooking.pystravel.vn%2Fuploads%2Fposts%2Favatar%2F1742721933.jpg&w=3840&q=75',
            ],
            [
                'ten_dia_diem' => 'Đền Hai Bà Trưng Đà Nẵng',
                'mo_ta'        => 'Đền thờ Hai Bà Trưng linh thiêng, di tích lịch sử văn hóa quan trọng của thành phố.',
                'dia_chi'      => 'Hai Bà Trưng, Hải Châu, Đà Nẵng',
                'kinh_do'      => 108.2141, 'vi_do' => 16.0701000,
                'gia_ve'       => 0, 'gio_mo_cua' => '06:00', 'gio_dong_cua' => '18:00',
                'danh_gia_trung_binh' => 4.5, 'loai_dia_diem' => 'Đền',
                'image'        => 'https://static.vinwonders.com/production/den-hai-ba-trung-1.jpg',
            ],
            [
                'ten_dia_diem' => 'Nhà thờ An Hải',
                'mo_ta'        => 'Nhà thờ lớn khu vực Sơn Trà, kiến trúc đẹp, hoạt động tích cực trong cộng đồng Công giáo.',
                'dia_chi'      => 'An Hải Tây, Sơn Trà, Đà Nẵng',
                'kinh_do'      => 108.2360, 'vi_do' => 16.0650000,
                'gia_ve'       => 0, 'gio_mo_cua' => '05:00', 'gio_dong_cua' => '20:00',
                'danh_gia_trung_binh' => 4.5, 'loai_dia_diem' => 'Nhà thờ',
                'image'        => 'https://upload.wikimedia.org/wikipedia/commons/2/2d/Nh%C3%A0_th%E1%BB%9D_Tho%E1%BA%A1i_Ng%E1%BB%8Dc_H%E1%BA%A7u%2C_An_H%E1%BA%A3i_T%C3%A2y%2C_S%C6%A1n_Tr%C3%A0%2C_%C4%90%C3%A0_N%E1%BA%B5ng_2.jpeg',
            ],
            [
                'ten_dia_diem' => 'Chùa Tam Bảo',
                'mo_ta'        => 'Ngôi chùa Phật giáo Nguyên Thủy (Theravada) mang kiến trúc pha trộn độc đáo và lưu giữ nhiều xá lợi.',
                'dia_chi'      => '323 Phan Châu Trinh, Hải Châu, Đà Nẵng',
                'kinh_do'      => 108.2201, 'vi_do' => 16.0575,
                'gia_ve'       => 0, 'gio_mo_cua' => '05:00', 'gio_dong_cua' => '21:00',
                'danh_gia_trung_binh' => 4.6, 'loai_dia_diem' => 'Chùa',
                'image'        => 'https://image.vietnamnews.vn/uploadvnnews/Article/2021/5/25/164741_10214871465922370_1928014529267104990_n.jpg',
            ],
            [
                'ten_dia_diem' => 'Chùa Phổ Đà',
                'mo_ta'        => 'Ngôi chùa cổ với kiến trúc hình chữ Khẩu, nơi đặt Học viện Phật giáo Việt Nam tại Đà Nẵng.',
                'dia_chi'      => '340 Phan Châu Trinh, Bình Hiên, Hải Châu, Đà Nẵng',
                'kinh_do'      => 108.2208, 'vi_do' => 16.0573,
                'gia_ve'       => 0, 'gio_mo_cua' => '05:00', 'gio_dong_cua' => '20:30',
                'danh_gia_trung_binh' => 4.7, 'loai_dia_diem' => 'Chùa',
                'image'        => 'https://phatgiaodanang.vn/wp-content/uploads/2019/12/Chua-Pho-Da-5-1024x768.jpg',
            ],
            [
                'ten_dia_diem' => 'Đền thờ Thoại Ngọc Hầu',
                'mo_ta'        => 'Đền thờ ghi nhớ công ơn vị danh tướng Thoại Ngọc Hầu có công khai hoang lập ấp.',
                'dia_chi'      => 'An Hải Tây, Sơn Trà, Đà Nẵng',
                'kinh_do'      => 108.2325, 'vi_do' => 16.0640,
                'gia_ve'       => 0, 'gio_mo_cua' => '07:00', 'gio_dong_cua' => '17:00',
                'danh_gia_trung_binh' => 4.4, 'loai_dia_diem' => 'Đền',
                'image'        => 'https://tourdanangcity.vn/wp-content/uploads/2021/04/thoai-ngoc-hau-da-nang-7.jpg',
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
            ChiTietDanhMuc::firstOrCreate(['id_danh_muc' => $danhMucTamLinh->id, 'id_dia_diem' => $diaDiem->id]);
            if (isset($subCategories[$loai])) {
                ChiTietDanhMuc::firstOrCreate(['id_danh_muc' => $subCategories[$loai]->id, 'id_dia_diem' => $diaDiem->id]);
            }
        }
    }
}
