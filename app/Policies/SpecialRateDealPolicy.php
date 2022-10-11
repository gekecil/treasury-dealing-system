<?php

namespace App\Policies;

use App\SpecialRateDeal;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SpecialRateDealPolicy
{
    use HandlesAuthorization;
	
	public function before($user, $ability)
	{
		if ($user->is_super_administrator) {
			return true;
		}
	}

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
		return $user->id;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\SpecialRateDeal  $specialRateDeal
     * @return mixed
     */
    public function view(User $user, SpecialRateDeal $specialRateDeal)
    {
		return (
			($user->branch_code === $specialRateDeal->salesDeal->branch->code) || (
				$user->is_head_office_dealer
			) || (
				$user->is_administrator
			)
		);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\SpecialRateDeal  $specialRateDeal
     * @return mixed
     */
    public function update(User $user, SpecialRateDeal $specialRateDeal)
    {
		return $user->is_administrator;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\SpecialRateDeal  $specialRateDeal
     * @return mixed
     */
    public function delete(User $user, SpecialRateDeal $specialRateDeal)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\SpecialRateDeal  $specialRateDeal
     * @return mixed
     */
    public function restore(User $user, SpecialRateDeal $specialRateDeal)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\SpecialRateDeal  $specialRateDeal
     * @return mixed
     */
    public function forceDelete(User $user, SpecialRateDeal $specialRateDeal)
    {
        //
    }
}
