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
    $levelSnapshot->test();

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
  }

}