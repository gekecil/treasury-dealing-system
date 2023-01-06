<?php

namespace App\Policies;

use App\SismontavarDeal;
use App\User;
use Carbon\Carbon;
use Illuminate\Auth\Access\HandlesAuthorization;

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
        if ($user->is_branch_office_dealer && Carbon::hasFormat($sismontavarDeal->transaction_date, 'Ymd His')) {
            return $user->branch()->first()
                ->salesDeal()
                ->where('created_at', Carbon::createFromFormat('Ymd His', $sismontavarDeal->transaction_date))
                ->exists();
        }

        return (($user->is_branch_office_dealer && $salesDeal) || $user->is_head_office_dealer);
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
