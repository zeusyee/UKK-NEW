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
        'deadline'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'deadline' => 'date'
    ];

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
}