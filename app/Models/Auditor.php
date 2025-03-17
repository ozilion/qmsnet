<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auditor extends Model
{
  // Tablo adı, eğer tablo adı varsayılan (model adının çoğulu) değilse belirtin.
  protected $table = 'auditors';

  // Formdan gelecek alanları tanımlayın.
  protected $fillable = [
    'uid',
    'name',
    'basdenetci',
    'denetci',
    'adaydenetci',
    'teknikuzman',
    'iku',
    'teknikgozdengeciren',
    'kararverici',
    'belgelendirmemuduru',
    'belgelendirmesorumlusu',
    'planlamasorumlusu',
  ];
}
