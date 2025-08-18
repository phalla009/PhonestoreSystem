<?php

namespace App\Http\Controllers;

use App\Models\UserManager;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagerController extends Controller
{
    public function index()
    {
        $users = UserManager::with('role')->get();
       return view('usermanagers.index', compact('users'));

    }

    public function create()
    {
        $roles = Role::all();
        return view('usermanagers.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        UserManager::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
        ]);

        return redirect()->route('usermanagers.index')->with('success', 'User created successfully!');
    }

    public function edit(UserManager $usermanager)
    {
        $roles = Role::all();
        return view('usermanagers.edit', compact('usermanager','roles'));
    }
    public function show(UserManager $usermanager)
    {
        return view('usermanagers.show', compact('usermanager'));
    }

    public function update(Request $request, UserManager $usermanager)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $usermanager->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        $usermanager->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->password) : $usermanager->password,
            'role_id' => $request->role_id,
        ]);

        return redirect()->route('usermanagers.index')->with('success', 'User updated successfully!');
    }

    public function destroy(UserManager $usermanager)
    {
        $usermanager->delete();
        return redirect()->route('usermanagers.index')->with('success', 'User deleted successfully!');
    }
}
