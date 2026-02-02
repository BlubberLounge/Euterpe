<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JamQueue extends Model
{
    /** @use HasFactory<\Database\Factories\JamQueueFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'jam_id',
        'added_by_user_id',
        'spotify_uri',
        'track_name',
        'artist_name',
        'album_name',
        'album_image_url',
        'duration_ms',
        'position',
        'played_at',
        'is_playing',
    ];

    protected $casts = [
        'played_at' => 'datetime',
        'is_playing' => 'boolean',
        'duration_ms' => 'integer',
        'position' => 'integer',
    ];

    public function jam(): BelongsTo
    {
        return $this->belongsTo(Jam::class);
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by_user_id');
    }

    public function markAsPlaying(): void
    {
        $this->update([
            'is_playing' => true,
            'played_at' => now(),
        ]);

        $this->jam->update(['current_track_id' => $this->id]);
    }

    public function markAsPlayed(): void
    {
        $this->update([
            'is_playing' => false,
        ]);
    }

    public static function createFromSpotifyTrack(Jam $jam, User $user, array $track): self
    {
        return self::create([
            'jam_id' => $jam->id,
            'added_by_user_id' => $user->id,
            'spotify_uri' => $track['uri'],
            'track_name' => $track['name'],
            'artist_name' => collect($track['artists'])->pluck('name')->join(', '),
            'album_name' => $track['album']['name'] ?? null,
            'album_image_url' => $track['album']['images'][0]['url'] ?? null,
            'duration_ms' => $track['duration_ms'] ?? null,
            'position' => $jam->getNextPosition(),
        ]);
    }
}
