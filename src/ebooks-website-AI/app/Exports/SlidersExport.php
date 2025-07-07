<?php

namespace App\Exports;

use App\Models\Slider;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class SlidersExport implements FromQuery, WithHeadings, WithMapping, WithStyles,ShouldAutoSize
{
    use Exportable;


    protected $selectItems;
    public function __construct($selectItems)
    {
        $this->selectItems = $selectItems;
    }

    public function map($slider): array
    {
        return [
            $slider->top_title,
            $slider->title,
            $slider->sub_title,
            $slider->link,
            $slider->offer,
            $slider->image,
            $slider->status,
            $slider->type,
            $slider->start_date,
            $slider->end_date,


        ];
    }







    public function headings(): array
    {
        return [
            'Tiêu đề đầu',
            'Tiêu đề chính',
            'Tiêu đề phụ',
            'Link',
            'Giảm giá',
            'Ảnh',
            'Trạng thái',
            'Type',
            'Ngày bắt đầu',
            'Ngày kết thúc',


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

    public function prepareRows($rows)
    {
        return $rows->transform(function ($slider) {
            $slider->offer .= ' (%)';

            return $slider;
        });
    }

    




    public function query()
    {
        return Slider::whereIn('id', $this->selectItems);
    }
}
