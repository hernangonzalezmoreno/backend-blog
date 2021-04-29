<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Verificamos el token
        $jwtToken = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken( $jwtToken );

        if( $checkToken ){
          return $next($request);
        }else{

          $data = array(
            'code'    => 400,
            'status'  => 'error',
            'message' => 'El usuario no esta autentificado.'
          );

          return response()->json( $data, $data[ 'code' ] );
        }

    }
}
