<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProductImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        try {
            // Kiểm tra trường bắt buộc
            if (empty($row['ten_san_pham'])) {
                Log::error('Thiếu tên sản phẩm ở dòng: ', $row);
                return null;
            }

            // Xử lý ảnh chính
            $imagePath = null;
            if (!empty($row['anh_chinh'])) {
                $imagePath = $this->downloadImage($row['anh_chinh']);
            }

            // Xử lý ảnh phụ
            $additionalImages = '';
            if (!empty($row['anh_phu'])) {
                $additionalImages = $this->downloadAdditionalImages($row['anh_phu']);
            }

            // Kiểm tra và xử lý reguler_price
            $regulerPrice = is_numeric($row['gia_goc']) && $row['gia_goc'] >= 0 ? (float)$row['gia_goc'] : 0;
            if ($regulerPrice === 0 && !empty($row['gia_goc'])) {
                Log::error('Giá gốc không hợp lệ ở dòng: ' . json_encode($row) . ' - Giá trị: ' . $row['gia_goc']);
            }

            // Kiểm tra và xử lý discount_type
            $discountType = null;
            if (!empty($row['loai_giam_gia'])) {
                $discountType = trim($row['loai_giam_gia']);
                if (!in_array($discountType, ['fixed', 'percentage'])) {
                    Log::error('Loại giảm giá không hợp lệ ở dòng: ' . json_encode($row) . ' - Giá trị: ' . $discountType);
                    $discountType = null;
                }
            }

            // Kiểm tra và xử lý discount_value
            $discountValue = null;
            if (!empty($row['gia_tri_giam_gia']) && is_numeric($row['gia_tri_giam_gia'])) {
                $discountValue = (float)$row['gia_tri_giam_gia'];
                if ($discountValue < 0) {
                    Log::error('Giá trị giảm giá không hợp lệ ở dòng: ' . json_encode($row) . ' - Giá trị: ' . $discountValue);
                    $discountValue = null;
                }
            }

            // Tính sale_price
            $salePrice = null;
            if ($discountType && $discountValue !== null && $regulerPrice > 0) {
                // Tính sale_price từ discount_type và discount_value
                if ($discountType === 'fixed') {
                    $salePrice = $regulerPrice - $discountValue;
                } elseif ($discountType === 'percentage') {
                    $salePrice = $regulerPrice * (1 - $discountValue / 100);
                }
                // Đảm bảo sale_price không âm
                if ($salePrice < 0) {
                    Log::error('Giá giảm tính toán không hợp lệ (âm) ở dòng: ' . json_encode($row) . ' - Giá trị: ' . $salePrice);
                    $salePrice = null;
                } else {
                    $salePrice = round($salePrice, 2);
                }
            } elseif (!empty($row['giam_gia']) && is_numeric($row['giam_gia'])) {
                // Sử dụng giam_gia nếu không có discount_type hoặc discount_value
                $salePrice = (float)$row['giam_gia'];
                if ($salePrice < 0) {
                    Log::error('Giá giảm không hợp lệ ở dòng: ' . json_encode($row) . ' - Giá trị: ' . $salePrice);
                    $salePrice = null;
                }
            }

            // Tạo sản phẩm mới
            return new Product([
                'name' => $row['ten_san_pham'],
                'short_description' => $row['mo_ta_ngan'] ?? '',
                'long_description' => $row['mo_ta_dai'] ?? '',
                'publisher' => $row['nha_xuat_ban'] ?? '',
                'author' => $row['tac_gia'] ?? '',
                'age' => $row['do_tuoi'] ?? '',
                'reguler_price' => $regulerPrice,
                'sale_price' => $salePrice,
                'quantity' => is_numeric($row['so_luong']) ? (int)$row['so_luong'] : 0,
                'image' => $imagePath ?? '',
                'images' => $additionalImages,
                'category_id' => $row['danh_muc'] ?? null,
                'slug' => Str::slug($row['ten_san_pham']),
                'discount_type' => $discountType,
                'discount_value' => $discountValue,
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi import dòng: ' . json_encode($row) . ' - Error: ' . $e->getMessage());
            return null;
        }
    }

    protected function downloadImage($url)
    {
        try {
            if (empty($url)) {
                return '';
            }

            // Kiểm tra URL hợp lệ
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                Log::error('URL ảnh không hợp lệ: ' . $url);
                return '';
            }

            $response = Http::timeout(30)->get($url);

            if ($response->successful()) {
                $imageName = uniqid('image_') . '.jpg';
                $path = public_path('admin/product/' . $imageName);

                file_put_contents($path, $response->body());
                return $imageName;
            }

            Log::error('Không thể tải ảnh từ URL: ' . $url);
            return '';
        } catch (\Exception $e) {
            Log::error('Lỗi khi tải ảnh: ' . $url . ' - ' . $e->getMessage());
            return '';
        }
    }

    protected function downloadAdditionalImages($urls)
    {
        try {
            if (empty($urls)) {
                return '';
            }

            $imageNames = [];
            $urlArray = array_filter(array_map('trim', explode(',', $urls)));

            foreach ($urlArray as $imageUrl) {
                $downloadedImage = $this->downloadImage($imageUrl);
                if ($downloadedImage) {
                    $imageNames[] = $downloadedImage;
                }
            }

            return implode(',', $imageNames);
        } catch (\Exception $e) {
            Log::error('Lỗi khi tải ảnh phụ: ' . $e->getMessage());
            return '';
        }
    }
}
