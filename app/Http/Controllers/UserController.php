<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\File;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::with(["role" => function ($query) {
            $query->select("id", "name");
        }])->get();
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
    public function show($id)
    {
        return User::with(["role" => function ($query) {
            $query->select("id", "name");
        }])->find($id);
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
        $user = User::find($id);
        if ($user->picture) {
            File::delete(public_path("storage/" . $user->picture));
        }
        return $user->delete();
    }
}
