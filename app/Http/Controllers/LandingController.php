<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Project;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_projects' => Project::count(),
            'completed_projects' => Project::where('status', 'completed')->count(),
            'active_projects' => Project::where('status', 'active')->count(),
        ];

        return view('landing', compact('stats'));
    }
}
