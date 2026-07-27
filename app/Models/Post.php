<?php

namespace App\Models;

use App\Models\Traits\BelongToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model

{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory,
        BelongToUser;

    protected $fillable = [
        'title',
        'user_id',
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
