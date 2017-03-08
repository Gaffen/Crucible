<?php
require_once dirname(__DIR__).'/vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Crucible\StringResponseListener;
use Symfony\Component\DependencyInjection\Reference;

$cc = include __DIR__.'/../src/container.php';

$cc->register('listener.string_response', StringResponseListener::class);
$cc->getDefinition('dispatcher')
  ->addMethodCall('addSubscriber',
    array(new Reference('listener.string_response')));

$cc->setParameter('config', __DIR__.'/../config.yml');
$cc->setParameter('debug', true);

$request = Request::createFromGlobals();

$response = $cc->get('framework')->handle($request);

// $framework = new Crucible\Framework($routes);
// $framework = new Symfony\Component\HttpKernel\HttpCache\HttpCache(
//   $framework,
//   new Symfony\Component\HttpKernel\HttpCache\Store(__DIR__.'/../cache'),
//   new Symfony\Component\HttpKernel\HttpCache\Esi(),
//   array('debug' => true)
// );

$response->send();
echo $cc->getParameter('debug');
