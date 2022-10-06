<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize("viewany", User::class);
        $users = User::with(["role:id,name"])->get();
        return $users;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validted = $request->validate([
            'firstname' => ['required', "min:3"],
            'lastname' => ['required', "min:3"],
            'email' => ['required', "email", "unique:users,email"],
            'password' => ['required', "min:8", "confirmed"],
            'role_id' => ['required', "integer", "exists:roles,id"],
            "username" => ['required', "unique:users,username"],
            "blocked" => ['required', "boolean"]
        ]);

        $validted["password"] = bcrypt($validted["password"]);

        if ($request->hasFile("picture")) {
            $validted["picture"] = $request->file("picture")->store("users_pictures", "public");
        }

        return response(User::create($validted), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(request $request, $id)
    {
        $user = User::findOrFail($id);

        if (Auth::guard("sanctum")->check()) {

            // return ["response" => Auth::guard("sanctum")->user()->can("view", $user)];

            if (Auth::guard("sanctum")->user()->can("view", $user)) {
                return $user->load("role:id,name");
            } else {
                return $user->load("role:id,name")->only([
                    "username",
                    "firstname",
                    "lastname",
                    "picture",
                    "role"
                ]);
            }
        } else {
            return $user->load("role:id,name")->only([
                "username",
                "firstname",
                "lastname",
                "picture",
                "role"
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validted = $request->validate([
            'firstname' => ['nullable', "min:3"],
            'lastname' => ['nullable', "min:3"],
            'email' => ['nullable', "email", "unique:users,email"],
            'password' => ['nullable', "min:8", "confirmed"],
            'role_id' => ['nullable', "integer", "exists:roles,id"],
            "username" => ['nullable', "unique:users,username"],
            "blocked" => ['nullable', "boolean"]
        ]);

        if (isset($validted["password"])) {
            $validted["password"] = bcrypt($validted["password"]);
        }

        if ($request->hasFile("picture")) {
            $validted["picture"] = $request->file("picture")->store("users_pictures", "public");
        }

        $user = User::find($id);

        $user->update($validted);

        return $user;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // return ["mesage" => "okay"];


        $user = User::find($id);
        if ($user->picture) {
            File::delete(public_path("storage/" . $user->picture));
        }
        return $user->delete();
    }
}
