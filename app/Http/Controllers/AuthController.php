<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return $this->redirectBasedOnRole(Auth::user());
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->withInput($request->except('password'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:users',
            'full_name' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create([
            'username' => $request->username,
            'full_name' => $request->full_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'current_task_status' => 'idle',
            'role' => 'member', // Default role is member
        ]);

        Auth::login($user);

        return $this->redirectBasedOnRole($user);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Redirect user to Google OAuth page
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Check if user already exists with this Google ID
            $user = User::where('google_id', $googleUser->getId())->first();
            
            if ($user) {
                // User exists, log them in
                Auth::login($user);
                return $this->redirectBasedOnRole($user);
            }
            
            // Check if user exists with this email
            $existingUser = User::where('email', $googleUser->getEmail())->first();
            
            if ($existingUser) {
                // Link Google account to existing user
                $existingUser->update([
                    'google_id' => $googleUser->getId()
                ]);
                
                Auth::login($existingUser);
                return $this->redirectBasedOnRole($existingUser);
            }
            
            // Create new user
            $newUser = User::create([
                'username' => $this->generateUniqueUsername($googleUser->getName()),
                'full_name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'current_task_status' => 'idle',
                'role' => 'member',
                'password' => null, // No password for Google users
            ]);
            
            Auth::login($newUser);
            return $this->redirectBasedOnRole($newUser);
            
        } catch (Exception $e) {
            return redirect()->route('login')->withErrors([
                'google' => 'Unable to login with Google. Please try again.'
            ]);
        }
    }

    /**
     * Generate unique username from name
     */
    protected function generateUniqueUsername($name)
    {
        // Remove spaces and convert to lowercase
        $baseUsername = strtolower(str_replace(' ', '', $name));
        $username = $baseUsername;
        $counter = 1;
        
        // Keep trying until we find a unique username
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }
        
        return $username;
    }

    protected function redirectBasedOnRole($user)
    {
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        
        // Check if user is a leader in any project
        $isLeader = \App\Models\ProjectMember::where('user_id', $user->user_id)
            ->whereIn('role', ['admin', 'leader'])
            ->exists();
        
        if ($isLeader) {
            return redirect()->route('leader.dashboard');
        }
        
        return redirect()->route('member.dashboard');
    }
}