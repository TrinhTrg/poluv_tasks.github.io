<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        return view('profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        // If only uploading profile picture (no other form fields), validate only the picture
        $isOnlyUploadingPicture = $request->hasFile('profile_picture') && 
                                  !$request->filled('username') && 
                                  !$request->filled('email');
        
        if ($isOnlyUploadingPicture) {
            $request->validate([
                'profile_picture' => 'required|image|mimes:jpeg,png,jpg|max:5120', // 5MB max
            ]);

            // Handle profile picture upload only
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $path = $request->file('profile_picture')->store('profile-pictures', 'public');
            $user->update(['profile_picture' => $path]);

            return redirect()->route('profile.show')->with('success', 'Profile picture updated successfully!');
        }

        // Otherwise, validate all fields
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'birthday' => 'nullable|date',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // 5MB max
        ], [
            'username.required' => 'Username is required.',
            'username.unique' => 'This username is already taken.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already taken.',
        ]);

        // Update name from first_name and last_name
        $name = trim(($validated['first_name'] ?? '') . ' ' . ($validated['last_name'] ?? ''));
        if (empty($name)) {
            $name = $user->name; // Keep existing name if both are empty
        }

        $updateData = [
            'username' => $validated['username'],
            'name' => $name,
            'email' => $validated['email'],
        ];

        // Add phone and birthday if they are in fillable (allow null/empty to clear values)
        $fillable = $user->getFillable();
        if (in_array('phone', $fillable)) {
            $updateData['phone'] = $validated['phone'] ?? null;
        }
        if (in_array('birthday', $fillable)) {
            $updateData['birthday'] = $validated['birthday'] ?? null;
        }

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            // Store new profile picture
            $path = $request->file('profile_picture')->store('profile-pictures', 'public');
            $updateData['profile_picture'] = $path;
        }

        $user->update($updateData);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Profile updated successfully!']);
        }

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully!');
    }
}
