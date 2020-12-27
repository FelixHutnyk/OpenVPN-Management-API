<?php

require '../vendor/autoload.php';

$app = new \Slim\App;

require '../src/router.php';

$app->run();

?>
