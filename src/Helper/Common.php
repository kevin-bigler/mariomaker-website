<?php
namespace KevinBigler\MM\Helper;

class Common {

  public function first($arr) {
    $arr = $arr ?? null;
    if (is_array($arr) && count($arr) > 0)
      return $arr[0];
    else
      return null;
  }

  public function stringBetweenStrings($string, $start, $end) {
    // from this SO answer: http://stackoverflow.com/questions/5696412/get-substring-between-two-strings-php
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
  }

}