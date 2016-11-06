<?php
namespace KevinBigler\MM\Helper;

class DomParseHelper {

  public function firstElementText($elements) { //, $extremeMeasures = false) {
    if ($elements->count() > 0) {
      // if ( $extremeMeasures ) // taking matters into our own hands
      //   return trim(strip_tags($elements[0]->innerHtml));
      // else
        return $elements[0]->text;
    } else {
      return null;
    }
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

  public function getClasses($element) {
    return explode( ' ', $element->getAttribute('class') );
  }

}