<?php

namespace App\Http\Middleware;

use Closure;

class RefreshToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->user()->newQuery()->count() < 1) {
            $user = $request->user()->newQuery()->withoutGlobalScopes()
                ->firstOrCreate([
                    'email' => $request->user()->email,
                    'role_id' => 1,
                    'first_name' => $request->user()->first_name,
                    'last_name' => $request->user()->last_name,
                ]);

            if (!$user->wasRecentlyCreated) {
                $user->restore();
            }

            $request->user()->setRawAttributes($user->forceFill($request->user()->getAttributes())->getAttributes());
            $request->user()->token->setRawAttributes($user->token->toArray());
            $request->user()->role->setRawAttributes($user->role->toArray());
        }

        if ($request->user()->token()->exists()) {
            $request->user()->token->fill(['api_token' => $request->session()->token()]);

            if ($request->user()->token->isDirty('api_token')) {
                while ($request->user()->token->newQuery()->where('api_token', $request->user()->token->api_token)->exists()) {
                    $request->session()->regenerateToken();
                    $request->user()->token->fill(['api_token' => $request->session()->token()]);
                }

                $request->user()->token->save();

            }
        }

        if ($request->isMethod('post')) {
            $request->session()->regenerateToken();
        }

        return $next($request);
    }
}
