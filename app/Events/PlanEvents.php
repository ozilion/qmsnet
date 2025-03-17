<?php

namespace App\Events;

use App\Http\Controllers\PusherNotificationController;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlanEvents implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public $planno;
  public $dentarihi;
  public $dtipi;
  public $firmaadi;

  /**
   * Create a new event instance.
   */
  public function __construct($planno, $dentarihi, $dtipi, $firmaadi)
  {
    $this->planno = $planno;
    $this->dentarihi = $dentarihi;
    $this->dtipi = $dtipi;
    $this->firmaadi = mb_substr($firmaadi, 0, 25, "UTF8");

    PusherNotificationController::notification($this->planno, $this->dentarihi, $this->dtipi, $this->firmaadi);
  }

  /**
   * Get the channels the event should broadcast on.
   *
   * @return \Illuminate\Broadcasting\Channel|array
   */
  public function broadcastOn()
  {
    return ['easynet-channel'];
  }

  public function broadcastAs()
  {
    return 'plan-event';
  }
}
