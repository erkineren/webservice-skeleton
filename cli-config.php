<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;

// replace with file to your own project bootstrap
require_once 'app.php';

$em = $app->getContainer()->entityManager;

return ConsoleRunner::createHelperSet($em);