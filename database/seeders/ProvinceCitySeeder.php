<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ProvinceCitySeeder extends Seeder
{
    public function run(): void
    {
        $provinces = Http::get('https://emsifa.github.io/api-wilayah-indonesia/api/provinces.json')->json();

        foreach ($provinces as $province) {
            $provinceId = $province['id'];
            $provinceName = $province['name'];

            $provinceDbId = DB::table('provinces')->insertGetId([
                'name' => $provinceName,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $cities = Http::get("https://emsifa.github.io/api-wilayah-indonesia/api/regencies/{$provinceId}.json")->json();

            $cityData = [];
            foreach ($cities as $city) {
                $cityData[] = [
                    'province_id' => $provinceDbId,
                    'name' => $city['name'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            DB::table('cities')->insert($cityData);
        }
    }
}
