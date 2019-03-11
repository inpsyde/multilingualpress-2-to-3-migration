<?php

namespace Inpsyde\MultilingualPress2to3;

use Exception;
use Traversable;

/**
 * Functionality for running a list of handlers.
 *
 * @package MultilingualPress2to3
 */
trait RunHandlersCapableTrait
{
    /**
     * Runs a given list of handlers.
     *
     * @param HandlerInterface[]|Traversable $handlers A list of handlers.
     *
     * @return void
     * @throws Exception If problem running.
     */
    protected function _runHandlers($handlers)
    {
        foreach ( $handlers as $handler ) {
            assert($handler instanceof HandlerInterface);
            $handler->run();
        }
    }
}