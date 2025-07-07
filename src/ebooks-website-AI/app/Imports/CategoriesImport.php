<?php

namespace App\Imports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;

class CategoriesImport implements ToModel
{


    public function model(array $row)
    {
        // Lấy giá trị status từ hàng
        // Lấy giá trị status từ hàng
        $status = strtolower(trim($row[2])); // Chuẩn hóa dữ liệu

        // Kiểm tra và thay thế giá trị status
        if ($status == 'Trạng thái') {
            $status = 1; // Hiển thị
        } else {
            $status = 0; // Ẩn
        }
        // Tạo đối tượng Category
        $category = new Category([
            'name' => $row[0],
            'image' => $row[1],
            'status' => $row[2],
        ]);


        // Sinh slug cho slider
        $category->generateSlug();
        return $category;
    }
}
