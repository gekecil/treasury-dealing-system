<?php

namespace App\OAuth2;

use App\Http\Controllers\Controller;
use App\User;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

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
                'nik' => data_get($user->toArray(), 'preferred_username'),
            ]);

        if ($userModel->isDirty(['first_name', 'last_name', 'nik'])) {
            $userModel->save();
        }

        if ($userModel->branch_code) {
            $controller = new Controller;
            $branch = $controller->branch($userModel->branch_code);
            $branch = $controller->fetch($branch)
                ->first();

            $branch = $userModel->branch()->first()
                ->fill([
                    'name' => $branch->name,
                    'region' => $branch->region,
                ]);

            if ($branch->isDirty(['name', 'region'])) {
                $branch->replicate()
                ->fill(['user_id' => $userModel->id])
                ->save();
            }
        }

        $userModel->gender = data_get($user->toArray(), 'sex');
        $userModel->photo = data_get($user->toArray(), 'picture');

		return $userModel;
    }
}
