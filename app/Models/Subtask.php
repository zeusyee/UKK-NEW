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
        'position',
        'created_by',
        'assigned_user_id',
        'started_at',
        'paused_at',
        'total_paused_seconds',
        'completed_at',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'completion_notes'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'started_at' => 'datetime',
        'paused_at' => 'datetime',
        'completed_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'status' => 'string',
        'estimated_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
        'position' => 'integer',
        'total_paused_seconds' => 'integer'
    ];

    // Relationships
    public function card()
    {
        return $this->belongsTo(Card::class, 'card_id', 'card_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id', 'user_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by', 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'subtask_id', 'subtask_id');
    }

    public function timeLogs()
    {
        return $this->hasMany(TimeLog::class, 'subtask_id', 'subtask_id');
    }

    /**
     * Check if there's another subtask in progress in the same card
     */
    public function hasOtherInProgressSubtasks()
    {
        return Subtask::where('card_id', $this->card_id)
            ->where('subtask_id', '!=', $this->subtask_id)
            ->where('status', 'in_progress')
            ->exists();
    }

    /**
     * Start this subtask (assign to user and mark as in_progress)
     */
    public function startSubtask($userId)
    {
        // Check if another subtask is already in progress
        if ($this->hasOtherInProgressSubtasks()) {
            throw new \Exception('Another subtask is already in progress. Please complete it first.');
        }

        $this->assigned_user_id = $userId;
        $this->status = 'in_progress';
        $this->started_at = now();
        $this->save();

        // Update card status to in_progress if it's still todo
        if ($this->card && $this->card->status === 'todo') {
            $this->card->status = 'in_progress';
            $this->card->save();
        }

        return $this;
    }

    /**
     * Complete this subtask
     */
    public function completeSubtask($completionNotes = null)
    {
        $this->status = 'done';
        $this->completed_at = now();
        if ($completionNotes) {
            $this->completion_notes = $completionNotes;
        }
        $this->save();

        // Check if all subtasks are done, then mark card as done
        $this->card->checkAndUpdateCompletion();

        return $this;
    }

    /**
     * Boot method to handle events
     */
    protected static function boot()
    {
        parent::boot();

        // When subtask is created, update card status
        static::created(function ($subtask) {
            // If card was 'done' and new subtask is added, move back to in_progress
            if ($subtask->card && $subtask->card->status === 'done') {
                $subtask->card->status = 'in_progress';
                $subtask->card->save();
            } else {
                $subtask->card->checkAndUpdateCompletion();
            }
        });

        // When subtask status changes, update card status
        static::updated(function ($subtask) {
            if ($subtask->isDirty('status')) {
                $subtask->card->checkAndUpdateCompletion();
            }
        });

        // When subtask is deleted, update card status
        static::deleted(function ($subtask) {
            if ($subtask->card) {
                $subtask->card->checkAndUpdateCompletion();
            }
        });
    }
}