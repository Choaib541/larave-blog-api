<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::with(["categories" => function ($query) {
            $query->select("name");
        }])->get();

        return $posts;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            "title" => ["required", "min:3"],
            "cover" => ["required"],
            "description" => ["required", "min:3"],
            "content" => ["required", "min:3"],
            "user_id" => ["required", "exists:users,id"],
            "tags" => ["required", 'regex:/^(\w+\|\w+)+$/i'],
        ]);

        $validated["cover"] = $request->file("cover")->store("posts_covers", "public");

        return  response(Post::create($validated), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Post::with(["categories" => function ($query) {
            $query->select("name");
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
        $validated = $request->validate([
            "title" => ["nullable", "min:3"],
            "description" => ["nullable", "min:3"],
            "content" => ["nullable", "min:3"],
            "user_id" => ["nullable", "exists:users,id"],
            "tags" => ["nullable", 'regex:/^(\w+\|\w+)+$/i'],
        ]);

        if ($request->hasFile("cover")) {
            $validated["cover"] = $request->file("cover")->store("posts_covers", "public");
        }

        $post = Post::fund($id);
        $post->update($validated);

        return  $post;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::find($id);
        File::delete(public_path("storage/" . $post->cover));
        return $post->delete();
    }
}
