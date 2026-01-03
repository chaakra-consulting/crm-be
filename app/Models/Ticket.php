<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tickets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    public function scopeByReporterId($query, $userId = null)
    {
        return $query->when($userId, function ($q) use ($userId) {
            $q->where('reporter_user_id', $userId);
        });
    }

    public function scopeByAssignedId($query, $userId = null)
    {
        return $query->when($userId, function ($q) use ($userId) {
            $q->where('assigned_user_id', $userId);
        });
    }

    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class)
            ->whereNull('ticket_message_id');
    }

    public function allAttachments()
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function messages()
    {
        return $this->hasMany(TicketMessage::class);
    }

    public function reporterUser()
    {
        return $this->belongsTo(User::class, 'reporter_user_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}
