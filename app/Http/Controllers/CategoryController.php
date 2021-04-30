<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{

    public function __construct(){
      $this->middleware( 'api.auth', [ 'except' => [ 'test', 'index', 'show' ] ] );
    }

    public function test(){
      echo 'Probando Category Controller';
    }

    public function index(){

      $categories = Category::all();

      return response()->json([
        'code'       => 200,
        'status'     => 'success',
        'categories' => $categories,
      ], 200 );

    }

    public function show( $id ){

      $category = Category::find( $id );

      if( is_object( $category ) ){
        $data = array(
          'code'     => 200,
          'status'   => 'success',
          'category' => $category,
        );
      }else{
        $data = array(
          'code'     => 404,
          'status'   => 'error',
          'message'  => 'La categoria no existe',
        );
      }

      return response()->json( $data, $data[ 'code' ] );

    }

    public function store( Request $request ){

      $json = $request->input( 'json', null );
      $params_array = json_decode( $json, true );

      if( !empty( $params_array ) ){

        // Validamos los datos
        $validate = \Validator::make( $params_array, [
          'name' => 'required|unique:categories'
        ]);

        if( !$validate->fails() ){

          // Creamos la nueva categoria y la salvamos en la Base de Datos
          $category = new Category();
          $category->name = $params_array[ 'name' ];
          $category->save();

          $data = array(
            'code'    => 200,
            'status'  => 'success',
            'category' => $category,
          );

        }else{
          $data = array(
            'code'    => 400,
            'status'  => 'error',
            'message' => 'Los parametros no son validos.',
          );
        }

      }else{
        $data = array(
          'code'    => 400,
          'status'  => 'error',
          'message' => 'Faltan los parametros.',
        );
      }

      return response()->json( $data, $data[ 'code' ] );

    }
}
