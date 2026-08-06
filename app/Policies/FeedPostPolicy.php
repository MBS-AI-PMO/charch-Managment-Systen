<?php

namespace App\Policies;

use App\Models\FeedPost;
use App\Models\User;

class FeedPostPolicy
{
    public function view(User $user, FeedPost $feed): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->can('manage-community-feed');
    }

    public function update(User $user, FeedPost $feed): bool
    {
        return $user->can('manage-community-feed');
    }

    public function delete(User $user, FeedPost $feed): bool
    {
        return $user->can('manage-community-feed');
    }
}
