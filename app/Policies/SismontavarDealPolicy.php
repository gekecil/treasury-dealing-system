<?php

namespace App\Policies;

use App\SismontavarDeal;
use App\User;
use Carbon\Carbon;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\DB;

class SismontavarDealPolicy
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
        return $user->is_head_office_dealer;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\SismontavarDeal  $sismontavarDeal
     * @return mixed
     */
    public function view(User $user, SismontavarDeal $sismontavarDeal)
    {
        $branch = User::where(DB::raw("TRIM(REGEXP_REPLACE(nik, '\s+', '', 'g'))"), $sismontavarDeal->trader_id)
            ->first()
            ->branch()
            ->firstOrNew([], ['code' => null]);

        return (($user->is_branch_office_dealer && ($user->branch_code === $branch->code)) || $user->is_head_office_dealer);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->is_head_office_dealer;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\SismontavarDeal  $sismontavarDeal
     * @return mixed
     */
    public function update(User $user, SismontavarDeal $sismontavarDeal)
    {
        return $user->is_head_office_dealer;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\SismontavarDeal  $sismontavarDeal
     * @return mixed
     */
    public function delete(User $user, SismontavarDeal $sismontavarDeal)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\SismontavarDeal  $sismontavarDeal
     * @return mixed
     */
    public function restore(User $user, SismontavarDeal $sismontavarDeal)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\SismontavarDeal  $sismontavarDeal
     * @return mixed
     */
    public function forceDelete(User $user, SismontavarDeal $sismontavarDeal)
    {
        //
    }
}
