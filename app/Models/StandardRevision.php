<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StandardRevision extends Model
{
  protected $fillable = [
    'standard_id', 'revision_number', 'revision_date',
    'revision_notes', 'docx_file_path', 'is_current'
  ];

  protected $casts = [
    'revision_date' => 'date',
    'is_current' => 'boolean',
  ];

  public function standard()
  {
    return $this->belongsTo(Standard::class);
  }

  public function commonSections()
  {
    return $this->hasMany(CommonSection::class);
  }

  public function standardSections()
  {
    return $this->hasMany(StandardSection::class)->orderBy('display_order');
  }

  public function audits()
  {
    return $this->hasMany(Audit::class);
  }
}
