<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class NguoiDungSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('nguoi_dungs')->insert([
            [
                'ten' => 'Nguyễn Văn A',
                'email' => 'nguyenvana@gmail.com',
                'mat_khau' => Hash::make('123456'),
                'so_dien_thoai' => '0912345678',
                'anh_dai_dien' => 'https://i.pinimg.com/736x/4c/9b/dd/4c9bdde2c8101d719cf460d8386a2436.jpg',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ten' => 'Trần Thị B',
                'email' => 'tranthib@gmail.com',
                'mat_khau' => Hash::make('123456'),
                'so_dien_thoai' => '0912345679',
                'anh_dai_dien' => 'https://canhgioi.com/wp-content/uploads/2024/11/tran-binh-an-kiem-lai-e1724816503371.jpg',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ten' => 'Lê Văn C',
                'email' => 'levanc@gmail.com',
                'mat_khau' => Hash::make('123456'),
                'so_dien_thoai' => '0912345680',
                'anh_dai_dien' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ten' => 'Phạm Minh D',
                'email' => 'phamminhd@gmail.com',
                'mat_khau' => Hash::make('123456'),
                'so_dien_thoai' => '0912345681',
                'anh_dai_dien' => 'https://i.pravatar.cc/150?img=4',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ten' => 'Hoàng Lan E',
                'email' => 'hoanglane@gmail.com',
                'mat_khau' => Hash::make('123456'),
                'so_dien_thoai' => '0912345682',
                'anh_dai_dien' => 'https://i.pravatar.cc/150?img=5',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ten' => 'Ngô Văn F',
                'email' => 'ngovanf@gmail.com',
                'mat_khau' => Hash::make('123456'),
                'so_dien_thoai' => '0912345683',
                'anh_dai_dien' => 'https://i.pravatar.cc/150?img=6',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ten' => 'Đỗ Thị G',
                'email' => 'dothig@gmail.com',
                'mat_khau' => Hash::make('123456'),
                'so_dien_thoai' => '0912345684',
                'anh_dai_dien' => 'https://i.pravatar.cc/150?img=7',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ten' => 'Bùi Văn H',
                'email' => 'buivanh@gmail.com',
                'mat_khau' => Hash::make('123456'),
                'so_dien_thoai' => '0912345685',
                'anh_dai_dien' => 'https://i.pravatar.cc/150?img=8',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ten' => 'Võ Thị I',
                'email' => 'vothii@gmail.com',
                'mat_khau' => Hash::make('123456'),
                'so_dien_thoai' => '0912345686',
                'anh_dai_dien' => 'https://i.pravatar.cc/150?img=9',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ten' => 'Đặng Văn K',
                'email' => 'dangvank@gmail.com',
                'mat_khau' => Hash::make('123456'),
                'so_dien_thoai' => '0912345687',
                'anh_dai_dien' => 'https://i.pravatar.cc/150?img=10',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ten' => 'Lý Thị L',
                'email' => 'lythil@gmail.com',
                'mat_khau' => Hash::make('123456'),
                'so_dien_thoai' => '0912345688',
                'anh_dai_dien' => 'https://i.pravatar.cc/150?img=11',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ten' => 'Trịnh Văn M',
                'email' => 'trinhvanm@gmail.com',
                'mat_khau' => Hash::make('123456'),
                'so_dien_thoai' => '0912345689',
                'anh_dai_dien' => 'https://i.pravatar.cc/150?img=12',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ten' => 'Phan Thị N',
                'email' => 'phanthin@gmail.com',
                'mat_khau' => Hash::make('123456'),
                'so_dien_thoai' => '0912345690',
                'anh_dai_dien' => 'https://i.pravatar.cc/150?img=13',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ten' => 'Mai Văn O',
                'email' => 'maivano@gmail.com',
                'mat_khau' => Hash::make('123456'),
                'so_dien_thoai' => '0912345691',
                'anh_dai_dien' => 'https://i.pravatar.cc/150?img=14',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ten' => 'Huỳnh Thị P',
                'email' => 'huynhthip@gmail.com',
                'mat_khau' => Hash::make('123456'),
                'so_dien_thoai' => '0912345692',
                'anh_dai_dien' => 'https://i.pravatar.cc/150?img=15',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ten' => 'Tạ Văn Q',
                'email' => 'tavanq@gmail.com',
                'mat_khau' => Hash::make('123456'),
                'so_dien_thoai' => '0912345693',
                'anh_dai_dien' => 'https://i.pravatar.cc/150?img=16',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ten' => 'Nguyễn Thị R',
                'email' => 'nguyenthir@gmail.com',
                'mat_khau' => Hash::make('123456'),
                'so_dien_thoai' => '0912345694',
                'anh_dai_dien' => 'https://i.pravatar.cc/150?img=17',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ten' => 'Lâm Văn S',
                'email' => 'lamvans@gmail.com',
                'mat_khau' => Hash::make('123456'),
                'so_dien_thoai' => '0912345695',
                'anh_dai_dien' => 'https://i.pravatar.cc/150?img=18',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
