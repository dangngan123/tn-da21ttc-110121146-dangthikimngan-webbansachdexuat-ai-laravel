<?php

namespace App\Imports;

use App\Models\Slider;
use Maatwebsite\Excel\Concerns\ToModel;

class SlidersImport implements ToModel
{
    public function model(array $row)
    {
        // Lấy giá trị status từ hàng
        $status = $row[6];

        // Kiểm tra và thay thế giá trị status nếu cần
        // Nếu giá trị status là 'Trạng thái', bạn cần thay thế bằng 0 hoặc 1
        if ($status == 'Trạng thái') {
            $status = 1;  // Nếu 'Trạng thái' có nghĩa là "hiện", thay bằng 1
        } else {
            $status = 0;  // Nếu không phải 'Trạng thái', thay bằng 0 (ẩn)
        }

        $slider = new Slider([
            'top_title' => $row[0],
            'title' => $row[1],
            'sub_title' => $row[2],
            'link' => $row[3],
            'offer' => $row[4],
            'image' => $row[5],
            'status' => $status,  // Đảm bảo giá trị status hợp lệ
            'type' => $row[7],
            'start_date' => $row[8],
            'end_date' => $row[9],
        ]);

        // Sinh slug cho slider
        $slider->generateSlug();
        return $slider;
    }
}
