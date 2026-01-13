<?php

namespace App\Services;

use App\Models\Source;
use Carbon\Carbon;

class Remappers
{
    public function remapContacts($contacts)
    {
        // $source = Source::get();
        return collect($contacts)->map(fn($item) => [
            'id'              => $item->id,
            'name'            => $item->name,
            'title_name'      => $item->title_name,
            'phone_number_1'  => $item->phone_number_1,
            'phone_number_2'  => $item->phone_number_2,
            'email'           => $item->user?->email,
            'email_token'     => $item->user?->token,
            'address'         => $item->address,
            'date_of_birth'   => $item->date_of_birth,
            'company_id'      => $item->company?->company_bukukas_id,
            'company_name'    => $item->company?->name,
            'source_id'       => $item->source_id,
            'source_name'     => $item->source?->name,
            'user_id'         => $item->user_id,
            'user_name'       => $item->user?->name,
            'owner_user_id'   => $item->owner_user_id,
            'owner_user_name' => $item->owner?->name,
            'province_id'     => $item->province_id,
            'province_name'   => $item->province?->name,
            'city_id'         => $item->city_id,
            'city_name'       => $item->city?->name,
            'created_at'      => $item->created_at?->format('Y-m-d H:i:s'),
            'created_at_format' => $item->created_at ? Carbon::parse($item->created_at)->locale('id')->translatedFormat('d F Y H:i:s') : null,
            'photo_path'      => $item->photo ? asset('storage/' . $item->photo) : null,
            'photo_url'      => $item->photo ? url('storage/' . $item->photo) : null,
            'facebook'         => $item->socialMedias->where('slug', 'facebook')->first()->pivot->detail ?? null,
            'twitterx'         => $item->socialMedias->where('slug', 'twitterx')->first()->pivot->detail ?? null,
            'instagram'        => $item->socialMedias->where('slug', 'instagram')->first()->pivot->detail ?? null,
            'whatsapp'         =>  $item->socialMedias->where('slug', 'whatsapp')->first()->pivot->detail ?? null,
            'tags'            => $item->tags->map(fn($tag) => [
                'id'    => $tag->id,
                'name'  => $tag->name,
                'slug'  => $tag->slug,
                'color' => $tag->color,
            ]),
        ]);
    }

    public function remapSources($sources)
    {
        // $source = Source::get();
        return collect($sources)->map(fn($item) => [
            'id'              => $item->id,
            'name'            => $item->name,
            'slug'            => $item->slug,
            'is_active'       => $item->is_active,
            'is_active_text'  => $item->is_active ? 'Aktif' : 'Tidak Aktif',
            'created_at'      => $item->created_at?->format('Y-m-d H:i:s'),
        ]);
    }

    public function remapTags($tags)
    {
        // $source = Source::get();
        return collect($tags)->map(fn($item) => [
            'id'              => $item->id,
            'name'            => $item->name,
            'slug'            => $item->slug,
            'color'           => $item->color,
            'color_text'      => Helpers::tagsColorToColorText($item->color),
            'created_at'      => $item->created_at?->format('Y-m-d H:i:s'),
        ]);
    }

    public function remapCompanies($companies)
    {
        return collect($companies)->map(fn($item) => [
            'id'              => $item->id,
            'name'            => $item->name,
            'code'            => $item->code,
            'jenis'           => $item->jenis,
            'bentuk'          => $item->bentuk,
            'npwp'            => $item->npwp,
            'address'         => $item->address,
            'memo'            => $item->memo,
            'deleted'         => $item->deleted,
            'pic_contact_id'  => $item->local ? $item->local->pic_contact_id : null,
            'pic_contact_name' => $item->local && $item->local->picContact ? $item->local->picContact->name : null,
            'province_id'     => $item->local ? $item->local->province_id : null,
            'province_name'   => $item->local && $item->local->province_id ? $item->province?->name : null,
            'city_id'         => $item->local ? $item->local->city_id : null,
            'city_name'       => $item->local && $item->local->city_id ? $item->city?->name : null,
            'created_at'      => $item->created_at,
            'created_at_format' => $item->created_at ? Carbon::parse($item->created_at)->locale('id')->translatedFormat('d F Y H:i:s') : null,
        ]);
    }


