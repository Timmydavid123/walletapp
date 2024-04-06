<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return User::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8', // Adding validation for password
        ]);

        $userData = $request->only(['name', 'email', 'password']); // Include password field

        $user = User::create($userData);

        return response()->json($user, 201);
    }
}
