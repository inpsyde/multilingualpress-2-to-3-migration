<?php

namespace Inpsyde\MultilingualPress2to3;

use Dhii\I18n\StringTranslatingTrait;
use Inpsyde\MultilingualPress2to3\Config\ConfigAwareTrait;
use Inpsyde\MultilingualPress2to3\Event\WpHookingTrait;
use Inpsyde\MultilingualPress2to3\Handler\HandlerInterface;
use Inpsyde\MultilingualPress2to3\Handler\HandlerTrait;
use Psr\Container\ContainerInterface;

/**
 * Responsible for handling integration with MLP2, MLP3, and between them.
 *
 * @package MultilingualPress2to3
 */
class IntegrationHandler implements HandlerInterface
{
    use HandlerTrait;

    use ConfigAwareTrait;

    use WpHookingTrait;

    /**
     * Handler constructor.
     *
     * @param ContainerInterface $config The configuration of this handler.
     */
    public function __construct(ContainerInterface $config)
    {
        $this->_setConfigContainer($config);
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return $this->_run();
    }

    /**
     * {@inheritdoc}
     */
    protected function _hook()
    {
        $filter = $this->_getConfig('filter_is_check_legacy');
        assert(is_string($filter) && !empty($filter));

        $this->_addFilter($filter, '__return_false');
    }
}
