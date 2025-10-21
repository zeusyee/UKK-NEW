<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subtask extends Model
{
    protected $primaryKey = 'subtask_id';
    protected $fillable = [
        'card_id',
        'subtask_title',
        'description',
        'status',
        'estimated_hours',
        'actual_hours',
        'position'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'status' => 'string',
        'estimated_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
        'position' => 'integer'
    ];

    // Relationships
    public function card()
    {
        return $this->belongsTo(Card::class, 'card_id', 'card_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'subtask_id', 'subtask_id');
    }

    public function timeLogs()
    {
        return $this->hasMany(TimeLog::class, 'subtask_id', 'subtask_id');
    }
}