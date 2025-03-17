<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StandardSection extends Model
{
  protected $fillable = [
    'standard_revision_id', 'clause_number', 'clause_title',
    'description', 'display_order', 'is_mandatory'
  ];

  protected $casts = [
    'is_mandatory' => 'boolean',
  ];

  public function standardRevision()
  {
    return $this->belongsTo(StandardRevision::class);
  }

  public function questions()
  {
    return $this->hasMany(Question::class)->orderBy('display_order');
  }

  public function nonconformities()
  {
    return $this->hasMany(Nonconformity::class);
  }
}
