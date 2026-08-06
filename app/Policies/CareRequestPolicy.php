<?php

namespace App\Policies;

use App\Models\CareRequest;
use App\Models\User;

class CareRequestPolicy
{
    public function view(User $user, CareRequest $r): bool
    {
        return $r->user_id === $user->id || $user->can('manage-knock-help');
    }

    public function update(User $user, CareRequest $r): bool
    {
        return $user->can('manage-knock-help');
    }
}
