<?php

namespace App\Http\Controllers;

use App\Market as MarketModel;
use Illuminate\Http\Request;

class Market extends Controller
{
    public function __invoke(Request $request)
    {
        $this->authorize('create', MarketModel::class);

        $markets = MarketModel::get();

		return view('market', [
            'markets' => $markets
        ]);
    }
}
