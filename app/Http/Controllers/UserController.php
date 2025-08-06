<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function profile($username = null)
    {
        if (!$username) {
            $user = Auth::user();
        } else {
            $user = User::where('username', $username)->firstOrFail();
        }

        return view('users.profile', compact('user'));
    }

    public function settings()
    {
        $user = Auth::user();
        return view('users.settings', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        // Validate
        $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Prepare update data
        $updateData = [
            'name' => $request->input('name'),
            'bio' => $request->input('bio'),
            'updated_at' => now()
        ];

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($user->avatar && file_exists(public_path('uploads/avatars/' . $user->avatar))) {
                unlink(public_path('uploads/avatars/' . $user->avatar));
            }

            // Upload new avatar
            $avatarFile = $request->file('avatar');
            $avatarName = time() . '_' . $userId . '.' . $avatarFile->getClientOriginalExtension();
            $avatarFile->move(public_path('uploads/avatars'), $avatarName);
            
            $updateData['avatar'] = $avatarName;
        }

        // Update using DB query (bypasses Eloquent issues)
        DB::table('users')->where('id', $userId)->update($updateData);

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }
}