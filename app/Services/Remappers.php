<?php
namespace App\Services;

class Remappers
{
    public function remapContacts($contacts)
    {
        return collect($contacts)->map(fn($item) => [
            'id'              => $item->id,
            'name'            => $item->name,
            'title_name'      => $item->title_name,
            'phone_number_1'  => $item->phone_number_1,
            'phone_number_2'  => $item->phone_number_2,
            'address'         => $item->address,
            'date_of_birth'   => $item->date_of_birth,
            'company_id'      => $item->company_id,
            'company_name'    => $item->company?->name,
            'source_id'       => $item->source_id,
            'source_name'     => $item->source?->name,
            'owner_user_id'   => $item->owner_user_id,
            'owner_user_name' => $item->owner?->name,
            'province_id'     => $item->province_id,
            'province_name'   => $item->province?->name,
            'city_id'         => $item->city_id,
            'city_name'       => $item->city?->name,
            'created_at'      => $item->created_at?->format('Y-m-d H:i:s'),
            'tags'            => $item->tags->map(fn($tag) => [
                                    'id'    => $tag->id,
                                    'name'  => $tag->name,
                                    'color' => $tag->color,
                                ]),
        ]);
    }
}
