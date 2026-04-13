<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new Customer with full profile data.
     */
    public function registerCustomer(Request $request)
    {
        $validated = $request->validate([
            'nama'                  => 'required|string|max:255',
            'username'              => 'required|string|max:50|unique:users,username|alpha_dash',
            'password'              => 'required|string|min:8|confirmed',
            'alamat'                => 'required|string|max:500',
            'no_telepon'            => 'required|string|max:20',
            'no_ktp'                => 'required|string|size:16|unique:users,no_ktp|numeric',
            'no_sim'                => 'required|string|max:30|unique:users,no_sim',
        ]);

        $user = User::create([
            'name'       => $validated['nama'],
            'username'   => $validated['username'],
            'password'   => Hash::make($validated['password']),
            'alamat'     => $validated['alamat'],
            'no_telepon' => $validated['no_telepon'],
            'no_ktp'     => $validated['no_ktp'],
            'no_sim'     => $validated['no_sim'],
        ]);

        $role = Role::where('name', 'Customer')->first();
        if ($role) {
            $user->roles()->attach($role);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message'       => 'Registrasi berhasil',
            'id_pelanggan'  => $user->id,
            'user'          => $user->load('roles'),
            'token'         => $token,
        ], 201);
    }

    /**
     * General register (admin use).
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'nullable|string|exists:roles,name',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $roleName = $validated['role'] ?? 'Customer';
        $role = Role::where('name', $roleName)->first();
        if ($role) {
            $user->roles()->attach($role);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user'    => $user->load('roles'),
            'token'   => $token,
        ], 201);
    }

    /**
     * Login — accepts email or username.
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        // Try email first, then username
        $user = User::where('email', $validated['login'])
                    ->orWhere('username', $validated['login'])
                    ->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'login' => ['Username / email atau password salah.'],
            ]);
        }

        // Revoke previous tokens
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'user'    => $user->load('roles'),
            'token'   => $token,
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully'], 200);
    }

    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user()->load('roles'),
        ], 200);
    }

    public function assignRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);

        $role = Role::where('name', $validated['role'])->first();
        if ($role && !$user->hasRole($validated['role'])) {
            $user->roles()->attach($role);
        }

        return response()->json([
            'message' => 'Role assigned successfully',
            'user'    => $user->load('roles'),
        ], 200);
    }

    public function removeRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);

        $role = Role::where('name', $validated['role'])->first();
        if ($role) {
            $user->roles()->detach($role);
        }

        return response()->json([
            'message' => 'Role removed successfully',
            'user'    => $user->load('roles'),
        ], 200);
    }

    public function getUserRoles(User $user)
    {
        return response()->json([
            'user_id' => $user->id,
            'roles'   => $user->roles()->select('id', 'name', 'description')->get(),
        ], 200);
    }
}
