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
        'assigned_user_id',
        'assignment_status',
        'started_at',
        'completed_at',
        'due_date',
        'status',
        'priority',
        'estimated_hours',
        'actual_hours'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'due_date' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'status' => 'string',
        'priority' => 'string',
        'assignment_status' => 'string',
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

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id', 'user_id');
    }

    public function subtasks()
    {
        return $this->hasMany(Subtask::class, 'card_id', 'card_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'card_id', 'card_id');
    }

    public function timeLogs()
    {
        return $this->hasMany(TimeLog::class, 'card_id', 'card_id');
    }

    /**
     * Check if any subtask is currently in progress
     */
    public function hasSubtaskInProgress()
    {
        return $this->subtasks()->where('status', 'in_progress')->exists();
    }

    /**
     * Check if all subtasks are completed
     */
    public function allSubtasksCompleted()
    {
        $subtaskCount = $this->subtasks()->count();
        if ($subtaskCount === 0) {
            return false; // No subtasks means not completed
        }
        
        return $this->subtasks()->where('status', 'done')->count() === $subtaskCount;
    }

    /**
     * Check if card can be started (no subtasks in progress)
     */
    public function canBeStarted()
    {
        return !$this->hasSubtaskInProgress();
    }

    /**
     * Check if any subtask can be started (no other subtask in progress)
     */
    public function canStartSubtask()
    {
        return !$this->hasSubtaskInProgress();
    }

    /**
     * Update card status based on subtask completion
     */
    public function updateStatusBasedOnSubtasks()
    {
        if ($this->allSubtasksCompleted()) {
            $this->update([
                'status' => 'done',
                'completed_at' => now()
            ]);
        } elseif ($this->hasSubtaskInProgress()) {
            $this->update([
                'status' => 'in_progress',
                'started_at' => $this->started_at ?? now()
            ]);
        } else {
            $this->update([
                'status' => 'todo',
                'started_at' => null,
                'completed_at' => null
            ]);
        }
    }
}