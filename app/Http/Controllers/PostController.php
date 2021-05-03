<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Helpers\JwtAuth;

class PostController extends Controller
{

    public function __construct(){
      $this->middleware( 'api.auth', [ 'except' => [ 'test', 'index', 'show' ] ] );
    }

    public function test(){
      echo 'Probando Post Controller';
    }

    public function index(){

      // Pido todos los posts, y ademas puedo pedir los registros relacionales como 'category' y 'user'
      $posts = Post::all()->load( 'category', 'user' );

      return response()->json([
        'code'   => 200,
        'status' => 'success',
        'posts'  => $posts,
      ], 200 );

    }

    public function show( $id ){

      $post = Post::find( $id );

      if( is_object( $post ) ){

        // Si el post existe, entonces tambien le pido que cargue su categoria
        $post->load( 'category' );
        // Tambien podria pedirle el usuario. El orden de los argumentos no importa
        // $post->load( 'user', 'category' );

        $data = array(
          'code'   => 200,
          'status' => 'success',
          'post'   => $post,
        );

      }else{

        $data = array(
          'code'   => 404,
          'status' => 'error',
          'message'   => 'El post no existe.',
        );

      }

      return response()->json( $data, $data['code'] );
    }

    public function store( Request $request ){

      // Obtengo los datos del usuario que quiere agregar un nuevo Post
      $jwtAuth = new JwtAuth();
      $jwtToken = $request->header( 'Authorization' );
      $user = $jwtAuth->checkToken( $jwtToken, true );

      // Obtengo los datos del Post
      $json = $request->input('json');
      $params = json_decode( $json );
      $params_array = json_decode( $json, true );

      if( !empty( $params ) ){

        // Valido los datos del Post
        $validate = \Validator::make( $params_array, [
          'category_id' => 'required',
          'title'       => 'required',
          'content'     => 'required',
        ]);

        if( !$validate->fails() ){

          // Creo el post
          $post = new Post();
          $post->user_id      = $user->sub;
          $post->category_id  = $params->category_id;
          $post->title        = $params->title;
          $post->content      = $params->content;

          $post->image = isset( $params->image )? $params->image : null;

          // Guardo el post
          $post->save();

          $data = [
            'code'    => 200,
            'status'  => 'success',
            'post' => $post,
          ];

        }else{
          $data = [
            'code'    => 400,
            'status'  => 'error',
            'message' => 'Los argumentos no son validos.',
          ];
        }


      }else{
        $data = [
          'code'    => 400,
          'status'  => 'error',
          'message' => 'Faltan los argumentos.'
        ];
      }

      return response()->json( $data, $data['code'] );
    }

    public function update( $id, Request $request ){

      // Mensaje de respuesta de error por default
      $data = [
        'code' => 400,
        'status' => 'error',
      ];

      // Obtenemos los parametros
      $json = $request->input( 'json' );
      $params_array = json_decode( $json, true );

      // Comprobamos que no esten vacios los parametros
      if( empty( $params_array ) ){
        $data[ 'message' ] = 'No se recibieron los parametros.';
        return response()->json( $data, $data['code'] );
      }

      // Validamos los parametros
      $validate = \Validator::make( $params_array, [
        'category_id' => 'required',
        'title'       => 'required',
        'content'     => 'required',
      ]);

      if( $validate->fails() ){
        $data[ 'message' ] = 'Los parametros no son validos.';
        $data[ 'validate_errors' ] = $validate->errors();
        return response()->json( $data, $data['code'] );
      }

      // Actualizamos el post
      $result = Post::where( 'id', $id )->update( $params_array ); // nos devuelve 'true' si pudo actualizar con exito, o 'false' en caso contrario
      //$post = Post::where( 'id', $id )->updateOrCreate( $params_array ); // nos devuelve el registro actualizado, o si no existe lo crea y lo devuelve

      // Mensaje de respuesta de exito
      $data = [
        'code' => 200,
        'status' => 'success',
        'changes' => $params_array,
        //'post' => $post,
      ];

      return response()->json( $data, $data['code'] );
    }

}
