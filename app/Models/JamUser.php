<?php

namespace App\Models;

use App\Enums\JamRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JamUser extends Model
{
    /** @use HasFactory<\Database\Factories\JamUserFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'jam_id',
        'user_id',
        'role',
        'joined_at',
        'left_at',
    ];

    protected $casts = [
        'role' => JamRole::class,
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
    ];

    public function jam(): BelongsTo
    {
        return $this->belongsTo(Jam::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isHost(): bool
    {
        return $this->role === JamRole::HOST;
    }
}
