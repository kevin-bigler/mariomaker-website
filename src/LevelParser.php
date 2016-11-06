<?php
namespace KevinBigler\MM;

use PHPHtmlParser\Dom;

class LevelParser {

  private $common;

  function __construct() {
    $this->common = new Helper\Common();
  }

  public function basicTest($str) {
    return 'You gave me ' . $str;
  }

  private function firstElementText($elements) {
    if ($elements->count() > 0)
      return $elements[0]->text;
    else
      return null;
  }

  public function parseLevelData($html) {
    $level = new Model\Level();
    // $level->test();

    $dom = new Dom;
    $dom->load($html);

    // player_nintendo_id
    $level->playerNintendoId = null;
    $creatorLink = $dom->find('.creator a#mii');
    if ($creatorLink->count() > 0) {
      $creatorUrl = $creatorLink[0]->getAttribute('href');
      // example: href="/profile/thek3vinator?type=posted"
      $creatorId = $this->common->stringBetweenStrings($creatorUrl, '/profile/', '?');
      $level->playerNintendoId = $creatorId;
    }

    // title
    $level->title = $this->firstElementText( $dom->find('.course-title') );

    // image_url

    // image_full_url

    // upload_date
    $this->uploadDate = $this->firstElementText( $dom->find('.upload') );
    /*
      `player_nintendo_id` text DEFAULT NULL,
      `title` text DEFAULT NULL,
      `image_url` text DEFAULT NULL,
      `image_full_url` text DEFAULT NULL,
      `upload_date` date DEFAULT NULL,
      `gameskin` text DEFAULT NULL, -- mode (SMB1, SMB2, SMW, NSMB)
    */

    echo 'level<pre>';
    print_r($level);
    die();
  }

  public function parseLevelSnapshotData($html) {

    /*
      `level_code` varchar(19) DEFAULT NULL,
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