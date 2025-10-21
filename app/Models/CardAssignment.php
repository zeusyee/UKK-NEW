<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CardAssignment extends Model
{
    protected $primaryKey = 'assignment_id';
    protected $fillable = [
        'card_id',
        'user_id',
        'assignment_status',
        'started_at',
        'completed_at'
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'assignment_status' => 'string'
    ];

    // Relationships
    public function card()
    {
        return $this->belongsTo(Card::class, 'card_id', 'card_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}