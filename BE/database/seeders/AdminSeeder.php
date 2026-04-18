<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admins = [
            [
                'ho_ten' => 'Nguyễn Văn A',
                'email' => 'admin1@example.com',
                'mat_khau' => Hash::make('12345678'),
                'id_chuc_vu' => 2,
                'so_dien_thoai' => '0123456789',
                'trang_thai' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ho_ten' => 'Trần Thị B',
                'email' => 'admin2@example.com',
                'mat_khau' => Hash::make('12345678'),
                'id_chuc_vu' => 2,
                'so_dien_thoai' => '0987654321',
                'trang_thai' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ho_ten' => 'Lê Văn C',
                'email' => 'admin3@example.com',
                'mat_khau' => Hash::make('12345678'),
                'id_chuc_vu' => 2,
                'so_dien_thoai' => '0111111111',
                'trang_thai' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ho_ten' => 'Phạm Thị D',
                'email' => 'admin4@example.com',
                'mat_khau' => Hash::make('12345678'),
                'id_chuc_vu' => 2,
                'so_dien_thoai' => '0222222222',
                'trang_thai' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ho_ten' => 'Hoàng Văn E',
                'email' => 'admin5@example.com',
                'mat_khau' => Hash::make('12345678'),
                'id_chuc_vu' => 2,
                'so_dien_thoai' => '0333333333',
                'trang_thai' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ho_ten' => 'Đỗ Thị F',
                'email' => 'admin6@example.com',
                'mat_khau' => Hash::make('12345678'),
                'id_chuc_vu' => 2,
                'so_dien_thoai' => '0444444444',
                'trang_thai' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ho_ten' => 'Bùi Văn G',
                'email' => 'admin7@example.com',
                'mat_khau' => Hash::make('12345678'),
                'id_chuc_vu' => 2,
                'so_dien_thoai' => '0555555555',
                'trang_thai' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ho_ten' => 'Vũ Thị H',
                'email' => 'admin8@example.com',
                'mat_khau' => Hash::make('12345678'),
                'id_chuc_vu' => 2,
                'so_dien_thoai' => '0666666666',
                'trang_thai' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ho_ten' => 'Ngô Văn I',
                'email' => 'admin9@example.com',
                'mat_khau' => Hash::make('12345678'),
                'id_chuc_vu' => 2,
                'so_dien_thoai' => '0777777777',
                'trang_thai' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ho_ten' => 'Đinh Thị J',
                'email' => 'admin10@example.com',
                'mat_khau' => Hash::make('12345678'),
                'id_chuc_vu' => 2,
                'so_dien_thoai' => '0888888888',
                'trang_thai' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ho_ten' => 'Nguyễn Văn K',
                'email' => 'admin11@example.com',
                'mat_khau' => Hash::make('12345678'),
                'id_chuc_vu' => 2,
                'so_dien_thoai' => '0999999999',
                'trang_thai' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ho_ten' => 'Trần Thị L',
                'email' => 'admin12@example.com',
                'mat_khau' => Hash::make('12345678'),
                'id_chuc_vu' => 2,
                'so_dien_thoai' => '0100000000',
                'trang_thai' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ho_ten' => 'Lê Văn M',
                'email' => 'admin13@example.com',
                'mat_khau' => Hash::make('12345678'),
                'id_chuc_vu' => 2,
                'so_dien_thoai' => '0111111112',
                'trang_thai' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ho_ten' => 'Phạm Thị N',
                'email' => 'admin14@example.com',
                'mat_khau' => Hash::make('12345678'),
                'id_chuc_vu' => 2,
                'so_dien_thoai' => '0222222223',
                'trang_thai' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ho_ten' => 'Hoàng Văn O',
                'email' => 'admin15@example.com',
                'mat_khau' => Hash::make('12345678'),
                'id_chuc_vu' => 2,
                'so_dien_thoai' => '0333333334',
                'trang_thai' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ho_ten' => 'Đỗ Thị P',
                'email' => 'admin16@example.com',
                'mat_khau' => Hash::make('12345678'),
                'id_chuc_vu' => 2,
                'so_dien_thoai' => '0444444445',
                'trang_thai' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ho_ten' => 'Bùi Văn Q',
                'email' => 'admin17@example.com',
                'mat_khau' => Hash::make('12345678'),
                'id_chuc_vu' => 2,
                'so_dien_thoai' => '0555555556',
                'trang_thai' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ho_ten' => 'Vũ Thị R',
                'email' => 'admin18@example.com',
                'mat_khau' => Hash::make('12345678'),
                'id_chuc_vu' => 2,
                'so_dien_thoai' => '0666666667',
                'trang_thai' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ho_ten' => 'Ngô Văn S',
                'email' => 'admin19@example.com',
                'mat_khau' => Hash::make('12345678'),
                'id_chuc_vu' => 2,
                'so_dien_thoai' => '0777777778',
                'trang_thai' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ho_ten' => 'Đinh Thị T',
                'email' => 'admin20@example.com',
                'mat_khau' => Hash::make('12345678'),
                'id_chuc_vu' => 2,
                'so_dien_thoai' => '0888888889',
                'trang_thai' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // Tài khoản admin nắm tất cả quyền
            [
                'ho_ten' => 'Super Admin',
                'email' => 'admin@travel.com',
                'mat_khau' => Hash::make('12345678'),
                'id_chuc_vu' => 1, // Giả sử 1 là quyền cao nhất
                'so_dien_thoai' => '0999999999',
                'trang_thai' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('admin')->insertOrIgnore($admins);
    }
}
