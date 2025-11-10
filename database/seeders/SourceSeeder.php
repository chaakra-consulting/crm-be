<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SourceSeeder extends Seeder
{
    public function run(): void
    {
        $sources = [
            [
                'name' => 'Website',
            ],
            [
                'name' => 'Instagram',
            ],
            [
                'name' => 'Facebook',
            ],
            [
                'name' => 'Google Ads',
            ],
            [
                'name' => 'Referral',
            ],
            [
                'name' => 'Event / Pameran',
            ],
            [
                'name' => 'Telemarketing',
            ],
            [
                'name' => 'Walk-in',
            ],
        ];

        foreach ($sources as $source) {
            DB::table('sources')->updateOrInsert(
                ['slug' => Str::slug($source['name'])],
                [
                    'name' => $source['name'],
                    'is_active' => true,
                ]
            );
        }
    }
}
