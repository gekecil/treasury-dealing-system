<?php

namespace App\Policies;

use App\SalesDeal;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SalesDealPolicy
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
     * @param  \App\SalesDeal  $salesDeal
     * @return mixed
     */
    public function view(User $user, SalesDeal $salesDeal)
    {
        return (($salesDeal->branch && ($user->branch_code === $salesDeal->branch->code)) || $user->is_head_office_dealer);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
		return ($user->is_branch_office_dealer || ($user->role_id && $user->role->is_sales_dealer));
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\SalesDeal  $salesDeal
     * @return mixed
     */
    public function update(User $user, SalesDeal $salesDeal)
    {
        return (($salesDeal->branch && ($user->branch_code === $salesDeal->branch->code)) || $user->is_head_office_dealer);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\SalesDeal  $salesDeal
     * @return mixed
     */
    public function delete(User $user, SalesDeal $salesDeal)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\SalesDeal  $salesDeal
     * @return mixed
     */
    public function restore(User $user, SalesDeal $salesDeal)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\SalesDeal  $salesDeal
     * @return mixed
     */
    public function forceDelete(User $user, SalesDeal $salesDeal)
    {
        //
    }
}
