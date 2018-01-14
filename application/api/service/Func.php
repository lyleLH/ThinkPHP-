<?php
namespace app\api\service;

class Func {
  public static function callBack($code, $msg, $data = null, $isExist = true) {
    header('Content-Type: application/json;charset=UTF-8');
    header('Access-Control-Allow-Origin: *');
    if (!empty($data)) {
      $data = Func::revert($data);
    } else {
      $data = (object) array();
    }
    $result = json_encode([
        'code'   =>  $code,
        'msg'     =>  $msg,
        'data'     =>  $data,
      ], JSON_UNESCAPED_UNICODE);
    if(!$isExist) return $result;
    Logs::newLog($code, $msg);
    exit($result);
  }


  public static function encrypt($string, $operation, $key = '') {
    $key = md5($key);
    $key_length = strlen($key);
    $string = $operation == 'D'? base64_decode($string): substr(md5($string . $key), 0, 8) . $string;
    $string_length = strlen($string);
    $rndkey = $box = array();
    $result = '';
    for ($i = 0; $i <= 255; $i++) {
      $rndkey[$i] = ord($key[$i % $key_length]);
      $box[$i] = $i;
    }
    for ($j = $i = 0; $i < 256; $i++) {
      $j = ($j + $box[$i] + $rndkey[$i]) % 256;
      $tmp = $box[$i];
      $box[$i] = $box[$j];
      $box[$j] = $tmp;
    }
    for ($a = $j = $i = 0; $i < $string_length; $i++) {
      $a = ($a + 1) % 256;
      $j = ($j + $box[$a]) % 256;
      $tmp = $box[$a];
      $box[$a] = $box[$j];
      $box[$j] = $tmp;
      $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    if ($operation == 'D') {
      if (substr($result, 0, 8) == substr(md5(substr($result,8) . $key), 0, 8)) {
        return substr($result, 8);
      } else {
        return '';
      }
    } else {
      return str_replace('=', '', base64_encode($result));
    }
  }


  public static function solveTime($time) {
    //  处理时间
    $time = substr($time, 5, 11);
    return $time;
  }

  public static function getToken() {
    if (!empty($_SERVER['HTTP_TOKEN'])) {
      return $_SERVER['HTTP_TOKEN'];
    }
    return '';
  }

  public static function getVersion() {
    if (!empty($_SERVER['HTTP_VERSION'])) {
      return $_SERVER['HTTP_VERSION'];
    }
    return '';
  }

  public static function getTimeStamp() {
    if (!empty($_SERVER['HTTP_TIMESTAMP'])) {
      return ($_SERVER['HTTP_TIMESTAMP']/1000);
    }
    return 0;
  }


  public static function checkParams($data, $keyArr) {
    $res = [];
    foreach($keyArr as $value) {
      if(empty($data[$value])) {
        if(isset($data[$value]) && is_numeric($data[$value]) && $data[$value] == 0)
          $res[$value] = $data[$value];
        else Func::callBack(500, '请按提示进行输入');
      }
      $res[$value] = $data[$value];
    }
    return $res;
  }


  private static function revert($data) {
    if(!is_array($data))
      return $data;
    $newData = array();
    foreach ($data as $key => $value) {
      $key = ($key === strtoupper($key))?strtolower($key):$key;
      $key = str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
      $key[0] = strtolower($key[0]);
      if(is_array($value))
        $value = Func::revert($value);
      $newData[$key] = $value;
    }
    return $newData;
  }

  public static function randStr($length, $type = 'all') {
    if ($type == 'all')
      $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    elseif ($type == 'num')
      $chars='1234567890';
    $str = "";
    for ($i = 0; $i < $length; $i++)
      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    return $str;
  }

  public static function getTime($format = null) {
    $format = empty($format)?'Y-m-d H:i:s':$format;
    return date($format);
  }

  public static function debug($data = null) {
    exit(var_dump($data));
  }

  public static function js2Array($data = null) {
    if(is_string($data)) return json_decode($data, true);
    else return $data;
  }

  public static function doCurl($url, $method = 'get', $data = null) {
    $header = [];
  	$ch = curl_init();
  	curl_setopt($ch, CURLOPT_URL, $url);
  	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
  	if($method == 'post') {
  		curl_setopt($ch, CURLOPT_POST, 1);
  		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
  	}
  	$response = curl_exec($ch);
  	if(curl_errno($ch)){
  		print curl_error($ch);
  	}
  	curl_close($ch);
  	return $response;
  }
}
