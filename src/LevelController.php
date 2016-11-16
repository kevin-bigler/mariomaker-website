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
    $page = $level['track'] ? 'levels/detail.phtml' : 'levels/detail-basic.phtml';

    $response = $this->ci->view->render($response, 'includes/.layout.phtml', ['page' => $page, 'router' => $this->ci->router, 'level_code' => $levelCode, 'level' => $level, 'response' => $response]);
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

  public function track(Request $request, Response $response, $args) {
    $levelCode = $args['level_code'];

    if ( ! $this->levelHelper->isValid($levelCode) )
      return $response->withRedirect('/levels/invalid?level_code=' . $levelCode);

    if ( ! $this->levelHelper->isFound($levelCode) )
      return $response->withRedirect('/levels/not-found?level_code=' . $levelCode);

    $numAffectedRows = $this->levelHelper->track($levelCode);
    // die('tracking affected ' . $numAffectedRows . ' rows');

    return $response->withRedirect( $this->ci->router->pathFor('level', ['level_code' =>$levelCode]) );
  }

  public function untrack(Request $request, Response $response, $args) {
    $levelCode = $args['level_code'];

    if ( ! $this->levelHelper->isValid($levelCode) )
      return $response->withRedirect('/levels/invalid?level_code=' . $levelCode);

    if ( ! $this->levelHelper->isFound($levelCode) )
      return $response->withRedirect('/levels/not-found?level_code=' . $levelCode);

    $this->levelHelper->untrack($levelCode);

    return $response->withRedirect( $this->ci->router->pathFor('level', ['level_code' =>$levelCode]) );
  }

  public function deltaTest(Request $request, Response $response, $args) {
      // $this->ci->logger->addInfo('Delta Test endpoint hit');
      $levelSnapshotA = array(
        'difficulty_rank' => 'Expert',
        'clear_rate' => 6.50,
        'number_stars' => 0,
        'number_footprints' => 0,
        'number_shares' => 0,
        'number_clears' => 0,
        'number_attempts' => 0,
        'number_comments' => 0,
        'tag' => '---',
        // 'world_record_player_id' => 'integer',
        'world_record_player_nintendo_id' => 'beebo',
        // 'world_record_player_info' => 'string',
        'world_record_time' => '6:54.00',
        // 'first_clear_player_id' => 'integer',
        'first_clear_player_nintendo_id' => 'geokia'
        // 'first_clear_player_info' => 'string',
        // 'recent_players_nintendo_ids' => 'string',
        // 'recent_players_infos' => 'string',
        // 'cleared_by_players_nintendo_ids' => 'string',
        // 'cleared_by_players_infos' => 'string',
        // 'starred_by_players_nintendo_ids' => 'string',
        // 'starred_by_players_infos' => 'string'
      );
      // same values
      /*$levelSnapshotB = array(
        'difficulty_rank' => 'Expert',
        'clear_rate' => 6.50,
        'number_stars' => 0,
        'number_footprints' => 0,
        'number_shares' => 0,
        'number_clears' => 0,
        'number_attempts' => 0,
        'number_comments' => 0,
        'tag' => '---',
        // 'world_record_player_id' => 'integer',
        'world_record_player_nintendo_id' => 'beebo',
        // 'world_record_player_info' => 'string',
        'world_record_time' => '6:54.00',
        // 'first_clear_player_id' => 'integer',
        'first_clear_player_nintendo_id' => 'geokia'
        // 'first_clear_player_info' => 'string',
        // 'recent_players_nintendo_ids' => 'string',
        // 'recent_players_infos' => 'string',
        // 'cleared_by_players_nintendo_ids' => 'string',
        // 'cleared_by_players_infos' => 'string',
        // 'starred_by_players_nintendo_ids' => 'string',
        // 'starred_by_players_infos' => 'string'
      );*/
      // different values
      $levelSnapshotB = array(
        'difficulty_rank' => 'Super Expert',
        'clear_rate' => 4.43,
        'number_stars' => 5,
        'number_footprints' => 3,
        'number_shares' => 2,
        'number_clears' => 10,
        'number_attempts' => 87,
        'number_comments' => 1,
        'tag' => 'Puzzle',
        // 'world_record_player_id' => 'integer',
        'world_record_player_nintendo_id' => 'beebo333',
        // 'world_record_player_info' => 'string',
        'world_record_time' => '3:42.00',
        // 'first_clear_player_id' => 'integer',
        'first_clear_player_nintendo_id' => 'geokia'
        // 'first_clear_player_info' => 'string',
        // 'recent_players_nintendo_ids' => 'string',
        // 'recent_players_infos' => 'string',
        // 'cleared_by_players_nintendo_ids' => 'string',
        // 'cleared_by_players_infos' => 'string',
        // 'starred_by_players_nintendo_ids' => 'string',
        // 'starred_by_players_infos' => 'string'
      );

      $levelDiffer = new LevelDiffer();
      $delta = $levelDiffer->findDelta($levelSnapshotA, $levelSnapshotB);

      echo 'delta<pre>';
      print_r($delta);
      die();
  }

  public function takeSnapshots(Request $request, Response $response, $args) {
    $this->ci->logger->addInfo('-----------------------------------');
    $this->ci->logger->addInfo('Levels: Take Snapshots endpoint hit');
    $this->ci->logger->addInfo('$_SERVER[REMOTE_ADDR]: ' . $_SERVER['REMOTE_ADDR']);

    // find all levels with track = 1, then snapshot each of them
    $trackedLevels = $this->ci->db->select('level', '*', ['track' => 1]);

    foreach($trackedLevels as $level) {
      $this->ci->logger->addInfo('Taking snapshot of level: ' . $level['level_code']);
      $this->levelHelper->takeSnapshot($level['level_code']);
    }

    if ($_SERVER['REMOTE_ADDR'] === '54.212.222.69') {
      // if it's the server triggering this endpoint, then don't render the view
      $this->ci->logger->addInfo('Finished taking snapshots of all Levels');
    } else {
      $response = $this->ci->view->render($response, 'includes/.layout.phtml', ['page' => 'levels/take-snapshots.phtml', 'router' => $this->ci->router, 'tracked_levels' => $trackedLevels, 'response' => $response]);
      return $response;
    }
  }

  public function takeSnapshot(Request $request, Response $response, $args) {
    $levelCode = $args['level_code'];

    if ( ! $this->levelHelper->isValid($levelCode) )
      return $response->withRedirect('/levels/invalid?level_code=' . $levelCode);

    if ( ! $this->levelHelper->isFound($levelCode) )
      return $response->withRedirect('/levels/not-found?level_code=' . $levelCode);

    $level = $this->levelHelper->takeSnapshot($levelCode);

    $response = $this->ci->view->render($response, 'includes/.layout.phtml', ['page' => 'levels/take-snapshot.phtml', 'router' => $this->ci->router, 'level_code' => $levelCode, 'level' => $level, 'response' => $response]);
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