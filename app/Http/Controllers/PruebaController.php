<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;

class PruebaController extends Controller
{
    //

    public function test(){

      /*
      $posts = Post::all();
      //var_dump( $posts );
      foreach ($posts as $post) {
        echo "<h1>".$post->title."</h1>";
        echo "<h6>{$post->user->name} | {$post->category->name}</h6>";
        echo "<p>".$post->content."</p>";
        echo '<hr>';
      }
      */

      $categories = Category::all();
      foreach ( $categories as $category ) {
        echo "<h3>{$category->name}</h3>";

        foreach ( $category->posts as $post ) {
          echo "<h4>{$post->title}</h4>";
          echo "<p>{$post->content}</p>";
          echo '<hr>';
        }
      }

      die();//corta la ejecucion y no me pide ninguna vista
    }

}
