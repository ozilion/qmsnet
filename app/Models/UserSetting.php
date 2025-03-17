<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
  protected $fillable = [
    'user_id',
    'auto_sync',
    'sync_frequency',
    'dark_mode',
    'notification_email',
    'notification_app',
  ];

  protected $casts = [
    'auto_sync' => 'boolean',
    'dark_mode' => 'boolean',
    'notification_email' => 'boolean',
    'notification_app' => 'boolean',
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
