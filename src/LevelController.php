<?php
namespace KevinBigler\MM;

// use Jgut\Slim\Controller\Controller;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Interop\Container\ContainerInterface as ContainerInterface;

class LevelController { // extends Controller {
  /*
    /levels
    /levels/{level_code}
    /levels/scrape/{level_code}
    /levels/parse/{level_code}
    /levels/scrape-and-parse/{level_code}
    /levels/search/{level_code}
  */

  protected $ci;

  //Constructor
  public function __construct(ContainerInterface $ci) {
    $this->ci = $ci;
  }

  public function levelsIndex(Request $request, Response $response, $args) {

    $levels = $this->ci->db->select('level', '*');

    $response = $this->ci->view->render($response, "levels/index.phtml", ["router" => $this->ci->router, "levels" => $levels]);

    return $response;
  }
}