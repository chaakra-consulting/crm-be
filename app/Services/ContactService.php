<?php

namespace App\Services;

use App\Models\{
    User,
    Contact,
    Company,
    Tag,
    ContactTag,
    SocialMedia,
    ContactSocialMedia
};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\Helpers;

class ContactService
{
    public function createContact(array $data, $photo = null)
    {
        DB::beginTransaction();

        try {
            // Create user
            $user = User::create([
                'role_id'   => 7,
                'username'  => $data['username'] ?? null,
                'name'      => $data['name'],
                'email'     => $data['email'] ?? null,
                'is_active' => 0,
            ]);

            $localCompany = !empty($data['company_id'])
                ? Company::where('company_bukukas_id', $data['company_id'])->first()
                : null;

            // Create contact
            $contact = Contact::create([
                'user_id'         => $user->id,
                'company_id'      => $localCompany ? $localCompany->id : null,
                'source_id'       => $data['source_id'] ?? null,
                'owner_user_id'   => $data['owner_user_id'] ?? null,
                'name'            => $data['name'],
                'title_name'      => $data['title_name'] ?? null,
                'date_of_birth'   => $data['date_of_birth'] ?? null,
                'phone_number_1'  => $data['phone_number_1'],
                'phone_number_2'  => $data['phone_number_2'] ?? null,
                'address'         => $data['address'] ?? null,
                'province_id'     => $data['province_id'] ?? null,
                'city_id'         => $data['city_id'] ?? null,
            ]);

            // Upload photo
            if ($photo) {
                $path = $photo->store('contacts', 'public');
                $user->update(['photo' => $path]);
                $contact->update(['photo' => $path]);
            }


            // Handle tags
            if (!empty($data['tags'])) {
                foreach ($data['tags'] as $cTag) {
                    $tagName = $cTag['name'];
                    $slug = Str::slug($tagName);

                    $tag = Tag::firstOrCreate(
                        ['slug' => $slug],
                        [
                            'name'  => $tagName,
                            'color' => Helpers::tagsColorTextToColor(null),
                        ]
                    );

                    ContactTag::create([
                        'contact_id' => $contact->id,
                        'tag_id'     => $tag->id,
                    ]);
                }
            }

            // Handle social media
            $socmeds = SocialMedia::whereIn(
                'slug',
                ['facebook', 'instagram', 'twitterx', 'whatsapp']
            )->get();

            foreach ($socmeds as $socmed) {
                if (!empty($data[$socmed->slug])) {
                    ContactSocialMedia::create([
                        'contact_id'       => $contact->id,
                        'social_media_id'  => $socmed->id,
                        'detail'           => $data[$socmed->slug],
                    ]);
                }
            }

            DB::commit();
            return $contact;

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function setCompanyId($contactId, $companyId)
    {
        if($contactId){
            $contact = Contact::findOrFail($contactId);

            if(!$contact->company_id){
                $contact->update([
                    'company_id' => $companyId,
                ]);
                return $contact;
            }
        }
        return null;
    }
}
