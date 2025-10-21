<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'full_name' => 'required|string|max:255',
            'role' => 'required|in:admin,member',
        ]);

        User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'full_name' => $request->full_name,
            'role' => $request->role,
            'current_task_status' => 'idle',
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully');
    }

    public function edit(User $user)
    {
        if ($user->user_id === auth()->id()) {
            return back()->with('error', 'You cannot edit your own account');
        }

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->user_id === auth()->id()) {
            return back()->with('error', 'You cannot edit your own account');
        }

        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,'.$user->user_id.',user_id',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->user_id.',user_id',
            'full_name' => 'required|string|max:255',
            'role' => 'required|in:admin,member',
        ]);

        $data = [
            'username' => $request->username,
            'email' => $request->email,
            'full_name' => $request->full_name,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:8',
            ]);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        if ($user->projectMemberships()->exists()) {
            return back()->with('error', 'Cannot delete user with active project memberships');
        }

        if ($user->user_id === auth()->id()) {
            return back()->with('error', 'Cannot delete your own account');
        }

        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully');
    }
}
