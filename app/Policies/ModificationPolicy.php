<?php

namespace App\Policies;

use App\Modification;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\User;

class ModificationPolicy
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
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\Modification  $modification
     * @return mixed
     */
    public function view(User $user, Modification $modification)
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
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\Modification  $modification
     * @return mixed
     */
    public function update(User $user, Modification $modification)
    {
        return (
			($modification->user->is_branch_office_dealer && ($user->is_head_office_dealer)) || (
				$user->is_administrator
			)
		);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Modification  $modification
     * @return mixed
     */
    public function delete(User $user, Modification $modification)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\Modification  $modification
     * @return mixed
     */
    public function restore(User $user, Modification $modification)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Modification  $modification
     * @return mixed
     */
    public function forceDelete(User $user, Modification $modification)
    {
        //
    }
}
