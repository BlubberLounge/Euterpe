<?php

namespace App\Models;

use App\Enums\ExternalServices;
use App\Enums\JamRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Jam extends Model
{
    /** @use HasFactory<\Database\Factories\JamFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'created_by',
        'name',
        'join_code',
        'is_active',
        'current_track_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Jam $jam) {
            if (empty($jam->join_code)) {
                $jam->join_code = self::generateUniqueJoinCode();
            }
        });
    }

    public static function generateUniqueJoinCode(): string
    {
        do {
            $code = strtoupper(Str::random(6));
        } while (self::where('join_code', $code)->exists());

        return $code;
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'jam_users')
            ->withPivot(['role', 'joined_at', 'left_at'])
            ->withTimestamps();
    }

    public function members(): BelongsToMany
    {
        return $this->users()->wherePivot('role', JamRole::MEMBER->value);
    }

    public function host(): BelongsToMany
    {
        return $this->users()->wherePivot('role', JamRole::HOST->value);
    }

    public function queueItems(): HasMany
    {
        return $this->hasMany(JamQueue::class)->orderBy('position');
    }

    public function upcomingTracks(): HasMany
    {
        return $this->queueItems()->whereNull('played_at')->orderBy('position');
    }

    public function playedTracks(): HasMany
    {
        return $this->queueItems()->whereNotNull('played_at')->orderBy('played_at', 'desc');
    }

    public function currentTrack(): BelongsTo
    {
        return $this->belongsTo(JamQueue::class, 'current_track_id');
    }

    public function isHost(User $user): bool
    {
        return $this->created_by === $user->id;
    }

    public function isMember(User $user): bool
    {
        return $this->users()->where('user_id', $user->id)->exists();
    }

    public function getJoinUrl(): string
    {
        return route('jam.join', ['code' => $this->join_code]);
    }

    public function getNextPosition(): int
    {
        return ($this->queueItems()->max('position') ?? 0) + 1;
    }

    public function getHostSpotifyToken(): ?string
    {
        $this->loadMissing('creator.externalAccounts');

        return $this->creator->getAccessTokenFor(ExternalServices::SPOTIFY);
    }
}
