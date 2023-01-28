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

  private function validateRequestValues($value)
  {
    $keys = ['lan', 'lon', 'name', 'address', 'phone'];
    foreach ($keys as $key) {
      if (!(array_key_exists($key, $value) && $value[$key])) {
        return false;
      }
    }
    return true;
  }

  public function createRequest(Request $request)
  {
    if ($request->has(['from', 'to'])) {
      if ($this->validateRequestValues($request->input('from')) && $this->validateRequestValues($request->input('to'))) {
        $lock = Cache::lock('requests', 1);
        $requests_list = [];
        try {

          $lock->block(2);

          if (Cache::has('requests')) {
            $requests_list = Cache::get('requests');
          }

          $id = sprintf("R-%d-%s", time(), substr(str_shuffle(md5(microtime())), 0, 7));

          $new_request = $request->input();
          $new_request['isProcessing'] = false;
          $new_request['id'] = $id;
          $requests_list[] = $new_request;

          Cache::put('requests', $requests_list);
          Cache::put($id, count($requests_list) - 1);

          $lock->release();

          return response($id, 200);
        } catch (LockTimeoutException) {

          $lock->release();
          return response('Race condition!', 500);
        }
      }
    }
    return response('Request params are malformed', 400);
  }

  public function cancelRequest(Request $request, $id)
  {

    if (!$id) {
      return response('', 400);
    }
    $index = Cache::get($id);
    if ($index === null) {
      return response('', 404);
    }

    $requests_list = Cache::get('requests');

    if (!array_key_exists($index, $requests_list)) {
      return response('', 404);
    }

    $req = $requests_list[$index];
    if (!$req) {
      return response('', 404);
    }

    if ($req['customer'] != $request->input('customer')) {
      return response('', 403);
    }

    if ($req['isProcessing']) {
      return response('Request is on process', 423);
    }
    Cache::forget($id);
    $lock = Cache::lock('request', 1);
    try {

      $lock->block(2);
      $requests_list = Cache::get('requests');
      unset($requests_list[$index]);
      Cache::put('requests', $requests_list);
      $lock->release();
    } catch (LockTimeoutException) {

      $lock->release();
      return response('Race condition!', 500);
    }
    return response('', 200);
  }
}
