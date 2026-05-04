<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class StatisticsExport implements FromArray, WithHeadings, WithTitle
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Tên địa điểm',
            'Danh mục',
            'Lượt chọn',
            'Đánh giá',
            'Biến động (%)',
        ];
    }

    public function title(): string
    {
        return 'Top 10 Địa Điểm';
    }
}
