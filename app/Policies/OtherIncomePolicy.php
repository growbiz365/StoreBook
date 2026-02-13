<?php

namespace App\Policies;

use App\Models\OtherIncome;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OtherIncomePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true; // Allow all authenticated users to view the index
    }

    public function view(User $user, OtherIncome $otherIncome)
    {
        return session('active_business') === $otherIncome->business_id;
    }

    public function create(User $user)
    {
        return true; // Allow all authenticated users to create
    }

    public function update(User $user, OtherIncome $otherIncome)
    {
        return session('active_business') === $otherIncome->business_id;
    }

    public function delete(User $user, OtherIncome $otherIncome)
    {
        return session('active_business') === $otherIncome->business_id;
    }
}
