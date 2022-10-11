<?php

namespace App\Policies;

use App\InterbankDeal;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\User;

class InterbankDealPolicy
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
		return $user->is_interbank_dealer;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\InterbankDeal  $interbankDeal
     * @return mixed
     */
    public function view(User $user, InterbankDeal $interbankDeal)
    {
        return $user->is_interbank_dealer;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->is_interbank_dealer;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\InterbankDeal  $interbankDeal
     * @return mixed
     */
    public function update(User $user, InterbankDeal $interbankDeal)
    {
        return (($user->id === $interbankDeal->user_id) || $user->is_administrator);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\InterbankDeal  $interbankDeal
     * @return mixed
     */
    public function delete(User $user, InterbankDeal $interbankDeal)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\InterbankDeal  $interbankDeal
     * @return mixed
     */
    public function restore(User $user, InterbankDeal $interbankDeal)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\InterbankDeal  $interbankDeal
     * @return mixed
     */
    public function forceDelete(User $user, InterbankDeal $interbankDeal)
    {
        //
    }
}
