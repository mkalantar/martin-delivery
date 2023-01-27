<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use UnexpectedValueException;

class Customer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $header = $request->header('Authorization');
        $token = [];
        if (!preg_match('/^Bearer\s(.*)/', $header, $token)) {
          return response('', 401);
        } else {
          try {
            $decoded_token = (array) JWT::decode($token[1], new Key(cache('secret'), 'HS256'));
            if (!preg_match('/^Customer.*/', $decoded_token['aud'])) return response('', 403);
          } catch (UnexpectedValueException $e) {
            return response('', 401);
          }
        }
        return $next($request);
    }
}
