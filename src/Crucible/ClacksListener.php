<?php
namespace Crucible;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class ClacksListener implements EventSubscriberInterface{
  public static function getSubscribedEvents(){
    return array('kernel.response' => 'onResponse');
  }

  public function onResponse(FilterResponseEvent $event){
    $response = $event->getResponse();
    $response->headers->set('X-Clacks-Overhead', 'GNU Terry Pratchett');
  }
}
