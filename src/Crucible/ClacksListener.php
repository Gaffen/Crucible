<?php
namespace Crucible;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ClacksListener implements EventSubscriberInterface{
  public static function getSubscribedEvents(){
    return array('response' => 'onResponse');
  }

  public function onResponse(ResponseEvent $event){
    $response = $event->getResponse();
    $response->headers->set('X-Clacks-Overhead', 'GNU Terry Pratchett');
  }
}
