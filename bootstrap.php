<?php

use Dhii\Di\ContainerAwareCachingContainer;
use Dhii\Cache\MemoryMemoizer;
use Inpsyde\MultilingualPress2to3\Handler\HandlerInterface;

/**
 * The function that bootstraps the application.
 *
 * @return HandlerInterface
 */
return function ($appRootPath, $appRootUrl) {
    $appRootDir = dirname($appRootPath);

    if (file_exists($autoload = "$appRootDir/vendor/autoload.php")) {
        require_once($autoload);
    }

    $servicesFactory = require_once("$appRootDir/includes/services.php");
    $c = new ContainerAwareCachingContainer(
        $servicesFactory($appRootPath, $appRootUrl),
        new MemoryMemoizer()
    );

    $handler = $c->get('handler_main');
    assert($handler instanceof HandlerInterface);

    return $handler;
};
