<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'password',
        'full_name',
        'email',
        'current_task_status',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'current_task_status' => 'string',
            'created_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the projects created by the user.
     */
    public function createdProjects()
    {
        return $this->hasMany(Project::class, 'created_by', 'user_id');
    }

    /**
     * Get the project memberships of the user.
     */
    public function projectMemberships()
    {
        return $this->hasMany(ProjectMember::class, 'user_id', 'user_id');
    }

    /**
     * Get the card assignments for the user.
     */
    public function cardAssignments()
    {
        return $this->hasMany(CardAssignment::class, 'user_id', 'user_id');
    }

    /**
     * Get the comments made by the user.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class, 'user_id', 'user_id');
    }

    /**
     * Get the time logs recorded by the user.
     */
    public function timeLogs()
    {
        return $this->hasMany(TimeLog::class, 'user_id', 'user_id');
    }

    /**
     * Get the project memberships where the user is a member
     */
    public function projects()
    {
        return $this->hasMany(ProjectMember::class, 'user_id', 'user_id');
    }
}
