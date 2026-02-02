<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\ExternalServices;
use App\Services\Music\SpotifyService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'access_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'access_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     *
     */
    public function externalAccounts(): HasMany
    {
        return $this->hasMany(ExternalServiceUser::class);
    }

    public function hostedJams(): HasMany
    {
        return $this->hasMany(Jam::class, 'created_by');
    }

    public function activeHostedJam(): ?Jam
    {
        return $this->hostedJams()->where('is_active', true)->first();
    }

    public function addedQueueItems(): HasMany
    {
        return $this->hasMany(JamQueue::class, 'added_by_user_id');
    }

    public function jams(): BelongsToMany
    {
        return $this->belongsToMany(Jam::class, 'jam_users')
            ->withPivot(['role', 'joined_at', 'left_at'])
            ->withTimestamps();
    }

    public function getAccessTokenFor(ExternalServices $service): ?string
    {
        $account = $this->externalAccounts()
            ->where('service', $service)
            ->first();

        if (!$account)
            return null;

        return match ($service) {
            ExternalServices::SPOTIFY => app(SpotifyService::class)->ensureValidAccessToken($account),
            // ExternalServices::SOUNDCLOUD => app(SoundcloudOAuthService::class)->ensureValidAccessToken($account),
            default => null,
        };
    }
}
