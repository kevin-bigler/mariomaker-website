<?php
namespace KevinBigler\MM;

// use Jgut\Slim\Controller\Controller;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Interop\Container\ContainerInterface as ContainerInterface;

class LevelController {

  protected $ci;
  protected $levelHelper;

  //Constructor
  public function __construct(ContainerInterface $ci) {
    $this->ci = $ci;
    $this->levelHelper = new Helper\LevelHelper($ci);
  }

  public function index(Request $request, Response $response, $args) {

    $levels = $this->ci->db->select('level', '*');

    $response = $this->ci->view->render($response, 'includes/.layout.phtml', ['page' => 'levels/index.phtml', 'page' => 'levels/index.phtml', 'router' => $this->ci->router, 'levels' => $levels, 'response' => $response]);
    return $response;
  }

  public function detail(Request $request, Response $response, $args) {
    $levelCode = $args['level_code'];

    if ( ! $this->levelHelper->isValid($levelCode) )
      return $response->withRedirect('/levels/invalid?level_code=' . $levelCode);

    if ( ! $this->levelHelper->isFound($levelCode) )
      return $response->withRedirect('/levels/not-found?level_code=' . $levelCode);

    $level = $this->levelHelper->select($levelCode);
    $response = $this->ci->view->render($response, 'includes/.layout.phtml', ['page' => 'levels/detail.phtml', 'router' => $this->ci->router, 'level_code' => $levelCode, 'level' => $level, 'response' => $response]);
    return $response;
  }

  public function invalid(Request $request, Response $response, $args) {
    if ( ! array_key_exists('level_code', $request->getQueryParams()) )
      return $response->withStatus(404);

    $levelCode = $request->getQueryParams()['level_code'];

    if ( $this->levelHelper->isValid($levelCode) ) {
      if ( $this->levelHelper->isFound($levelCode) )
        return $response->withRedirect( $this->ci->router->pathFor('level', ['level_code' =>$levelCode]) );
      else
        return $response->withRedirect('/levels/not-found?level_code=' . $levelCode);
    }

    $response = $this->ci->view->render($response, 'includes/.layout.phtml', ['page' => 'levels/invalid.phtml', 'router' => $this->ci->router, 'level_code' => $levelCode, 'response' => $response]);
    return $response;
  }

  public function notFound(Request $request, Response $response, $args) {
    if ( ! array_key_exists('level_code', $request->getQueryParams()) )
      return $response->withStatus(404);

    $levelCode = $request->getQueryParams()['level_code'];

    if ( ! $this->levelHelper->isValid($levelCode) )
      return $response->withRedirect('/levels/invalid?level_code=' . $levelCode);

    if ( $this->levelHelper->isFound($levelCode) )
      return $response->withRedirect( $this->ci->router->pathFor('level', ['level_code' =>$levelCode]) );

    $response = $this->ci->view->render($response, 'includes/.layout.phtml', ['page' => 'levels/not-found.phtml', 'router' => $this->ci->router, 'level_code' => $levelCode, 'response' => $response]);
    return $response;
  }

  public function add(Request $request, Response $response, $args) {
    if ( ! array_key_exists('level_code', $request->getQueryParams()) )
      return $response->withStatus(404);

    $levelCode = $request->getQueryParams()['level_code'];

    if ( ! $this->levelHelper->isValid($levelCode) )
      return $response->withRedirect('/levels/invalid?level_code=' . $levelCode);

    if ( $this->levelHelper->isFound($levelCode) )
      return $response->withRedirect( $this->ci->router->pathFor('level', ['level_code' =>$levelCode]) );

    $level = $this->levelHelper->add($levelCode);

    if ($level === false) {
      return $response->withRedirect('/levels/nintendo-not-found?level_code=' . $levelCode);
    } else if ($level === null) {
      return $response->withRedirect('/levels/add-error?level_code=' . $levelCode);
    } else {
      return $response->withRedirect( $this->ci->router->pathFor('level', ['level_code' =>$levelCode]) );
    }
  }

  public function nintendoNotFound(Request $request, Response $response, $args) {
    if ( ! array_key_exists('level_code', $request->getQueryParams()) )
      return $response->withStatus(404);

    $levelCode = $request->getQueryParams()['level_code'];

    if ( ! $this->levelHelper->isValid($levelCode) )
      return $response->withRedirect('/levels/invalid?level_code=' . $levelCode);

    if ( $this->levelHelper->isFound($levelCode) )
      return $response->withRedirect( $this->ci->router->pathFor('level', ['level_code' =>$levelCode]) );

    $response = $this->ci->view->render($response, 'includes/.layout.phtml', ['page' => 'levels/nintendo-not-found.phtml', 'router' => $this->ci->router, 'level_code' => $levelCode, 'response' => $response]);
    return $response;
  }

  public function addError(Request $request, Response $response, $args) {
    if ( ! array_key_exists('level_code', $request->getQueryParams()) )
      return $response->withStatus(404);

    $levelCode = $request->getQueryParams()['level_code'];

    if ( ! $this->levelHelper->isValid($levelCode) )
      return $response->withRedirect('/levels/invalid?level_code=' . $levelCode);

    if ( $this->levelHelper->isFound($levelCode) )
      return $response->withRedirect( $this->ci->router->pathFor('level', ['level_code' =>$levelCode]) );

    $response = $this->ci->view->render($response, 'includes/.layout.phtml', ['page' => 'levels/add-error.phtml', 'router' => $this->ci->router, 'level_code' => $levelCode, 'response' => $response]);
    return $response;
  }

  public function takeSnapshots(Request $request, Response $response, $args) {
    $levelCode = $args['level_code'];
    // TODO
    // find all levels with track = 1, then snapshot each of them
  }

  public function takeSnapshot(Request $request, Response $response, $args) {
    $levelCode = $args['level_code'];

    $level = $this->levelHelper->takeSnapshot($levelCode);

    $response = $this->ci->view->render($response, 'includes/.layout.phtml', ['page' => 'levels/take-snapshot.phtml', 'router' => $this->ci->router, 'level_code' => $levelCode, 'found_scrape' => $foundScrape, 'level' => $level, 'response' => $response]);
    return $response;
  }

  public function scrape(Request $request, Response $response, $args) {
    $levelCode = $args['level_code'];

    $scrapeId = $this->levelHelper->scrape($levelCode);

    $response = $this->ci->view->render($response, 'includes/.layout.phtml', ['page' => 'levels/scrape.phtml', 'router' => $this->ci->router, 'level_code' => $levelCode, 'response' => $response]);
    return $response;
  }

  public function parse(Request $request, Response $response, $args) {
    $levelCode = $args['level_code'];

    $foundScrape = $this->levelHelper->parse($levelCode);

    $response = $this->ci->view->render($response, 'includes/.layout.phtml', ['page' => 'levels/parse.phtml', 'router' => $this->ci->router, 'level_code' => $levelCode, 'found_scrape' => $foundScrape, 'response' => $response]);
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

    $response = $this->ci->view->render($response, 'includes/.layout.phtml', ['page' => 'levels/scrapes.phtml', 'router' => $this->ci->router, 'level_code' => $levelCode, 'scrapes' => $scrapes, 'response' => $response]);
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

    $response = $this->ci->view->render($response, 'includes/.layout.phtml', ['page' => 'levels/snapshots.phtml', 'router' => $this->ci->router, 'level_code' => $levelCode, 'snapshots' => $snapshots, 'response' => $response]);
    return $response;
  }
}