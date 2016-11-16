<?php
namespace KevinBigler\MM;

use PHPHtmlParser\Dom;

class LevelDiffer {

  /*
    Delta Types: Float (Difference), Integer (Difference), String (Replacement)
  */

  public function findDelta($levelSnapshotA, $levelSnapshotB) {

    $deltaFields = array(
      'difficulty_rank' => 'string',
      'clear_rate' => 'float',
      'number_stars' => 'integer',
      'number_footprints' => 'integer',
      'number_shares' => 'integer',
      'number_clears' => 'integer',
      'number_attempts' => 'integer',
      'number_comments' => 'integer',
      'tag' => 'string',
      // 'world_record_player_id' => 'integer',
      'world_record_player_nintendo_id' => 'string',
      // 'world_record_player_info' => 'string',
      'world_record_time' => 'string',
      // 'first_clear_player_id' => 'integer',
      'first_clear_player_nintendo_id' => 'string'
      // 'first_clear_player_info' => 'string',
      // 'recent_players_nintendo_ids' => 'string',
      // 'recent_players_infos' => 'string',
      // 'cleared_by_players_nintendo_ids' => 'string',
      // 'cleared_by_players_infos' => 'string',
      // 'starred_by_players_nintendo_ids' => 'string',
      // 'starred_by_players_infos' => 'string'
    );

    $delta = array();
    foreach ($deltaFields as $deltaField => $deltaType) {

      $valueA = $levelSnapshotA[$deltaField];
      $valueB = $levelSnapshotB[$deltaField];

      $calculatedDelta = null;
      if ($deltaType === 'float')
        $calculatedDelta = $this->calculateFloatDelta($valueA, $valueB);
      else if ($deltaType === 'integer')
        $calculatedDelta = $this->calculateIntegerDelta($valueA, $valueB);
      else if ($deltaType === 'string')
        $calculatedDelta = $this->calculateStringDelta($valueA, $valueB);

      if ($calculatedDelta !== null)
        $delta[$deltaField] = $calculatedDelta;
    }

    return $delta;
  }

  private function calculateFloatDelta($valueA, $valueB) {
    if ( ! isset($valueB) || $valueB === null || ! is_numeric($valueB) )
      return null;

    if ( ! isset($valueA) || $valueA === null || ! is_numeric($valueA) )
      return $valueB;

    $difference = floatval($valueB) - floatval($valueA);

    if ($difference !== 0.0)
      return $difference;
    else
      return null;
  }

  private function calculateIntegerDelta($valueA, $valueB) {
    if ( ! isset($valueB) || $valueB === null || ! is_numeric($valueB) )
      return null;

    if ( ! isset($valueA) || $valueA === null || ! is_numeric($valueA) )
      return $valueB;

    $difference = intval($valueB) - intval($valueA);

    if ($difference !== 0)
      return $difference;
    else
      return null;
  }

  private function calculateStringDelta($valueA, $valueB) {
    if ( ! isset($valueB) || $valueB === null ||  ! is_string($valueB) )
      return null;

    if ( ! isset($valueA) || $valueA === null ||  ! is_string($valueA) )
      return $valueB;

    if ( strcasecmp($valueA, $valueB) !== 0)
      return $valueB;

    return null;
  }
}