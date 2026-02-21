<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectBukukas;
use App\Models\ProjectItemsBukukas;
use App\Models\Ticket;
use App\Services\Helpers;
use App\Services\Remappers;
use App\Traits\ResponseFactory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    use ResponseFactory;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $auth = Auth::user();
        $roleSlug = $auth->role?->slug;
        $picCompanyUserId = $roleSlug == 'pic-customer' ? $auth->id : null;
        $picProjectUserId = $roleSlug == 'pic-project' ? $auth->id : null;

        $project = ProjectBukukas::where('deleted', 0)
            ->byPicCompany($picCompanyUserId)
            ->byPicProject($picProjectUserId)
            ->latest()->get();

        $remapper = new Remappers();
        $remapProjects = $remapper->remapProjects($project);


        return response()->json($remapProjects);
    }

    function projectList()
    {
        $project = ProjectBukukas::where('deleted', 0)
            ->latest()->get();

        $remapper = new Remappers();
        $remapProjects = $remapper->remapProjects($project);

        return response()->json(['data' => $remapProjects]);
    }

    public function detail($id): JsonResponse
    {
        $project = ProjectBukukas::findOrFail($id);

        $remapper = new Remappers();
        $remapProjects = $remapper->mapProjectItem($project, true);

        return response()->json($remapProjects);
    }

    public function store(Request $request)
    {
        $request->validate([
            'spk_code'              => 'nullable|string|max:255',
            'potongan'              => 'nullable|numeric|min:0',

            'termin'                => 'required|integer|min:1',
            'inv_date'              => 'nullable|date',
            'inv_contract_date'     => 'nullable|date',

            'fid_tax'               => 'nullable|integer',
            'fid_cust'              => 'nullable|integer',
            'fid_custt'             => 'nullable|integer',

            /** Item Invoice */
            'invoice_item_title'    => 'required|string|max:255',
            'invoice_item_rate'     => 'required|numeric|min:0',

            /** Project CRM */
            'pic_project_user_id'   => 'required|integer',
            'pic_company_user_id'   => 'required|integer',
        ]);

        DB::beginTransaction();

        try {

            $project = ProjectBukukas::create(
                [
                    // 'code'              => $bukpot,
                    'spk_code'          => $request->spk_code ?? '',
                    'fid_custt'         => $request->fid_custt, // untuk menyimpan company yg ngerjain (chaakra consulting)
                    // 'fid_custtt'        => $request->fid_custtt,
                    // 'fid_custttt'       => $request->fid_custttt,
                    'fid_cust'          => $request->fid_cust, // untuk menyimpan company client
                    'potongan'          => $request->potongan,
                    'status'            => 'draft',
                    'paid'              => 'Not Paid',
                    'termin'            => $request->termin,
                    'inv_date'          => $request->inv_date,
                    'inv_contract_date' => $request->inv_contract_date,
                    'fid_tax'           => $request->fid_tax,
                    'is_verified'       => 0,
                    'deleted'           => 0,

                    'no_inv'            => '',
                    'coa_sales'         => 0,
                    'inv_address'       => '',
                    'currency'          => '',
                    'sub_total'         => 0,
                    'penjualan'         => '',
                    'amount'            => 0,
                    'residual'          => 0,
                    'dikirim'           => '',
                    'tgl_dikirim'       => Carbon::now(),
                    'keterangan'        => '',
                    'created_at'        => Carbon::now(),
                ]
            );

            ProjectItemsBukukas::create(
                [
                    'fid_invoices' => $project->id,
                    'title'        => $request->invoice_item_title,
                    'rate'         => $request->invoice_item_rate,
                    'total'        => $request->invoice_item_rate,
                ]
            );

            Project::updateOrCreate(
                ['project_bukukas_id' => $project->id],
                [
                    'project_bukukas_id'  => $project->id,
                    'pic_project_user_id' => $request->pic_project_user_id,
                    'pic_company_user_id' => $request->pic_company_user_id,
                    'is_active'           => 1,
                ]
            );

            DB::commit();

            /**
             * ============================
             * SYNC KE SDM
             * ============================
             */
            $responseSdm = Http::asForm()->post(
                config('services.sdm.url') . '/bukukas-sync/project',
                [
                    'bukukas_id'    => $project->id,
                    'perusahaan_id' => $request->fid_cust,
                    'nama_project'  => $request->invoice_item_title,
                    'waktu_mulai'   => $request->inv_date,
                    'deadline'      => $request->inv_contract_date,
                ]
            );

            if ($responseSdm->failed()) {
                \Log::error('SDM Sync Failed', [
                    'response' => $responseSdm->body(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Project created successfully.',
                'data' => $project
            ], 201);
        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to save project bukukas',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'spk_code'              => 'nullable|string|max:255',
            'potongan'              => 'nullable|numeric|min:0',

            'termin'                => 'required|integer|min:1',
            'inv_date'              => 'nullable|date',
            'inv_contract_date'     => 'nullable|date',

            'fid_tax'               => 'nullable|integer',
            'fid_cust'              => 'nullable|integer',
            'fid_custt'             => 'nullable|integer',

            /** Item Invoice */
            'invoice_item_title'    => 'required|string|max:255',
            'invoice_item_rate'     => 'required|numeric|min:0',

            /** Project CRM */
            'pic_project_user_id'   => 'required|integer',
            'pic_company_user_id'   => 'required|integer',
        ]);

        DB::beginTransaction();

        try {

            /** =========================
             * Update Project Bukukas
             * ========================= */
            $project = ProjectBukukas::findOrFail($id);

            $project->update([
                'spk_code'          => $request->spk_code ?? '',
                'fid_custt'         => $request->fid_custt,
                'fid_cust'          => $request->fid_cust,
                'potongan'          => $request->potongan,
                'termin'            => $request->termin,
                'inv_date'          => $request->inv_date,
                'inv_contract_date' => $request->inv_contract_date,
                'fid_tax'           => $request->fid_tax,

                // status tetap
                'status'            => $project->status ?? 'draft',
                'paid'              => $project->paid ?? 'Not Paid',

                // optional
                'updated_at'        => Carbon::now(),
            ]);

            /** =========================
             * Update Item Invoice
             * ========================= */
            ProjectItemsBukukas::updateOrCreate(
                ['fid_invoices' => $project->id],
                [
                    'title' => $request->invoice_item_title,
                    'rate'  => $request->invoice_item_rate,
                    'total' => $request->invoice_item_rate,
                ]
            );

            /** =========================
             * Update / Create Project CRM
             * ========================= */
            Project::updateOrCreate(
                ['project_bukukas_id' => $project->id],
                [
                    'pic_project_user_id' => $request->pic_project_user_id,
                    'pic_company_user_id' => $request->pic_company_user_id,
                    'is_active'           => 1,
                ]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Project updated successfully.',
                'data'    => $project->fresh()
            ], 200);
        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update project bukukas',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    function get_tickets(Request $request, $project_id)
    {
        $sort_by = $request->sort_by ?? 'asc';
        $tickets = Ticket::with(['attachments', 'messages', 'reporterUser'])->where('project_id', $project_id);
        if ($sort_by == 'asc' || $sort_by == 'desc') {
            $tickets = $tickets->orderBy('created_at', $sort_by);
        }
        $tickets = $tickets->get();
        return $this->successResponseData("Tickets Data", $tickets);
    }
}
