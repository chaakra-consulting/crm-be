<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CompanyBukukas;
use App\Models\Company;

class CompanySyncSeeder extends Seeder
{
    public function run(): void
    {
        $bukukasCompanies = CompanyBukukas::all();

        foreach ($bukukasCompanies as $bk) {

            $local = Company::where('company_bukukas_id', $bk->id)->first();

            if (!$local) {
                // CREATE
                Company::create([
                    'company_bukukas_id' => $bk->id,
                    'pic_contact_id' => null,
                ]);

            }
            // else {
            //     // UPDATE (jika ingin melengkapi data, optional)
            //     $local->update([
            //         'name' => $bk->name ?? $local->name,
            //         'phone' => $bk->phone ?? $local->phone,
            //         'address' => $bk->address ?? $local->address,
            //     ]);
            // }
        }
    }
}
