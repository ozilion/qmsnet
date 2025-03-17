<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Standard extends Model
{
  use SoftDeletes;

  protected $fillable = [
    'code', 'name', 'version', 'description', 'is_active'
  ];

  public function revisions()
  {
    return $this->hasMany(StandardRevision::class);
  }

  public function currentRevision()
  {
    return $this->hasOne(StandardRevision::class)->where('is_current', true);
  }
}
