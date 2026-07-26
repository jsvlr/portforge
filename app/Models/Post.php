<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'cover_image',
        'status',
        'published_at',
        'post_category_id',
        'views',
        'tags',
        'meta_title',
        'meta_description'
    ];

    protected $casts = [
        'status' => \App\Enums\PostStatusEnum::class,
        'views' => 'integer',
        'published_at' => 'date',
        'tags' => 'array'
    ];

    public function post_category(): BelongsTo
    {
        return $this->belongsTo(PostCategory::class);
    }
}
