<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tkt_attachment extends Model
{
protected $table = 'tkt_attachments';

    protected $fillable = [
        'ticket_id',
        'file_path',
        'file_name',
        'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    // Relación con ticket
    public function ticket()
    {
        return $this->belongsTo(Tkt_ticket::class);
    }
}
