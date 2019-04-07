<?php
declare(strict_types=1);

namespace Inpsyde\MultilingualPress2to3;

use Dhii\I18n\StringTranslatingTrait;
use Inpsyde\MultilingualPress2to3\Cli\AddCliCommandCapableWpTrait;
use Inpsyde\MultilingualPress2to3\Config\ConfigAwareTrait;
use Inpsyde\MultilingualPress2to3\Event\WpHookingTrait;
use Inpsyde\MultilingualPress2to3\Handler\HandlerInterface;
use Inpsyde\MultilingualPress2to3\Handler\ControllerTrait;
use Psr\Container\ContainerInterface;
use Throwable;

/**
 * The handler that adds the `migrate` command.
 *
 * @package MultilingualPress2to3
 */
class MigrateCliCommandHandler implements HandlerInterface
{
    use ControllerTrait;

    use ConfigAwareTrait;

    use WpHookingTrait;

    use AddCliCommandCapableWpTrait;

    use StringTranslatingTrait;

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
        return $this->_hook();
    }

    /**
     * {@inheritdoc}
     *
     * @throws Throwable If problem hooking.
     */
    protected function _hook()
    {
        if (!class_exists('WP_CLI')) {
            return;
        }

        $key = $this->_getConfig('wpcli_command_key_mlp2to3_migrate');

        // This allows the command to be lazy-loaded
        $this->_addCliCommand($key, function ($positionalArgs, $associativeArgs) {
            $handler = $this->_getConfig('wpcli_command_migrate');
            assert(is_callable($handler));

            $handler($positionalArgs, $associativeArgs);
        });
    }
}
