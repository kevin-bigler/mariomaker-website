<?php
namespace KevinBigler\MM\Helper;

use \Interop\Container\ContainerInterface as ContainerInterface;

class LevelHelper {

  protected $ci;

  //Constructor
  public function __construct(ContainerInterface $ci) {
    $this->ci = $ci;
  }

  public function scrape($levelCode) {
    $url = 'https://supermariomakerbookmark.nintendo.net/courses/' . $levelCode;

    $pageResponse = \Httpful\Request::get($url)
      ->expectsHtml()
      ->send();

    $html = $pageResponse->body;
    $responseCode = $pageResponse->code;

    $scrapeId = $this->ci->db->insert('page_scrape', [
      'url' => $url,
      'html' => $html,
      'response_code' => $responseCode,
      'scrape_type' => 'level',
      'scrape_params' => $levelCode
    ]);

    return $scrapeId;
  }

  public function parse($levelCode, $scrapeId = 0) {
    $latestScrapes = [];

    if ( $scrapeId > 0 ) {
      $latestScrapes = $this->ci->db->select('page_scrape', '*', [
        'AND' => [
          'id' => $scrapeId,
          'scrape_type' => 'level',
          'scrape_params' => $levelCode
        ]
      ]);
    } else {
      $latestScrapes = $this->ci->db->select('page_scrape', '*', [
        'AND' => [
          'scrape_type' => 'level',
          'scrape_params' => $levelCode
        ],
        'ORDER' => ['updated' => 'DESC'],
        'LIMIT' => 1
      ]);
    }

    // die($this->ci->db->last_query());

    $foundScrape = false;

    if ($latestScrapes && is_array($latestScrapes) && count($latestScrapes) > 0) {
      $foundScrape = true;
      $latestScrape = $latestScrapes[0];

      $html = $latestScrape['html'];

      $levelParser = new MM\LevelParser();
      $modelHelper = new MM\Helper\ModelHelper();

      if ( ! $this->ci->db->has('level', [ 'level_code' => $levelCode ]) ) {
        $level = $levelParser->parseLevelData($html);
        $levelDb = $modelHelper->objectToDatabaseAssoc($level);
        $levelDb['level_code'] = $levelCode;
        $this->ci->db->insert('level', $levelDb);
      }

      $levelSnapshot = $levelParser->parseLevelSnapshotData($html);

      $levelSnapshotDb = $modelHelper->objectToDatabaseAssoc($levelSnapshot);
      $levelSnapshotDb['level_code'] = $levelCode;
      $levelSnapshotDb['page_scrape_id'] = $latestScrape['id'];

      $levelIdSelect = $this->ci->db->select('level', 'id', ['level_code' => $levelCode]);
      if ($levelIdSelect && is_array($levelIdSelect))
        $levelSnapshotDb['level_id'] = $levelIdSelect[0];

      $this->ci->db->insert('level_snapshot', $levelSnapshotDb);

      // die($this->ci->db->last_query());
      // echo '<pre>';
      // print_r($this->ci->db->error());
      // die();

      // echo 'levelDb<pre>';
      // print_r($levelSnapshotDb);
      // die();
    }

    return $foundScrape;
  }

  public function select($levelCode) {
    // level and level_snapshot (latest) joined
    // TODO specific columns from both tables
  }

  public function isValid($levelCode) {
    $regex = '/^[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}$/';
    $result = preg_match($regex, $levelCode);

    if ($result === 1) {
      echo $levelCode . ' is valid!';
    } else if ($result === 0) {
      echo $levelCode . ' is invalid :(';
    } else if ($result === false) {
      echo 'an error occurred x_x';
    } else {
      echo 'I have no clue what happened... o_o';
    }
    return $result === 1;
  }
}