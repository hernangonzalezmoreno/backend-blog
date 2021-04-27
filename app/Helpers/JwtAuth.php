<?php

namespace App\Helpers; // definimos el namespace

use Firebase\JWT\JWT; // Esto nos permite utilizar la libreria JWT para usar el Token
use Illuminate\Support\Facades\DB; // Esto nos permite conectarnos a la Base de Datos con Laravel
use App\Models\User; // Cargamos el modelo

class JwtAuth{

  public function singup(){
    return 'Metodo singup() de la clase JwtAuth.';
  }

}
