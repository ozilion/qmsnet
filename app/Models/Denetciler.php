<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class Denetciler extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    protected $table = 'denetciler';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
  protected $fillable = [
    'id',
    'uid',
    'denetci',
    'ea',
    'nace',
    'kategori',
    'kategorioic',
    'kategoribg',
    'teknikalan',
    'komiteea9',
    'komiteea14',
    'komiteea45',
    'komiteea50',
    'atama9001',
    'atama14001',
    'atama22000',
    'atama27001',
    'atama45001',
    'atama50001',
    'atamaOicsmiic',
    'atamaOicsmiic6',
    'atamaOicsmiic9',
    'atamaOicsmiic171',
    'atamaOicsmiic24',
    'iku',
    'kararkomite',
    'is_active'
  ];

    /**
     * Route notifications for the mail channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return array|string
     */
    public function routeNotificationForMail($notification)
    {
        // Return email address only...
        return "ozcanarslan@aliment.com.tr";//$this->email_address;

        // Return name and email address...
//        return [$this->email_address => $this->name];
    }

  public function user()
  {
    // Denetciler tablosundaki uid, User tablosundaki id ile eşleşir.
    return $this->belongsTo(\App\Models\User::class, 'uid', 'id');
  }

}
