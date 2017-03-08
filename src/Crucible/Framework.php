<?php
namespace Crucible;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation;
use Symfony\Component\HttpKernel;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Routing;

class Framework extends HttpKernel\HttpKernel{

  private $config = array();

  public function __construct(
    EventDispatcher $dispatcher,
    HttpKernel\Controller\ControllerResolver $controllerResolver,
    HttpFoundation\RequestStack $requestStack,
    HttpKernel\Controller\ArgumentResolver $argumentResolver,
    string $config_path
  ){
    $this->config = Yaml::parse(file_get_contents($config_path));
    var_dump($this->config);
    var_dump($dispatcher);

    $routes = new Routing\RouteCollection();

    if(isset($this->config['routes'])){
      foreach ($this->config['routes'] as $name => $route) {
        $routes->add($name, new Routing\Route($route['route'], array(
          'rootUrl' => isset($route['root'])? $route['root'] : $this->config['rooturls']['default'],
          'endpoint' => $route['endpoint'],
          '_controller' => 'Crucible\Controller\RenderController::render'
        )));
      }
    }

    $matcher = new Routing\Matcher\UrlMatcher($routes, new Routing\RequestContext());
    $router = new HttpKernel\EventListener\RouterListener($matcher, $requestStack);
    var_dump($router->getSubscribedEvents());

    $dispatcher->addSubscriber($router);

    parent::__construct($dispatcher, $controllerResolver, $requestStack, $argumentResolver);
  }
}
