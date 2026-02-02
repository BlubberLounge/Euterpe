<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\ExternalServices;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExternalServiceUser extends Model
{
    /** @use HasFactory<\Database\Factories\ExternalServiceUserFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'service',
        'name',
        'email',
        'access_token',
        'refresh_token',
        'token_expires_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'service' => ExternalServices::class,
            'token_expires_at' => 'datetime',
        ];
    }

    /**
     * Get the creator
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
