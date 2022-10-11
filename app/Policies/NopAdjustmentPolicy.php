<?php

namespace App\Policies;

use App\NopAdjustment;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\User;

class NopAdjustmentPolicy
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
     * @param  \App\NopAdjustment  $nopAdjustment
     * @return mixed
     */
    public function view(User $user, NopAdjustment $nopAdjustment)
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
        return $user->is_interbank_dealer;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\NopAdjustment  $nopAdjustment
     * @return mixed
     */
    public function update(User $user, NopAdjustment $nopAdjustment)
    {
        return $user->is_interbank_dealer;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\NopAdjustment  $nopAdjustment
     * @return mixed
     */
    public function delete(User $user, NopAdjustment $nopAdjustment)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\NopAdjustment  $nopAdjustment
     * @return mixed
     */
    public function restore(User $user, NopAdjustment $nopAdjustment)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\NopAdjustment  $nopAdjustment
     * @return mixed
     */
    public function forceDelete(User $user, NopAdjustment $nopAdjustment)
    {
        //
    }
}
