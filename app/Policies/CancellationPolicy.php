<?php

namespace App\Policies;

use App\Cancellation;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CancellationPolicy
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
     * @param  \App\Cancellation  $cancellation
     * @return mixed
     */
    public function view(User $user, Cancellation $cancellation)
    {
        return (
            (!$cancellation->salesDeal && $user->is_branch_office_dealer) || (
                $cancellation->salesDeal && ($user->branch_code === $cancellation->salesDeal->branch->code)
            ) || (
                $user->is_administrator
            ) || (
                $user->is_head_office_dealer
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
		return $user->is_administrator;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\Cancellation  $cancellation
     * @return mixed
     */
    public function update(User $user, Cancellation $cancellation)
    {
		return ($user->is_administrator && !$cancellation->note);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Cancellation  $cancellation
     * @return mixed
     */
    public function delete(User $user, Cancellation $cancellation)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\Cancellation  $cancellation
     * @return mixed
     */
    public function restore(User $user, Cancellation $cancellation)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Cancellation  $cancellation
     * @return mixed
     */
    public function forceDelete(User $user, Cancellation $cancellation)
    {
        //
    }
}
