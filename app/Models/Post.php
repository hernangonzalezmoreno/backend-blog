<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $table = 'posts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'title',
        'content',
        'image',
    ];

    public function user(){
      return $this->belongsTo( User::class );
    }

    public function category(){
      return $this->belongsTo( Category::class );
    }
}
