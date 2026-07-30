<?php

namespace App\Models;

use App\Models\Traits\BelongToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory,
        BelongToUser;

    protected $fillable = [
        'title',
        'slug',
        'user_id',
        'content',
        'client',
        'project_role_id',
        'status',
        'started_at',
        'completed_at',
        'is_active',
        'gallery',
        'meta_title',
        'meta_description',
        'views'
    ];

    protected $casts = [
        'status' => \App\Enums\ProjectStatusEnum::class,
        'started_at' => 'date',
        'completed_at' => 'date',
        'is_active' => 'bool',
        'gallery' => 'array'
    ];
}
