<?php

ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use PHPHtmlParser\Dom;

use \KevinBigler\MM;

require '../composer/vendor/autoload.php';

// -----------------------------------------------------------------------------
// Config
// -----------------------------------------------------------------------------

require __DIR__ . '/db_config.php';

$pkg = '\\KevinBigler\\MM\\';

$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

$config['db']['host']   = $dbConfig['db']['host'];
$config['db']['user']   = $dbConfig['db']['user'];
$config['db']['pass']   = $dbConfig['db']['pass'];
$config['db']['dbname'] = $dbConfig['db']['dbname'];

$app = new \Slim\App(["settings" => $config]);

// -----------------------------------------------------------------------------
// Container Dependency Injection (DIC)
// -----------------------------------------------------------------------------

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

// -----------------------------------------------------------------------------
// Home
// -----------------------------------------------------------------------------
$homeController = $pkg . 'HomeController';
$app->get('/', $homeController . ':index')->setName('home');

// -----------------------------------------------------------------------------
// Levels
// -----------------------------------------------------------------------------
$levelController = $pkg . 'LevelController';
$app->get('/levels', $levelController . ':index')->setName('levels');
$app->get('/levels/invalid', $levelController . ':invalid')->setName('invalid-level');
$app->get('/levels/not-found', $levelController . ':notFound')->setName('level-not-found');
$app->get('/levels/add', $levelController . ':add')->setName('add-level');
$app->get('/levels/nintendo-not-found', $levelController . ':nintendoNotFound')->setName('level-nintendo-not-found');
$app->get('/levels/add-error', $levelController . ':addError')->setName('level-add-error');
$app->get('/levels/track', $levelController . ':track')->setName('track-level');
$app->get('/levels/takeSnapshots', $levelController . ':takeSnapshots')->setName('take-snapshots-levels');

// {level_code} can have this regex: [0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}
// however, we probably should handle that in the controller (not as part of the route regex) so that we can give meaningful errors per page where the user has failed to comply with that regex
$app->group('/levels/{level_code}', function() {
  global $levelController;
  $this->get('', $levelController . ':detail')->setName('level');
  $this->get('/scrape', $levelController . ':scrape')->setName('scrape-level');
  $this->get('/parse', $levelController . ':parse')->setName('parse-level');
  $this->get('/track', $levelController . ':track')->setName('track-level');
  $this->get('/take-snapshot', $levelController . ':takeSnapshot')->setName('take-snapshot-level');
  $this->get('/scrapes', $levelController . ':scrapes')->setName('scrapes');
  $this->get('/snapshots', $levelController . ':snapshots')->setName('snapshots');
});

// -----------------------------------------------------------------------------
// Players
// -----------------------------------------------------------------------------
$playerController = $pkg . 'PlayerController';
$app->get('/players', $playerController . ':index')->setName('players');

// {nintendo_id} can have a regex
// however, we probably should handle that in the controller (not as part of the route regex) so that we can give meaningful errors per page where the user has failed to comply with that regex
$app->group('/players/{nintendo_id}', function() {
  global $playerController;
  $this->get('', $playerController . ':detail')->setName('player');
  $this->get('/scrape', $playerController . ':scrape')->setName('scrape-player');
  $this->get('/parse', $playerController . ':parse')->setName('parse-player');
  $this->get('/scrape-and-parse', $playerController . ':scrapeAndParse')->setName('scrape-and-parse-player');
});

// -----------------------------------------------------------------------------
// Run App (Slim)
// -----------------------------------------------------------------------------
$app->run();