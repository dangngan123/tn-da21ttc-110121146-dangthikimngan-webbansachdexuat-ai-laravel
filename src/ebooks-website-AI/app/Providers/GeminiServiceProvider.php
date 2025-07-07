<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('gemini', function () {
            return new class {
                protected $apiKey;
                protected $endpoint;

                public function __construct()
                {
                    $this->apiKey = config('services.gemini.api_key');
                    $this->endpoint = config('services.gemini.endpoint');
                }

                public function generateDescription(string $content, string $language = 'vi'): string
                {
                    $cacheKey = 'gemini_' . md5($content);
                    if (Cache::has($cacheKey)) {
                        return Cache::get($cacheKey);
                    }

                    $prompt = $language === 'vi'
                        ? "Viết mô tả ngắn gọn, hấp dẫn (160 ký tự) cho sản phẩm sau:\n\n"
                        : "Write a short, compelling (max 160 chars) product description:\n\n";

                    $response = Http::timeout(15)->post($this->endpoint . '?key=' . $this->apiKey, [
                        'contents' => [
                            'parts' => [['text' => $prompt . $content]]
                        ],
                        'generationConfig' => [
                            'temperature' => 0.7,
                            'maxOutputTokens' => 200,
                            'topP' => 0.9,
                        ]
                    ]);

                    $description = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? 'Không thể tạo mô tả.';

                    Cache::put($cacheKey, $description, now()->addHours(24));

                    return $description;
                }
            };
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/services.php',
            'services'
        );
    }
}
