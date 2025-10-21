<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $primaryKey = 'comment_id';
    protected $fillable = [
        'card_id',
        'subtask_id',
        'user_id',
        'comment_text',
        'comment_type'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'comment_type' => 'string'
    ];

    // Relationships
    public function card()
    {
        return $this->belongsTo(Card::class, 'card_id', 'card_id');
    }

    public function subtask()
    {
        return $this->belongsTo(Subtask::class, 'subtask_id', 'subtask_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}