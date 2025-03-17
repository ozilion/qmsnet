<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PlanEvent implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public $planno;
  public $dentarihi;
  public $dtipi;
  public $firmaadi;
  public $asama;

  public function __construct($planno, $dentarihi, $dtipi, $firmaadi, $asama)
  {
    $this->planno = $planno;
    $this->dentarihi = $dentarihi;
    $this->dtipi = $dtipi;
    $this->firmaadi = mb_substr($firmaadi, 0, 25, "UTF8");
    $this->asama = $asama;
  }


  public function broadcastOn()
  {
    return ['easynet-channel'];
  }

  public function broadcastAs()
  {
    return 'plan-event';
  }
}
