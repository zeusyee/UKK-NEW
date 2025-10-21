<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectMember extends Model
{
    protected $primaryKey = 'member_id';
    protected $fillable = [
        'project_id',
        'user_id',
        'role'
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'role' => 'string'
    ];

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}