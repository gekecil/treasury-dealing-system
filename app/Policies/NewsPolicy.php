<?php

namespace App\Policies;

use App\News;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class NewsPolicy
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
     * @param  \App\News  $news
     * @return mixed
     */
    public function view(User $user, News $news)
    {
		return $user->id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
		return $user->is_administrator;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\News  $news
     * @return mixed
     */
    public function update(User $user, News $news)
    {
		return $user->is_administrator;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\News  $news
     * @return mixed
     */
    public function delete(User $user, News $news)
    {
		return $user->is_administrator;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\News  $news
     * @return mixed
     */
    public function restore(User $user, News $news)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\News  $news
     * @return mixed
     */
    public function forceDelete(User $user, News $news)
    {
        //
    }
}
