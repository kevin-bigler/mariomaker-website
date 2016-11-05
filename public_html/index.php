<?php

ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use PHPHtmlParser\Dom;

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
  $levels = $this->db->select('level', '*');
  // $levels = [];
  $response = $this->view->render($response, "index.phtml", ["router" => $this->router, "levels" => $levels]);
  return $response;
});

$app->get('/scrape/level/{level_id}', function (Request $request, Response $response, $args) {
  $levelId = $args['level_id'];

  $response->getBody()->write('level id: ' . $levelId);
})->setName('scrape-level');

$app->get('/scrape/player/{player_id}', function (Request $request, Response $response, $args) {
  $playerId = $args['player_id'];

  $response->getBody()->write('player id: ' . $playerId);
})->setName('scrape-player');

$app->get('/template/test/{secret}', function(Request $request, Response $response, $args) {
  $response = $this->view->render($response, "template-test.phtml", ["secret" => $args['secret']]);
  return $response;
});

$app->get('/hello/{name}', function (Request $request, Response $response) {
  $name = $request->getAttribute('name');
  $response->getBody()->write("Hello, $name");

  $this->logger->addInfo("Something interesting happened");

  return $response;
});

$app->get('/get-params-test', function (Request $request, Response $response) {
  $data = $request->getQueryParams();
  $levelData = [];
  $levelData['level_id'] = filter_var($data['level_id'], FILTER_SANITIZE_STRING);

  $response->getBody()->write('<pre>'.print_r($levelData, true));
});

$app->get('/db-test', function(Request $request, Response $response) {
  $players = $this->db->select('player', '*');

  $response->getBody()->write('<pre>'.print_r($players, true));

  return $response;
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

$app->get('/scrape-page', function(Request $request, Response $response) {
  $url = 'https://supermariomakerbookmark.nintendo.net/courses/DC0C-0000-02AD-6EBC';

  $response = \Httpful\Request::get($url)
    ->expectsHtml()
    ->send();

  $html = $response->body;

  $this->db->insert('page_scrape', [
    'url' => $url,
    'html' => $html
  ]);
});

$app->get('/parse-stored-html', function(Request $request, Response $response) {

  $pageScrapes = $this->db->select('page_scrape', '*');

  if ( $pageScrapes && count($pageScrapes) > 0)
    $pageScrape = $pageScrapes[0];

  $url = $pageScrape['url'];
  $html = $pageScrape['html'];

  $dom = new Dom;
  $dom->load($html);
  $courseTitle = $dom->find('.course-title')[0]->text;
  echo 'course title: ' . $courseTitle . '<br><br>';
  echo 'from html stored by initially scraping url: ' . $url;
});

$app->run();