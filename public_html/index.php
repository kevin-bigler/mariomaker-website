<?php

ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use PHPHtmlParser\Dom;

use \KevinBigler\MM;

require '../composer/vendor/autoload.php';

require __DIR__ . '/db_config.php';

$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

$config['db']['host']   = $dbConfig['db']['host'];
$config['db']['user']   = $dbConfig['db']['user'];
$config['db']['pass']   = $dbConfig['db']['pass'];
$config['db']['dbname'] = $dbConfig['db']['dbname'];

$app = new \Slim\App(["settings" => $config]);

$container = $app->getContainer();

$container['logger'] = function($c) {
  $logger = new \Monolog\Logger('my_logger');
  $fileHandler = new \Monolog\Handler\StreamHandler("../logs/app.log");
  $logger->pushHandler($fileHandler);
  return $logger;
};

$container['db'] = function($c) {
  $db = $c['settings']['db'];

  // medoo library to access mysql
  // Initialize
  $database = new medoo([
    'database_type' => 'mysql',
    'database_name' => $db['dbname'],
    'server' => $db['host'],
    'username' => $db['user'],
    'password' => $db['pass'],
    'charset' => 'utf8'
  ]);

  return $database;
};

$container['view'] = new \Slim\Views\PhpRenderer("./templates/");

$app->get('/', function(Request $request, Response $response) {

  // $this->logger->addInfo("Index page visited");

  $levels = $this->db->select('level', '*');
  // $levels = [];
  $response = $this->view->render($response, "index.phtml", ["router" => $this->router, "levels" => $levels]);
  return $response;
});

$app->get('/scrape/level/{level_code}', function (Request $request, Response $response, $args) {
  $levelCode = $args['level_code'];

  $url = 'https://supermariomakerbookmark.nintendo.net/courses/' . $levelCode;

  $pageResponse = \Httpful\Request::get($url)
    ->expectsHtml()
    ->send();

  $html = $pageResponse->body;
  $responseCode = $pageResponse->code;

  $this->db->insert('page_scrape', [
    'url' => $url,
    'html' => $html,
    'response_code' => $responseCode,
    'scrape_type' => 'level',
    'scrape_params' => $levelCode
  ]);

  $response = $this->view->render($response, "scrape-level.phtml", ["router" => $this->router, "level_code" => $levelCode]);
  return $response;

})->setName('scrape-level');

$app->get('/parse/level/{level_code}', function(Request $request, Response $response, $args) {
  $levelCode = $args['level_code'];

  $latestScrapes = $this->db->select('page_scrape', '*', [
    'AND' => [
      'scrape_type' => 'level',
      'scrape_params' => $levelCode
    ],
    'ORDER' => ['updated' => 'DESC'],
    'LIMIT' => 1
  ]);

  // die($this->db->last_query());

  $foundScrape = false;

  if ($latestScrapes && is_array($latestScrapes) && count($latestScrapes) > 0) {
    $foundScrape = true;
    $latestScrape = $latestScrapes[0];

    $html = $latestScrape['html'];

    $levelParser = new MM\LevelParser();
    $modelHelper = new MM\Helper\ModelHelper();

    if ( ! $this->db->has('level', [ 'level_code' => $levelCode ]) ) {
      $level = $levelParser->parseLevelData($html);
      $levelDb = $modelHelper->objectToDatabaseAssoc($level);
      $levelDb['level_code'] = $levelCode;
      $this->db->insert('level', $levelDb);
    }

    $levelSnapshot = $levelParser->parseLevelSnapshotData($html);
    // echo 'levelSnapshot<pre>';
    // print_r($levelSnapshot);
    // die();
    $levelSnapshotDb = $modelHelper->objectToDatabaseAssoc($levelSnapshot);
    $levelSnapshotDb['level_code'] = $levelCode;
    $levelSnapshotDb['page_scrape_id'] = $latestScrape['id'];

    $levelIdSelect = $this->db->select('level', 'id', ['level_code' => $levelCode]);
    if ($levelIdSelect && is_array($levelIdSelect))
      $levelSnapshotDb['level_id'] = $levelIdSelect[0];

    $this->db->insert('level_snapshot', $levelSnapshotDb);

    // die($this->db->last_query());
    // echo '<pre>';
    // print_r($this->db->error());
    // die();

    // echo 'levelDb<pre>';
    // print_r($levelSnapshotDb);
    // die();
  }

  $response = $this->view->render($response, "parse-level.phtml", ["router" => $this->router, "level_code" => $levelCode, "found_scrape" => $foundScrape]);
  return $response;

})->setName('parse-level');

$app->get('/scrape/player/{player_id}', function (Request $request, Response $response, $args) {
  $playerId = $args['player_id'];

  $response->getBody()->write('player id: ' . $playerId);
})->setName('scrape-player');

$app->get('/hello/{name}', function (Request $request, Response $response) {
  $name = $request->getAttribute('name');
  $response->getBody()->write("Hello, $name");


  return $response;
});

$app->get('/get-params-test', function (Request $request, Response $response) {
  $data = $request->getQueryParams();
  $levelData = [];
  $levelData['level_id'] = filter_var($data['level_id'], FILTER_SANITIZE_STRING);

  $response->getBody()->write('<pre>'.print_r($levelData, true));
});

$app->get('/get-page-test', function(Request $request, Response $response) {
  $url = 'https://supermariomakerbookmark.nintendo.net/courses/DC0C-0000-02AD-6EBC';

  $response = \Httpful\Request::get($url)
    ->expectsHtml()
    ->send();

  $html = $response->body;

  $dom = new Dom;
  $dom->load($html);
  $courseTitle = $dom->find('.course-title')[0]->text;
  echo 'course title: ' . $courseTitle;
});


$app->run();