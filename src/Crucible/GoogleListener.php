<?php
namespace Crucible;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class GoogleListener implements EventSubscriberInterface{
  public static function getSubscribedEvents(){
    return array('kernel.response' => 'onResponse');
  }

  public function onResponse(FilterResponseEvent $event){
    $response = $event->getResponse();

    if($response->isRedirection() || ($response->headers->has('Content-Type') && false === strpos($response->headers->get('Content-Type'), 'html')) || 'html' !== $event->getRequest()->getRequestFormat()){
      return;
    }
    $response->setContent($response->getContent().'GA CODE');
  }
}
