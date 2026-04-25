<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DiaDiem;
use App\Models\DanhMuc;
use App\Models\ChiTietDanhMuc;

class ThemDiaDiemSeeder extends Seeder
{
    public function run(): void
    {
        $danhMucAmThuc = DanhMuc::firstOrCreate(['ten_danh_muc' => 'Ẩm thực']);
        $danhMucCheckin = DanhMuc::firstOrCreate(['ten_danh_muc' => 'Check-in']);
        $danhMucGiaiTri = DanhMuc::firstOrCreate(['ten_danh_muc' => 'Giải trí']);
        $danhMucTamLinh = DanhMuc::firstOrCreate(['ten_danh_muc' => 'Tâm linh']);

        $places = [
            // Ẩm thực - Hải Sản
            ['ten_dia_diem' => 'Hải sản Năm Đảnh', 'loai_dia_diem' => 'Hải sản', 'mo_ta' => 'Quán hải sản nổi tiếng Đà Nẵng, ngon bổ rẻ.', 'dia_chi' => '139/59/38 Trần Quang Khải, Thọ Quang, Sơn Trà', 'vi_do' => 16.1082, 'kinh_do' => 108.2435, 'gia_ve' => 200000, 'gio_mo_cua' => '10:00', 'gio_dong_cua' => '22:00', 'danh_gia' => 4.5, 'danh_muc' => $danhMucAmThuc],
            ['ten_dia_diem' => 'Hải sản Bé Mặn', 'loai_dia_diem' => 'Hải sản', 'mo_ta' => 'Hải sản tươi sống, view biển mát mẻ.', 'dia_chi' => 'Lô 11 Võ Nguyên Giáp, Mân Thái, Sơn Trà', 'vi_do' => 16.0841, 'kinh_do' => 108.2467, 'gia_ve' => 300000, 'gio_mo_cua' => '09:00', 'gio_dong_cua' => '23:00', 'danh_gia' => 4.6, 'danh_muc' => $danhMucAmThuc],
            ['ten_dia_diem' => 'Hải sản Bà Thôi', 'loai_dia_diem' => 'Hải sản', 'mo_ta' => 'Quán hải sản lâu đời, chất lượng uy tín.', 'dia_chi' => '96 Lê Đình Dương, Hải Châu', 'vi_do' => 16.0621, 'kinh_do' => 108.2195, 'gia_ve' => 250000, 'gio_mo_cua' => '10:00', 'gio_dong_cua' => '22:00', 'danh_gia' => 4.3, 'danh_muc' => $danhMucAmThuc],
            ['ten_dia_diem' => 'Hải sản Cua Biển Quán', 'loai_dia_diem' => 'Hải sản', 'mo_ta' => 'Chuyên các món cua và hải sản cao cấp.', 'dia_chi' => 'Lô 10 Võ Nguyên Giáp, Sơn Trà', 'vi_do' => 16.0712, 'kinh_do' => 108.2461, 'gia_ve' => 350000, 'gio_mo_cua' => '10:00', 'gio_dong_cua' => '23:00', 'danh_gia' => 4.4, 'danh_muc' => $danhMucAmThuc],
            ['ten_dia_diem' => 'Nhà hàng Hải sản Mỹ Hạnh', 'loai_dia_diem' => 'Hải sản', 'mo_ta' => 'View biển Mỹ Khê tuyệt đẹp, phục vụ chuyên nghiệp.', 'dia_chi' => 'Lô 18 Võ Nguyên Giáp, Sơn Trà', 'vi_do' => 16.0601, 'kinh_do' => 108.2465, 'gia_ve' => 400000, 'gio_mo_cua' => '10:00', 'gio_dong_cua' => '23:30', 'danh_gia' => 4.5, 'danh_muc' => $danhMucAmThuc],
            ['ten_dia_diem' => 'Quán Lão Đại', 'loai_dia_diem' => 'Hải sản', 'mo_ta' => 'Nhậu hải sản bình dân, không gian rộng rãi.', 'dia_chi' => '10 Phạm Tứ, Cẩm Lệ', 'vi_do' => 16.0245, 'kinh_do' => 108.2031, 'gia_ve' => 150000, 'gio_mo_cua' => '15:00', 'gio_dong_cua' => '23:00', 'danh_gia' => 4.2, 'danh_muc' => $danhMucAmThuc],

            // Ẩm thực - Đặc sản
            ['ten_dia_diem' => 'Mì Quảng Bà Mua', 'loai_dia_diem' => 'Đặc sản', 'mo_ta' => 'Thương hiệu mì quảng nổi danh Đà Nẵng.', 'dia_chi' => '19-21 Trần Bình Trọng, Hải Châu', 'vi_do' => 16.0592, 'kinh_do' => 108.2165, 'gia_ve' => 45000, 'gio_mo_cua' => '06:30', 'gio_dong_cua' => '22:00', 'danh_gia' => 4.4, 'danh_muc' => $danhMucAmThuc],
            ['ten_dia_diem' => 'Mì Quảng 1A', 'loai_dia_diem' => 'Đặc sản', 'mo_ta' => 'Mì quảng chuẩn vị truyền thống.', 'dia_chi' => '1A Hải Phòng, Hải Châu', 'vi_do' => 16.0718, 'kinh_do' => 108.2205, 'gia_ve' => 40000, 'gio_mo_cua' => '06:00', 'gio_dong_cua' => '21:00', 'danh_gia' => 4.3, 'danh_muc' => $danhMucAmThuc],
            ['ten_dia_diem' => 'Bánh tráng cuốn thịt heo Trần', 'loai_dia_diem' => 'Đặc sản', 'mo_ta' => 'Đặc sản bánh tráng cuốn thịt heo hai đầu da.', 'dia_chi' => '4 Lê Duẩn, Hải Châu', 'vi_do' => 16.0715, 'kinh_do' => 108.2241, 'gia_ve' => 120000, 'gio_mo_cua' => '09:00', 'gio_dong_cua' => '22:00', 'danh_gia' => 4.5, 'danh_muc' => $danhMucAmThuc],
            ['ten_dia_diem' => 'Bánh xèo Bà Dưỡng', 'loai_dia_diem' => 'Đặc sản', 'mo_ta' => 'Bánh xèo miền Trung giòn rụm, nước chấm ngon đỉnh.', 'dia_chi' => 'K280/23 Hoàng Diệu, Hải Châu', 'vi_do' => 16.0583, 'kinh_do' => 108.2152, 'gia_ve' => 70000, 'gio_mo_cua' => '09:30', 'gio_dong_cua' => '21:00', 'danh_gia' => 4.6, 'danh_muc' => $danhMucAmThuc],
            ['ten_dia_diem' => 'Bê thui Cầu Mống Mười', 'loai_dia_diem' => 'Đặc sản', 'mo_ta' => 'Bê thui thơm mềm, cuốn cùng rau sống chấm mắm nêm.', 'dia_chi' => '138 Nguyễn Tri Phương, Thanh Khê', 'vi_do' => 16.0612, 'kinh_do' => 108.2045, 'gia_ve' => 150000, 'gio_mo_cua' => '09:00', 'gio_dong_cua' => '22:30', 'danh_gia' => 4.4, 'danh_muc' => $danhMucAmThuc],
            ['ten_dia_diem' => 'Cơm niêu Nhà Đỏ', 'loai_dia_diem' => 'Cơm', 'mo_ta' => 'Cơm niêu đập đậm chất truyền thống Việt.', 'dia_chi' => '86 Nguyễn Tri Phương, Thanh Khê', 'vi_do' => 16.0631, 'kinh_do' => 108.2040, 'gia_ve' => 120000, 'gio_mo_cua' => '10:00', 'gio_dong_cua' => '21:30', 'danh_gia' => 4.5, 'danh_muc' => $danhMucAmThuc],
            ['ten_dia_diem' => 'Cơm gà A Hải', 'loai_dia_diem' => 'Cơm', 'mo_ta' => 'Cơm gà quay giòn ngon nổi tiếng.', 'dia_chi' => '96 Phan Châu Trinh, Hải Châu', 'vi_do' => 16.0645, 'kinh_do' => 108.2221, 'gia_ve' => 55000, 'gio_mo_cua' => '08:00', 'gio_dong_cua' => '22:00', 'danh_gia' => 4.3, 'danh_muc' => $danhMucAmThuc],
            ['ten_dia_diem' => 'Bún chả cá Ông Tạ', 'loai_dia_diem' => 'Bún', 'mo_ta' => 'Tô bún chả cá nóng hổi thơm phức.', 'dia_chi' => '113A Nguyễn Chí Thanh, Hải Châu', 'vi_do' => 16.0682, 'kinh_do' => 108.2215, 'gia_ve' => 40000, 'gio_mo_cua' => '06:00', 'gio_dong_cua' => '22:00', 'danh_gia' => 4.5, 'danh_muc' => $danhMucAmThuc],
            ['ten_dia_diem' => 'Bún mắm Ngọc', 'loai_dia_diem' => 'Bún', 'mo_ta' => 'Bún mắm nêm thịt luộc, tai heo cực ngon.', 'dia_chi' => '20 Đoàn Thị Điểm, Hải Châu', 'vi_do' => 16.0685, 'kinh_do' => 108.2155, 'gia_ve' => 35000, 'gio_mo_cua' => '06:00', 'gio_dong_cua' => '22:00', 'danh_gia' => 4.3, 'danh_muc' => $danhMucAmThuc],
            
            // Ăn vặt - Kem, Chè, Trà sữa
            ['ten_dia_diem' => 'Chè Xuân Trang', 'loai_dia_diem' => 'Chè', 'mo_ta' => 'Quán chè lâu đời, đông khách, có cả bò khô.', 'dia_chi' => '31 Lê Duẩn, Hải Châu', 'vi_do' => 16.0725, 'kinh_do' => 108.2238, 'gia_ve' => 20000, 'gio_mo_cua' => '09:00', 'gio_dong_cua' => '22:00', 'danh_gia' => 4.4, 'danh_muc' => $danhMucAmThuc],
            ['ten_dia_diem' => 'Chè Sầu Liên', 'loai_dia_diem' => 'Chè', 'mo_ta' => 'Chè sầu riêng đặc sản mang về.', 'dia_chi' => '189 Hoàng Diệu, Hải Châu', 'vi_do' => 16.0621, 'kinh_do' => 108.2173, 'gia_ve' => 30000, 'gio_mo_cua' => '08:00', 'gio_dong_cua' => '22:00', 'danh_gia' => 4.6, 'danh_muc' => $danhMucAmThuc],
            ['ten_dia_diem' => 'Kem bơ Cô Vân', 'loai_dia_diem' => 'Kem', 'mo_ta' => 'Kem bơ ngon trứ danh trong Chợ Bắc Mỹ An.', 'dia_chi' => 'Chợ Bắc Mỹ An, Ngũ Hành Sơn', 'vi_do' => 16.0401, 'kinh_do' => 108.2391, 'gia_ve' => 25000, 'gio_mo_cua' => '07:00', 'gio_dong_cua' => '18:00', 'danh_gia' => 4.7, 'danh_muc' => $danhMucAmThuc],
            ['ten_dia_diem' => 'Trà sữa Phúc Long - Indochina', 'loai_dia_diem' => 'Trà sữa', 'mo_ta' => 'Trà đậm vị, không gian hiện đại.', 'dia_chi' => '74 Bạch Đằng, Hải Châu', 'vi_do' => 16.0691, 'kinh_do' => 108.2245, 'gia_ve' => 55000, 'gio_mo_cua' => '07:00', 'gio_dong_cua' => '22:30', 'danh_gia' => 4.5, 'danh_muc' => $danhMucAmThuc],
            ['ten_dia_diem' => 'Trà sữa Koi Thé', 'loai_dia_diem' => 'Trà sữa', 'mo_ta' => 'Lục trà macchiato cực ngon.', 'dia_chi' => 'Vincom Center, Ngô Quyền, Sơn Trà', 'vi_do' => 16.0710, 'kinh_do' => 108.2301, 'gia_ve' => 60000, 'gio_mo_cua' => '09:00', 'gio_dong_cua' => '22:00', 'danh_gia' => 4.6, 'danh_muc' => $danhMucAmThuc],
            ['ten_dia_diem' => 'Bánh tráng kẹp Dì Hoa', 'loai_dia_diem' => 'Ăn vặt', 'mo_ta' => 'Bánh tráng nướng kẹp pate siêu ngon.', 'dia_chi' => '62A/2A Núi Thành, Hải Châu', 'vi_do' => 16.0505, 'kinh_do' => 108.2210, 'gia_ve' => 30000, 'gio_mo_cua' => '14:30', 'gio_dong_cua' => '21:30', 'danh_gia' => 4.5, 'danh_muc' => $danhMucAmThuc],

            // Quán Chay
            ['ten_dia_diem' => 'Nhà hàng Chay Liên Hoa', 'loai_dia_diem' => 'Quán chay', 'mo_ta' => 'Ẩm thực chay thanh tịnh, phong phú.', 'dia_chi' => '49 Lê Hồng Phong, Hải Châu', 'vi_do' => 16.0655, 'kinh_do' => 108.2198, 'gia_ve' => 80000, 'gio_mo_cua' => '07:00', 'gio_dong_cua' => '21:30', 'danh_gia' => 4.5, 'danh_muc' => $danhMucAmThuc],
            ['ten_dia_diem' => 'Quán Chay Thúy', 'loai_dia_diem' => 'Quán chay', 'mo_ta' => 'Quán chay bình dân, ngon miệng.', 'dia_chi' => '122 Hoàng Diệu, Hải Châu', 'vi_do' => 16.0641, 'kinh_do' => 108.2165, 'gia_ve' => 30000, 'gio_mo_cua' => '06:30', 'gio_dong_cua' => '21:00', 'danh_gia' => 4.3, 'danh_muc' => $danhMucAmThuc],
            ['ten_dia_diem' => 'AnS Vegetarian', 'loai_dia_diem' => 'Quán chay', 'mo_ta' => 'Nhà hàng chay hiện đại, fusion.', 'dia_chi' => '169F Trưng Nữ Vương, Hải Châu', 'vi_do' => 16.0542, 'kinh_do' => 108.2161, 'gia_ve' => 150000, 'gio_mo_cua' => '08:00', 'gio_dong_cua' => '22:00', 'danh_gia' => 4.7, 'danh_muc' => $danhMucAmThuc],

            // Ăn sáng (Phở, Bún, Bánh mì)
            ['ten_dia_diem' => 'Bánh mì Phượng (Hội An)', 'loai_dia_diem' => 'Bánh mì', 'mo_ta' => 'Bánh mì ngon nhất thế giới.', 'dia_chi' => '2B Phan Châu Trinh, Hội An', 'vi_do' => 15.8778, 'kinh_do' => 108.3283, 'gia_ve' => 35000, 'gio_mo_cua' => '06:00', 'gio_dong_cua' => '21:00', 'danh_gia' => 4.6, 'danh_muc' => $danhMucAmThuc],
            ['ten_dia_diem' => 'Bánh mì Madam Khánh', 'loai_dia_diem' => 'Bánh mì', 'mo_ta' => 'The Banh Mi Queen.', 'dia_chi' => '115 Trần Cao Vân, Hội An', 'vi_do' => 15.8812, 'kinh_do' => 108.3268, 'gia_ve' => 30000, 'gio_mo_cua' => '06:30', 'gio_dong_cua' => '19:00', 'danh_gia' => 4.7, 'danh_muc' => $danhMucAmThuc],
            ['ten_dia_diem' => 'Phở Thìn Lò Đúc - Đà Nẵng', 'loai_dia_diem' => 'Phở', 'mo_ta' => 'Phở tái lăn đậm đà.', 'dia_chi' => '156 Gò Nảy 7, Liên Chiểu', 'vi_do' => 16.0610, 'kinh_do' => 108.1560, 'gia_ve' => 60000, 'gio_mo_cua' => '06:00', 'gio_dong_cua' => '13:00', 'danh_gia' => 4.3, 'danh_muc' => $danhMucAmThuc],
            ['ten_dia_diem' => 'Phở 29', 'loai_dia_diem' => 'Phở', 'mo_ta' => 'Phở Bắc chuẩn vị.', 'dia_chi' => '35 Trần Quốc Toản, Hải Châu', 'vi_do' => 16.0682, 'kinh_do' => 108.2225, 'gia_ve' => 50000, 'gio_mo_cua' => '06:00', 'gio_dong_cua' => '22:00', 'danh_gia' => 4.4, 'danh_muc' => $danhMucAmThuc],

            // Check-in / Ngắm cảnh / Cầu
            ['ten_dia_diem' => 'Bãi cỏ lau Thọ Quang', 'loai_dia_diem' => 'Ngắm cảnh', 'mo_ta' => 'Bãi cỏ lau rộng lớn, check in sống ảo lãng mạn.', 'dia_chi' => 'Thọ Quang, Sơn Trà', 'vi_do' => 16.1101, 'kinh_do' => 108.2422, 'gia_ve' => 0, 'gio_mo_cua' => '06:00', 'gio_dong_cua' => '18:00', 'danh_gia' => 4.3, 'danh_muc' => $danhMucCheckin],
            ['ten_dia_diem' => 'Hồ Xanh', 'loai_dia_diem' => 'Ngắm cảnh', 'mo_ta' => 'Hồ nước trong xanh tĩnh lặng ngay chân núi Sơn Trà.', 'dia_chi' => 'Hoàng Sa, Thọ Quang, Sơn Trà', 'vi_do' => 16.1185, 'kinh_do' => 108.2562, 'gia_ve' => 0, 'gio_mo_cua' => '06:00', 'gio_dong_cua' => '18:00', 'danh_gia' => 4.4, 'danh_muc' => $danhMucCheckin],
            ['ten_dia_diem' => 'Cầu Trần Thị Lý', 'loai_dia_diem' => 'Cầu nổi tiếng', 'mo_ta' => 'Cầu dây văng kiến trúc cánh buồm.', 'dia_chi' => 'Trần Thị Lý, Hải Châu', 'vi_do' => 16.0505, 'kinh_do' => 108.2325, 'gia_ve' => 0, 'gio_mo_cua' => '00:00', 'gio_dong_cua' => '23:59', 'danh_gia' => 4.5, 'danh_muc' => $danhMucCheckin],
            ['ten_dia_diem' => 'Cầu Thuận Phước', 'loai_dia_diem' => 'Cầu nổi tiếng', 'mo_ta' => 'Cầu treo dây võng dài nhất Việt Nam.', 'dia_chi' => 'Thuận Phước, Hải Châu', 'vi_do' => 16.0945, 'kinh_do' => 108.2201, 'gia_ve' => 0, 'gio_mo_cua' => '00:00', 'gio_dong_cua' => '23:59', 'danh_gia' => 4.6, 'danh_muc' => $danhMucCheckin],
            ['ten_dia_diem' => 'Đèo Hải Vân', 'loai_dia_diem' => 'Ngắm cảnh', 'mo_ta' => 'Thiên hạ đệ nhất hùng quan.', 'dia_chi' => 'Hải Vân, Liên Chiểu', 'vi_do' => 16.1852, 'kinh_do' => 108.1465, 'gia_ve' => 0, 'gio_mo_cua' => '05:00', 'gio_dong_cua' => '18:00', 'danh_gia' => 4.8, 'danh_muc' => $danhMucCheckin],
            ['ten_dia_diem' => 'Cây đa ngàn năm', 'loai_dia_diem' => 'Tự nhiên', 'mo_ta' => 'Cây đa khổng lồ ở bán đảo Sơn Trà.', 'dia_chi' => 'Bán đảo Sơn Trà', 'vi_do' => 16.1365, 'kinh_do' => 108.2865, 'gia_ve' => 0, 'gio_mo_cua' => '06:00', 'gio_dong_cua' => '18:00', 'danh_gia' => 4.4, 'danh_muc' => $danhMucCheckin],
            ['ten_dia_diem' => 'Làng bích họa Tam Thanh', 'loai_dia_diem' => 'Ngắm cảnh', 'mo_ta' => 'Làng chài với những bức tranh tường rực rỡ.', 'dia_chi' => 'Tam Thanh, Tam Kỳ, Quảng Nam', 'vi_do' => 15.6023, 'kinh_do' => 108.5301, 'gia_ve' => 0, 'gio_mo_cua' => '06:00', 'gio_dong_cua' => '18:00', 'danh_gia' => 4.5, 'danh_muc' => $danhMucCheckin],

            // Cafe Check-in
            ['ten_dia_diem' => 'Nia Coffee', 'loai_dia_diem' => 'Cafe', 'mo_ta' => 'Quán cà phê sinh thái, không gian xanh mát.', 'dia_chi' => '3/12 Phan Thành Tài, Hải Châu', 'vi_do' => 16.0481, 'kinh_do' => 108.2201, 'gia_ve' => 40000, 'gio_mo_cua' => '07:00', 'gio_dong_cua' => '22:00', 'danh_gia' => 4.5, 'danh_muc' => $danhMucCheckin],
            ['ten_dia_diem' => 'Danang Souvenirs & Cafe', 'loai_dia_diem' => 'Cafe', 'mo_ta' => 'Mua đồ lưu niệm và uống cafe view sông Hàn.', 'dia_chi' => '34 Bạch Đằng, Hải Châu', 'vi_do' => 16.0745, 'kinh_do' => 108.2241, 'gia_ve' => 50000, 'gio_mo_cua' => '07:00', 'gio_dong_cua' => '22:30', 'danh_gia' => 4.4, 'danh_muc' => $danhMucCheckin],
            ['ten_dia_diem' => 'Cộng Cà Phê - Bạch Đằng', 'loai_dia_diem' => 'Cafe', 'mo_ta' => 'Cafe dừa ngon, phong cách bao cấp.', 'dia_chi' => '96-98 Bạch Đằng, Hải Châu', 'vi_do' => 16.0682, 'kinh_do' => 108.2248, 'gia_ve' => 60000, 'gio_mo_cua' => '07:00', 'gio_dong_cua' => '23:00', 'danh_gia' => 4.6, 'danh_muc' => $danhMucCheckin],
            ['ten_dia_diem' => 'Aroi Dessert Cafe', 'loai_dia_diem' => 'Cafe', 'mo_ta' => 'Bánh gấu dễ thương, view bờ sông.', 'dia_chi' => '124 Bạch Đằng, Hải Châu', 'vi_do' => 16.0661, 'kinh_do' => 108.2245, 'gia_ve' => 50000, 'gio_mo_cua' => '08:00', 'gio_dong_cua' => '23:00', 'danh_gia' => 4.3, 'danh_muc' => $danhMucCheckin],
            ['ten_dia_diem' => 'Sơn Trà Marina', 'loai_dia_diem' => 'Cafe', 'mo_ta' => 'View Santorini bên bờ biển Đà Nẵng.', 'dia_chi' => 'Đường Hồ Xanh, Thọ Quang, Sơn Trà', 'vi_do' => 16.1152, 'kinh_do' => 108.2521, 'gia_ve' => 80000, 'gio_mo_cua' => '07:00', 'gio_dong_cua' => '22:00', 'danh_gia' => 4.5, 'danh_muc' => $danhMucCheckin],

            // Tâm linh
            ['ten_dia_diem' => 'Chùa Linh Ứng - Bà Nà', 'loai_dia_diem' => 'Chùa', 'mo_ta' => 'Chùa linh thiêng nằm trên đỉnh Bà Nà.', 'dia_chi' => 'Bà Nà Hills, Hòa Vang', 'vi_do' => 15.9985, 'kinh_do' => 107.9892, 'gia_ve' => 0, 'gio_mo_cua' => '08:00', 'gio_dong_cua' => '17:30', 'danh_gia' => 4.8, 'danh_muc' => $danhMucTamLinh],
            ['ten_dia_diem' => 'Chùa Linh Ứng - Ngũ Hành Sơn', 'loai_dia_diem' => 'Chùa', 'mo_ta' => 'Ngôi chùa cổ kính trong quần thể danh thắng Ngũ Hành Sơn.', 'dia_chi' => 'Ngũ Hành Sơn', 'vi_do' => 16.0045, 'kinh_do' => 108.2635, 'gia_ve' => 0, 'gio_mo_cua' => '07:00', 'gio_dong_cua' => '17:30', 'danh_gia' => 4.7, 'danh_muc' => $danhMucTamLinh],
            ['ten_dia_diem' => 'Chùa Nam Sơn', 'loai_dia_diem' => 'Chùa', 'mo_ta' => 'Ngôi chùa có kiến trúc tuyệt đẹp ở Hòa Vang.', 'dia_chi' => 'Hòa Châu, Hòa Vang', 'vi_do' => 15.9961, 'kinh_do' => 108.2105, 'gia_ve' => 0, 'gio_mo_cua' => '08:00', 'gio_dong_cua' => '17:00', 'danh_gia' => 4.8, 'danh_muc' => $danhMucTamLinh],
            ['ten_dia_diem' => 'Đền thờ Thoại Ngọc Hầu', 'loai_dia_diem' => 'Đền', 'mo_ta' => 'Đền thờ danh nhân lịch sử.', 'dia_chi' => 'Sơn Trà, Đà Nẵng', 'vi_do' => 16.0910, 'kinh_do' => 108.2341, 'gia_ve' => 0, 'gio_mo_cua' => '07:30', 'gio_dong_cua' => '17:00', 'danh_gia' => 4.3, 'danh_muc' => $danhMucTamLinh],
            ['ten_dia_diem' => 'Đình làng Hải Châu', 'loai_dia_diem' => 'Di tích', 'mo_ta' => 'Đình làng cổ xưa nhất tại Đà Nẵng.', 'dia_chi' => 'Tổ 6, phường Hải Châu 1, Hải Châu', 'vi_do' => 16.0695, 'kinh_do' => 108.2201, 'gia_ve' => 0, 'gio_mo_cua' => '08:00', 'gio_dong_cua' => '17:00', 'danh_gia' => 4.4, 'danh_muc' => $danhMucTamLinh],

            // Giải trí, Bar, Pub, Chợ đêm, Acoustic
            ['ten_dia_diem' => 'Chợ đêm Sơn Trà', 'loai_dia_diem' => 'Chợ đêm', 'mo_ta' => 'Chợ đêm ngay đầu cầu Rồng, sầm uất.', 'dia_chi' => 'Mai Hắc Đế, Sơn Trà', 'vi_do' => 16.0631, 'kinh_do' => 108.2310, 'gia_ve' => 100000, 'gio_mo_cua' => '18:00', 'gio_dong_cua' => '23:30', 'danh_gia' => 4.5, 'danh_muc' => $danhMucGiaiTri],
            ['ten_dia_diem' => 'Helio Center', 'loai_dia_diem' => 'Khu vui chơi', 'mo_ta' => 'Chợ đêm Helio và khu giải trí trong nhà.', 'dia_chi' => 'Đường 2/9, Hải Châu', 'vi_do' => 16.0385, 'kinh_do' => 108.2255, 'gia_ve' => 150000, 'gio_mo_cua' => '17:00', 'gio_dong_cua' => '22:30', 'danh_gia' => 4.6, 'danh_muc' => $danhMucGiaiTri],
            ['ten_dia_diem' => 'Sky36 Bar', 'loai_dia_diem' => 'Bar', 'mo_ta' => 'Bar tầng thượng cao nhất Đà Nẵng.', 'dia_chi' => '36 Bạch Đằng, Hải Châu', 'vi_do' => 16.0741, 'kinh_do' => 108.2241, 'gia_ve' => 300000, 'gio_mo_cua' => '18:00', 'gio_dong_cua' => '02:00', 'danh_gia' => 4.4, 'danh_muc' => $danhMucGiaiTri],
            ['ten_dia_diem' => 'OQ Lounge Pub', 'loai_dia_diem' => 'Pub', 'mo_ta' => 'Pub sôi động nổi tiếng Đà Nẵng.', 'dia_chi' => '18 Bạch Đằng, Hải Châu', 'vi_do' => 16.0765, 'kinh_do' => 108.2238, 'gia_ve' => 200000, 'gio_mo_cua' => '19:00', 'gio_dong_cua' => '02:00', 'danh_gia' => 4.5, 'danh_muc' => $danhMucGiaiTri],
            ['ten_dia_diem' => 'On The Radio Bar', 'loai_dia_diem' => 'Acoustic', 'mo_ta' => 'Quán bar acoustic chất lượng nhạc sống.', 'dia_chi' => '76 Thái Phiên, Hải Châu', 'vi_do' => 16.0645, 'kinh_do' => 108.2221, 'gia_ve' => 100000, 'gio_mo_cua' => '19:00', 'gio_dong_cua' => '01:00', 'danh_gia' => 4.7, 'danh_muc' => $danhMucGiaiTri],
            ['ten_dia_diem' => 'Phố đi bộ An Thượng', 'loai_dia_diem' => 'Phố đi bộ', 'mo_ta' => 'Khu phố Tây sầm uất với nhiều pub và nhà hàng.', 'dia_chi' => 'An Thượng, Ngũ Hành Sơn', 'vi_do' => 16.0501, 'kinh_do' => 108.2451, 'gia_ve' => 150000, 'gio_mo_cua' => '18:00', 'gio_dong_cua' => '23:59', 'danh_gia' => 4.5, 'danh_muc' => $danhMucGiaiTri],
            ['ten_dia_diem' => 'Mikazuki Water Park', 'loai_dia_diem' => 'Công viên nước', 'mo_ta' => 'Công viên nước suối khoáng nóng phong cách Nhật Bản.', 'dia_chi' => 'Khu du lịch Xuân Thiều, Liên Chiểu', 'vi_do' => 16.1015, 'kinh_do' => 108.1325, 'gia_ve' => 350000, 'gio_mo_cua' => '09:00', 'gio_dong_cua' => '20:00', 'danh_gia' => 4.6, 'danh_muc' => $danhMucGiaiTri],
            ['ten_dia_diem' => 'Suối Thần Tài', 'loai_dia_diem' => 'Khu vui chơi', 'mo_ta' => 'Khu du lịch suối khoáng nóng, vui chơi giải trí.', 'dia_chi' => 'Hòa Phú, Hòa Vang', 'vi_do' => 15.9621, 'kinh_do' => 107.9865, 'gia_ve' => 450000, 'gio_mo_cua' => '08:00', 'gio_dong_cua' => '18:00', 'danh_gia' => 4.7, 'danh_muc' => $danhMucGiaiTri],
            ['ten_dia_diem' => 'VinWonders Nam Hội An', 'loai_dia_diem' => 'Khu vui chơi', 'mo_ta' => 'Tổ hợp vui chơi giải trí, bảo tồn văn hóa, safari.', 'dia_chi' => 'Bình Minh, Thăng Bình, Quảng Nam', 'vi_do' => 15.7531, 'kinh_do' => 108.4125, 'gia_ve' => 600000, 'gio_mo_cua' => '09:00', 'gio_dong_cua' => '20:00', 'danh_gia' => 4.8, 'danh_muc' => $danhMucGiaiTri],

            // Biển
            ['ten_dia_diem' => 'Bãi biển Tiên Sa', 'loai_dia_diem' => 'Biển', 'mo_ta' => 'Bãi biển khuất gió, yên tĩnh.', 'dia_chi' => 'Sơn Trà, Đà Nẵng', 'vi_do' => 16.1215, 'kinh_do' => 108.2755, 'gia_ve' => 10000, 'gio_mo_cua' => '06:00', 'gio_dong_cua' => '18:00', 'danh_gia' => 4.3, 'danh_muc' => $danhMucCheckin],
            ['ten_dia_diem' => 'Bãi tắm Mân Thái', 'loai_dia_diem' => 'Biển', 'mo_ta' => 'Bãi tắm địa phương, có thể xem kéo lưới.', 'dia_chi' => 'Mân Thái, Sơn Trà', 'vi_do' => 16.0885, 'kinh_do' => 108.2461, 'gia_ve' => 0, 'gio_mo_cua' => '00:00', 'gio_dong_cua' => '23:59', 'danh_gia' => 4.4, 'danh_muc' => $danhMucCheckin],
            ['ten_dia_diem' => 'Bãi Obama (Bãi Đa)', 'loai_dia_diem' => 'Biển', 'mo_ta' => 'Bãi đá hoang sơ có cầu gỗ check-in đẹp.', 'dia_chi' => 'Bán đảo Sơn Trà', 'vi_do' => 16.1082, 'kinh_do' => 108.2751, 'gia_ve' => 0, 'gio_mo_cua' => '07:00', 'gio_dong_cua' => '17:30', 'danh_gia' => 4.5, 'danh_muc' => $danhMucCheckin],

            // Bổ sung 19 địa điểm nữa
            ['ten_dia_diem' => 'Bún bò Huế Bà Thương', 'loai_dia_diem' => 'Bún', 'mo_ta' => 'Bún bò ngon nức tiếng, đậm vị Huế.', 'dia_chi' => '23 Trần Quốc Toản, Hải Châu', 'vi_do' => 16.0688, 'kinh_do' => 108.2223, 'gia_ve' => 50000, 'gio_mo_cua' => '06:00', 'gio_dong_cua' => '13:00', 'danh_gia' => 4.5, 'danh_muc' => $danhMucAmThuc],
            ['ten_dia_diem' => 'Xôi gà cô Vui', 'loai_dia_diem' => 'Xôi', 'mo_ta' => 'Xôi gà, xôi thịt chả ngon, phục vụ ăn sáng và tối.', 'dia_chi' => '55 Lê Hồng Phong, Hải Châu', 'vi_do' => 16.0651, 'kinh_do' => 108.2195, 'gia_ve' => 35000, 'gio_mo_cua' => '06:00', 'gio_dong_cua' => '22:00', 'danh_gia' => 4.4, 'danh_muc' => $danhMucAmThuc],
            ['ten_dia_diem' => 'Bánh canh Thu', 'loai_dia_diem' => 'Bún', 'mo_ta' => 'Bánh canh chả cá, xương chả nổi tiếng.', 'dia_chi' => '78 Nguyễn Chí Thanh, Hải Châu', 'vi_do' => 16.0691, 'kinh_do' => 108.2212, 'gia_ve' => 40000, 'gio_mo_cua' => '06:30', 'gio_dong_cua' => '22:00', 'danh_gia' => 4.6, 'danh_muc' => $danhMucAmThuc],
            ['ten_dia_diem' => 'Quán nhậu hải sản Thời Cổ', 'loai_dia_diem' => 'Hải sản', 'mo_ta' => 'Hải sản đồng giá 60k, ngon rẻ phù hợp nhậu nhẹt.', 'dia_chi' => '354/1 Võ Nguyên Giáp, Ngũ Hành Sơn', 'vi_do' => 16.0465, 'kinh_do' => 108.2458, 'gia_ve' => 150000, 'gio_mo_cua' => '10:30', 'gio_dong_cua' => '23:30', 'danh_gia' => 4.3, 'danh_muc' => $danhMucAmThuc],
            ['ten_dia_diem' => 'Thịt heo luộc hai đầu da Quán Mậu', 'loai_dia_diem' => 'Đặc sản', 'mo_ta' => 'Quán bánh tráng thịt heo rất được lòng dân bản địa.', 'dia_chi' => '35 Đỗ Thúc Tịnh, Cẩm Lệ', 'vi_do' => 16.0241, 'kinh_do' => 108.2081, 'gia_ve' => 80000, 'gio_mo_cua' => '08:00', 'gio_dong_cua' => '22:00', 'danh_gia' => 4.5, 'danh_muc' => $danhMucAmThuc],
            ['ten_dia_diem' => 'Chè chuối nướng cô Liên', 'loai_dia_diem' => 'Chè', 'mo_ta' => 'Chè chuối nướng thơm ngon, nóng hổi cho chiều mưa.', 'dia_chi' => 'Ngõ 384 Hoàng Diệu, Hải Châu', 'vi_do' => 16.0592, 'kinh_do' => 108.2168, 'gia_ve' => 20000, 'gio_mo_cua' => '15:00', 'gio_dong_cua' => '22:00', 'danh_gia' => 4.4, 'danh_muc' => $danhMucAmThuc],
            ['ten_dia_diem' => 'Highlands Coffee - Indochina', 'loai_dia_diem' => 'Cafe', 'mo_ta' => 'Thưởng thức cà phê với view sông Hàn từ tầng trệt Indochina.', 'dia_chi' => '74 Bạch Đằng, Hải Châu', 'vi_do' => 16.0691, 'kinh_do' => 108.2245, 'gia_ve' => 60000, 'gio_mo_cua' => '07:00', 'gio_dong_cua' => '23:00', 'danh_gia' => 4.5, 'danh_muc' => $danhMucCheckin],
            ['ten_dia_diem' => 'The Coffee House - Nguyễn Văn Linh', 'loai_dia_diem' => 'Cafe', 'mo_ta' => 'Không gian hiện đại, thích hợp làm việc và checkin.', 'dia_chi' => '461 Trần Hưng Đạo, Sơn Trà', 'vi_do' => 16.0631, 'kinh_do' => 108.2275, 'gia_ve' => 60000, 'gio_mo_cua' => '07:00', 'gio_dong_cua' => '22:30', 'danh_gia' => 4.4, 'danh_muc' => $danhMucCheckin],
            ['ten_dia_diem' => 'Retro Kitchen & Bar', 'loai_dia_diem' => 'Nhà hàng', 'mo_ta' => 'Nhà hàng kết hợp bar không gian retro sang trọng.', 'dia_chi' => '85-87 Trần Phú, Hải Châu', 'vi_do' => 16.0682, 'kinh_do' => 108.2235, 'gia_ve' => 250000, 'gio_mo_cua' => '10:00', 'gio_dong_cua' => '23:00', 'danh_gia' => 4.6, 'danh_muc' => $danhMucAmThuc],
            ['ten_dia_diem' => 'Pizza 4Ps Hoàng Văn Thụ', 'loai_dia_diem' => 'Nhà hàng', 'mo_ta' => 'Pizza kiểu Nhật ngon nổi tiếng với phô mai burrata.', 'dia_chi' => '8 Hoàng Văn Thụ, Hải Châu', 'vi_do' => 16.0625, 'kinh_do' => 108.2215, 'gia_ve' => 250000, 'gio_mo_cua' => '11:00', 'gio_dong_cua' => '22:00', 'danh_gia' => 4.8, 'danh_muc' => $danhMucAmThuc],
            ['ten_dia_diem' => 'Cà phê Nam House', 'loai_dia_diem' => 'Cafe', 'mo_ta' => 'Cà phê phong cách cổ xưa mang nét hoài niệm.', 'dia_chi' => '15/1 Lê Hồng Phong, Hải Châu', 'vi_do' => 16.0661, 'kinh_do' => 108.2201, 'gia_ve' => 40000, 'gio_mo_cua' => '07:00', 'gio_dong_cua' => '22:00', 'danh_gia' => 4.6, 'danh_muc' => $danhMucCheckin],
            ['ten_dia_diem' => 'Bảo tàng Mỹ thuật Đà Nẵng', 'loai_dia_diem' => 'Bảo tàng', 'mo_ta' => 'Nơi trưng bày các tác phẩm nghệ thuật đặc sắc của miền Trung.', 'dia_chi' => '78 Lê Duẩn, Hải Châu', 'vi_do' => 16.0718, 'kinh_do' => 108.2225, 'gia_ve' => 20000, 'gio_mo_cua' => '08:00', 'gio_dong_cua' => '17:00', 'danh_gia' => 4.5, 'danh_muc' => $danhMucCheckin],
            ['ten_dia_diem' => 'Cung thể thao Tiên Sơn', 'loai_dia_diem' => 'Kiến trúc', 'mo_ta' => 'Công trình hình chiếc đĩa bay độc đáo, thích hợp check-in.', 'dia_chi' => 'Đường 2/9, Hải Châu', 'vi_do' => 16.0351, 'kinh_do' => 108.2268, 'gia_ve' => 0, 'gio_mo_cua' => '08:00', 'gio_dong_cua' => '20:00', 'danh_gia' => 4.3, 'danh_muc' => $danhMucCheckin],
            ['ten_dia_diem' => 'Chùa Bát Nhã', 'loai_dia_diem' => 'Chùa', 'mo_ta' => 'Ngôi chùa yên bình giữa lòng thành phố.', 'dia_chi' => '176 Triệu Nữ Vương, Hải Châu', 'vi_do' => 16.0645, 'kinh_do' => 108.2165, 'gia_ve' => 0, 'gio_mo_cua' => '07:00', 'gio_dong_cua' => '18:00', 'danh_gia' => 4.4, 'danh_muc' => $danhMucTamLinh],
            ['ten_dia_diem' => 'Nhà thờ Phước Tường', 'loai_dia_diem' => 'Nhà thờ', 'mo_ta' => 'Nhà thờ giáo xứ Phước Tường kiến trúc cổ kính.', 'dia_chi' => 'Cẩm Lệ, Đà Nẵng', 'vi_do' => 16.0355, 'kinh_do' => 108.1885, 'gia_ve' => 0, 'gio_mo_cua' => '07:00', 'gio_dong_cua' => '18:00', 'danh_gia' => 4.2, 'danh_muc' => $danhMucTamLinh],
            ['ten_dia_diem' => 'Chợ hải sản Thọ Quang', 'loai_dia_diem' => 'Chợ', 'mo_ta' => 'Chợ đầu mối hải sản lớn nhất Đà Nẵng vào rạng sáng.', 'dia_chi' => '20 Vân Đồn, Sơn Trà', 'vi_do' => 16.0965, 'kinh_do' => 108.2385, 'gia_ve' => 0, 'gio_mo_cua' => '03:00', 'gio_dong_cua' => '08:00', 'danh_gia' => 4.3, 'danh_muc' => $danhMucCheckin],
            ['ten_dia_diem' => 'Apocalypse Now Bar', 'loai_dia_diem' => 'Bar', 'mo_ta' => 'Quán bar ven biển lâu đời và nổi tiếng tại Đà Nẵng.', 'dia_chi' => '51 Võ Nguyên Giáp, Ngũ Hành Sơn', 'vi_do' => 16.0521, 'kinh_do' => 108.2455, 'gia_ve' => 200000, 'gio_mo_cua' => '20:00', 'gio_dong_cua' => '02:00', 'danh_gia' => 4.1, 'danh_muc' => $danhMucGiaiTri],
            ['ten_dia_diem' => 'Nơm Bistro', 'loai_dia_diem' => 'Nhà hàng', 'mo_ta' => 'Nhà hàng phong cách lồng nơm cá, view khu Đảo Xanh cực chill.', 'dia_chi' => 'Khu biệt thự Đảo Xanh, Hải Châu', 'vi_do' => 16.0461, 'kinh_do' => 108.2255, 'gia_ve' => 200000, 'gio_mo_cua' => '09:00', 'gio_dong_cua' => '22:30', 'danh_gia' => 4.6, 'danh_muc' => $danhMucAmThuc],
            ['ten_dia_diem' => 'Bánh xèo Tôm nhảy Năm Hiền', 'loai_dia_diem' => 'Đặc sản', 'mo_ta' => 'Bánh xèo nhân tôm đất nhảy lách tách giòn ngon.', 'dia_chi' => '46 Phan Thanh, Thanh Khê', 'vi_do' => 16.0645, 'kinh_do' => 108.2091, 'gia_ve' => 70000, 'gio_mo_cua' => '10:00', 'gio_dong_cua' => '22:00', 'danh_gia' => 4.5, 'danh_muc' => $danhMucAmThuc],

            // ────── Quán nhậu / Bia hơi / Hải sản đêm ──────
            ['ten_dia_diem' => 'Quán nhậu Trần', 'loai_dia_diem' => 'Quán nhậu', 'mo_ta' => 'Quán nhậu hải sản bình dân nổi tiếng khu vực Mỹ Khê, đông khách địa phương vào buổi tối.', 'dia_chi' => '10 Hồ Xuân Hương, Sơn Trà, Đà Nẵng', 'vi_do' => 16.0572, 'kinh_do' => 108.2481, 'gia_ve' => 120000, 'gio_mo_cua' => '16:00', 'gio_dong_cua' => '23:30', 'danh_gia' => 4.4, 'danh_muc' => $danhMucGiaiTri],

            ['ten_dia_diem' => 'Bia Hơi Hà Nội - Bạch Đằng', 'loai_dia_diem' => 'Quán nhậu', 'mo_ta' => 'Quán bia hơi view sông Hàn, vừa nhậu vừa ngắm cầu Rồng lung linh. Giá bình dân, không khí sôi động.', 'dia_chi' => '150 Bạch Đằng, Hải Châu, Đà Nẵng', 'vi_do' => 16.0658, 'kinh_do' => 108.2253, 'gia_ve' => 80000, 'gio_mo_cua' => '15:00', 'gio_dong_cua' => '23:59', 'danh_gia' => 4.3, 'danh_muc' => $danhMucGiaiTri],

            ['ten_dia_diem' => 'Hải sản Phước Mỹ - Lò Đúc', 'loai_dia_diem' => 'Quán nhậu', 'mo_ta' => 'Quán hải sản nhậu bờ biển Phước Mỹ, không khí dân dã, hải sản tươi sống, giá hợp lý theo cân.', 'dia_chi' => '28 Phước Mỹ, Sơn Trà, Đà Nẵng', 'vi_do' => 16.0765, 'kinh_do' => 108.2462, 'gia_ve' => 150000, 'gio_mo_cua' => '16:00', 'gio_dong_cua' => '23:00', 'danh_gia' => 4.5, 'danh_muc' => $danhMucGiaiTri],

            ['ten_dia_diem' => 'Quán Nhậu 99 - Ngũ Hành Sơn', 'loai_dia_diem' => 'Quán nhậu', 'mo_ta' => 'Quán nhậu đồng giá 99k nổi tiếng gần biển Mỹ Khê, đông khách du lịch và dân địa phương.', 'dia_chi' => '8 Trường Sa, Ngũ Hành Sơn, Đà Nẵng', 'vi_do' => 16.0482, 'kinh_do' => 108.2472, 'gia_ve' => 99000, 'gio_mo_cua' => '17:00', 'gio_dong_cua' => '23:30', 'danh_gia' => 4.2, 'danh_muc' => $danhMucGiaiTri],

            ['ten_dia_diem' => 'Nhậu Sân Thượng - Rooftop Beer', 'loai_dia_diem' => 'Quán nhậu', 'mo_ta' => 'Quán bia sân thượng tầng 5, view toàn cảnh thành phố Đà Nẵng về đêm. Nhạc live, gió mát, giá tầm trung.', 'dia_chi' => '79 Nguyễn Văn Linh, Hải Châu, Đà Nẵng', 'vi_do' => 16.0695, 'kinh_do' => 108.2218, 'gia_ve' => 180000, 'gio_mo_cua' => '18:00', 'gio_dong_cua' => '00:30', 'danh_gia' => 4.6, 'danh_muc' => $danhMucGiaiTri],

            ['ten_dia_diem' => 'Hải Sản Đêm Mân Thái', 'loai_dia_diem' => 'Quán nhậu', 'mo_ta' => 'Khu nhậu hải sản tươi sống ngay cảng cá Mân Thái, mực nhảy, tôm hùm, cua biển giá cực rẻ.', 'dia_chi' => '45 Vân Đồn, Mân Thái, Sơn Trà, Đà Nẵng', 'vi_do' => 16.0912, 'kinh_do' => 108.2401, 'gia_ve' => 200000, 'gio_mo_cua' => '17:00', 'gio_dong_cua' => '23:30', 'danh_gia' => 4.5, 'danh_muc' => $danhMucGiaiTri],

            ['ten_dia_diem' => 'Quán Ốc Đêm Cô Ba', 'loai_dia_diem' => 'Quán nhậu', 'mo_ta' => 'Quán ốc đêm nổi tiếng khu Thanh Khê, hơn 30 loại ốc, xào me cực ngon, giá bình dân, đông khách từ 20h.', 'dia_chi' => '112 Điện Biên Phủ, Thanh Khê, Đà Nẵng', 'vi_do' => 16.0688, 'kinh_do' => 108.2058, 'gia_ve' => 100000, 'gio_mo_cua' => '18:00', 'gio_dong_cua' => '00:00', 'danh_gia' => 4.4, 'danh_muc' => $danhMucGiaiTri],

            ['ten_dia_diem' => 'BBQ Beer Garden - Đà Nẵng', 'loai_dia_diem' => 'Quán nhậu', 'mo_ta' => 'Khu nướng BBQ ngoài trời kết hợp beer garden, không gian xanh mát, nhạc acoustic live mỗi cuối tuần.', 'dia_chi' => '32 Lê Văn Hiến, Ngũ Hành Sơn, Đà Nẵng', 'vi_do' => 16.0318, 'kinh_do' => 108.2295, 'gia_ve' => 200000, 'gio_mo_cua' => '17:00', 'gio_dong_cua' => '23:00', 'danh_gia' => 4.5, 'danh_muc' => $danhMucGiaiTri],

            ['ten_dia_diem' => 'Nhậu Kiểu Đà Nẵng - Hoàng Diệu', 'loai_dia_diem' => 'Quán nhậu', 'mo_ta' => 'Quán nhậu bình dân kiểu Đà Nẵng chính hiệu: bê thui, nem lụi, bánh tráng cuốn thịt heo, bia Larue.', 'dia_chi' => '220 Hoàng Diệu, Hải Châu, Đà Nẵng', 'vi_do' => 16.0578, 'kinh_do' => 108.2163, 'gia_ve' => 100000, 'gio_mo_cua' => '16:30', 'gio_dong_cua' => '23:00', 'danh_gia' => 4.3, 'danh_muc' => $danhMucGiaiTri],

            ['ten_dia_diem' => 'Lẩu & Nhậu Đêm Phạm Văn Đồng', 'loai_dia_diem' => 'Quán nhậu', 'mo_ta' => 'Quán lẩu hải sản kết hợp nhậu, nằm trên trục đường Phạm Văn Đồng gần biển, gió mát, giá tốt.', 'dia_chi' => '198 Phạm Văn Đồng, Sơn Trà, Đà Nẵng', 'vi_do' => 16.0695, 'kinh_do' => 108.2468, 'gia_ve' => 160000, 'gio_mo_cua' => '17:00', 'gio_dong_cua' => '23:30', 'danh_gia' => 4.4, 'danh_muc' => $danhMucGiaiTri],
        ];

        foreach ($places as $placeData) {
            $danhMuc = $placeData['danh_muc'];
            unset($placeData['danh_muc']);

            $diaDiem = DiaDiem::firstOrCreate(
                ['ten_dia_diem' => $placeData['ten_dia_diem']],
                [
                    'loai_dia_diem' => $placeData['loai_dia_diem'],
                    'mo_ta' => $placeData['mo_ta'],
                    'dia_chi' => $placeData['dia_chi'],
                    'vi_do' => $placeData['vi_do'],
                    'kinh_do' => $placeData['kinh_do'],
                    'gia_ve' => $placeData['gia_ve'],
                    'gio_mo_cua' => $placeData['gio_mo_cua'],
                    'gio_dong_cua' => $placeData['gio_dong_cua'],
                    'danh_gia_trung_binh' => $placeData['danh_gia'],
                ]
            );

            ChiTietDanhMuc::firstOrCreate(['id_danh_muc' => $danhMuc->id, 'id_dia_diem' => $diaDiem->id]);
        }
    }
}
