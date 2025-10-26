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
     * Check and update card completion status based on subtasks
     */
    public function checkAndUpdateCompletion()
    {
        $subtasks = $this->subtasks;

        // If no subtasks exist, do nothing
        if ($subtasks->isEmpty()) {
            return;
        }

        // Check if all subtasks are done
        $allDone = $subtasks->every(function ($subtask) {
            return $subtask->status === 'done';
        });

        // Check if any subtask is in progress
        $anyInProgress = $subtasks->contains(function ($subtask) {
            return $subtask->status === 'in_progress';
        });

        // Update card status
        if ($allDone) {
            $this->status = 'done';
        } elseif ($anyInProgress) {
            $this->status = 'in_progress';
        } else {
            // If none are in progress and not all are done, keep as todo
            $anyStarted = $subtasks->contains(function ($subtask) {
                return in_array($subtask->status, ['in_progress', 'review', 'done']);
            });
            
            if ($anyStarted) {
                $this->status = 'in_progress';
            } else {
                $this->status = 'todo';
            }
        }

        $this->save();
    }

    /**
     * Get the currently assigned user (from active subtask)
     */
    public function getCurrentlyAssignedUser()
    {
        $activeSubtask = $this->subtasks()
            ->where('status', 'in_progress')
            ->with('assignedUser')
            ->first();

        return $activeSubtask ? $activeSubtask->assignedUser : null;
    }

    /**
     * Check if card can have a new subtask started
     */
    public function canStartNewSubtask()
    {
        return !$this->subtasks()
            ->where('status', 'in_progress')
            ->exists();
    }

    /**
     * Get progress percentage based on completed subtasks
     */
    public function getProgressPercentage()
    {
        $totalSubtasks = $this->subtasks->count();
        
        if ($totalSubtasks === 0) {
            return 0;
        }

        $completedSubtasks = $this->subtasks->where('status', 'done')->count();
        
        return round(($completedSubtasks / $totalSubtasks) * 100, 1);
    }

    /**
     * Get subtasks count by status
     */
    public function getSubtasksCountByStatus()
    {
        $subtasks = $this->subtasks;
        
        return [
            'total' => $subtasks->count(),
            'todo' => $subtasks->where('status', 'todo')->count(),
            'in_progress' => $subtasks->where('status', 'in_progress')->count(),
            'review' => $subtasks->where('status', 'review')->count(),
            'done' => $subtasks->where('status', 'done')->count(),
        ];
    }
}