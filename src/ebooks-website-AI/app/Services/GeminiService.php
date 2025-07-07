<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/';
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
    }

    public function generateContent($prompt)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . 'models/gemini-2.0-flash:generateContent?key=' . $this->apiKey, [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ]);

        if ($response->successful()) {
            $jsonResponse = $response->json();
            $text = $jsonResponse['candidates'][0]['content']['parts'][0]['text'] ?? 'Không có mô tả trả về';

            // Làm sạch phản hồi: loại bỏ khối mã và nội dung không phải JSON
            $text = preg_replace('/```json\n|\n```/', '', $text); // Loại bỏ ```json và ```
            $text = preg_replace('/\n\n\*\*Giải thích và cách sử dụng:\*\*.*/s', '', $text); // Loại bỏ phần giải thích
            $text = str_replace('\n', '', $text); // Loại bỏ \n
            $text = trim($text); // Loại bỏ khoảng trắng thừa

            // Ghi log để kiểm tra phản hồi đã làm sạch
            Log::info('Cleaned API Response Text: ' . $text);

            // Thử phân tích JSON
            try {
                $descriptions = json_decode($text, true);
                if (isset($descriptions['short_description']) && isset($descriptions['long_description'])) {
                    return $text; // Trả về JSON gốc nếu đúng định dạng
                }
                throw new \Exception('Phản hồi JSON không chứa các trường cần thiết.');
            } catch (\Exception $e) {
                // Nếu không phải JSON, xử lý văn bản thô
                $shortDescription = substr($text, 0, 200);
                $longDescription = substr($text, 0, 1000);
                return json_encode([
                    'short_description' => $shortDescription,
                    'long_description' => $longDescription
                ]);
            }
        }

        throw new \Exception('Lỗi khi gọi API: ' . $response->body());
    }
}
