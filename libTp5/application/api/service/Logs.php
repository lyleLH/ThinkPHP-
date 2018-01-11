<?php
namespace app\api\service;

/*
 *  本类用于系统自动记录 Log 与请求判重
 *  如非必要，请勿自行调用
 *  MateorChan
 */

use think\Cache;
use think\Request;
use app\api\model\RequestLog;
use app\api\cache\LogCache;

class Logs {
  public static function moduleInit() {
    $r = Request::instance();
    if($r->method() !== 'POST')
    //  过滤所有非 POST 请求
      return ;
    $data['module'] = $r->controller();
    $data['method'] = $r->action();
    $data['param'] = $r->param();
    $data['token'] = Func::getToken();
    $data['timestamp'] = Func::getTimeStamp();
    $hash = hash('sha256', json_encode($data, JSON_UNESCAPED_UNICODE));
    if(LogCache::ifExist($hash))
      Func::callBack(501, '您的操作太快，服务器要被您玩坏啦～');
    LogCache::setHash($hash);
  }

  public static function getUid() {
    Cache::connect(config('cache'));
    $token = Cache::get('user_'.Func::getToken());
    $uid = $token['uid'];
    return empty($uid)?0:$uid;
  }

  public static function newLog($code, $msg) {
    // $r = Request::instance();
    $data['code'] = $code;
    $data['msg'] = $msg;
    $data['uid'] = '1';
    RequestLog::addNew($data);
  }
}