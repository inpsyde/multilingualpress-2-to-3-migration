<?php
/**
 * Handler trait.
 *
 * @package MultilingualPress2to3
 */

namespace Inpsyde\MultilingualPress2to3;

use Dhii\I18n\FormatTranslatorInterface;
use Exception;

/**
 * Base functionality for handlers.
 *
 * @package MultilingualPress2to3
 */
trait HandlerTrait
{

    /**
     * Runs the handler.
     *
     * @throws Exception If problem running.
     *
     * @return mixed The result of running the handler.
     */
    protected function _run()
    {
        $this->_hook();

        return null;
    }

    /**
     * Procedural way to run the handler.
     *
     * @throws Exception If problem during invoking.
     *
     * @return mixed The result of handling.
     */
    public function __invoke()
    {
        return $this->_run();
    }

    /**
     * Retrieves a URL to the JS directory of the handler.
     *
     * @param string $path The path relative to the JS directory.
     *
     * @throws Exception If problem retrieving.
     *
     * @return string The absolute URL to the JS directory.
     */
    protected function getJsUrl($path = '')
    {
        $baseUrl = $this->_getConfig('base_url');

        return "$baseUrl/assets/js/$path";
    }

    /**
     * Retrieves a URL to the CSS directory of the handler.
     *
     * @param string $path The path relative to the CSS directory.
     *
     * @throws Exception If problem retrieving.
     *
     * @return string The absolute URL to the CSS directory.
     */
    protected function getCssUrl($path = '')
    {
        $baseUrl = $this->_getConfig('base_url');

        return "$baseUrl/assets/css/$path";
    }

    /**
     * Adds handler hooks.
     *
     * @return void
     *
     * @throws Exception If problem hooking.
     */
    abstract protected function _hook();

    /**
     * Retrieves the translator from configuration.
     */
    protected function _getTranslator()
    {
        $translator = $this->_getConfig('translator');
        assert($translator instanceof FormatTranslatorInterface);

        return $translator;
    }

    /**
     * Retrieves a configuration value that corresponds to the specified key.
     *
     * @param string $key The configuration key
     *
     * @return mixed The configuration value.
     */
    abstract protected function _getConfig($key);
}
