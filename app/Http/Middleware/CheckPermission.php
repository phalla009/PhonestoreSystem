<?php

use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    public function handle($request, Closure $next, $permission)
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user || !$user->hasPermission($permission)) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
