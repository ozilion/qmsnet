<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nonconformity extends Model
{
  protected $fillable = [
    'uuid', 'audit_id', 'standard_section_id', 'description',
    'severity', 'correction', 'corrective_action', 'due_date',
    'status', 'is_synced', 'sync_timestamp'
  ];

  protected $casts = [
    'due_date' => 'date',
    'is_synced' => 'boolean',
    'sync_timestamp' => 'datetime',
  ];

  public function audit()
  {
    return $this->belongsTo(Audit::class);
  }

  public function standardSection()
  {
    return $this->belongsTo(StandardSection::class);
  }
}
