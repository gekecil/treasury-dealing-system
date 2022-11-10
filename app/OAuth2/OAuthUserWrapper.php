<?php

namespace App\OAuth2;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use App\User;

class OAuthUserWrapper
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(ResourceOwnerInterface $user)
    {
		$userModel = User::firstOrNew([
				'email' => data_get($user->toArray(), 'email')
			])
			->fill([
				'first_name' => Str::before(data_get($user->toArray(), 'name'), ' '),
				'last_name' => Str::after(Str::contains(data_get($user->toArray(), 'name'), ' ') ? data_get($user->toArray(), 'name') : null, ' '),
				'nik' => Str::contains(data_get($user->toArray(), 'employee_number'), ' '),
			]);

		if (Route::getCurrentRequest()->isMethod('get')) {
			$userModel->forceFill([
				'gender' => data_get($user->toArray(), 'sex'),
				'photo' => data_get($user->toArray(), 'picture')
			]);
		}

		return $userModel;
    }
}
