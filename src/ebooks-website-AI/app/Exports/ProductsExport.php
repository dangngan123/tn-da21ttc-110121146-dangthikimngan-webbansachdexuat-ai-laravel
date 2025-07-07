<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ProductsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    use Exportable;

    protected $selectItems;

    public function __construct($selectItems)
    {
        $this->selectItems = $selectItems;
    }

    public function query()
    {
        return Product::whereIn('id', $this->selectItems);
    }

    public function headings(): array
    {
        return [
            'Tên sản phẩm',
            'Mô tả ngắn',
            'Mô tả dài',
            'Nhà xuất bản',
            'Tác giả',
            'Độ tuổi',
            'Giá gốc',
            'Giá giảm',
            'Loại giảm giá',
            'Giá trị giảm giá',
            'Số lượng',
            'Ảnh chính',
            'Ảnh phụ',
            'Danh mục',
        ];
    }

    public function map($product): array
    {
        return [
            $product->name,
            $product->short_description,
            $product->long_description,
            $product->publisher,
            $product->author,
            $product->age,
            $product->reguler_price,
            $product->sale_price,
            $product->discount_type,
            $product->discount_value,
            $product->quantity,
            $product->image,
            $product->images,
            $product->category_id,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style tiêu đề in đậm
            1 => ['font' => ['bold' => true]],
        ];
    }
}
