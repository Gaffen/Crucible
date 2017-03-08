<?php
namespace Crucible\Controller;

use Symfony\Component\HttpFoundation\Response;
use CurlHelper;

class RenderController {
  public function render($endpoint, $rootUrl){
    $response = CurlHelper::factory($rootUrl.$endpoint)->exec();
    return new Response(json_encode($response));
  }
}
