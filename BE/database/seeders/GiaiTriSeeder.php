<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DiaDiem;
use App\Models\DanhMuc;
use App\Models\ChiTietDanhMuc;

class GiaiTriSeeder extends Seeder
{
    public function run(): void
    {
        $danhMucGiaiTri = DanhMuc::firstOrCreate(
            ['ten_danh_muc' => 'Giải trí'],
            ['mo_ta' => 'Các địa điểm giải trí tại Đà Nẵng']
        );

        $subCategories = [
            'Ngoài trời' => DanhMuc::firstOrCreate(['ten_danh_muc' => 'Ngoài trời'], ['mo_ta' => 'Hoạt động ngoài trời']),
            'Mua sắm'    => DanhMuc::firstOrCreate(['ten_danh_muc' => 'Mua sắm'],    ['mo_ta' => 'Trung tâm thương mại']),
            'Xem phim'   => DanhMuc::firstOrCreate(['ten_danh_muc' => 'Xem phim'],   ['mo_ta' => 'Rạp chiếu phim']),
            'Công viên'  => DanhMuc::firstOrCreate(['ten_danh_muc' => 'Công viên'],  ['mo_ta' => 'Công viên']),
            'Âm nhạc'    => DanhMuc::firstOrCreate(['ten_danh_muc' => 'Âm nhạc'],   ['mo_ta' => 'Bar, nhạc sống']),
            'Cafe'       => DanhMuc::firstOrCreate(['ten_danh_muc' => 'Cafe'],       ['mo_ta' => 'Quán cà phê']),
        ];

        $places = [
            [
                'ten_dia_diem' => 'Cầu Rồng – Giải trí tối',
                'mo_ta'        => 'Điểm đến không thể bỏ qua vào cuối tuần, xem biểu diễn phun lửa và nước hoành tráng lúc 21h.',
                'dia_chi'      => 'Đường Nguyễn Văn Linh, Quận Hải Châu, Đà Nẵng',
                'kinh_do'      => 108.2271, 'vi_do' => 16.0613000,
                'gia_ve'       => 0, 'gio_mo_cua' => '00:00', 'gio_dong_cua' => '23:59',
                'danh_gia_trung_binh' => 4.8, 'loai_dia_diem' => 'Ngoài trời',
                'image'        => 'https://dynamic-media-cdn.tripadvisor.com/media/photo-o/1a/f2/a9/d6/20200219-211234-largejpg.jpg?w=900&h=500&s=1',
            ],
            [
                'ten_dia_diem' => 'The Espresso Station',
                'mo_ta'        => 'Quán cà phê concept đẹp, không gian làm việc mở, thức uống chất lượng, wifi tốc độ cao.',
                'dia_chi'      => '48 Trần Phú, Quận Hải Châu, Đà Nẵng',
                'kinh_do'      => 108.2182, 'vi_do' => 16.0610000,
                'gia_ve'       => 50000, 'gio_mo_cua' => '06:30', 'gio_dong_cua' => '22:00',
                'danh_gia_trung_binh' => 4.7, 'loai_dia_diem' => 'Cafe',
                'image'        => 'https://dynamic-media-cdn.tripadvisor.com/media/photo-o/16/05/e9/f7/coffee-and-fashion.jpg?w=900&h=500&s=1',
            ],
            [
                'ten_dia_diem' => 'Cong Caphe – Trần Phú',
                'mo_ta'        => 'Quán café phong cách Đông Dương vintage đặc trưng, thiết kế tuyệt đẹp để check-in.',
                'dia_chi'      => '96-98 Bạch Đằng, Hải Châu, Đà Nẵng',
                'kinh_do'      => 108.2230, 'vi_do' => 16.0674000,
                'gia_ve'       => 40000, 'gio_mo_cua' => '07:30', 'gio_dong_cua' => '23:00',
                'danh_gia_trung_binh' => 4.5, 'loai_dia_diem' => 'Cafe',
                'image'        => 'https://lookaside.fbsbx.com/lookaside/crawler/media/?media_id=194016579807453',
            ],
            [
                'ten_dia_diem' => 'Rainforest Coffee',
                'mo_ta'        => 'Quán café xanh mát giữa lòng đô thị, không gian thoáng đãng với nhiều cây xanh và nước.',
                'dia_chi'      => '12 Lê Duẩn, Quận Hải Châu, Đà Nẵng',
                'kinh_do'      => 108.2153, 'vi_do' => 16.0517903,
                'gia_ve'       => 45000, 'gio_mo_cua' => '07:00', 'gio_dong_cua' => '22:30',
                'danh_gia_trung_binh' => 4.6, 'loai_dia_diem' => 'Cafe',
                'image'        => 'https://lookaside.fbsbx.com/lookaside/crawler/media/?media_id=532059435475068',
            ],
            [
                'ten_dia_diem' => 'Vincom Plaza Đà Nẵng',
                'mo_ta'        => 'Trung tâm thương mại lớn nhất trung tâm TP, hội tụ hàng trăm thương hiệu trong và ngoài nước.',
                'dia_chi'      => '910A Ngô Quyền, Hải Châu, Đà Nẵng',
                'kinh_do'      => 108.2228, 'vi_do' => 16.0670000,
                'gia_ve'       => 0, 'gio_mo_cua' => '09:30', 'gio_dong_cua' => '22:00',
                'danh_gia_trung_binh' => 4.6, 'loai_dia_diem' => 'Mua sắm',
                'image'        => 'https://dynamic-media-cdn.tripadvisor.com/media/photo-o/2f/b7/5c/b9/caption.jpg?w=1100&h=1100&s=1',
            ],
            [
                'ten_dia_diem' => 'Lotte Mart Đà Nẵng',
                'mo_ta'        => 'Siêu thị và trung tâm mua sắm Hàn Quốc, đa dạng hàng hóa với giá tốt.',
                'dia_chi'      => '6 Nại Nam, Hải Châu, Đà Nẵng',
                'kinh_do'      => 108.2154, 'vi_do' => 16.0566000,
                'gia_ve'       => 0, 'gio_mo_cua' => '08:00', 'gio_dong_cua' => '22:00',
                'danh_gia_trung_binh' => 4.4, 'loai_dia_diem' => 'Mua sắm',
                'image'        => 'https://danangticket.com/wp-content/uploads/2024/03/lotte-1.jpg',
            ],
            [
                'ten_dia_diem' => 'CGV Cinema – Vincom Đà Nẵng',
                'mo_ta'        => 'Rạp chiếu phim hiện đại nhất thành phố, âm thanh Dolby Atmos, màn hình IMAX.',
                'dia_chi'      => 'Vincom Plaza, 910A Ngô Quyền, Đà Nẵng',
                'kinh_do'      => 108.2229, 'vi_do' => 16.0670000,
                'gia_ve'       => 90000, 'gio_mo_cua' => '09:00', 'gio_dong_cua' => '23:30',
                'danh_gia_trung_binh' => 4.5, 'loai_dia_diem' => 'Xem phim',
                'image'        => 'https://iguov8nhvyobj.vcdn.cloud/media/site/cache/1/980x415/b58515f018eb873dafa430b6f9ae0c1e/v/i/vin-dn-2_8.png',
            ],
            [
                'ten_dia_diem' => 'Galaxy Cinema Đà Nẵng',
                'mo_ta'        => 'Rạp phim thoải mái, ghế ngồi êm, phòng chiếu đa dạng, giá vé hợp lý.',
                'dia_chi'      => 'Lotte Mart, 6 Nại Nam, Hải Châu, Đà Nẵng',
                'kinh_do'      => 108.2155, 'vi_do' => 16.0565000,
                'gia_ve'       => 75000, 'gio_mo_cua' => '09:00', 'gio_dong_cua' => '23:00',
                'danh_gia_trung_binh' => 4.4, 'loai_dia_diem' => 'Xem phim',
                'image'        => 'https://danang365.com/wp-content/uploads/2024/10/lotte-mart-da-nang1.jpg',
            ],
            [
                'ten_dia_diem' => 'Công viên Biển Đông',
                'mo_ta'        => 'Công viên rộng lớn ven biển, không gian tập thể dục thể thao, thoáng mát đặc biệt về sáng.',
                'dia_chi'      => 'Võ Văn Kiệt, Sơn Trà, Đà Nẵng',
                'kinh_do'      => 108.2401, 'vi_do' => 16.0700000,
                'gia_ve'       => 0, 'gio_mo_cua' => '05:00', 'gio_dong_cua' => '22:00',
                'danh_gia_trung_binh' => 4.7, 'loai_dia_diem' => 'Công viên',
                'image'        => 'https://static.vinwonders.com/2022/06/ZwIlGdJ2-cong-vien-bien-dong-1.jpg',
            ],
            [
                'ten_dia_diem' => 'Công viên 29/3',
                'mo_ta'        => 'Công viên lớn nhất trung tâm Đà Nẵng, hồ rộng, cây xanh, sân khấu ngoài trời.',
                'dia_chi'      => '02 Điện Biên Phủ, Thanh Khê, Đà Nẵng',
                'kinh_do'      => 108.2072, 'vi_do' => 16.0773000,
                'gia_ve'       => 0, 'gio_mo_cua' => '05:00', 'gio_dong_cua' => '22:00',
                'danh_gia_trung_binh' => 4.5, 'loai_dia_diem' => 'Công viên',
                'image'        => 'https://danangbest.com/upload_content/cong-vien-29-thang-3-1.webp',
            ],
            [
                'ten_dia_diem' => 'Sky36 Rooftop Bar',
                'mo_ta'        => 'Bar trên sân thượng tầng 36, view 360° toàn thành phố và sông Hàn đêm tuyệt đẹp.',
                'dia_chi'      => 'Tầng 36, Novotel Đà Nẵng, 36 Bạch Đằng, Đà Nẵng',
                'kinh_do'      => 108.2101, 'vi_do' => 16.0500000,
                'gia_ve'       => 150000, 'gio_mo_cua' => '17:00', 'gio_dong_cua' => '01:00',
                'danh_gia_trung_binh' => 4.4, 'loai_dia_diem' => 'Âm nhạc',
                'image'        => 'https://dynamic-media-cdn.tripadvisor.com/media/photo-o/2f/d0/96/f8/turn-up-your-senses-with.jpg?w=1200&h=-1&s=1',
            ],
            [
                'ten_dia_diem' => 'Brown Eyes Music & Bar',
                'mo_ta'        => 'Bar âm nhạc sống động xinh đẹp, band nhạc live mỗi tối, cocktail sáng tạo và đặc biệt.',
                'dia_chi'      => '225 Nguyễn Văn Linh, Thanh Khê, Đà Nẵng',
                'kinh_do'      => 108.2011, 'vi_do' => 16.0621000,
                'gia_ve'       => 100000, 'gio_mo_cua' => '18:00', 'gio_dong_cua' => '02:00',
                'danh_gia_trung_binh' => 4.5, 'loai_dia_diem' => 'Âm nhạc',
                'image'        => 'https://lookaside.instagram.com/seo/google_widget/crawler/?media_id=3846249509614229169',
            ],
            [
                'ten_dia_diem' => 'Da Nang Souvenirs & Café',
                'mo_ta'        => 'Kết hợp mua sắm quà lưu niệm Đà Nẵng và uống café, thiết kế độc đáo, view sông Hàn.',
                'dia_chi'      => '34 Bạch Đằng, Hải Châu, Đà Nẵng',
                'kinh_do'      => 108.2232, 'vi_do' => 16.0672000,
                'gia_ve'       => 30000, 'gio_mo_cua' => '08:00', 'gio_dong_cua' => '22:00',
                'danh_gia_trung_binh' => 4.6, 'loai_dia_diem' => 'Mua sắm',
                'image'        => 'https://lookaside.fbsbx.com/lookaside/crawler/media/?media_id=109945604127235',
            ],
            [
                'ten_dia_diem' => 'Trà Chanh Đà Nẵng',
                'mo_ta'        => 'Quán trà chanh bình dân giữa phố, tụ điểm lý tưởng của giới trẻ buổi tối.',
                'dia_chi'      => '55 Trần Phú, Hải Châu, Đà Nẵng',
                'kinh_do'      => 108.2219, 'vi_do' => 16.0668000,
                'gia_ve'       => 20000, 'gio_mo_cua' => '16:00', 'gio_dong_cua' => '23:00',
                'danh_gia_trung_binh' => 4.4, 'loai_dia_diem' => 'Cafe',
                'image'        => 'https://cdn.tgdd.vn/Files/2022/03/08/1419009/10-quan-tra-chanh-ngon-hut-khach-nhat-tai-da-nang-202203080643010157.jpg',
            ],
            [
                'ten_dia_diem' => 'The Coffee House – Đà Nẵng',
                'mo_ta'        => 'Chuỗi café nổi tiếng, không gian hiện đại thoải mái, wifi nhanh, thức uống đa dạng.',
                'dia_chi'      => '76 Hùng Vương, Hải Châu, Đà Nẵng',
                'kinh_do'      => 108.2131, 'vi_do' => 16.0661000,
                'gia_ve'       => 55000, 'gio_mo_cua' => '07:00', 'gio_dong_cua' => '23:00',
                'danh_gia_trung_binh' => 4.5, 'loai_dia_diem' => 'Cafe',
                'image'        => 'https://dynamic-media-cdn.tripadvisor.com/media/photo-o/1a/9d/aa/38/photo0jpg.jpg?w=900&h=500&s=1',
            ],
            [
                'ten_dia_diem' => 'Big C Đà Nẵng',
                'mo_ta'        => 'Siêu thị lớn với đa dạng hàng hóa, ẩm thực, thời trang, điện máy và vui chơi.',
                'dia_chi'      => '255 Hùng Vương, Hải Châu, Đà Nẵng',
                'kinh_do'      => 108.2118, 'vi_do' => 16.0649000,
                'gia_ve'       => 0, 'gio_mo_cua' => '08:00', 'gio_dong_cua' => '22:00',
                'danh_gia_trung_binh' => 4.3, 'loai_dia_diem' => 'Mua sắm',
                'image'        => 'https://danangfantasticity.com/wp-content/uploads/2017/06/bigc-da-nang-2.jpg',
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
            ChiTietDanhMuc::firstOrCreate(['id_danh_muc' => $danhMucGiaiTri->id, 'id_dia_diem' => $diaDiem->id]);
            if (isset($subCategories[$loai])) {
                ChiTietDanhMuc::firstOrCreate(['id_danh_muc' => $subCategories[$loai]->id, 'id_dia_diem' => $diaDiem->id]);
            }
        }
    }
}
