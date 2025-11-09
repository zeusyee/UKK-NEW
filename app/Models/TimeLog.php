<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeLog extends Model
{
    protected $primaryKey = 'log_id';
    protected $fillable = [
        'card_id',
        'subtask_id',
        'user_id',
        'start_time',
        'end_time',
        'duration_minutes',
        'description'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'duration_minutes' => 'integer'
    ];

    // Relationships
    public function card()
    {
        return $this->belongsTo(Card::class, 'card_id');
    }

    public function subtask()
    {
        return $this->belongsTo(Subtask::class, 'subtask_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function board()
    {
        return $this->hasOneThrough(
            Board::class,
            Card::class,
            'card_id',
            'board_id',
            'card_id',
            'board_id'
        );
    }

    public function project()
    {
        return $this->hasOneThrough(
            Project::class,
            Board::class,
            'board_id',
            'project_id',
            'card_id',
            'project_id'
        );
    }
}