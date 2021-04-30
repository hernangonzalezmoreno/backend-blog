<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

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

}
