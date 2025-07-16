<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserPermissionController extends Controller
{
    public function index()
    {
        $this->authorize('manage-roles');
        
        $users = User::with('roles', 'team', 'manager')->paginate(10);
        $roles = Role::all();
        $teams = Team::all();
        
        return view('users.permissions', compact('users', 'roles', 'teams'));
    }

    public function createUser(Request $request)
    {
        $this->authorize('manage-users');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
            'team_id' => 'nullable|exists:teams,id',
            'manager_id' => 'nullable|exists:users,id',
            'job_title' => 'nullable|string|max:255',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'team_id' => $validated['team_id'],
            'manager_id' => $validated['manager_id'],
            'job_title' => $validated['job_title'],
        ]);

        $user->roles()->sync($validated['roles']);

        return back()->with('success', 'User created successfully.');
    }

    public function updateUser(Request $request, User $user)
    {
        $this->authorize('manage-users');

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'team_id' => 'nullable|exists:teams,id',
            'manager_id' => 'nullable|exists:users,id',
            'job_title' => 'nullable|string|max:255',
        ];

        // Only validate password if it's being updated
        if ($request->filled('password')) {
            $rules['password'] = ['required', 'confirmed', Rules\Password::defaults()];
        }

        $validated = $request->validate($rules);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'team_id' => $validated['team_id'],
            'manager_id' => $validated['manager_id'],
            'job_title' => $validated['job_title'],
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return back()->with('success', 'User updated successfully.');
    }

    public function toggleUserStatus(User $user)
    {
        $this->authorize('manage-users');
        
        $user->is_active = !$user->is_active;
        $user->save();

        return back()->with('success', 'User status updated successfully.');
    }

    public function deleteUser(User $user)
    {
        $this->authorize('manage-users');

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        // Optional: Add additional checks here
        // For example, check if user has any associated data that should be handled before deletion

        $user->roles()->detach(); // Remove role associations
        $user->delete();

        return back()->with('success', 'User deleted successfully.');
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('manage-roles');

        $validated = $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id'
        ]);

        $user->roles()->sync($request->roles);

        return back()->with('success', 'User roles updated successfully.');
    }

    public function assignSuperAdmin(User $user)
    {
        $this->authorize('manage-roles');
        
        $superAdminRole = Role::where('slug', 'super-admin')->first();
        
        if ($superAdminRole) {
            $user->roles()->sync([$superAdminRole->id]);
            return back()->with('success', 'Super Admin role assigned successfully.');
        }
        
        return back()->with('error', 'Super Admin role not found.');
    }
} 