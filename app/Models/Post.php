<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

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

    public static function search($search){
        return self::with("categories:name", "user:id,username,picture")->whereHas("categories", function(Builder $query) use($search) {
            $query->where("categories.name", "like", "%$search%");
        })->orWhereHas("user", function(Builder $query) use($search) {
            $query->where("username", "like", "%$search%");
        })->orWhere("title", "like", "%$search%")->paginate(9);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, "category_posts", "post_id", "category_id");
    }

    public function user()
    {
        return $this->belongsTo(User::class, "user_id", "id");
    }
}
