<?php
/**
 * Declares project configuration.
 *
 * @package MultilingualPress2to3
 */

use Dhii\Cache\MemoryMemoizer;
use Dhii\Cache\SimpleCacheInterface;
use Dhii\Di\ContainerAwareCachingContainer;
use Dhii\I18n\FormatTranslatorInterface;
use Dhii\Wp\I18n\FormatTranslator;
use Inpsyde\MultilingualPress2to3\Handler\CompositeHandler;
use Inpsyde\MultilingualPress2to3\Migration\ContentRelationshipMigrator;
use Inpsyde\MultilingualPress2to3\IntegrationHandler;
use Inpsyde\MultilingualPress2to3\MainHandler;
use Inpsyde\MultilingualPress2to3\MigrateRelationshipsCliCommand;
use Inpsyde\MultilingualPress2to3\MigrateCliCommandHandler;
use Psr\Container\ContainerInterface;

return function ( $base_path, $base_url ) {
	return [
		'version'                 => '[*next-version*]',
		'base_path'               => $base_path,
		'base_dir'                => function ( ContainerInterface $c ) {
			return dirname( $c->get( 'base_path' ) );
		},
		'base_url'                => $base_url,
		'js_path'                 => '/assets/js',
		'templates_dir'           => '/templates',
		'translations_dir'        => '/languages',
		'text_domain'             => 'mlp2to3',

        'wpcli_command_key_mlp2to3_migrate' => 'mlp2to3 relationships',
        'filter_is_check_legacy'  => 'multilingualpress.is_check_legacy',

        /* The main handler */
        'handler_main' => function (ContainerInterface $c) {
            return new MainHandler($c, $c->get('handlers'));
        },

        /*
         * List of handlers to run
         */
        'handlers' => function (ContainerInterface $c) {
            return [
                $c->get('handler_migrate_cli_command'),
                $c->get('handler_integration'),
            ];
        },

        'translator'              => function ( ContainerInterface $c ) {
            return new FormatTranslator($c->get('text_domain'));
        },

        'memory_cache_factory' => function (ContainerInterface $c): callable {
            return function () use($c): SimpleCacheInterface {
                return new MemoryMemoizer();
            };
        },

        'container_factory' => function (ContainerInterface $c): callable {
            $cacheFactory = $c->get('memory_cache_factory');
            return function (array $data) use ($cacheFactory, $c) {
                $cache = $cacheFactory();
                assert($cache instanceof SimpleCacheInterface);

                return new ContainerAwareCachingContainer($data, $cache, $c);
            };
        },


        'composite_handler_factory' => function (ContainerInterface $c): callable {
            return function (array $handlers) {
                return new CompositeHandler($handlers);
            };
        },

        'progress_bar_factory' => function (ContainerInterface $c) {
            return function (int $total = 1, string $message = '' ): Bar {
                return new Bar($message, $total);
            };
        },

        /*
         * Tracks total progress of migration.
         */
        'migration_progress' => function (ContainerInterface $c): Progress {
            $f = $c->get('progress_bar_factory');
            assert(is_callable($f));
            $t = $c->get('translator');
            assert($t instanceof FormatTranslatorInterface);

            return $f(1, $t->translate('Modules'));
        },

        /*
         * Tracks the progress of an individual migration module.
         */
        'migration_modules_progress' => function (ContainerInterface $c): Progress {
            $f = $c->get('progress_bar_factory');
            assert(is_callable($f));
            $t = $c->get('translator');
            assert($t instanceof FormatTranslatorInterface);

            return $f(1, $t->translate('Tasks'));
        },

        'handler_migrate_cli_command' => function (ContainerInterface $c) {
            return new MigrateCliCommandHandler($c);
        },

        'wpcli_command_migrate_relationships' => function (ContainerInterface $c) {
            return new MigrateRelationshipsCliCommand(
                $c->get('migrator_relationships'),
                $c->get('wpdb'),
                $c->get('translator')
            );
        },

        'migrator_relationships' => function (ContainerInterface $c) {
            return new ContentRelationshipMigrator(
                $c->get('wpdb'),
                $c->get('translator')
            );
        },

        'wpdb' => function (ContainerInterface $c) {
            global $wpdb;

            return $wpdb;
        },

        'handler_integration' => function (ContainerInterface $c) {
            return new IntegrationHandler(
                $c
            );
        },
	];
};
