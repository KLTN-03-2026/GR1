<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DiaDiem;
use App\Models\DanhMuc;
use App\Models\ChiTietDanhMuc;

class AmThucSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tạo danh mục ẩm thực tổng (nếu chưa có)
        $danhMucAmThuc = DanhMuc::firstOrCreate(
            ['ten_danh_muc' => 'Ẩm thực'],
            ['mo_ta' => 'Các địa điểm ăn uống, quán ăn, street food tại Đà Nẵng']
        );

        // 2. Tạo các danh mục con
        $subCategories = [
            'Quán ăn'   => DanhMuc::firstOrCreate(['ten_danh_muc' => 'Quán ăn'],   ['mo_ta' => 'Các quán ăn']),
            'Street food' => DanhMuc::firstOrCreate(['ten_danh_muc' => 'Street food'], ['mo_ta' => 'Ẩm thực đường phố']),
            'Hải sản'   => DanhMuc::firstOrCreate(['ten_danh_muc' => 'Hải sản'],   ['mo_ta' => 'Nhà hàng hải sản']),
            'Quán nhậu' => DanhMuc::firstOrCreate(['ten_danh_muc' => 'Quán nhậu'], ['mo_ta' => 'Quán nhậu, beer club']),
            'Ăn vặt'    => DanhMuc::firstOrCreate(['ten_danh_muc' => 'Ăn vặt'],    ['mo_ta' => 'Quán ăn vặt, chè, bánh']),
        ];

        // 3. Dữ liệu các địa điểm ẩm thực
        $places = [
            [
                'ten_dia_diem'        => 'Bún chả cá Bà Lữ',
                'mo_ta'               => 'Quán bún chả cá nổi tiếng, nước dùng đậm đà chuẩn vị Đà Nẵng.',
                'dia_chi'             => '319 Hùng Vương, Quận Hải Châu, Đà Nẵng',
                'kinh_do'             => 108.2131238,
                'vi_do'               => 16.0655421,
                'gia_ve'              => 35000,
                'gio_mo_cua'          => '06:00',
                'gio_dong_cua'        => '21:00',
                'danh_gia_trung_binh' => 4.7,
                'loai_dia_diem'       => 'Quán ăn',
                'image'               => 'https://buulong.com.vn/wp-content/uploads/2025/12/bun-cha-ca-ba-lu-hung-vuong-da-nang-8b70bc-1200x675.webp',
            ],
            [
                'ten_dia_diem'        => 'Mì Quảng Bà Mua',
                'mo_ta'               => 'Mì Quảng chuẩn vị Quảng Nam, topping đầy đặn, nước dùng đậm.',
                'dia_chi'             => '95A Nguyễn Tri Phương, Quận Hải Châu, Đà Nẵng',
                'kinh_do'             => 108.2045543,
                'vi_do'               => 16.0583421,
                'gia_ve'              => 40000,
                'gio_mo_cua'          => '06:30',
                'gio_dong_cua'        => '22:00',
                'danh_gia_trung_binh' => 4.6,
                'loai_dia_diem'       => 'Quán ăn',
                'image'               => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRTz4tLHpk50RCeYugQE7kK6JNn2ybbXJaMfw&s',
            ],
            [
                'ten_dia_diem'        => 'Bánh xèo Bà Dưỡng',
                'mo_ta'               => 'Bánh xèo giòn rụm, nước chấm đặc trưng, rất đông khách.',
                'dia_chi'             => 'K280/23 Hoàng Diệu, Quận Hải Châu, Đà Nẵng',
                'kinh_do'             => 108.2163214,
                'vi_do'               => 16.0544521,
                'gia_ve'              => 30000,
                'gio_mo_cua'          => '10:00',
                'gio_dong_cua'        => '21:00',
                'danh_gia_trung_binh' => 4.7,
                'loai_dia_diem'       => 'Street food',
                'image'               => 'https://down-vn.img.susercontent.com/vn-11134259-7r98o-lwgi5rsf80d533@resize_w800',
            ],
            [
                'ten_dia_diem'        => 'Hải sản Bé Mặn',
                'mo_ta'               => 'Quán hải sản tươi sống, giá hợp lý, nổi tiếng du khách.',
                'dia_chi'             => 'Lô 14 Hoàng Sa, Sơn Trà, Đà Nẵng',
                'kinh_do'             => 108.2473865,
                'vi_do'               => 16.0820695,
                'gia_ve'              => 150000,
                'gio_mo_cua'          => '10:00',
                'gio_dong_cua'        => '23:00',
                'danh_gia_trung_binh' => 4.5,
                'loai_dia_diem'       => 'Hải sản',
                'image'               => 'https://www.wikidanang.com/tin-tuc/images/Wiki/nha-hang/be-man/nha-hang-be-man-vng-1.jpg',
            ],
            [
                'ten_dia_diem'        => 'Bánh tráng cuốn thịt heo Trần',
                'mo_ta'               => 'Đặc sản Đà Nẵng, thịt heo luộc mềm, rau sống phong phú.',
                'dia_chi'             => '4 Lê Duẩn, Hải Châu, Đà Nẵng',
                'kinh_do'             => 108.1977924,
                'vi_do'               => 16.0206886,
                'gia_ve'              => 80000,
                'gio_mo_cua'          => '09:00',
                'gio_dong_cua'        => '22:00',
                'danh_gia_trung_binh' => 4.6,
                'loai_dia_diem'       => 'Street food',
                'image'               => 'https://ticotravel.com.vn/wp-content/uploads/2022/05/Banh-trang-cuon-thit-heo-Da-Nang-4.jpg',
            ],
            [
                'ten_dia_diem'        => 'Ốc hút Hạnh',
                'mo_ta'               => 'Quán ốc bình dân, nổi tiếng với món ốc hút cay.',
                'dia_chi'             => '277 Đống Đa, Hải Châu, Đà Nẵng',
                'kinh_do'             => 108.2140000,
                'vi_do'               => 16.0690000,
                'gia_ve'              => 25000,
                'gio_mo_cua'          => '15:00',
                'gio_dong_cua'        => '22:00',
                'danh_gia_trung_binh' => 4.5,
                'loai_dia_diem'       => 'Street food',
                'image'               => 'https://mms.img.susercontent.com/vn-11134513-7r98o-lsvhcsgdf7h5bc@resize_ss1242x600!@crop_w1242_h600_cT',
            ],
            [
                'ten_dia_diem'        => 'Bún mắm nêm Bà Thuyên',
                'mo_ta'               => 'Bún mắm nêm đậm vị, topping đầy đủ, rất được người địa phương yêu thích.',
                'dia_chi'             => 'K424/03 Lê Duẩn, Hải Châu, Đà Nẵng',
                'kinh_do'             => 108.2076930,
                'vi_do'               => 16.0675630,
                'gia_ve'              => 30000,
                'gio_mo_cua'          => '07:00',
                'gio_dong_cua'        => '20:00',
                'danh_gia_trung_binh' => 4.6,
                'loai_dia_diem'       => 'Quán ăn',
                'image'               => 'https://cdn.eva.vn/upload/2-2022/images/2022-05-10/quan-bun-mam-hon-30-nam-o-da-nang-khach-un-un-keo-den-chi-nho-1-bi-quyet-b--n-m---m-b---v--n------n---ng-1652184693-107-width780height760.jpg',
            ],
            [
                'ten_dia_diem'        => 'Chè Liên',
                'mo_ta'               => 'Quán chè nổi tiếng Đà Nẵng, đặc biệt là chè Thái sầu riêng.',
                'dia_chi'             => '175 Hải Phòng, Hải Châu, Đà Nẵng',
                'kinh_do'             => 108.2080000,
                'vi_do'               => 16.0680000,
                'gia_ve'              => 25000,
                'gio_mo_cua'          => '09:00',
                'gio_dong_cua'        => '22:30',
                'danh_gia_trung_binh' => 4.7,
                'loai_dia_diem'       => 'Ăn vặt',
                'image'               => 'https://interstellas.com.vn/wp-content/uploads/2025/10/khong-gian-quan-che-sau-lien-hai-phong.jpg',
            ],
            [
                'ten_dia_diem'        => 'Nem lụi Bà Hường',
                'mo_ta'               => 'Nem lụi nướng thơm, ăn kèm bánh tráng và rau sống cực ngon.',
                'dia_chi'             => 'K35/2 Hàm Nghi, Thanh Khê, Đà Nẵng',
                'kinh_do'             => 108.2200821,
                'vi_do'               => 16.0670938,
                'gia_ve'              => 50000,
                'gio_mo_cua'          => '10:00',
                'gio_dong_cua'        => '21:00',
                'danh_gia_trung_binh' => 4.6,
                'loai_dia_diem'       => 'Street food',
                'image'               => 'https://checkinvietnam.vtc.vn/media/20230829/images/quan-nem-lui-da-nang-1.jpg',
            ],
            [
                'ten_dia_diem'        => 'Bánh mì Ông Tý',
                'mo_ta'               => 'Bánh mì giòn nóng, nhân đầy đặn, giá rẻ phù hợp ăn nhanh.',
                'dia_chi'             => '272 Hùng Vương, Hải Châu, Đà Nẵng',
                'kinh_do'             => 108.2120000,
                'vi_do'               => 16.0660000,
                'gia_ve'              => 20000,
                'gio_mo_cua'          => '05:30',
                'gio_dong_cua'        => '21:00',
                'danh_gia_trung_binh' => 4.5,
                'loai_dia_diem'       => 'Street food',
                'image'               => 'https://visitdanang.travel/VisitDaNang/_default_upload_bucket/2799/image-thumb__2799__720_jpg/banh-my-da-nang-1_1743328866.9de5dc32.jpg',
            ],
            [
                'ten_dia_diem'        => 'Bún bò Huế Bà Đào',
                'mo_ta'               => 'Bún bò chuẩn vị Huế, nước dùng cay nhẹ, thịt mềm ngon.',
                'dia_chi'             => '37 Nguyễn Chí Thanh, Hải Châu, Đà Nẵng',
                'kinh_do'             => 108.2167501,
                'vi_do'               => 16.0781337,
                'gia_ve'              => 45000,
                'gio_mo_cua'          => '06:00',
                'gio_dong_cua'        => '22:00',
                'danh_gia_trung_binh' => 4.6,
                'loai_dia_diem'       => 'Quán ăn',
                'image'               => 'https://www.tiktok.com/api/img/?itemId=7422136345742658837&location=0&aid=1988',
            ],
            [
                'ten_dia_diem'        => 'Nhậu Tới Bến',
                'mo_ta'               => 'Quán nhậu bình dân, menu đa dạng, không khí sôi động, rất đông khách buổi tối.',
                'dia_chi'             => '01 Nguyễn Tri Phương, Thanh Khê, Đà Nẵng',
                'kinh_do'             => 108.2035686,
                'vi_do'               => 16.0630596,
                'gia_ve'              => 100000,
                'gio_mo_cua'          => '16:00',
                'gio_dong_cua'        => '00:00',
                'danh_gia_trung_binh' => 4.4,
                'loai_dia_diem'       => 'Quán nhậu',
                'image'               => 'https://top10danang.com/wp-content/uploads/2025/09/moi-premium-grill-chill-top10danang.jpg',
            ],
            [
                'ten_dia_diem'        => 'Quán Bé Ni 2',
                'mo_ta'               => 'Quán nhậu hải sản nổi tiếng, giá rẻ, đông dân local.',
                'dia_chi'             => 'Hoàng Sa, Sơn Trà, Đà Nẵng',
                'kinh_do'             => 108.2449953,
                'vi_do'               => 16.0668016,
                'gia_ve'              => 120000,
                'gio_mo_cua'          => '15:00',
                'gio_dong_cua'        => '23:30',
                'danh_gia_trung_binh' => 4.5,
                'loai_dia_diem'       => 'Quán nhậu',
                'image'               => 'https://dynamic-media-cdn.tripadvisor.com/media/photo-o/22/df/1a/50/inside-of-restaurant.jpg?w=900&h=500&s=1',
            ],
            [
                'ten_dia_diem'        => 'Hải Sản Năm Đảnh',
                'mo_ta'               => 'Quán nhậu hải sản nổi tiếng giá rẻ, cực kỳ đông khách.',
                'dia_chi'             => 'K139/H59/38 Trần Quang Khải, Sơn Trà, Đà Nẵng',
                'kinh_do'             => 108.2440000,
                'vi_do'               => 16.0680000,
                'gia_ve'              => 150000,
                'gio_mo_cua'          => '10:00',
                'gio_dong_cua'        => '22:00',
                'danh_gia_trung_binh' => 4.6,
                'loai_dia_diem'       => 'Hải sản',
                'image'               => 'https://reviewdanang.com.vn/wp-content/uploads/2020/10/hai-san-nam-danh-da-nang-dia-chi-o-k139-h59-38-tran-quang-khai-dong-gia-60k.jpg',
            ],
            [
                'ten_dia_diem'        => 'Nhà hàng Madame Lân',
                'mo_ta'               => 'Nhà hàng hải sản cao cấp bên sông Hàn, view đẹp, phục vụ chuyên nghiệp.',
                'dia_chi'             => '4 Bạch Đằng, Hải Châu, Đà Nẵng',
                'kinh_do'             => 108.2231000,
                'vi_do'               => 16.0671000,
                'gia_ve'              => 350000,
                'gio_mo_cua'          => '10:00',
                'gio_dong_cua'        => '22:30',
                'danh_gia_trung_binh' => 4.7,
                'loai_dia_diem'       => 'Hải sản',
                'image'               => 'https://madamelan.vn/storage/mg-5220.jpg',
            ],
            [
                'ten_dia_diem'        => 'Phở Thìn Đà Nẵng',
                'mo_ta'               => 'Phở bò Hà Nội chuẩn vị, nước dùng trong vắt, thịt mềm và thơm.',
                'dia_chi'             => '88 Điện Biên Phủ, Hải Châu, Đà Nẵng',
                'kinh_do'             => 108.2188000,
                'vi_do'               => 16.0713000,
                'gia_ve'              => 55000,
                'gio_mo_cua'          => '06:00',
                'gio_dong_cua'        => '22:00',
                'danh_gia_trung_binh' => 4.6,
                'loai_dia_diem'       => 'Quán ăn',
                'image'               => 'https://lookaside.fbsbx.com/lookaside/crawler/media/?media_id=803526569100472',
            ],
            [
                'ten_dia_diem'        => 'Cơm gà Bà Buội',
                'mo_ta'               => 'Cơm gà xứ Quảng nổi tiếng nhất Đà Nẵng, gà ta thả vườn, cơm dẻo thơm.',
                'dia_chi'             => '49 Nguyễn Đình Chiểu, Hải Châu, Đà Nẵng',
                'kinh_do'             => 108.2213000,
                'vi_do'               => 16.0635000,
                'gia_ve'              => 60000,
                'gio_mo_cua'          => '09:00',
                'gio_dong_cua'        => '21:30',
                'danh_gia_trung_binh' => 4.8,
                'loai_dia_diem'       => 'Quán ăn',
                'image'               => 'https://dynamic-media-cdn.tripadvisor.com/media/photo-o/2f/ab/ee/00/caption.jpg?w=1100&h=1100&s=1',
            ],
            [
                'ten_dia_diem'        => 'Bánh canh chả cá Thu Phương',
                'mo_ta'               => 'Quán bánh canh chả cá ngon nổi tiếng, đông khách từ sáng sớm.',
                'dia_chi'             => '89 Trưng Nữ Vương, Hải Châu, Đà Nẵng',
                'kinh_do'             => 108.2198765,
                'vi_do'               => 16.0592341,
                'gia_ve'              => 35000,
                'gio_mo_cua'          => '06:30',
                'gio_dong_cua'        => '12:00',
                'danh_gia_trung_binh' => 4.7,
                'loai_dia_diem'       => 'Quán ăn',
                'image'               => 'https://down-vn.img.susercontent.com/vn-11134259-7r98o-lwc9r6llplnd56@resize_ss640x400',
            ],
            [
                'ten_dia_diem'        => 'Kem Trân Châu Mì',
                'mo_ta'               => 'Quán ăn vặt phong cách châu Á sáng tạo, topping đa dạng.',
                'dia_chi'             => '121 Trần Phú, Hải Châu, Đà Nẵng',
                'kinh_do'             => 108.2232000,
                'vi_do'               => 16.0668000,
                'gia_ve'              => 30000,
                'gio_mo_cua'          => '09:00',
                'gio_dong_cua'        => '23:00',
                'danh_gia_trung_binh' => 4.5,
                'loai_dia_diem'       => 'Ăn vặt',
                'image'               => 'https://lookaside.fbsbx.com/lookaside/crawler/media/?media_id=3358059707696286',
            ],
            [
                'ten_dia_diem'        => 'Làng Hải Sản Bình Dân',
                'mo_ta'               => 'Khu ẩm thực hải sản bình dân ngoài trời, gần biển Mỹ Khê.',
                'dia_chi'             => 'Phạm Văn Đồng, Sơn Trà, Đà Nẵng',
                'kinh_do'             => 108.2463000,
                'vi_do'               => 16.0562000,
                'gia_ve'              => 200000,
                'gio_mo_cua'          => '10:00',
                'gio_dong_cua'        => '23:00',
                'danh_gia_trung_binh' => 4.5,
                'loai_dia_diem'       => 'Hải sản',
                'image'               => 'https://www.tiktok.com/api/img/?itemId=7456384500826705172&location=0&aid=1988',
            ],
            [
                'ten_dia_diem'        => 'Beer Club 54',
                'mo_ta'               => 'Beer club hiện đại, nhạc sống, phù hợp nhóm bạn giải trí tối.',
                'dia_chi'             => '72 Bạch Đằng, Hải Châu, Đà Nẵng',
                'kinh_do'             => 108.2230000,
                'vi_do'               => 16.0672000,
                'gia_ve'              => 200000,
                'gio_mo_cua'          => '18:00',
                'gio_dong_cua'        => '02:00',
                'danh_gia_trung_binh' => 4.5,
                'loai_dia_diem'       => 'Quán nhậu',
                'image'               => 'https://chillvietnam.com/wp-content/uploads/2022/10/marine-sky-bar-dia-diem-de-high-chill-1666302237-1024x471.jpg',
            ],
        ];

        // 4. Insert địa điểm và gắn danh mục
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

            // Gắn vào danh mục ẩm thực tổng
            ChiTietDanhMuc::firstOrCreate([
                'id_danh_muc' => $danhMucAmThuc->id,
                'id_dia_diem' => $diaDiem->id,
            ]);

            // Gắn vào danh mục con (loại cụ thể)
            if (isset($subCategories[$loai])) {
                ChiTietDanhMuc::firstOrCreate([
                    'id_danh_muc' => $subCategories[$loai]->id,
                    'id_dia_diem' => $diaDiem->id,
                ]);
            }
        }
    }
}
