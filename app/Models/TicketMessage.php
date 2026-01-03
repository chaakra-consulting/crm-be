<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketMessage extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ticket_messages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }
}
