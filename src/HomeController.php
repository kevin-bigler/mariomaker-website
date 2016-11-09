<?php
namespace KevinBigler\MM;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Interop\Container\ContainerInterface as ContainerInterface;

class HomeController {

  protected $ci;

  //Constructor
  public function __construct(ContainerInterface $ci) {
    $this->ci = $ci;
  }

  public function index(Request $request, Response $response, $args) {

    // $this->logger->addInfo("Index page visited");

    $response = $this->view->render($response, "index.phtml", ["router" => $this->router]);
    return $response;

  }
}