<?php
namespace Crucible\Controller;

use Symfony\Component\HttpFoundation\Response;
use CurlHelper;
use Twig_Loader_Filesystem;
use Twig_Environment;

class RenderController {

  private $twig;

  public function render($endpoint, $rootUrl, $templates, $cache, $template){
    $loader = new Twig_Loader_Filesystem($templates);
    $this->twig = new Twig_Environment($loader, array($cache));

    $template = $this->twig->resolveTemplate(array($template.'.twig', 'index.twig'));

    $response = CurlHelper::factory($rootUrl.$endpoint)->exec();
    return new Response($this->twig->render($template, $response));
  }

  public function template_search($filenames){
    foreach ($filenames as $filename) {
      if(file_exists($filenames)){

      }
    }
  }
}
