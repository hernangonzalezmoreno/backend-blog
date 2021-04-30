<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
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
}
