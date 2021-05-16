<?php

namespace App\Helpers; // definimos el namespace

use Firebase\JWT\JWT; // Esto nos permite utilizar la libreria JWT para usar el Token
use Illuminate\Support\Facades\DB; // Esto nos permite conectarnos a la Base de Datos con Laravel
use App\Models\User; // Cargamos el modelo

class JwtAuth{

  public $key;

  public function __construct(){
    $this->key = 'clave_781687125465738743_secreta';
  }

  public function singup( $email, $password, $getDecodedToken = null ){

    // El dato de retorno por default es false
    $data = false;

    // Buscamos si existe el usuario con sus credenciales
    // El metodo where() hace una busqueda SQL de usando WHERE
    $user = User::where([
      'email'    =>  $email,
      'password' =>  $password
    ])->first(); // con first() obtenemos el primer resultado

    // Comprobamos si la busqueda encontro un usuario
    $singup = is_object( $user )? true : false;

    // Si exite un usuario, creamos un token
    if( $singup ){

      $datasOfToken = array(
        'sub'     =>    $user->id,            // JWT maneja "id" con el nombre "sub"
        'email'   =>    $user->email,
        'name'    =>    $user->name,
        'surname' =>    $user->surname,
        'description' => $user->description,
        'iat'     =>    time(),               // fecha y hora de la creacion del Token
        'exp'     =>    time() + (7*24*60*60) // expira en una semana
      );

      $jwtToken = JWT::encode( $datasOfToken, $this->key, 'HS256' );
      $decodedToken = JWT::decode( $jwtToken, $this->key, ['HS256'] );

      $data = is_null( $getDecodedToken )? $jwtToken : $decodedToken;

    }

    return $data;
  }

  public function checkToken( $jwtToken, $getIdentify = false ){

    $auth = false;

    try{

      $jwtToken = str_replace( '"', '', $jwtToken ); // Limpiamos el Token por si viene entre comillas
      $decodedToken = JWT::decode( $jwtToken, $this->key, ['HS256'] );

    }catch( \UnexpectedValueException $e ){
      $auth = false;
    }catch( \DomainException $e ){
      $auth = false;
    }

    if( !empty( $decodedToken ) && is_object( $decodedToken ) && isset( $decodedToken->sub ) ){
      $auth = true;
    }else{
      $auth = false;
    }

    return $getIdentify? $decodedToken : $auth;

  }

}
