<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DenetciAtama extends Model
{
  protected $table = 'denetci_atamalar';

  protected $fillable = [
    'denetci_id',
    'standard',
    'ea',
    'nace',
    'istecrube',
    'danismanlik',
    'referans',
    'teknikAlan',
    'isTecrubesi',
    'danismanlikTecrubesi',
    'atamaReferansi',
    'teknolojikAlan',
    'kategori',
    'altKategori',
  ];
}
