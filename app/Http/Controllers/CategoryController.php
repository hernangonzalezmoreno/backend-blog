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

    public function update( $id, Request $request ){

      // el metodo input() adminte un segundo parametro para especificar el valor por defecto,
      // en caso de que no llegue nada. Sin embargo si no llega nada el valor por defecto es null
      // por lo que no hace falta hacer lo siguiente:
      // $json = $request->input( 'json', null ); // ya que es redundante

      $json = $request->input('json');
      $params_array = json_decode( $json, true );

      if( !empty( $params_array ) ){

        // Valido los datos
        $validate = \Validator::make( $params_array, [
            'name' => 'required|unique:categories',
        ]);

        if( !$validate->fails() ){

          // Quito los datos que no quiero actualizar (en caso de que lleguen)
          unset( $params_array[ 'id' ] );
          unset( $params_array[ 'created_at' ] );

          // Hago la actualizacion. Si todo sale bien me devuelve 'true'
          $result = Category::where( 'id', $id )->update( $params_array );

          $data = array(
            'code'    => 200,
            'status'  => 'success',
            'category' => $params_array,
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

      return response()->json( $data, $data['code'] );
    }
}
