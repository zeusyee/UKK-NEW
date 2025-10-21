<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $primaryKey = 'card_id';
    protected $fillable = [
        'board_id',
        'card_title',
        'description',
        'position',
        'created_by',
        'due_date',
        'status',
        'priority',
        'estimated_hours',
        'actual_hours'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'due_date' => 'date',
        'status' => 'string',
        'priority' => 'string',
        'estimated_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
        'position' => 'integer'
    ];

    // Relationships
    public function board()
    {
        return $this->belongsTo(Board::class, 'board_id', 'board_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function subtasks()
    {
        return $this->hasMany(Subtask::class, 'card_id', 'card_id');
    }

    public function assignments()
    {
        return $this->hasMany(CardAssignment::class, 'card_id', 'card_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'card_id', 'card_id');
    }

    public function timeLogs()
    {
        return $this->hasMany(TimeLog::class, 'card_id', 'card_id');
    }
}