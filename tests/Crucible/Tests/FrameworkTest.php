<?php
namespace Crucible\Tests;

use Crucible\Framework;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\Routing;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpFoundation\Response;


class FrameworkTest extends \PHPUnit_Framework_TestCase {
  public function testNotFoundHandling() {
    $framework = $this->getFrameworkForException(
      new ResourceNotFoundException);

    $response = $framework->handle(new Request());

    $this->assertEquals(404, $response->getStatusCode());
  }

  /**
    * @expectedException        Exception
    */
  public function testErrorHandling(){
    $framework = $this->getFrameworkForException(
      new \RuntimeException);

    $response = $framework->handle(new Request());

    $this->assertEquals(500, $response->getStatusCode());
  }

  public function testControllerResponse() {
    $matcher =
      $this->createMock(Routing\Matcher\UrlMatcherInterface::class);

    $matcher
      ->expects($this->once())
      ->method('match')
      ->will($this->returnValue(array(
          '_route' => 'foo',
          'name' => 'Fabien',
          '_controller' => function ($name) {
              return new Response('Hello '.$name);
          }
      )));

    $matcher
      ->expects($this->once())
      ->method('getContext')
      ->will($this->returnValue($this->createMock(Routing\RequestContext::class)));
      
    $controllerResolver = new ControllerResolver();
    $argumentResolver = new ArgumentResolver();

    $framework = new Framework($matcher, $controllerResolver, $argumentResolver);

    $response = $framework->handle(new Request());

    $this->assertEquals(200, $response->getStatusCode());
    $this->assertContains('Hello Fabien', $response->getContent());
  }

  public function getFrameworkForException($exception) {
    $matcher =
      $this->createMock(Routing\Matcher\UrlMatcherInterface::class);
    $matcher
      ->expects($this->once())
      ->method('match')
      ->will($this->throwException($exception));

    $matcher
      ->expects($this->once())
      ->method('getContext')
      ->will($this->returnValue(
        $this->createMock(Routing\RequestContext::class)));

    $controllerResolver =
      $this->createMock(ControllerResolverInterface::class);
    $argumentResolver =
      $this->createMock(ArgumentResolverInterface::class);

    return new Framework($matcher, $controllerResolver, $argumentResolver);
  }
}
