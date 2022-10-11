<?php

namespace App\Policies;

use App\CurrencyPair;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\User;

class CurrencyPairPolicy
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
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\CurrencyPair  $currencyPair
     * @return mixed
     */
    public function view(User $user, CurrencyPair $currencyPair)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
		return $user->is_administrator;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\CurrencyPair  $currencyPair
     * @return mixed
     */
    public function update(User $user, CurrencyPair $currencyPair)
    {
		return $user->is_administrator;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\CurrencyPair  $currencyPair
     * @return mixed
     */
    public function delete(User $user, CurrencyPair $currencyPair)
    {
		return $user->is_administrator;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\CurrencyPair  $currencyPair
     * @return mixed
     */
    public function restore(User $user, CurrencyPair $currencyPair)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\CurrencyPair  $currencyPair
     * @return mixed
     */
    public function forceDelete(User $user, CurrencyPair $currencyPair)
    {
        //
    }
}
