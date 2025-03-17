<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Pusher\Pusher;

class PusherNotificationController extends Controller
{
    public static function notification($planno,$dentarihi,$dtipi,$firmaadi)
    {
        $options = array(
            'cluster' => env('PUSHER_APP_CLUSTER'),
            'encrypted' => true
        );
        $pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            $options
        );

        $data['planno'] = $planno;
        $data['dentarihi'] = $dentarihi;
        $data['dtipi'] = $dtipi;
        $data['firmaadi'] = mb_substr($firmaadi,0,25,"UTF8");
        $pusher->trigger('easynet', 'App\\Events\\PlanEvents', $data);

    }
}
