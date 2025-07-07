<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class VietnamAddressService
{
    protected $apiUrl = 'https://provinces.open-api.vn/api';
    protected $cacheDuration = 24 * 60; // Cache 24 giờ (tính bằng phút)

    public function getProvinces()
    {
        $cacheKey = 'vietnam_provinces';

        return Cache::remember($cacheKey, now()->addMinutes($this->cacheDuration), function () {
            Log::info('Fetching provinces from Vietnam Address API');

            try {
                $response = Http::withOptions(['verify' => false])
                    ->retry(3, 200) // Tăng khoảng cách retry lên 200ms
                    ->timeout(15) // Tăng timeout lên 15 giây
                    ->get("{$this->apiUrl}/p/");

                if ($response->failed()) {
                    Log::error('Vietnam Address Provinces API Error', [
                        'status' => $response->status(),
                        'body' => $response->json(),
                    ]);
                    return [];
                }

                $provinces = collect($response->json())->map(function ($item) {
                    return [
                        'id' => $item['code'],
                        'name' => $this->normalizeName($item['name']),
                    ];
                })->sortBy('name')->values()->toArray();

                if (empty($provinces)) {
                    Log::warning('Vietnam Address API returned empty provinces');
                    Cache::forget('vietnam_provinces'); // Xóa cache nếu rỗng
                } else {
                    Log::info('Provinces fetched successfully', ['count' => count($provinces)]);
                }

                return $provinces;
            } catch (\Exception $e) {
                Log::error('Exception in getProvinces', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);
                Cache::forget('vietnam_provinces'); // Xóa cache nếu lỗi
                return [];
            }
        });
    }

    public function getDistricts($provinceId)
    {
        $cacheKey = "vietnam_districts_{$provinceId}";

        return Cache::remember($cacheKey, now()->addMinutes($this->cacheDuration), function () use ($provinceId) {
            Log::info('Fetching districts from Vietnam Address API', ['province_id' => $provinceId]);

            try {
                $response = Http::withOptions(['verify' => false])
                    ->retry(3, 200)
                    ->timeout(15)
                    ->get("{$this->apiUrl}/p/{$provinceId}?depth=2");

                Log::info('API response for districts', [
                    'province_id' => $provinceId,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                if ($response->failed()) {
                    Log::error('Vietnam Address Districts API Error', [
                        'status' => $response->status(),
                        'body' => $response->json(),
                    ]);
                    Cache::forget("vietnam_districts_{$provinceId}"); // Xóa cache nếu lỗi
                    return [];
                }

                $districts = collect($response->json()['districts'])->map(function ($item) {
                    return [
                        'id' => $item['code'],
                        'name' => $this->normalizeName($item['name']),
                    ];
                })->sortBy('name')->values()->toArray();

                if (empty($districts)) {
                    Log::warning('Vietnam Address API returned empty districts', ['province_id' => $provinceId]);
                    Cache::forget("vietnam_districts_{$provinceId}"); // Xóa cache nếu rỗng
                } else {
                    Log::info('Districts fetched successfully', ['province_id' => $provinceId, 'count' => count($districts)]);
                }

                return $districts;
            } catch (\Exception $e) {
                Log::error('Exception in getDistricts', [
                    'message' => $e->getMessage(),
                    'province_id' => $provinceId,
                ]);
                Cache::forget("vietnam_districts_{$provinceId}"); // Xóa cache nếu lỗi
                return [];
            }
        });
    }

    public function getWards($districtId)
    {
        $cacheKey = "vietnam_wards_{$districtId}";

        return Cache::remember($cacheKey, now()->addMinutes($this->cacheDuration), function () use ($districtId) {
            Log::info('Fetching wards from Vietnam Address API', ['district_id' => $districtId]);

            try {
                $response = Http::withOptions(['verify' => false])
                    ->retry(3, 200)
                    ->timeout(15)
                    ->get("{$this->apiUrl}/d/{$districtId}?depth=2");

                Log::info('API response for wards', [
                    'district_id' => $districtId,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                if ($response->failed()) {
                    Log::error('Vietnam Address Wards API Error', [
                        'status' => $response->status(),
                        'body' => $response->json(),
                    ]);
                    Cache::forget("vietnam_wards_{$districtId}"); // Xóa cache nếu lỗi
                    return [];
                }

                $wards = collect($response->json()['wards'])->map(function ($item) {
                    return [
                        'id' => $item['code'],
                        'name' => $this->normalizeName($item['name']),
                    ];
                })->sortBy('name')->values()->toArray();

                if (empty($wards)) {
                    Log::warning('Vietnam Address API returned empty wards', ['district_id' => $districtId]);
                    Cache::forget("vietnam_wards_{$districtId}"); // Xóa cache nếu rỗng
                } else {
                    Log::info('Wards fetched successfully', ['district_id' => $districtId, 'count' => count($wards)]);
                }

                return $wards;
            } catch (\Exception $e) {
                Log::error('Exception in getWards', [
                    'message' => $e->getMessage(),
                    'district_id' => $districtId,
                ]);
                Cache::forget("vietnam_wards_{$districtId}"); // Xóa cache nếu lỗi
                return [];
            }
        });
    }

    public function calculateShippingFee($toDistrictId, $toWardCode, $items)
    {
        Log::info('Calculating shipping fee (mock)', [
            'to_district_id' => $toDistrictId,
            'to_ward_code' => $toWardCode,
            'items_count' => $items->count(),
        ]);

        return 20; // 20,000 VND (giả lập)
    }

    protected function normalizeName($name)
    {
        return str_replace(['Tỉnh ', 'Thành phố ', 'Huyện ', 'Thị xã ', 'Phường ', 'Xã '], '', $name);
    }
}