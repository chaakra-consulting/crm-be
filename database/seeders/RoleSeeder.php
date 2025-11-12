<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $sources = [
            [
                'name' => 'Superadmin',
            ],
            [
                'name' => 'Direktur',
            ],
            [
                'name' => 'Manager',
            ],
            [
                'name' => 'Admin',
            ],
            [
                'name' => 'PIC Project',
            ],
            [
                'name' => 'Marketing',
            ],
            [
                'name' => 'PIC Customer',
            ],
        ];

        foreach ($sources as $source) {
            DB::table('roles')->updateOrInsert(
                ['slug' => Str::slug($source['name'])],
                [
                    'name' => $source['name'],
                    // 'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