    public function mapUserActivationItem($item)
    {
        return [
            'id'                => $item->id,
            'name'              => $item->name,
            'email_token'       => $item->email_token,
            'is_active'         => $item->is_active,
            'is_active_text'    => $item->is_active ? 'Aktif' : 'Tidak Aktif',
            'photo_path'        => $item->photo ? asset('storage/' . $item->photo) : null,
            'photo_url'         => $item->photo ? url('storage/' . $item->photo) : null,
        ];
    }

    public function mapUserItem($item)
    {
        return [
            'id'                => $item->id,
            'name'              => $item->name,
            'role_id'           => $item->role_id,
            'role_slug'         => $item->role?->slug,
            'role_name'         => $item->role?->name,
            "sdm_user_id"       => $item->sdm_user_id,
            'username'          => $item->username,
            'email'             => $item->email,
            'email_verified_at' => $item->email_verified_at,
            'is_password'       => $item->password ? true : false,
            'is_active'         => $item->is_active,
            'is_active_text'    => $item->is_active ? 'Aktif' : 'Tidak Aktif',
            'photo_path'        => $item->photo ? asset('storage/' . $item->photo) : null,
            'photo_url'         => $item->photo ? url('storage/' . $item->photo) : null,
            "photo"             => $item->photo ? url('storage/' . $item->photo) : null,
            'created_at'        => $item->created_at,
            'created_at_format' => $item->created_at ? Carbon::parse($item->created_at)->locale('id')->translatedFormat('d F Y H:i:s') : null,
            'updated_at'        => $item->updated_at,
            'updated_at_format' => $item->updated_at ? Carbon::parse($item->updated_at)->locale('id')->translatedFormat('d F Y H:i:s') : null,
        ];
    }

    public function remapUsers($users)
    {
        return collect($users)->map(fn($item) => $this->mapUserItem($item));
    }

    public function mapProjectItem($item, $withDetail = false)
    {
        $local = $item->local;
        $picContact = $local?->picContact;
        $picCompany = $local?->picCompany;
        $projectItem = $item->item;

        $invoiceTotalSummary = $withDetail ? Helpers::getInvoicesTotalSummary($item->id) : null;
        $payments = $withDetail ? $item->payments : null;
        return [
            'id'                => $item->id,
            'code'              => $item->code,
            'spk_code'          => $item->spk_code,
            'fid_cust'          => $item->fid_cust ? $item->fid_cust : ($item->fid_custtt ? $item->fid_custtt : $item->fid_custttt),
            'company_name'      => $item->companyBukukas?->name,
            'fid_custt'         => $item->fid_custt,

            'no_inv'            => $item->no_inv,
            'coa_sales'         => $item->coa_sales,
            'inv_address'       => $item->inv_address,
            'status_bukukas'    => $item->status,
            'paid'              => $item->paid,
            'fid_tax'           => $item->fid_tax,
            'termin'            => $item->termin,
            'currency'          => $item->currency,
            'sub_total'         => $item->sub_total,
            'penjualan'         => $item->penjualan,
            'potongan'          => $item->potongan,
            'amount'            => $item->amount,
            'residual'          => $item->residual,

            'inv_date'          => $item->inv_date,
            'inv_date_format'   => $item->inv_date
                ? Carbon::parse($item->inv_date)->locale('id')->translatedFormat('d F Y')
                : null,

            'inv_contract_date' => $item->inv_contract_date,
            'inv_contract_date_format' => $item->inv_contract_date
                ? Carbon::parse($item->inv_contract_date)->locale('id')->translatedFormat('d F Y')
                : null,

            'created_at'        => $item->created_at,
            'created_at_format' => $item->created_at
                ? Carbon::parse($item->created_at)->locale('id')->translatedFormat('d F Y H:i:s')
                : null,

            'is_verified'       => $item->is_verified,
            'dikirim'           => $item->dikirim,
            'tgl_dikirim'       => $item->tgl_dikirim,
            'keterangan'        => $item->keterangan,
            'deleted'           => $item->deleted,

            'title'             => $projectItem?->title,
            'total'             => $projectItem?->total,

            'pic_project_user_id' => $local?->pic_project_user_id,
            'pic_project_name'    => $picContact?->name,
            'pic_company_user_id' => $local?->pic_company_user_id,
            'pic_company_name'    => $picCompany?->name,
            'rewards'             => $local?->rewards,
            'feedback_point'      => $local?->feedback_point,
            'feedback_text'       => $local?->feedback_text,
            'is_active'           => $local?->is_active,


            "status"               => "proses",
            "progress"             => $local?->progress,
            'invoice_total_summary' => $invoiceTotalSummary,
            'payments'              => $payments,
        ];
    }

