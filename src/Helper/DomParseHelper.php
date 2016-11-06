<?php
namespace KevinBigler\MM\Helper;

class DomParseHelper {

  private $common;

  function __construct() {
    $this->common = new Common();
  }

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

  public function getPlayerBasicInfo($elements) {
    // TODO
    return [
      'name' => '',
      'nintendo_id' => '',
      'flag' => '',
      'profile_image_url' => ''
    ];
  }

  public function getPlayerNintendoIdFromProfileLink($linkElements) {
    if ($linkElements->count() > 0) {
      $linkElement = $linkElements[0];
      return $this->getPlayerNintendoIdFromProfileUrl( $linkElement->getAttribute('href') );
    }

    return null;
  }

  public function getAllPlayerNintendoIdsFromProfileLinks($linkElements) {
    $playerNintendoIds = [];
    foreach ($linkElements as $linkElement) {
      $playerNintendoIds[] = $this->getPlayerNintendoIdFromProfileUrl( $linkElement->getAttribute('href') );
    }
    return $playerNintendoIds;
  }

  public function getPlayerNintendoIdFromProfileUrl($url) {
    if ($url) {
      // example: href="/profile/thek3vinator?type=posted"
      return $this->common->stringBetweenStrings($url, '/profile/', '?');
    }

    return null;
  }

}