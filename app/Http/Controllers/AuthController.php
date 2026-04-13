<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'nullable|string|exists:roles,name'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $roleNames = $validated['role'] ?? 'Customer';
        $role = Role::where('name', $roleNames)->first();
        if ($role) {
            $user->roles()->attach($role);
        }

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user->load('roles'),
            'token' => $user->createToken('auth_token')->plainTextToken
        ], 201);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials provided.']
            ]);
        }

        return response()->json([
            'message' => 'Login successful',
            'user' => $user->load('roles'),
            'token' => $user->createToken('auth_token')->plainTextToken
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ], 200);
    }

    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user()->load('roles')
        ], 200);
    }

    public function assignRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|string|exists:roles,name'
        ]);

        $role = Role::where('name', $validated['role'])->first();
        if ($role && !$user->hasRole($validated['role'])) {
            $user->roles()->attach($role);
        }

        return response()->json([
            'message' => 'Role assigned successfully',
            'user' => $user->load('roles')
        ], 200);
    }

    public function removeRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|string|exists:roles,name'
        ]);

        $role = Role::where('name', $validated['role'])->first();
        if ($role) {
            $user->roles()->detach($role);
        }

        return response()->json([
            'message' => 'Role removed successfully',
            'user' => $user->load('roles')
        ], 200);
    }

    public function getUserRoles(User $user)
    {
        return response()->json([
            'user_id' => $user->id,
            'roles' => $user->roles()->select('id', 'name', 'description')->get()
        ], 200);
    }
}
