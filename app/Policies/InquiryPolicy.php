<?php

namespace App\Policies;

use App\Models\User;

class InquiryPolicy extends AdminPolicy
{
    public function create(User $user): bool
    {
        return false;
    }
}
