<?php
use Symfony\Component\DependencyInjection;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpFoundation;
use Symfony\Component\HttpKernel;
use Symfony\Component\Routing;
use Symfony\Component\EventDispatcher;
use Crucible\Framework;

$cc = new DependencyInjection\ContainerBuilder();
$cc->register('context', Routing\RequestContext::class);

$cc->register('request_stack', HttpFoundation\RequestStack::class);
$cc->register('controller_resolver', HttpKernel\Controller\ControllerResolver::class);
$cc->register('argument_resolver',
  HttpKernel\Controller\ArgumentResolver::class);

$cc->register('listener.response',
  HttpKernel\EventListener\ResponseListener::class)
    ->setArguments(array('%charset%'));
$cc->setParameter('charset', 'UTF-8');

$cc->register('listener.exception',
  HttpKernel\EventListener\ExceptionListener::class)
    ->setArguments(array('Calendar\Controller\ErrorController::exceptionAction'));

$cc->register('dispatcher', EventDispatcher\EventDispatcher::class)
  ->addMethodCall('addSubscriber',
    array(new Reference('listener.response')))
  ->addMethodCall('addSubscriber',
    array(new Reference('listener.exception')));

$cc->register('framework', Framework::class)
  ->setArguments(array(
    '%config%'
  ));

return $cc;
