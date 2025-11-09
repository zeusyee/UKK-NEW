<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * CardAssignment Model
 * 
 * Tracks the lifecycle of card assignments to users.
 * Each assignment records when a card is assigned, when work starts/completes,
 * and the final status.
 */
class CardAssignment extends Model
{
    protected $primaryKey = 'assignment_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'card_id',
        'user_id',
        'assigned_at',
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

    protected $dates = ['assigned_at', 'started_at', 'completed_at'];

    // ========== RELATIONSHIPS ==========

    /**
     * Get the card this assignment belongs to
     */
    public function card()
    {
        return $this->belongsTo(Card::class, 'card_id');
    }

    /**
     * Get the user assigned to this card
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ========== SCOPES ==========

    /**
     * Scope: Get active assignments
     */
    public function scopeActive($query)
    {
        return $query->whereIn('assignment_status', ['assigned', 'in_progress']);
    }

    /**
     * Scope: Get completed assignments
     */
    public function scopeCompleted($query)
    {
        return $query->where('assignment_status', 'completed');
    }

    /**
     * Scope: Get rejected assignments
     */
    public function scopeRejected($query)
    {
        return $query->where('assignment_status', 'rejected');
    }

    /**
     * Scope: Get assignments for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Get assignments for a specific card
     */
    public function scopeForCard($query, $cardId)
    {
        return $query->where('card_id', $cardId);
    }

    /**
     * Scope: Get overdue assignments (past due_date and not completed)
     */
    public function scopeOverdue($query)
    {
        return $query->whereHas('card', function ($q) {
            $q->where('due_date', '<', now())
              ->where('status', '!=', 'done');
        })
        ->where('assignment_status', '!=', 'completed');
    }

    // ========== STATIC METHODS ==========

    /**
     * Get the active (most recent) assignment for a card
     */
    public static function getActiveAssignment($cardId)
    {
        return self::forCard($cardId)
            ->active()
            ->latest('assigned_at')
            ->first();
    }

    /**
     * Create a new assignment for a card
     * 
     * - Automatically marks previous active assignments as rejected
     * - Creates new assignment record
     */
    public static function createAssignment($cardId, $userId)
    {
        // Mark all active assignments for this card as rejected
        self::forCard($cardId)
            ->active()
            ->update(['assignment_status' => 'rejected']);

        // Create new assignment
        return self::create([
            'card_id' => $cardId,
            'user_id' => $userId,
            'assigned_at' => now(),
            'assignment_status' => 'assigned'
        ]);
    }

    // ========== INSTANCE METHODS ==========

    /**
     * Start the assignment (member begins work)
     */
    public function startAssignment()
    {
        $this->update([
            'assignment_status' => 'in_progress',
            'started_at' => now()
        ]);

        // Update card status to in_progress
        if ($this->card) {
            $this->card->update(['status' => 'in_progress']);
        }

        return $this;
    }

    /**
     * Complete the assignment (member finishes work)
     */
    public function completeAssignment()
    {
        $this->update([
            'assignment_status' => 'completed',
            'completed_at' => now()
        ]);

        return $this;
    }

    /**
     * Reject the assignment (cancel and reset)
     */
    public function rejectAssignment()
    {
        $this->update([
            'assignment_status' => 'rejected'
        ]);

        // Reset card to todo if needed
        if ($this->card && $this->card->status !== 'done') {
            $this->card->update(['status' => 'todo']);
        }

        return $this;
    }

    /**
     * Get assignment duration in minutes
     */
    public function getDurationInMinutes()
    {
        if (!$this->started_at || !$this->completed_at) {
            return null;
        }

        return $this->started_at->diffInMinutes($this->completed_at);
    }

    /**
     * Get assignment duration in hours
     */
    public function getDurationInHours()
    {
        $minutes = $this->getDurationInMinutes();
        return $minutes ? round($minutes / 60, 2) : null;
    }

    /**
     * Check if assignment is overdue (based on card due_date)
     */
    public function isOverdue()
    {
        if (!$this->card || !$this->card->due_date) {
            return false;
        }

        if ($this->assignment_status === 'completed') {
            return $this->completed_at > $this->card->due_date;
        }

        return now() > $this->card->due_date && $this->assignment_status !== 'completed';
    }

    /**
     * Get days to deadline (negative if overdue)
     */
    public function getDaysToDeadline()
    {
        if (!$this->card || !$this->card->due_date) {
            return null;
        }

        return $this->card->due_date->diffInDays(now(), false);
    }

    /**
     * Get human-readable status label
     */
    public function getStatusLabel()
    {
        return match($this->assignment_status) {
            'assigned' => 'Pending',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'rejected' => 'Rejected',
            default => 'Unknown'
        };
    }

    /**
     * Get human-readable duration
     */
    public function getHumanDuration()
    {
        $hours = $this->getDurationInHours();
        
        if (!$hours) {
            return 'Not started or completed';
        }

        if ($hours < 1) {
            return $this->getDurationInMinutes() . ' minutes';
        }

        return round($hours, 1) . ' hours';
    }

    /**
     * Check if assignment is in progress
     */
    public function isInProgress()
    {
        return $this->assignment_status === 'in_progress';
    }

    /**
     * Check if assignment is completed
     */
    public function isCompleted()
    {
        return $this->assignment_status === 'completed';
    }

    /**
     * Check if assignment is pending (assigned but not started)
     */
    public function isPending()
    {
        return $this->assignment_status === 'assigned';
    }

    /**
     * Check if assignment is rejected
     */
    public function isRejected()
    {
        return $this->assignment_status === 'rejected';
    }

    // ========== ACCESSOR/MUTATOR ==========

    /**
     * Get assignment status with color code for UI
     */
    public function getStatusColorAttribute()
    {
        return match($this->assignment_status) {
            'assigned' => 'yellow',
            'in_progress' => 'blue',
            'completed' => 'green',
            'rejected' => 'red',
            default => 'gray'
        };
    }

    /**
     * Get time percentage (for progress bar)
     */
    public function getTimePercentageAttribute()
    {
        if (!$this->card || !$this->card->due_date) {
            return null;
        }

        $total = $this->assigned_at->diffInSeconds($this->card->due_date);
        $elapsed = $this->assigned_at->diffInSeconds(now());

        if ($total <= 0) {
            return 100;
        }

        return min(100, round(($elapsed / $total) * 100));
    }
}
