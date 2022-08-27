<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{


    public function authenticate(Request $request)
    {
        $validated = $request->validate([
            'email' => ["required", "email", "exists:users,email"],
            'password' => ["required", "min:8"],
        ]);

        if (!Auth::attempt($validated)) {
            return response([
                "status" => false,
                "message" => "password is not correct"
            ], 400);
        }

        $user = User::where("email", $validated["email"])->first();

        $abilites = [$user->role->name];

        return response([
            "user" => $user,
            'token' => $user->createToken("API TOKEN", $abilites)->plainTextToken
        ], 200);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'firstname' => ["required", "min:3"],
            'lastname' => ["required", "min:3"],
            'email' => ["required", "email", "unique:users,email"],
            'password' => ["required", "min:8", "confirmed"],
            "username" => ["required", "min:3", "unique:users,username"],
        ]);

        if ($request->hasFile("picture")) {
            $validated["picture"] = $request->file("picture")->store("users_pictures", "public");
        }

        $validated["password"] = bcrypt($validated["password"]);

        $user = User::create(
            $validated
        );

        $abilites = [$user->role->name];

        return response([
            "user" => $user,
            'token' => $user->createToken("API TOKEN", $abilites)->plainTextToken
        ], 201);
    }


    public function logout()
    {
        return auth()->user()->tokens()->delete();
    }


    public function profile()
    {
        $user = auth()->user();
        return $user->load("role:id,name");
    }
}
