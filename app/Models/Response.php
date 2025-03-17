<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
  protected $fillable = [
    'uuid', 'audit_id', 'question_id', 'response_text',
    'is_compliant', 'evidence', 'is_synced', 'sync_timestamp'
  ];

  protected $casts = [
    'is_compliant' => 'boolean',
    'is_synced' => 'boolean',
    'sync_timestamp' => 'datetime',
  ];

  public function audit()
  {
    return $this->belongsTo(Audit::class);
  }

  public function question()
  {
    return $this->belongsTo(Question::class);
  }
}
