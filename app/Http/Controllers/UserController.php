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
      $validate = \Validator::make( $params_array, [
          'name'      => 'required|alpha',
          'surname'   => 'required|alpha',
          'email'     => 'required|email|unique:users', // con unique verificamos que el email no este registrado en la tabla users. De lo contrario, la validacion falla
          'password'  => 'required',
      ]);

      if( $validate->fails() ){
        //La validacion ha fallado
        $data = array(
          'status'  => 'error',
          'code'    => 404,
          'message' => 'Datos no validos. El usuario no se a creado.',
          'errors'  => $validate->errors()
        );

      }else{
        //La validacion paso correctamente

        // Ciframos el password
        // cambio de cifrado de password_hash() a hash(), ya que el otro metodo devuelve siempre un cifrado distinto
        $password_cifrado = hash( 'sha256', $params->password );

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
      $jwtAuth = new \JwtAuth();

      // Recibimos los datos
      $json = $request->input( 'json', null );
      $params_array = json_decode( $json, true );

      // Aplico un trim a todos los datos del array para quitarle los espacios
      $params_array = array_map( 'trim', $params_array );

      // Validamos los datos
      $validate = \Validator::make( $params_array, [
          'email'     => 'required|email',
          'password'  => 'required',
      ]);

      if( $validate->fails() ){
        // La validacion ha fallado
        $singup = array(
          'status'  => 'error',
          'code'    => 404,
          'message' => 'No se a podido logear.',
          'errors'  => $validate->errors()
        );
      }else{

        // Ciframos el password
        $password_cifrado = hash( 'sha256', $params_array[ 'password' ] );

        // Comprobamos si debemos devolver el token o los datos
        $getDecodedToken = empty( $params_array[ 'getDecodedToken' ] )? null : true;

        // Creamos el token
        $token = $jwtAuth->singup( $params_array[ 'email' ], $password_cifrado, $getDecodedToken );

        // Listo para retornarlo
        $singup = $token;
      }

      // Devolvemos el resultado en formato JSON
      return response()->json( $singup, 200 );

    }

    public function update( Request $request ){

      // Verificamos el token
      $jwtToken = $request->header('Authorization');
      $jwtAuth = new \JwtAuth();
      $checkToken = $jwtAuth->checkToken( $jwtToken );

      // Obtenemos los datos por post
      $json = $request->input( 'json', null );
      $params_array = json_decode( $json, true );

      // Si el token es correcto y tenemos datos por post
      if( $checkToken && !empty( $params_array ) ){

        // Obtenemos el usuario indentificado
        $user = $jwtAuth->checkToken( $jwtToken, true );

        // Validamos
        $validate = \Validator::make( $params_array, [
            'nombre'  => 'required|alpha',
            'surname' => 'required|alpha',
            'email'   => 'required|email|unique:users,'.$user->sub
        ]);

        // Quitamos campos que no queremos actualizar
        unset( $params_array[ 'id' ] );
        unset( $params_array[ 'role' ] );
        unset( $params_array[ 'password' ] );
        unset( $params_array[ 'created_at' ] );
        unset( $params_array[ 'remember_token' ] );

        // Actualizamos el usuario en la Base de Datos
        $user_update = User::where( 'id', $user->sub )->update( $params_array );

        // Creamos la respuesta
        $data = array(
          'code'    => 200,
          'status'  => 'success',
          'message' => 'Se ha actualizado el usuario.',
          'user'    => $user,
          'changes' => $params_array
        );

      }else{

        $data = array(
          'code'    => 400,
          'status'  => 'error',
          'message' => 'El usuario no esta autentificado.'
        );

      }

      return response()->json( $data, $data[ 'code' ] );
    }

}
