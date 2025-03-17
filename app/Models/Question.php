<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
  protected $fillable = [
    'standard_section_id', 'item_number', 'question_text', 'question_type',
    'options', 'is_required', 'display_order'
  ];

  protected $casts = [
    'options' => 'array',
    'is_required' => 'boolean',
  ];

  public function standardSection()
  {
    return $this->belongsTo(StandardSection::class);
  }

  public function responses()
  {
    return $this->hasMany(Response::class);
  }
}
