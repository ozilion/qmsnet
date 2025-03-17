<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
  use HasFactory;
  protected $table = 'roles';


  // Formdan gelecek alanları tanımlayın.
  protected $fillable = [
    'id',
    'name',
    'guard_name',
  ];
}
