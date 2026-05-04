<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class AiStatisticsExport implements FromArray, WithHeadings, WithTitle
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
            'Loại Chỉ Số / Yêu Cầu',
            'Giá Trị / Số Lượng',
        ];
    }

    public function title(): string
    {
        return 'Thống kê AI';
    }
}
