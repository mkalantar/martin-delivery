<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Middleware\Customer as CustomerMiddleware;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\LockTimeoutException;

class Customer extends Controller
{

  public function __construct()
  {
    $this->middleware(CustomerMiddleware::class);
  }

  private function validateRequestValues($value) {
    $keys = ['lan', 'lon', 'name', 'address', 'phone'];
    foreach($keys as $key) {
      if (!(array_key_exists($key, $value) && $value[$key])) {
        return false;
      }
    }
    return true;
  }
  
  public function createRequest(Request $request) {
    if ($request->has(['from', 'to'])) {
      if ($this->validateRequestValues($request->input('from')) && $this->validateRequestValues($request->input('to'))) {
        $lock = Cache::lock('requests', 0.5);
        $requests_list = [];
        try {
          $lock->block(1);
          if (Cache::has('requests')) {
            $requests_list = Cache::get('requests');
          }
          $requests_list[] = $request->input();
          Cache::put('requests', $requests_list);
          $lock->release();

          $id = sprintf("R-%d-%s", time(), substr(str_shuffle(md5(microtime())), 0, 7));
          Cache::put($id, count($requests_list) - 1);

          return response($id, 200);

        } catch(LockTimeoutException $e) {
          $lock->release();
          return response('Race condition!', 500);
        }
      }
    }
    return response('Request params are malformed', 400);
  }

  public function cancelRequest(Request $request, $id) {

    if (!$id) {
      return response('', 400);
    }
    $index = Cache::get($id);
    if ($index !== null) {
      return response('', 404);
    }

    $requests_list = Cache::get('requests');
    $req = $requests_list[$index];
    if (!$req) {
      return response('', 404);
    }

    if ($req['isProcessing']) {
      return response('Request is on process', 423);
    }

    return response('', 200);
  }
}