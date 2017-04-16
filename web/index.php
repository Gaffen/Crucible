<?php
require_once dirname(__DIR__).'/vendor/autoload.php';

use Crucible\Framework;
$framework = new Crucible\Framework(dirname(__DIR__).'/config.yml');
$framework->run();
