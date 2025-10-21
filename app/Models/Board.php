<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    protected $primaryKey = 'board_id';
    protected $fillable = [
        'project_id',
        'board_name',
        'description',
        'position'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'position' => 'integer'
    ];

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    public function cards()
    {
        return $this->hasMany(Card::class, 'board_id', 'board_id');
    }
}