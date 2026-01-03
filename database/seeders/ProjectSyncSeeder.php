<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\ProjectBukukas;

class ProjectSyncSeeder extends Seeder
{
    public function run(): void
    {
        $bukukasCompanies = ProjectBukukas::all();

        foreach ($bukukasCompanies as $bk) {

            $local = Project::where('project_bukukas_id', $bk->id)->first();

            if (!$local) {
                // CREATE
                Project::create([
                    'project_bukukas_id' => $bk->id,
                    // 'pic_contact_id' => null,
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
