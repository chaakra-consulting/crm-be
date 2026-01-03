<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contact extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'contacts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    public function scopeFilterDateRange($query, $start, $end)
    {
        if ($start && $end) {
            $query->whereBetween('created_at', [$start, $end]);
        }
        return $query;
    }

    public function scopeFilterNoCompany($query, $condition = false)
    {
        if ($condition == true) {
            return $query->where('company_id', null);
        }
        return $query;
    }

    public function scopeFilterByCompany($query, $company_id)
    {
        if (!empty($company_id)) {
            return $query->where('company_id', $company_id);
        }
        return $query;
    }

    public function scopeFilterByCompanyBukukas($query, $company_id)
    {
        if (!empty($company_id)) {
            $localCompany = $company_id ? Company::where('company_bukukas_id', $company_id)->first() : null;
            return $query->where('company_id', $localCompany->id);
        }
        return $query;
    }

    public function scopeFilterOwners($query, $owners)
    {
        if (!empty($owners)) {
            // owners = array of IDs
            return $query->whereIn('owner_user_id', $owners);
        }
        return $query;
    }

    public function scopeFilterTags($query, $tags)
    {
        if (!empty($tags)) {
            foreach ($tags as $tagId) {
                $query->whereHas('tags', function ($q) use ($tagId) {
                    $q->where('tags.id', $tagId);
                });
            }
        }
        // if (!empty($tags)) {
        //     return $query->whereHas('tags', function ($q) use ($tags) {
        //         $q->whereIn('tags.id', $tags);
        //     });
        // }
        return $query;
    }

    /**
     * 🔹 Relasi ke user yang membuat contact
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 🔹 Relasi ke user yang menjadi owner contact
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    /**
     * 🔹 Relasi ke sumber leads (source)
     */
    public function source()
    {
        return $this->belongsTo(Source::class);
    }

    /**
     * 🔹 Relasi ke provinsi
     */
    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    /**
     * 🔹 Relasi ke kota
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * 🔹 Relasi ke perusahaan (jika ada table companies)
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Many-to-Many: Contact ↔ Tag
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'contact_tags')
                    ->withTimestamps();
    }
    /**
     * Many-to-Many: Contact ↔ Social Media
     */
    public function socialMedias()
    {
        return $this->belongsToMany(SocialMedia::class, 'contact_social_medias')
                    ->withPivot('detail');
    }
}
