<?php

namespace App\Observers;

use App\User as UserModel;
use App\Token;
use App\Branch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class User
{
    /**
     * Handle the user "created" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function created(UserModel $user)
    {
		$token = Str::random(40);

		while (Token::where('api_token', $token)->exists()) {
			$token = Str::random(40);
		}

		Token::create([
			'user_id' => $user->id,
			'api_token' => $token
		]);
    }

    /**
     * Handle the user "updated" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function updated(UserModel $user)
    {
        //
    }

	public function deleting(UserModel $user)
    {
		Token::where('user_id', $user->id)
			->delete();
    }

    /**
     * Handle the user "deleted" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function deleted(UserModel $user)
    {
        //
    }

    /**
     * Handle the user "restored" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function restored(UserModel $user)
    {
		$token = Str::random(40);

		while (Token::where('api_token', $token)->exists()) {
			$token = Str::random(40);
		}

		Token::updateOrCreate(
            [
                'user_id' => $user->id,
            ],
            [
                'api_token' => $token
            ]
        );
    }

    /**
     * Handle the user "force deleted" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function forceDeleted(UserModel $user)
    {
        //
    }
}
