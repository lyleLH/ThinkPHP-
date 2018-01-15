<?php
namespace app\api\model;

class RequestLog {
  public static function addNew($data) {
    return db('request_log')
      ->insert($data);
  }
}