<?php
namespace KevinBigler\MM\Helper;

class DomParseHelper {

  public function firstElementText($elements) {
    if ($elements->count() > 0)
      return $elements[0]->text;
    else
      return null;
  }

  public function firstElementAttribute($elements, $attribute) {
    if ($elements->count() > 0)
      return $elements[0]->getAttribute($attribute);
    else
      return null;
  }

  public function domHasElement($dom, $selector) {
    $elements = $dom->find($selector);
    return $elements->count() > 0;
  }

}