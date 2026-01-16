<?php

namespace App\Policies;

use App\Models\Meme;
use App\Models\User;

class MemePolicy
{
    public function update(User $user, Meme $meme): bool
    {
        return $meme->user_id === $user->id;
    }

    public function delete(User $user, Meme $meme): bool
    {
        return $meme->user_id === $user->id;
    }
}
