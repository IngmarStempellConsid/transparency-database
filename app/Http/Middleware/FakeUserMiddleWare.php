<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class FakeUserMiddleWare
{
    public function handle(Request $request, Closure $next): Response
    {
        $request->setUserResolver(
            function () {
                return User::firstOrCreate(

                    [
                        "name" => "Otto",
                        "platform_id" => "42",
                        "eu_login_username" => "Otto",
                        "email" => "Otto",
                        "password" => "Otto",
                    ]
                );


            }
        );

        // Perform action
        return $next($request);
    }
}
