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
use App\Models\Category;


class CategoriesExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    use Exportable;


    protected $selectItems;
    public function __construct($selectItems)
    {
        $this->selectItems = $selectItems;
    }

    public function map($category): array
    {
        return [
            $category->name,
            $category->image,
            $category->status,


        ];
    }







    public function headings(): array
    {
        return [
            'Tên danh mục',
            'Ảnh',
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
        return Category::whereIn('id', $this->selectItems);
    }
}
