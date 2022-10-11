<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Log extends Controller
{
	public function out(Request $request)
	{
        $request->session()->invalidate();
        $request->session()->flush();

        if (app()->environment(['local', 'staging'])) {
            return redirect('https://sso.ccbi.co.id/auth/realms/devel/protocol/openid-connect/logout?redirect_uri='.url()->to('/'));
        }

        return redirect('https://sso.ccbi.co.id/auth/realms/ccbi/protocol/openid-connect/logout?redirect_uri='.url()->to('/'));
	}
}
