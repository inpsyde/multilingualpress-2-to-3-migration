<?php

namespace Inpsyde\MultilingualPress2to3;

use Exception;
use Inpsyde\MultilingualPress2to3\Handler\HandlerInterface;
use Inpsyde\MultilingualPress2to3\Handler\HandlerTrait;
use Inpsyde\MultilingualPress2to3\Handler\RunHandlersCapableTrait;
use Psr\Container\ContainerInterface;

/**
 * A composite handler that runs sub-handlers.
 *
 * This is typically an application's main class.
 *
 * @package MultilingualPress2to3
 */
class MainHandler implements HandlerInterface
{
    use HandlerTrait;

    use ConfigAwareTrait;

    use RunHandlersCapableTrait;

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
        $result = $this->_run();
        $handlers = (array) $this->_getConfig('handlers');
        $this->_runHandlers($handlers);

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _hook()
    {
        add_action(
            'plugins_loaded',
            function () {
                $this->loadTranslations();
            }
        );
    }

    /**
     * Loads the plugin translations.
     *
     * @throws Exception If problem loading.
     */
    protected function loadTranslations()
    {
        $base_dir = $this->_getConfig('base_dir');
        $translations_dir = trim($this->_getConfig('translations_dir'), '/');
        $rel_path = basename($base_dir);

        load_plugin_textdomain('product-code-for-woocommerce', false, "$rel_path/$translations_dir");
    }
}
