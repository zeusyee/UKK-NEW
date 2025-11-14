<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $primaryKey = 'card_id';
    public $keyType = 'int';
    public $incrementing = true;

    public function getRouteKeyName()
    {
        return 'card_id';
    }

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
        'actual_hours',
        'help_requested',
        'help_requested_at',
        'help_message'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'due_date' => 'date',
        'status' => 'string',
        'priority' => 'string',
        'estimated_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
        'position' => 'integer',
        'help_requested' => 'boolean',
        'help_requested_at' => 'datetime'
    ];

    // Relationships
    public function board()
    {
        return $this->belongsTo(Board::class, 'board_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function cardAssignments()
    {
        return $this->hasMany(CardAssignment::class, 'card_id');
    }

    public function subtasks()
    {
        return $this->hasMany(Subtask::class, 'card_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'card_id');
    }

    public function timeLogs()
    {
        return $this->hasMany(TimeLog::class, 'card_id');
    }

    /**
     * Get the active assignment for this card
     */
    public function getActiveAssignment()
    {
        return CardAssignment::getActiveAssignment($this->card_id);
    }

    /**
     * Assign card to a user
     */
    public function assignToUser($userId)
    {
        $this->update(['assigned_user_id' => $userId, 'status' => 'in_progress']);
        
        // Create CardAssignment record
        return CardAssignment::createAssignment($this->card_id, $userId);
    }

    /**
     * Get all assignments history for this card
     */
    public function getAssignmentHistory()
    {
        return $this->cardAssignments()->orderBy('assigned_at', 'desc')->get();
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

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // When card is updated with assigned_user_id
        static::updating(function ($card) {
            // Check if assigned_user_id is being changed
            if ($card->isDirty('assigned_user_id') && $card->assigned_user_id) {
                // Create new CardAssignment record
                CardAssignment::createAssignment($card->card_id, $card->assigned_user_id);
            }
        });
    }
}