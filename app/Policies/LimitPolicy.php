<?php

namespace App\Policies;

use App\Limit;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\User;

class LimitPolicy
{
    use HandlesAuthorization;

	public function before($user, $ability)
	{
		if ($user->is_administrator) {
			return true;
		}
	}

    /**
     * Determine whether the user can view any models.
     *
     * @param  \  $
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \  $
     * @param  \App\Limit  $limit
     * @return mixed
     */
    public function view(User $user, Limit $limit)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \  $
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \  $
     * @param  \App\Limit  $limit
     * @return mixed
     */
    public function update(User $user, Limit $limit)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \  $
     * @param  \App\Limit  $limit
     * @return mixed
     */
    public function delete(User $user, Limit $limit)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \  $
     * @param  \App\Limit  $limit
     * @return mixed
     */
    public function restore(User $user, Limit $limit)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \  $
     * @param  \App\Limit  $limit
     * @return mixed
     */
    public function forceDelete(User $user, Limit $limit)
    {
        //
    }
}
