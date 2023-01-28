<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;

class Courier extends Controller
{
  //
  public function availableRequests()
  {
    $requests_list = array_filter(Cache::get('requests', []), function ($item) {
      return !$item['isProcessing'];
    });
    return response()->json($requests_list);
  }

  public function acceptRequest($id)
  {
  }

  public function startDelivery($id)
  {
  }

  public function finishDelivery($id)
  {
  }
}
