<?php
declare(strict_types=1);

namespace Inpsyde\MultilingualPress2to3;

use Dhii\I18n\StringTranslatingTrait;
use Psr\Container\ContainerInterface;
use Throwable;

/**
 * The handler that adds the `migrate` command.
 *
 * @package MultilingualPress2to3
 */
class MigrateCliCommandHandler implements HandlerInterface
{
    use HandlerTrait;

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
        return $this->_run();
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
    }
}
