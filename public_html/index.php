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
$app->get('/hello/{name}', function (Request $request, Response $response) {
  $name = $request->getAttribute('name');
  $response->getBody()->write("Hello, $name");

  return $response;
});

$app->get('/db-test', function(Request $request, Response $response) {
  // medoo library to access mysql
  // Initialize
  $database = new medoo([
    'database_type' => 'mysql',
    'database_name' => $this->get('settings')['db']['dbname'],
    'server' => $this->get('settings')['db']['host'],
    'username' => $this->get('settings')['db']['user'],
    'password' => $this->get('settings')['db']['pass'],
    'charset' => 'utf8'
  ]);

  $players = $database->select('player', '*');

  echo 'players<pre>';
  print_r($players);
  die();

  // Enjoy
  // $database->insert('account', [
  //   'user_name' => 'foo',
  //   'email' => 'foo@bar.com',
  //   'age' => 25,
  //   'lang' => ['en', 'fr', 'jp', 'cn']
  // ]);
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

  $database = new medoo([
    'database_type' => 'mysql',
    'database_name' => $this->get('settings')['db']['dbname'],
    'server' => $this->get('settings')['db']['host'],
    'username' => $this->get('settings')['db']['user'],
    'password' => $this->get('settings')['db']['pass'],
    'charset' => 'utf8'
  ]);

  $database->insert('page_scrape', [
    'url' => $url,
    'html' => $html
  ]);
});

$app->get('/parse-stored-html', function(Request $request, Response $response) {
  $database = new medoo([
    'database_type' => 'mysql',
    'database_name' => $this->get('settings')['db']['dbname'],
    'server' => $this->get('settings')['db']['host'],
    'username' => $this->get('settings')['db']['user'],
    'password' => $this->get('settings')['db']['pass'],
    'charset' => 'utf8'
  ]);

  $pageScrapes = $database->select('page_scrape', '*');
  // echo '<pre>';
  // print_r($pageScrapes);
  // die();
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