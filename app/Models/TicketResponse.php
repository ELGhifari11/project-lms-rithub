<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketResponse extends Model
{
    /** @use HasFactory<\Database\Factories\TicketResponseFactory> */
    use HasFactory;

    protected $table = 'ticket_responses';


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'ticket_id',
        'responder_id',
        'response',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public $timestamps = false; // Karena hanya ada created_at


    /**
     * Get the ticket that owns the response.
     */
    public function ticket()
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }

    /**
     * Get the user who responded to the ticket.
     */
    public function responder()
    {
        return $this->belongsTo(User::class, 'responder_id');
    }
}
