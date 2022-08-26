<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;


    protected $fillable = [
        "title",
        "cover",
        "description",
        "content",
        "user_id",
        "tags",
        "views_count",
    ];


    public function categories()
    {
        return $this->belongsToMany(Category::class, "category_posts", "post_id", "category_id");
    }
}