    public function remapProjects($projects)
    {
        // try {
        return collect($projects)->map(fn($item) => $this->mapProjectItem($item));
        // } catch (\Throwable $e) {
        //     dd($e->getMessage(), $e->getLine());
        // }
    }

    public function mapTicketMessage($item)
    {
        $attachments = $item->attachments ?: [];
        return [
            'id'                => $item->id,
            'user'              => $item->user_id,
            'user_name'         => $item->user?->name,
            'ticket'            => $item->ticket_id,
            'message'           => $item->message,
            'created_at'        => $item->created_at,
            'created_at_format' => $item->created_at
                ? Carbon::parse($item->created_at)->locale('id')->translatedFormat('d F Y H:i:s')
                : null,
            'attachments'       => $attachments,
        ];
    }

    public function mapTicketItem($item, $withRelation = false)
    {
        $attachments = $withRelation ? $item->attachments : [];
        $messages = $withRelation ? collect($item->messages)
            ->sortByDesc('created_at')
            ->values()
            ->map(fn($m) => $this->mapTicketMessage($m))
            : [];

        return [
            'id'                => $item->id,
            'ticket_number'     => $item->ticket_number,
            'title'             => $item->title,
            'description'       => $item->description,
            'priority'          => $item->priority ? Helpers::prioritySlugToText($item->priority) : null,
            'priority_slug'     => $item->priority ? $item->priority : null,
            'status'            => $item->status ? Helpers::statusSlugToText($item->status) : null,
            'status_slug'       => $item->status ? $item->status : null,
            'type'              => $item->type ? Helpers::typeSlugToText($item->type) : null,
            'type_slug'         => $item->type ? $item->type : null,
            'project_id'        => $item->project?->id,
            'project_bukukas_id' => $item->project?->project_bukukas_id,
            'project_name'      => $item->project?->bukukas?->item?->title,
            'reporter_user_id' => $item->reporterUser?->id,
            'reporter_name'    => $item->reporterUser?->name,
            'assigned_user_id' => $item->assignedUser?->id,
            'assigned_name'    => $item->assignedUser?->name,
            'created_at'        => $item->created_at,
            'created_at_format' => $item->created_at
                ? Carbon::parse($item->created_at)->locale('id')->translatedFormat('d F Y H:i:s')
                : null,
            'updated_at'        => $item->updated_at,
            'updated_at_format' => $item->updated_at
                ? Carbon::parse($item->updated_at)->locale('id')->translatedFormat('d F Y H:i:s')
                : null,
            'attachments'       => $attachments,
            'messages'       => $messages,
        ];
    }

    public function remapTickets($projects)
    {
        // try {
        return collect($projects)->map(fn($item) => $this->mapTicketItem($item));
        // } catch (\Throwable $e) {
        //     dd($e->getMessage(), $e->getLine());
        // }
    }
}
