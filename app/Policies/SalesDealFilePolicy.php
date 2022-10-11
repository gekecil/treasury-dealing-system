<?php

namespace App\Policies;

use App\SalesDealFile;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\User;

class SalesDealFilePolicy
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
     * @param  \App\SalesDealFile  $salesDealFile
     * @return mixed
     */
    public function view(User $user, SalesDealFile $salesDealFile)
    {
		return (
			($user->branch_code === $salesDealFile->salesDeal->branch->code) || (
				$user->is_head_office_dealer
			) || (
				$user->is_administrator
			)
		);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \  $
     * @return mixed
     */
    public function create(User $user)
    {
		return ($user->id);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \  $
     * @param  \App\SalesDealFile  $salesDealFile
     * @return mixed
     */
    public function update(User $user, SalesDealFile $salesDealFile)
    {
        return (
			($salesDealFile->user->is_branch_office_dealer && ($user->is_head_office_dealer)) || (
				$user->is_administrator
			)
		);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \  $
     * @param  \App\SalesDealFile  $salesDealFile
     * @return mixed
     */
    public function delete(User $user, SalesDealFile $salesDealFile)
    {
		return (
			($user->branch_code === $salesDealFile->salesDeal->branch->code) || (
				$user->is_head_office_dealer
			) || (
				$user->is_administrator
			)
		);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \  $
     * @param  \App\SalesDealFile  $salesDealFile
     * @return mixed
     */
    public function restore(User $user, SalesDealFile $salesDealFile)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \  $
     * @param  \App\SalesDealFile  $salesDealFile
     * @return mixed
     */
    public function forceDelete(User $user, SalesDealFile $salesDealFile)
    {
        //
    }
}
