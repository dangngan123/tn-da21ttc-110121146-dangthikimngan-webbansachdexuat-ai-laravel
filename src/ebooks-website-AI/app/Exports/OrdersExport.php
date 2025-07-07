<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Models\Order;


class OrdersExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    use Exportable;


    protected $selectItems;
    public function __construct($selectItems)
    {
        $this->selectItems = $selectItems;
    }

    public function map($orders): array
    {
        return [
            $orders->id,
            $orders->name,
            $orders->phone,
            $orders->email,
            $orders->subtotal,
            // Kết hợp 3 giá trị lại thành một chuỗi
            ($orders->province ?? 'Không có thông tin') . ' - ' .
                ($orders->district ?? 'Không có thông tin') . ' - ' .
                ($orders->ward ?? 'Không có thông tin'),
            $orders->additional_info,
            $orders->status,


        ];
    }







    public function headings(): array
    {
        return [
            'ID',
            'Tên',
            'Số điện thoại',
            'Email',
            'Tổng cộng',
            'Địa chỉ',
            'Thông tin thêm',
            'Trạng thái',


        ];
    }


    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
        ];
    }

    // public function columnWidths(): array
    // {
    //     return [
    //         'A' => 25,
    //         'B' => 25,
    //         'C' => 25,
    //         'D' => 25,

    //         'F' => 25,
    //         'H' => 25,
    //         'I' => 25,
    //         'J' => 25,
    //         'K' => 25,

    //     ];
    // }

    // public function prepareRows($rows)
    // {
    //     return $rows->transform(function ($category) {
    //         $category->offer .= ' (%)';

    //         return $category;
    //     });
    // }






    public function query()
    {
        return Order::whereIn('id', $this->selectItems);
    }
}
