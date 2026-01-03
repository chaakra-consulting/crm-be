<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SocialMediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $social_medias = [
            [
                'name' => 'Twitter/X',
            ],
            [
                'name' => 'Instagram',
            ],
            [
                'name' => 'Facebook',
            ],
            [
                'name' => 'Whatsapp',
            ],
        ];

        foreach ($social_medias as $social_media) {
            DB::table('social_medias')->updateOrInsert(
                ['slug' => Str::slug($social_media['name'])],
                [
                    'name' => $social_media['name'],
                    'is_active' => true,
                ]
            );
        }
    }
}
