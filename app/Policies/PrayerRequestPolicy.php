<?php

namespace App\Policies;

use App\Models\PrayerRequest;
use App\Models\User;

class PrayerRequestPolicy
{
    public function view(User $user, PrayerRequest $p): bool
    {
        return $p->is_public
            || $p->user_id === $user->id
            || $user->can('manage-prayer-requests');
    }

    public function update(User $user, PrayerRequest $p): bool
    {
        return $p->user_id === $user->id || $user->can('manage-prayer-requests');
    }

    public function delete(User $user, PrayerRequest $p): bool
    {
        return $p->user_id === $user->id || $user->can('manage-prayer-requests');
    }
}
