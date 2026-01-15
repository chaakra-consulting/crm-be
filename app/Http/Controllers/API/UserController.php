<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\SDMProjectPerusahaan;
use App\Models\SDMUserProject;
use App\Models\User;
use App\Services\Helpers;
use App\Services\Remappers;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        if ($request->daterange) {
            [$startRaw, $endRaw] = explode(' - ', $request->daterange);
            $startFormatted = Carbon::createFromFormat('m/d/Y', trim($startRaw))->format('d/m/Y');
            $endFormatted   = Carbon::createFromFormat('m/d/Y', trim($endRaw))->format('d/m/Y');

            $start = Carbon::createFromFormat('d/m/Y', $startFormatted)->startOfDay();
            $end   = Carbon::createFromFormat('d/m/Y', $endFormatted)->endOfDay();
        } else {
            $start = null;
            $end = null;
        }

        $users = User::filterRoles($request->roles)
            ->filterDateRange($start, $end)
            ->get();

        $remapper = new Remappers();
        $remapUsers = $remapper->remapUsers($users);

        return response()->json($remapUsers);
    }

    public function show(Request $request, $id = null): JsonResponse
    {
        $showType   = $request->query('show_type');
        $emailToken = $request->query('email_token');

        $remapper = new Remappers();

        if ($showType === 'activation') {

            if (!$emailToken) {
                return response()->json([
                    'message' => 'Email token wajib diisi'
                ], 422);
            }

            $user = User::where('email_token', $emailToken)->first();

            if (!$user) {
                return response()->json([
                    'message' => 'Token aktivasi tidak valid atau sudah digunakan'
                ], 404);
            }

            return response()->json([
                'data' => $remapper->mapUserActivationItem($user)
            ]);
        }

        // default show by id
        if (!$id) {
            return response()->json([
                'message' => 'User ID wajib diisi'
            ], 422);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'data' => $remapper->mapUserItem($user)
        ]);
    }

    public function indexRoles(Request $request): JsonResponse
    {
        $roles = Role::get();
        return response()->json($roles);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'sdm_user_id' => 'nullable',
            'name' => 'required|string|max:255',
            'username' => 'nullable|unique:users,username|max:255',
            'email' => 'nullable|unique:users,email|email|max:255',
            'is_active' => 'required|boolean',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:5120',
            'password' => 'required|string|min:8|confirmed',
        ]);

        DB::beginTransaction();

        try {
            // Create user
            $user = User::create([
                'role_id' => $request->role_id  ?? null,
                'sdm_user_id' => $request->sdm_user_id  ?? null,
                'name' => $request->name ?? null,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_active' => $request->is_active,
            ]);

            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('users', 'public');

                $user->update([
                    'photo' => $path
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'User created successfully.',
                'data' => $user
            ], 201);
        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Failed to create user.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'sdm_user_id' => 'nullable',
            'name' => 'required|string|max:255',
            'username' => 'nullable|max:255|unique:users,username,' . $id,
            'email' => 'nullable|email|max:255|unique:users,email,' . $id,
            'is_active' => 'required|boolean',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:5120',
        ]);

        DB::beginTransaction();

        try {

            $user = User::findOrFail($id);

            $user->update([
                'role_id' => $request->role_id  ?? null,
                'sdm_user_id' => $request->sdm_user_id  ?? null,
                'name' => $request->name ?? null,
                'username' => $request->username,
                'email' => $request->email,
                'is_active' => $request->is_active,
            ]);

            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('users', 'public');

                $user->update([
                    'photo' => $path
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'User updated successfully.',
                'data' => $user
            ], 201);
        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Gagal membuat user.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function changePassword(Request $request, $id): JsonResponse
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        DB::beginTransaction();

        try {

            $user = User::findOrFail($id);

            $user->update([
                'password' => Hash::make($request->password),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'User updated successfully.',
                'data' => $user
            ], 201);
        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Gagal mengubah password.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function activateAccount(Request $request, $id): JsonResponse
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        DB::beginTransaction();

        try {

            $user = User::findOrFail($id);

            $user->update([
                'password' => Hash::make($request->password),
                'email_token' => null,
                'email_verified_at' => now(),
                'is_active' => 1,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'User updated successfully.',
                'data' => $user
            ], 201);
        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Gagal mengubah password.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'message' => 'user deleted successfully.'
        ]);
    }

    public function indexUserSDM(Request $request): JsonResponse
    {
        try {
            $response = Http::get(config('services.sdm.url') . '/users/index');

            if (!$response->successful()) {
                return response()->json([
                    'message' => 'Gagal mengambil data user dari API SDM',
                    "detail" => $response->status()
                ], $response->status());
            }

            // Kembalikan response apa adanya
            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat menghubungi API SDM',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function indexUserFromUserSDMByBukukasProject(Request $request): JsonResponse
    {
        $bukukasProjectId = $request->bukukas_project_id;

        $sdmProject = SDMProjectPerusahaan::where('ref_bukukas_id', '=', $bukukasProjectId)->first();
        $userProject = SDMUserProject::where('project_perusahaan_id', '=', $sdmProject->id)->get();

        if (!$sdmProject) {
            return response()->json([
                'message' => 'Bukukas project tidak ditemukan',
            ], 404);
        }

        $sdmUserIds = collect($userProject)
            ->pluck('user_id')
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        // dd($sdmUserIds);

        $users = User::whereIn('sdm_user_id', $sdmUserIds)->get();

        $remapper = new Remappers();
        $remapUsers = $remapper->remapUsers($users);

        return response()->json($remapUsers);
    }
}
