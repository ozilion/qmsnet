<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommonSection extends Model
{
  protected $fillable = [
    'standard_revision_id', 'section_title', 'section_type',
    'content', 'display_order'
  ];

  public function standardRevision()
  {
    return $this->belongsTo(StandardRevision::class);
  }
}
