<?php
namespace KevinBigler\MM;

use PHPHtmlParser\Dom;

class LevelParser {

  private $common;
  private $domParseHelper;

  function __construct() {
    $this->common = new Helper\Common();
    $this->domParseHelper = new Helper\DomParseHelper();
  }

  private function getGameskin($dom) {
    // -- mode (SMB1, SMB2, SMW, NSMB)

    $gameskinSelectors = [
      'SMB1' => '.course-meta-info .common_gs_sb',
      'SMB3' => '.course-meta-info .common_gs_sb3',
      'SMW' => '.course-meta-info .common_gs_sw',
      'NSMB' => '.course-meta-info .common_gs_sbu'
    ];

    foreach ($gameskinSelectors as $gameskin => $selector) {
      if ($this->domParseHelper->domHasElement($dom, $selector))
        return $gameskin;
    }

    return null;
  }

  public function parseLevelData($html) {
    $level = new Model\Level();

    /*
      `player_nintendo_id` text DEFAULT NULL,
      `title` text DEFAULT NULL,
      `image_url` text DEFAULT NULL,
      `image_full_url` text DEFAULT NULL,
      `upload_date` date DEFAULT NULL,
      `gameskin` text DEFAULT NULL,
    */

    $dom = new Dom;
    $dom->load($html);

    // player_nintendo_id
    $level->playerNintendoId = null;
    $creatorUrl = $this->domParseHelper->firstElementAttribute( $dom->find('.creator a#mii'), 'href' );
    if ($creatorUrl) {
      // example: href="/profile/thek3vinator?type=posted"
      $level->playerNintendoId = $this->common->stringBetweenStrings($creatorUrl, '/profile/', '?');
    }

    // title
    $level->title = $this->domParseHelper->firstElementText( $dom->find('.course-title') );

    // image_url
    $level->imageUrl = $this->domParseHelper->firstElementAttribute( $dom->find('img.course-image'), 'src' );

    // image_full_url
    $level->imageFullUrl = $this->domParseHelper->firstElementAttribute( $dom->find('img.course-image-full'), 'src' );

    // upload_date
    $createdAtSource = $this->domParseHelper->firstElementText( $dom->find('.created_at') );
    // example: 10/17/2016
    // need to convert to Y-m-d
    $createdAtDate = \DateTime::createFromFormat('m/d/Y', $createdAtSource);
    $level->uploadDate = $createdAtDate->format('Y-m-d');

    // gameskin
    // -- mode (SMB1, SMB2, SMW, NSMB)
    $level->gameskin = $this->getGameskin($dom);

    echo 'level:<pre>';
    print_r($level);
    die();
  }

  public function parseLevelSnapshotData($html) {
    $levelSnapshot = new Model\LevelSnapshot();

    /*
      `difficulty_rank` text DEFAULT NULL,
      `clear_rate` DECIMAL(5, 2) DEFAULT NULL,
      `number_stars` int(10) unsigned DEFAULT NULL,
      `number_footprints` int(10) unsigned DEFAULT NULL,
      `number_shares` int(10) unsigned DEFAULT NULL,
      `number_clears` int(10) unsigned DEFAULT NULL,
      `number_attempts` int(10) unsigned DEFAULT NULL,
      `number_comments` int(10) unsigned DEFAULT NULL,
      `tag` text DEFAULT NULL,
      `world_record_player_id` int(10) unsigned DEFAULT NULL,
      `world_record_player_nintendo_id` text DEFAULT NULL,
      `world_record_time` text DEFAULT NULL,
      `first_clear_player_id` int(10) unsigned DEFAULT NULL,
      `first_clear_player_nintendo_id` text DEFAULT NULL,
      `recent_players_nintendo_ids` longtext DEFAULT NULL,
      `cleared_by_nintendo_ids` longtext DEFAULT NULL,
      `starred_by_nintendo_ids` longtext DEFAULT NULL,
    */

    $dom = new Dom;
    $dom->load($html);

    // difficulty_rank
    $levelSnapshot->difficultyRank = $this->domParseHelper->firstElementText( $dom->find('.course-header') );

    // clear_rate
    $levelSnapshot->clearRate = $this->getClearRate($dom);

    // number_stars


    // number_footprints


    // number_shares


    // number_clears


    // number_attempts


    // number_comments


    // tag


    // world_record_player_id


    // world_record_player_nintendo_id


    // world_record_time


    // first_clear_player_id


    // first_clear_player_nintendo_id


    // recent_players_nintendo_ids


    // cleared_by_nintendo_ids


    // starred_by_nintendo_ids


    echo 'levelSnapshot:<pre>';
    print_r($levelSnapshot);
    die();
  }

  private function getClearRate($dom) {
    /*
      Example (6.45%)

      <div class="clear-rate">
        <div class="clear-flag"></div>
        <div class="typography typography-6"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 14.6 22"><path fill="#231815" d="M14.6 12.8v5.5L11 22H3.7L0 18.3V3.7L3.7 0h9.2v3.7H5.5L3.7 5.5v3.7H11l3.6 3.6zM11 16.5v-1.9l-1.8-1.8H3.7v3.7l1.8 1.8h3.7l1.8-1.8z"></path></svg></div>
        <div class="typography typography-second"><svg xmlns="http://www.w3.org/2000/svg" viewBox="293.3 0 213.3 1280"><path fill="#A58C26" d="M293.3 1066.7h213.3V1280H293.3v-213.3z"></path></svg></div>
        <div class="typography typography-4"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 14.5 22"><path fill="#231815" d="M14.5 18.3h-1.7V22H9.2v-3.7H0V9.2L9.2 0h3.7v14.7h1.7v3.6zm-5.3-3.6V5.5L3.7 11v3.7h5.5z"></path></svg></div>
        <div class="typography typography-5"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 14.5 22"><path fill="#231815" d="M3.7 3.7v5.5h7.5l3.4 3.7v5.5L11.1 22H0v-3.7h9.3l1.8-1.8v-1.8l-1.8-1.8H1.8L0 11V0h14.5v3.7H3.7z"></path></svg></div>
        <div class="typography typography-percent"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 41 45"><path fill="#A58C26" d="M41.2.1v7.5L3.7 45H0v-7.5L37.4.1h3.8zM3.7 18.8L0 15.1V3.8L3.7.1h11.2l3.8 3.7v11.3l-3.8 3.7H3.7zm2.2-6h6.8V6H5.9v6.8zm31.5 13.5l3.8 3.8v11.2L37.4 45H26.2l-3.8-3.7V30.1l3.8-3.8h11.2zm-2.2 5.9h-6.8V39h6.8v-6.8z"></path></svg></div>
      </div>
    */
    $clearRateTokens = [];

    $clearRateElement = $dom->find('.clear-rate');

    if ($clearRateElement->count() > 0) {
      $typographyElements = $clearRateElement->find('.typography');

      if ($typographyElements->count() > 0) {
        foreach ($typographyElements as $element) {
          $classes = $this->domParseHelper->getClasses($element);
          foreach ($classes as $class) {
            if ( $this->common->stringContainsString($class, '-') ) {
              // echo 'found one: ' . $class;
              $token = $this->common->stringAfterString($class, '-');
              // echo '<br>token: ' . $token;
              // if token is a number, push it onto our $clearRateTokens
              if (is_numeric($token)) {
                $clearRateTokens[] = $token;
              } else if ($token === 'second') {
                $clearRateTokens[] = '.';
              }
            }
          }
        }
      }
    }

    return implode($clearRateTokens);
  }

}