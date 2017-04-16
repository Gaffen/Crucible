<?php
namespace Crucible;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation;
use Symfony\Component\HttpKernel;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Routing;

class Framework extends HttpKernel\HttpKernel{

  private $config = array();

  private $templates;
  private $cache = 'cache';
  private $root;
  private $debug;

  protected $dispatcher;
  private $controllerResolver;
  protected $requestStack;
  private $argumentResolver;

  public function __construct(
    string $config_path
  ){

    $this->dispatcher = new EventDispatcher();
    $this->controllerResolver = new HttpKernel\Controller\ControllerResolver();
    $this->requestStack = new HttpFoundation\RequestStack();
    $this->argumentResolver = new HttpKernel\Controller\ArgumentResolver();

    $this->dispatcher->addSubscriber(new HttpKernel\EventListener\ResponseListener('UTF-8'));
    $this->dispatcher->addSubscriber(new ClacksListener());
    $this->dispatcher->addSubscriber(new StringResponseListener());

    $this->root = dirname($config_path);
    $this->config = Yaml::parse(file_get_contents($config_path));
    $this->templates = $this->root."/".$this->config['webroot']."/templates";
    $this->debug = isset($this->config['debug']) && $this->config['debug'] === true;

    $routes = new Routing\RouteCollection();

    if(isset($this->config['routes'])){
      foreach ($this->config['routes'] as $name => $route) {
        $routes->add($name, new Routing\Route($route['route'], array(
          'rootUrl'     => isset($route['root'])? $route['root'] : $this->config['rooturls']['default'],
          'endpoint'    => $route['endpoint'],
          'templates'   => $this->templates,
          'cache'       => $this->cache,
          'template'    => (isset($route['template']))? $route['template'] : $name,
          'debug'       => $this->debug,
          '_controller' => 'Crucible\Controller\RenderController::render'
        )));
      }
    }

    $matcher = new Routing\Matcher\UrlMatcher($routes, new Routing\RequestContext());
    $router = new HttpKernel\EventListener\RouterListener($matcher, $this->requestStack);

    $this->dispatcher->addSubscriber($router);

    parent::__construct($this->dispatcher, $this->controllerResolver, $this->requestStack, $this->argumentResolver);
  }

  public function run(HttpFoundation\Request $request = null){
    if($request === null){
      $request = HttpFoundation\Request::createFromGlobals();
    }

    $response = $this->handle($request);
    $response->send();
  }
}
