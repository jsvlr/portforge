<?php

namespace App\Models;

use App\Models\Traits\BelongToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectRole extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectRoleFactory> */
    use HasFactory,
        BelongToUser;

    protected $fillable = [
        'name',
        'slug',
        'user_id',
    ];

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
