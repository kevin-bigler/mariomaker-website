<?php
namespace KevinBigler\MM;

// use Jgut\Slim\Controller\Controller;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Interop\Container\ContainerInterface as ContainerInterface;

class LevelController {

  protected $ci;

  //Constructor
  public function __construct(ContainerInterface $ci) {
    $this->ci = $ci;
  }

  public function index(Request $request, Response $response, $args) {

    $levels = $this->ci->db->select('level', '*');

    $response = $this->ci->view->render($response, "levels/index.phtml", ["router" => $this->ci->router, "levels" => $levels]);

    return $response;
  }

  public function detail(Request $request, Response $response, $args) {
    $levelCode = $args['level_code'];
    // TODO
  }

  public function scrape(Request $request, Response $response, $args) {
    $levelCode = $args['level_code'];

    $url = 'https://supermariomakerbookmark.nintendo.net/courses/' . $levelCode;

    $pageResponse = \Httpful\Request::get($url)
      ->expectsHtml()
      ->send();

    $html = $pageResponse->body;
    $responseCode = $pageResponse->code;

    $this->ci->db->insert('page_scrape', [
      'url' => $url,
      'html' => $html,
      'response_code' => $responseCode,
      'scrape_type' => 'level',
      'scrape_params' => $levelCode
    ]);

    $response = $this->ci->view->render($response, "levels/scrape.phtml", ["router" => $this->ci->router, "level_code" => $levelCode]);
    return $response;
  }

  public function parse(Request $request, Response $response, $args) {
    $levelCode = $args['level_code'];

    $latestScrapes = $this->ci->db->select('page_scrape', '*', [
      'AND' => [
        'scrape_type' => 'level',
        'scrape_params' => $levelCode
      ],
      'ORDER' => ['updated' => 'DESC'],
      'LIMIT' => 1
    ]);

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
      // echo 'levelSnapshot<pre>';
      // print_r($levelSnapshot);
      // die();
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

    $response = $this->ci->view->render($response, "levels/parse.phtml", ["router" => $this->ci->router, "level_code" => $levelCode, "found_scrape" => $foundScrape]);
    return $response;
  }

  public function scrapeAndParse(Request $request, Response $response, $args) {
    $levelCode = $args['level_code'];
    // TODO
  }
}