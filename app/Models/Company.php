<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
  use HasFactory;

  // Tablo adı, eğer tablo adı varsayılan (model adının çoğulu) değilse belirtin.
  protected $table = 'company';

  // Formdan gelecek alanları tanımlayın.
  protected $fillable = [
    'id',
    'adi',
    'adres',
    'country',
  ];
}
