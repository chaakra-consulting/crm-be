<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('users')->insert([
            [
                'id' => 1,
                'role_id' => 1,
                'name' => 'Superadmin',
                'email' => 'superadmin@chaakra.com',
                'password' => Hash::make('12345678'),
                'photo' => 'contacts/e8Fo63XKDiFT6cbpcuj6YFgHKmKqLTWG0uQXN5BI.jpg',
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'role_id' => 2,
                'name' => 'Lina Subandriyo',
                'email' => 'direktur@chaakra.com',
                'password' => Hash::make('12345678'),
                'photo' => 'contacts/e8Fo63XKDiFT6cbpcuj6YFgHKmKqLTWG0uQXN5BI.jpg',
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'role_id' => 3,
                'name' => 'Gigih Prihantono',
                'email' => 'manajer@chaakra.com',
                'password' => Hash::make('12345678'),
                'photo' => 'contacts/e8Fo63XKDiFT6cbpcuj6YFgHKmKqLTWG0uQXN5BI.jpg',
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'role_id' => 4,
                'name' => 'Acik Ayu',
                'email' => 'admin@chaakra.com',
                'password' => Hash::make('12345678'),
                'photo' => 'contacts/e8Fo63XKDiFT6cbpcuj6YFgHKmKqLTWG0uQXN5BI.jpg',
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 5,
                'role_id' => 6,
                'name' => 'Donna',
                'email' => 'marketing@chaakra.com',
                'password' => Hash::make('12345678'),
                'photo' => 'contacts/e8Fo63XKDiFT6cbpcuj6YFgHKmKqLTWG0uQXN5BI.jpg',
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 6,
                'role_id' => 5,
                'name' => 'Rico',
                'email' => 'karyawan@chaakra.com',
                'password' => Hash::make('12345678'),
                'photo' => 'contacts/e8Fo63XKDiFT6cbpcuj6YFgHKmKqLTWG0uQXN5BI.jpg',
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 7,
                'role_id' => 7,
                'name' => 'Anjasmara',
                'email' => 'anjas@gmail.com',
                'password' => Hash::make('12345678'),
                'photo' => 'contacts/e8Fo63XKDiFT6cbpcuj6YFgHKmKqLTWG0uQXN5BI.jpg',
                'is_active' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
