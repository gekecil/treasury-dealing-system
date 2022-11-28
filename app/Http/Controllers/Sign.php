<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Sign extends Controller
{
    public function out(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->flush();

        return redirect(env('OAUTH2_URL_SIGN_OUT'));
    }
}
