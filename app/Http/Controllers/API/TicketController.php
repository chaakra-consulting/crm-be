<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketMessage;
use App\Services\Helpers;
use App\Services\Remappers;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $auth = Auth::user();
        $roleSlug = $auth->role?->slug;

        $reporterUserId = $roleSlug == 'pic-customer' ? $auth->id : null;
        $assignedUserId = $roleSlug == 'pic-project' ? $auth->id : null;
        $tickets = Ticket::byReporterId($reporterUserId)
                        ->byAssignedId($assignedUserId)
                        ->latest()
                        ->get();

        $remapper = new Remappers();
        $remapTickets = $remapper->remapTickets($tickets);

        return response()->json($remapTickets);
    }

    public function detail($id): JsonResponse
    {
        $ticket = Ticket::findOrFail($id);

        $remapper = new Remappers();
        $remapTickets = $remapper->mapTicketItem($ticket, true);

        return response()->json($remapTickets);
    }


    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'priority'    => 'required|string',
            'type'        => 'required|string',
            'description' => 'required|string',
            'project_id' => 'nullable|exists:projects,id',
            'attachments'   => 'nullable|array',
            'attachments.*' => 'file|max:5120', // 5 MB per file
        ]);

        DB::beginTransaction();

        try {

            $userId = Auth::id();
            $ticketNumber = Helpers::generateTicketNumber();

            $ticket = Ticket::create([
                'reporter_user_id' => $userId,
                'ticket_number' => $ticketNumber,
                'title'       => $request->title,
                'priority'    => $request->priority,
                'type'        => $request->type,
                'description' => $request->description,
                'project_id' => $request->project_id,
                'status'      => 'waiting-approval',
            ]);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {

                    $path = $file->store('tickets/attachments', 'public');

                    TicketAttachment::create([
                        'ticket_id'         => $ticket->id,
                        'ticket_message_id' => null,
                        //'attachment'         => $file->getClientOriginalName(),
                        'attachment'         => $path,
                        'size'               => Helpers::formatFileSize($file->getSize()),
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Tiket berhasil dibuat',
                'data'    => $ticket,
            ], 201);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Gagal membuat tiket',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function storeMessage(Request $request, $id)
    {
        $request->validate([
            'message'    => 'required|string',
            'attachments'   => 'nullable|array',
            'attachments.*' => 'file|max:5120',
        ]);

        DB::beginTransaction();

        try {

            $userId = Auth::id();

            $ticket = Ticket::findOrFail($id);
            if($ticket->status == 'closed') {
                $ticket->update(['status'=>'on-progress']);
            }
            // elseif($ticket->status == 'on-progress' && $ticket->reporter_user_id == $userId){
            //     $ticket->update(['status'=>'customer-reply']);
            // }elseif($ticket->status == 'on-progress' && $ticket->assigned_user_id == $userId){
            //     $ticket->update(['status'=>'customer-reply']);
            // }

            $ticketMessage = TicketMessage::create([
                'ticket_id'     => $id,
                'user_id'       => $userId,
                'message'       => $request->message,
            ]);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {

                    $path = $file->store('tickets/attachments', 'public');

                    TicketAttachment::create([
                        'ticket_id'         => $id,
                        'ticket_message_id' => $ticketMessage->id,
                        //'attachment'         => $file->getClientOriginalName(),
                        'attachment'         => $path,
                        'size'               => Helpers::formatFileSize($file->getSize()),
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Jawaban berhasil dikirim',
                'data'    => $ticketMessage,
            ], 201);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Gagal membuat tiket',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id): JsonResponse
    {
        $request->validate([
            'status'            => 'required|string|max:255',
            // 'assigned_user_id'  => 'nullable|exists:users,id',
        ]);

        DB::beginTransaction();

        try {

            $ticket = Ticket::findOrFail($id);

            $ticket->update([
                "status"            => $request->status,
                // "assigned_user_id"  => $request->assigned_user_id ?? null,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Tiket berhasil diupdate.',
                'data'    => $ticket,
            ], 201);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Gagal Mengubah Data Tiket.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateApproval(Request $request, $id): JsonResponse
    {
        $request->validate([
            'approval'            => 'required|string|in:approved,rejected',
            'type'                => 'nullable|required_if:approval,approved|string|max:255',
            'priority'            => 'nullable|required_if:approval,approved|string|max:255',
            'assigned_user_id'    => 'nullable',
            'answer'              => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {

            $ticket = Ticket::findOrFail($id);

            if($request->approval == 'approved') {
                $newStatus = 'on-progress';
            } else {
                $newStatus = 'rejected';
            }

            $ticket->update([
                "status"            => $newStatus,
                "type"              => $request->type ?? $ticket->type,
                "priority"          => $request->priority ?? $ticket->priority,
                "assigned_user_id"  => $request->assigned_user_id ?? null,
                "answer"            => $request->answer ?? null,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Tiket berhasil diupdate.',
                'data'    => $ticket,
            ], 201);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Gagal Mengubah Data Tiket.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
