<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

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
                "errors" => ["password" => ["password is not correct"]]
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

    public function profile_update(Request $request)
    {
        $validated = $request->validate([
            "username" => ["nullable", "unique:users,username"],
            "firstname" => ["nullable"],
            "lastname" => ["nullable"],
            "email" => ["nullable", "email:filter", "unique:users,email"],
            "password" => ["nullable", "confirmed", "min:8"],
            "current_password" => ["nullable"]
        ]);

        $user = auth()->user();


        if (isset($validated["password"])) {
            if (!isset($validated["current_password"])) {
                throw ValidationException::withMessages(["current_password" => "The Current Password is Required"]);
            } else {
                if (!Hash::check($validated["current_password"], $user->password)) {
                    throw ValidationException::withMessages(["current_password" => "The Current Password is Not Correct"]);
                }
                $validated["password"] = bcrypt($validated["password"]);
            }
        }


        if ($request->hasFile("picture")) {
            $path = public_path("storage/" . $user->picture);

            if (file_exists($path)) File::delete($path);

            $validated["picture"] = $request->file("picture")->store("users_pictures", "public");
        }


        $user->update($validated);
        return $user;
    }
}
