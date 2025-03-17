<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
  protected $fillable = [
    'uuid', 'user_id', 'standard_revision_id', 'plan_no',
    'audit_type', 'company_name', 'audit_date', 'status'
  ];

  protected $casts = [
    'audit_date' => 'date',
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function standardRevision()
  {
    return $this->belongsTo(StandardRevision::class);
  }

  public function responses()
  {
    return $this->hasMany(Response::class);
  }

  public function nonconformities()
  {
    return $this->hasMany(Nonconformity::class);
  }

  public function plan()
  {
    // Assuming you want to link to existing planlar table
    return $this->belongsTo(Planlar::class, 'plan_no', 'planno');
  }
}
