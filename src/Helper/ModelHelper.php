<?php
namespace KevinBigler\MM\Helper;

class ModelHelper {

  private $common;

  function __construct() {
    $this->common = new Common();
  }

  public function objectToDatabaseAssoc($obj) {
    $obj = $obj ?? null;
    
    if ( is_object($obj) ) {
      $assoc = [];
      foreach ($obj as $property => $value) {
        $key = $this->common->camelCaseToUnderscore($property);
        $assoc[$key] = $value;
      }
      return $assoc;
    }

    return null;
  }

}