<?php

namespace App\Observers;

use App\SalesDealFile as SalesDealFileObserver;
use Illuminate\Support\Facades\Storage;

class SalesDealFile
{
    /**
     * Handle the sales deal file "created" event.
     *
     * @param  \App\SalesDealFile  $salesDealFile
     * @return void
     */
    public function creating(SalesDealFileObserver $salesDealFile)
    {
        //
    }

    /**
     * Handle the sales deal file "created" event.
     *
     * @param  \App\SalesDealFile  $salesDealFile
     * @return void
     */
    public function created(SalesDealFileObserver $salesDealFile)
    {
        //
    }

    /**
     * Handle the sales deal file "updated" event.
     *
     * @param  \App\SalesDealFile  $salesDealFile
     * @return void
     */
    public function updating(SalesDealFileObserver $salesDealFile)
    {
		$date = $salesDealFile->updated_at;
		
		Storage::disk('local')
		->delete('uploads/'.$date->format('Y').'/'.$date->format('M').'/'.$salesDealFile->getOriginal('filename'));
    }

    /**
     * Handle the sales deal file "updated" event.
     *
     * @param  \App\SalesDealFile  $salesDealFile
     * @return void
     */
    public function updated(SalesDealFileObserver $salesDealFile)
    {
        //
    }

    /**
     * Handle the sales deal file "deleted" event.
     *
     * @param  \App\SalesDealFile  $salesDealFile
     * @return void
     */
    public function deleted(SalesDealFileObserver $salesDealFile)
    {
        //
    }

    /**
     * Handle the sales deal file "restored" event.
     *
     * @param  \App\SalesDealFile  $salesDealFile
     * @return void
     */
    public function restored(SalesDealFileObserver $salesDealFile)
    {
        //
    }

    /**
     * Handle the sales deal file "force deleted" event.
     *
     * @param  \App\SalesDealFile  $salesDealFile
     * @return void
     */
    public function forceDeleted(SalesDealFileObserver $salesDealFile)
    {
        //
    }
}
