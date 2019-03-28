<?php

namespace Inpsyde\MultilingualPress2to3\Handler;

use Exception;
use Inpsyde\MultilingualPress2to3\Handler\HandlerInterface;
use Traversable;

/**
 * Functionality for running a list of handlers.
 *
 * @package MultilingualPress2to3
 */
trait RunHandlerListCapableTrait
{
    /**
     * Runs a given list of handlers.
     *
     * @param HandlerInterface[]|Traversable $handlers A list of handlers.
     *
     * @return void
     * @throws Exception If problem running.
     */
    protected function _runHandlerList($handlers)
    {
        foreach ($handlers as $handler) {
            assert($handler instanceof HandlerInterface);
            $handler->run();
            $this->_afterRun($handler);
        }
    }

    /**
     * Invokes after a handler is run.
     *
     * @param HandlerInterface $handler THe handler that was run.
     */
    protected function _afterRun(HandlerInterface $handler)
    {
        // Override this method to do stuff after every handler is run
    }
}
