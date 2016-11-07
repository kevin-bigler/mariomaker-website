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

  public function stringContainsString($haystack, $needle) {
    return strpos($haystack, $needle) !== false;
  }

  public function stringAfterString($haystack, $delimiter) {
    $arr = explode($delimiter, $haystack);
    if (count($arr) > 1)
      return $arr[1];
    else
      return '';
  }

  public function stringBeforeString($haystack, $delimiter) {
    $arr = explode($delimiter, $haystack);
    if (count($arr) > 0)
      return $arr[0];
    else
      return '';
  }

  public function forceUtf8($str) {
    return \ForceUTF8\Encoding::toUTF8($str);
  }

  public function forceLatin1($str) {
    return \ForceUTF8\Encoding::toLatin1($str);
  }

  public function camelCaseToUnderscore($str) {
    return strtolower( \SSD\StringConverter\Factory::camelToUnderscore($str) );
  }

  public function underscoreToCamelCase($str) {
    return strtolower( \SSD\StringConverter\Factory::underscoreToCamel($str) );
  }

}