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

    $response = $this->ci->view->render($response, 'levels/index.phtml', ['router' => $this->ci->router, 'levels' => $levels, 'response' => $response]);
    return $response;
  }

  public function takeSnapshots(Request $request, Response $response, $args) {
    $levelCode = $args['level_code'];
    // TODO
    // find all levels with track = 1, then snapshot each of them
  }

  public function detail(Request $request, Response $response, $args) {
    $levelCode = $args['level_code'];

    $levelHelper = new MM\Helper\LevelHelper();
    $level = $levelHelper->select($levelCode);

    $response = $this->ci->view->render($response, 'levels/scrape.phtml', ['router' => $this->ci->router, 'level_code' => $levelCode, 'level' => $level]);
    return $response;
  }

  public function scrape(Request $request, Response $response, $args) {
    $levelCode = $args['level_code'];

    $levelHelper = new MM\Helper\LevelHelper();
    $scrapeId = $levelHelper->scrape($levelCode);

    $response = $this->ci->view->render($response, 'levels/scrape.phtml', ['router' => $this->ci->router, 'level_code' => $levelCode]);
    return $response;
  }

  public function parse(Request $request, Response $response, $args) {
    $levelCode = $args['level_code'];

    $levelHelper = new MM\Helper\LevelHelper();
    $foundScrape = $levelHelper->parse($levelCode);

    $response = $this->ci->view->render($response, 'levels/parse.phtml', ['router' => $this->ci->router, 'level_code' => $levelCode, 'found_scrape' => $foundScrape]);
    return $response;
  }

  public function takeSnapshot(Request $request, Response $response, $args) {
    $levelCode = $args['level_code'];

    $levelHelper = new MM\Helper\LevelHelper();
    $scrapeId = $levelHelper->scrape($levelCode);
    $foundScrape = $levelHelper->parse($levelCode);

    $level = $levelHelper->select($level);

    $response = $this->ci->view->render($response, 'levels/take-snapshot.phtml', ['router' => $this->ci->router, 'level_code' => $levelCode, 'found_scrape' => $foundScrape, 'level' => $level]);
    return $response;
  }

  public function scrapes(Request $request, Response $response, $args) {
    $levelCode = $args['level_code'];

    $scrapes = $this->ci->db->select('page_scrape', '*', [
      'AND' => [
        'scrape_type' => 'level',
        'scrape_params' => $levelCode
      ],
      'ORDER' => ['updated' => 'DESC'],
      'LIMIT' => 1
    ]); // TODO join level_snapshot

    $response = $this->ci->view->render($response, 'levels/parse.phtml', ['router' => $this->ci->router, 'level_code' => $levelCode, 'scrapes' => $scrapes]);
    return $response;
  }

  public function snapshots(Request $request, Response $response, $args) {
    $levelCode = $args['level_code'];

    $snapshots = $this->ci->db->select('level_snapshot', '*', [
      'AND' => [
        'scrape_type' => 'level',
        'level_code' => $levelCode
      ],
      'ORDER' => ['updated' => 'DESC'],
      'LIMIT' => 1
    ]); // TODO join page_scrape

    $response = $this->ci->view->render($response, 'levels/parse.phtml', ['router' => $this->ci->router, 'level_code' => $levelCode, 'snapshots' => $snapshots]);
    return $response;
  }
}