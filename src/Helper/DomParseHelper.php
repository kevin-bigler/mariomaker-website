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

  public function getPlayersBasicInfos($userWrapperElements) {
    // TODO
    /*
      Example from a "Cleared by" list item:
      <div class="user-wrapper">
        <div class="mii-wrapper">
          <a id="mii" class="icon-mii link bg-white" href="/profile/Itsaname42?type=posted"><img src="http://mii-images.cdn.nintendo.net/1f1x0xp88j36o_normal_face.png" alt="1f1x0xp88j36o normal face"></a>
        </div>
        <div class="user-info">
          <div class="flag US"></div>
          <div class="name">Aaron</div>
        </div>
      </div>
    */
    $allPlayers = [];
    if ($userWrapperElements->count() > 0) {
      foreach ($userWrapperElements as $userWrapperElement) {
        $allPlayers[] = $this->getPlayerBasicInfo($userWrapperElement);
      }
    }
    // return [
    //   [
    //   'name' => '',
    //   'nintendo_id' => '',
    //   'flag' => '',
    //   'profile_image_url' => ''
    //   ]
    // ];
    return $allPlayers;
  }

  public function getPlayerBasicInfo($userWrapperElement) {
    if ( ! $userWrapperElement || $userWrapperElement->count() < 1)
      return null;

    $player = [];

    // name
    $player['name'] = $this->firstElementText( $userWrapperElement->find('.name') );
    // $player['name'] = $this->common->forceUtf8($player['name']); // TODO figure out how to get special characters to show up / save (instead of showing as '?')

    // nintendo_id
    $player['nintendo_id'] = $this->getPlayerNintendoIdFromProfileLink( $userWrapperElement->find('a#mii') );

    // flag
    $flagElementClass = $this->firstElementAttribute( $userWrapperElement->find('.flag'), 'class' );
    $player['flag'] = $this->common->stringAfterString($flagElementClass, 'flag ');

    // profile_image_url
    $player['profile_image_url'] = $this->firstElementAttribute( $userWrapperElement->find('.mii-wrapper img'), 'src' );

    // return [
    // 'name' => '',
    // 'nintendo_id' => '',
    // 'flag' => '',
    // 'profile_image_url' => ''
    // ];
    return $player;
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