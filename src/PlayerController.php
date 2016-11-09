<?php
namespace KevinBigler\MM;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Interop\Container\ContainerInterface as ContainerInterface;

class PlayerController {

  protected $ci;

  //Constructor
  public function __construct(ContainerInterface $ci) {
    $this->ci = $ci;
  }

  public function index(Request $request, Response $response, $args) {
    // TODO
  }

  public function detail(Request $request, Response $response, $args) {
    $nintendoId = $args['nintendo_id'];
    // TODO
  }

  public function scrape(Request $request, Response $response, $args) {
    $nintendoId = $args['nintendo_id'];
    // TODO
    $response = $this->ci->view->render($response, "players/scrape.phtml", ["router" => $this->ci->router, "nintendo_id" => $nintendoId]);
    return $response;
  }

  public function parse(Request $request, Response $response, $args) {
    $nintendoId = $args['nintendo_id'];
    // TODO
    $response = $this->ci->view->render($response, "players/parse.phtml", ["router" => $this->ci->router, "nintendo_id" => $nintendoId, "found_scrape" => $foundScrape]);
    return $response;
  }

  public function scrapeAndParse(Request $request, Response $response, $args) {
    $nintendoId = $args['nintendo_id'];
    // TODO
  }
}