<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Post;
use App\Helpers\JwtAuth;

class PostController extends Controller
{

    public function __construct(){
      $this->middleware( 'api.auth', [ 'except' => [
        'test',
        'index',
        'show',
        'getImage',
        'getPostsByCategory',
        'getPostsByUser',
      ] ] );
    }

    private function getUserIdentity( Request $request ){
      $jwtAuth = new JwtAuth();
      $jwtToken = $request->header( 'Authorization' );
      return $jwtAuth->checkToken( $jwtToken, true );
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
      $user = $this->getUserIdentity( $request );

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

      // Actualizamos el post (sin preguntar por el usuario)
      //$result = Post::where( 'id', $id )->update( $params_array ); // nos devuelve 'true' si pudo actualizar con exito, o 'false' en caso contrario
      //$post = Post::where( 'id', $id )->updateOrCreate( $params_array ); // nos devuelve el registro actualizado, o si no existe lo crea y lo devuelve

      // Obtenemos la identificacion del usuario que quiere hacer la modificacion del post
      $user = $this->getUserIdentity( $request );

      // Obtenemos el post que se quiere modificar, y comprobamos que el usuario sea propietario del mismo
      $post = Post::where( 'id', $id )
                  ->where( 'user_id', $user->sub )
                  ->first();

      if( empty( $post ) || !is_object( $post ) ){
        $data[ 'message' ] = 'El post no existe, o el usuario autentificado no es el propietario.';
        return response()->json( $data, $data['code'] );
      }

      // Actualizamos el registro
      $post->update( $params_array );

      // Mensaje de respuesta de exito
      $data = [
        'code' => 200,
        'status' => 'success',
        'changes' => $params_array,
        'post' => $post,
      ];

      return response()->json( $data, $data['code'] );
    }

    public function destroy( $id, Request $request ){

      // Obtenemos el usuario que quiere realizar la eliminacion del post
      $user = $this->getUserIdentity( $request );

      // Obtenemos el post
      //$post = Post::find( $id );

      // Obtenemos el post solo si el usuario autentificado es quien creo el post
      $post = Post::where( 'id', $id )
                  ->where( 'user_id', $user->sub )
                  ->first();

      // Si no existe retornamos error
      if( empty( $post ) ){
        $data = [
          'code'    => 404,
          'status'  => 'error',
          'message' => 'El post no existe, o el usuario autentificado no es el propietario.'
        ];
        return response()->json( $data, $data['code'] );
      }

      // Si existe lo borramos de la Base de Datos
      $post->delete();

      // Retornamos exito
      $data = [
        'code'    => 200,
        'status'  => 'success',
        'message' => 'El post se ha eliminado correctamente.',
        'post'    => $post,
      ];

      return response()->json( $data, $data['code'] );

    }

    public function upload( Request $request ){

      // Obtenemos la imagen
      $image = $request->file( 'file0' );

      // Validamos la imagen
      $validate = \Validator::make( $request->all(), [
        'file0' => 'required|image|mimes:jpg,jpeg,png,gif'
      ]);

      // Devolvemos error en caso de que la imagen no sea valida
      if( !$image || $validate->fails() ){
        $data = [
          'code' => 400,
          'status' => 'error',
          'message' => 'No se ha enviado una imagen o no es valida.',
        ];
      }else{

        // Creamos el nombre
        $image_name = time().'_'.$image->getClientOriginalName();

        // Guardamos la imagen
        \Storage::disk( 'images' )->put( $image_name, \File::get( $image ) );

        $data = [
          'code' => 200,
          'status' => 'success',
          'message' => 'La imagen se ha guardado correctamente.',
          'image'   => $image_name,
        ];

      }

      return response()->json( $data, $data['code'] );

    }

    public function getImage( $filename ){

      // Comprobamos si existe
      $isset = \Storage::disk( 'images' )->exists( $filename );

      if( $isset ){

        // Obtenemos la imagen
        $file = \Storage::disk( 'images' )->get( $filename );

        // Devolvemos la imagen
        return new Response( $file, 200 );
      }

      $data = [
        'code' => 404,
        'status' => 'error',
        'message' => 'La imagen no existe.',
      ];
      return response()->json( $data, $data['code'] );

    }

    public function getPostsByCategory( $category_id ){

      // Obtenemos todos los posts de determinada categoria
      $posts = Post::where( 'category_id', $category_id )->get();

      return response()->json([
        'status' => 'success',
        'posts'  => $posts
      ], 200);

    }

    public function getPostsByUser( $user_id ){

      // Obtenemos todos los posts de determinada categoria
      $posts = Post::where( 'user_id', $user_id )->get();

      return response()->json([
        'status' => 'success',
        'posts'  => $posts
      ], 200);

    }

}
