<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $primaryKey = 'project_id';
    protected $fillable = [
        'project_name',
        'description',
        'created_by',
        'deadline',
        'status',
        'completed_at',
        'completed_by'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'deadline' => 'date',
        'completed_at' => 'datetime',
        'status' => 'string'
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically create a default board when a project is created
        static::created(function ($project) {
            Board::create([
                'project_id' => $project->project_id,
                'board_name' => $project->project_name,
                'position' => 0
            ]);
        });
    }

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function members()
    {
        return $this->hasMany(ProjectMember::class, 'project_id', 'project_id');
    }

    public function boards()
    {
        return $this->hasMany(Board::class, 'project_id', 'project_id');
    }

    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by', 'user_id');
    }

    /**
     * Check if all cards in the project are completed
     */
    public function areAllCardsCompleted()
    {
        $totalCards = 0;
        $completedCards = 0;

        foreach ($this->boards as $board) {
            foreach ($board->cards as $card) {
                $totalCards++;
                if ($card->status === 'done') {
                    $completedCards++;
                }
            }
        }

        return $totalCards > 0 && $totalCards === $completedCards;
    }

    /**
     * Check if project is ready to be completed (all cards done and status is active)
     */
    public function isReadyToComplete()
    {
        return $this->status === 'active' && $this->areAllCardsCompleted();
    }
}