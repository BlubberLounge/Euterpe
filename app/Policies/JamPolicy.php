<?php

namespace App\Policies;

use App\Models\Jam;
use App\Models\JamQueue;
use App\Models\User;

class JamPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Jam $jam): bool
    {
        return $jam->isMember($user);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Jam $jam): bool
    {
        return $jam->isHost($user);
    }

    public function delete(User $user, Jam $jam): bool
    {
        return $jam->isHost($user);
    }

    public function addToQueue(User $user, Jam $jam): bool
    {
        return $jam->is_active && $jam->isMember($user);
    }

    public function removeFromQueue(User $user, Jam $jam, JamQueue $queueItem): bool
    {
        return $jam->isHost($user) || $queueItem->added_by_user_id === $user->id;
    }

    public function restore(User $user, Jam $jam): bool
    {
        return $jam->isHost($user);
    }

    public function forceDelete(User $user, Jam $jam): bool
    {
        return $jam->isHost($user);
    }
}
