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

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'username' => 'required|string|max:255|unique:users',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'full_name' => 'required|string|max:255',
                'role' => 'required|in:admin,member',
            ]);

            User::create([
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'full_name' => $validated['full_name'],
                'role' => $validated['role'],
                'current_task_status' => 'idle',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, User $user)
    {
        try {
            // Check if user is trying to edit their own account
            if ($user->user_id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot edit your own account'
                ], 403);
            }

            $validationRules = [
                'username' => 'required|string|max:255|unique:users,username,'.$user->user_id.',user_id',
                'email' => 'required|string|email|max:255|unique:users,email,'.$user->user_id.',user_id',
                'full_name' => 'required|string|max:255',
                'role' => 'required|in:admin,member',
            ];

            // Only validate password if provided
            if ($request->filled('password')) {
                $validationRules['password'] = 'required|string|min:8';
            }

            $validated = $request->validate($validationRules);

            $data = [
                'username' => $validated['username'],
                'email' => $validated['email'],
                'full_name' => $validated['full_name'],
                'role' => $validated['role'],
            ];

            if ($request->filled('password')) {
                $data['password'] = Hash::make($validated['password']);
            }

            $user->update($data);

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(User $user)
    {
        try {
            if ($user->user_id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete your own account'
                ], 403);
            }

            if ($user->projectMemberships()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete user with active project memberships'
                ], 422);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(User $user)
    {
        return response()->json([
            'success' => true,
            'user' => $user
        ]);
    }
}