<?php

namespace App\Policies;

use App\SismontavarDeal;
use App\User;
use App\SalesDeal;
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
		return $user->id;
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
        $salesDeal = SalesDeal::select([(new SalesDeal)->getKeyName(), 'branch_id'])
            ->whereDate('created_at', $sismontavarDeal->created_at->toDateString())
            ->oldest()
            ->get();

        $salesDeal = $salesDeal->get(
                $salesDeal->pluck((new SalesDeal)->getKeyName())->search(((int) substr($sismontavarDeal->transaction_id, -3)) -1)
            );

        $branch = ($salesDeal ?: new SalesDeal)->branch()->firstOrNew([], ['code' => null]);

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
        //
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
        //
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
