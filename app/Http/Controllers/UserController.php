<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function test(){
      echo 'Probando User Controller';
    }

    public function register( Request $request ){

      // Recibir los datos del usuario por post
      $json = $request->input( 'json', null );// El segundo parametro define en caso que no me llegue el JSON que le asigne null
      $params = json_decode( $json ); // Decodifica el JSON y lo convierte en un objeto
      $params_array = json_decode( $json, true ); // Decodifica el JSON y lo convierte en un array

      //var_dump( $json );
      //var_dump( $params->name );
      //var_dump( $params_array['name'] );

      // Si estan vacios
      if( empty( $params ) || empty( $params_array ) ){

        $data = array(
          'status'  => 'error',
          'code'    => 404,
          'message' => 'El JSON está corrupto.',
        );

        return response()->json( $data, $data['code'] );
      }

      // Aplico un trim a todos los datos del array para quitarle los espacios
      $params_array = array_map( 'trim', $params_array );

      // Validar los datos
      $validated = \Validator::make( $params_array, [
          'name'      => 'required|alpha',
          'surname'   => 'required|alpha',
          'email'     => 'required|email|unique:users', // con unique verificamos que el email no este registrado en la tabla users. De lo contrario, la validacion falla
          'password'  => 'required',
      ]);

      if( $validated->fails() ){
        //La validacion ha fallado
        $data = array(
          'status'  => 'error',
          'code'    => 404,
          'message' => 'Datos no validos. El usuario no se a creado.',
          'errors'  => $validated->errors()
        );

      }else{
        //La validacion paso correctamente

        // Ciframos el password
        // el cost indica la cantidad de veces a cifrar. En este caso aplicamos 4 veces el cifrado.
        // Es decir, ciframos una vez, luego a ese cifrado lo volvemos a cifrar, y asi hasta hacerlo 4 veces
        // Esto tiene costo computacional.
        $password_cifrado = password_hash( $params->password, PASSWORD_BCRYPT, [ 'cost' => 4 ] );

        // Creamos el usuario
        $user = new User();
        $user->name = $params_array[ 'name' ];
        $user->surname = $params_array[ 'surname' ];
        $user->email = $params_array[ 'email' ];
        $user->password = $password_cifrado;
        $user->role = "ROLE_USER";

        // Guardamos el usuario en la base de datos
        $user->save(); // Esto realiza todo lo necesario para guardar en la tabla users. Hacer el INSERT a MySQL

        // Creamos el response
        $data = array(
          'status'  => 'success',
          'code'    => 200,
          'message' => 'El usuario se a creado correctamente.',
          'user'    => $user
        );
      }

      return response()->json( $data, $data['code'] );
    }

    public function login( Request $request ){

    }
}
