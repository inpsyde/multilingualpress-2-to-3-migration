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
use cli\Progress;
use Dhii\Wp\I18n\FormatTranslator;
use Inpsyde\MultilingualPress2to3\Handler\CompositeHandler;
use Inpsyde\MultilingualPress2to3\Handler\CompositeProgressHandler;
use Inpsyde\MultilingualPress2to3\Handler\HandlerInterface;
use Inpsyde\MultilingualPress2to3\LanguageRedirectMigrationHandler;
use Inpsyde\MultilingualPress2to3\Migration\ContentRelationshipMigrator;
use Inpsyde\MultilingualPress2to3\IntegrationHandler;
use Inpsyde\MultilingualPress2to3\MainHandler;
use Inpsyde\MultilingualPress2to3\MigrateCliCommand;
use Inpsyde\MultilingualPress2to3\MigrateCliCommandHandler;
use Inpsyde\MultilingualPress2to3\Migration\LanguageRedirectMigrator;
use Inpsyde\MultilingualPress2to3\Migration\ModulesMigrator;
use Inpsyde\MultilingualPress2to3\Migration\RedirectMigrator;
use Inpsyde\MultilingualPress2to3\ModulesMigrationHandler;
use Inpsyde\MultilingualPress2to3\RedirectMigrationHandler;
use Inpsyde\MultilingualPress2to3\RelationshipsMigrationHandler;
use Psr\Container\ContainerInterface;
use cli\progress\Bar;

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

        'wpcli_command_key_mlp2to3_migrate' => 'mlp2to3',
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

        'translator' => function (ContainerInterface $c) {
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

        'composite_progress_handler_factory' => function (ContainerInterface $c): callable {
            return function (array $handlers, Progress $progress): HandlerInterface {
                return new CompositeProgressHandler($handlers, $progress);
            };
        },

        'migrations_handler_factory' => function (ContainerInterface $c): callable {
            $progressHandlerFactory = $c->get('composite_progress_handler_factory');

            return function ($handlers) use ($progressHandlerFactory, $c): HandlerInterface {
                $progress = $c->get('migration_progress');
                $handler = $progressHandlerFactory($handlers, $progress);

                return $handler;
            };
        },

        /**
         * Provides a layer for name-based lazy-loading of migration modules
         */
        'migration_modules' => function (ContainerInterface $c) {
            $f = $c->get('container_factory');
            assert(is_callable($f));

            return $f($c->get('migration_module_definitions'));
        },

        'migration_module_names' => function (ContainerInterface $c) {
            $definitions = $c->get('migration_module_definitions');
            assert(is_array($definitions));

            return array_keys($definitions);
        },

        'migration_module_definitions' => function (ContainerInterface $c) {
            return [
                'relationships'         => function (ContainerInterface $c) {
                    return $c->get('handler_relationships_migration');
                },
                'redirects'             => function (ContainerInterface $c) {
                    return $c->get('handler_redirect_migration');
                },
                'modules'               => function (ContainerInterface $c) {
                    return $c->get('handler_modules_migration');
                },
                'lang_redirects'               => function (ContainerInterface $c) {
                    return $c->get('handler_language_redirect_migration');
                },
            ];
        },

        'handler_relationships_migration' => function (ContainerInterface $c): HandlerInterface {
            $progress = $c->get('migration_modules_progress');
            assert($progress instanceof Progress);

            $t = $c->get('translator');
            assert($t instanceof FormatTranslatorInterface);

            return new RelationshipsMigrationHandler(
                $c->get('migrator_relationships'),
                $c->get('wpdb'),
                $progress,
                0 // Everything
            );
        },

        'handler_redirect_migration' => function (ContainerInterface $c): HandlerInterface {
            $progress = $c->get('migration_modules_progress');
            assert($progress instanceof Progress);

            $t = $c->get('translator');
            assert($t instanceof FormatTranslatorInterface);

            return new RedirectMigrationHandler(
                $c->get('migrator_redirects'),
                $c->get('wpdb'),
                $progress,
                0 // Everything
            );
        },

        'handler_modules_migration' => function (ContainerInterface $c): HandlerInterface {
            $progress = $c->get('migration_modules_progress');
            assert($progress instanceof Progress);

            $t = $c->get('translator');
            assert($t instanceof FormatTranslatorInterface);

            return new ModulesMigrationHandler(
                $c->get('migrator_modules'),
                $c->get('wpdb'),
                $progress,
                0 // Everything
            );
        },

        'handler_language_redirect_migration' => function (ContainerInterface $c): HandlerInterface {
            $progress = $c->get('migration_modules_progress');
            assert($progress instanceof Progress);

            $t = $c->get('translator');
            assert($t instanceof FormatTranslatorInterface);

            return new LanguageRedirectMigrationHandler(
                $c->get('migrator_language_redirects'),
                $c->get('wpdb'),
                $progress,
                0 // Everything
            );
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

        'wpcli_command_migrate' => function (ContainerInterface $c) {
            return new MigrateCliCommand(
                $c->get('translator'),
                $c->get('migration_modules'),
                $c->get('migration_module_names'),
                $c->get('migrations_handler_factory')
            );
        },

        'migrator_relationships' => function (ContainerInterface $c) {
            return new ContentRelationshipMigrator(
                $c->get('wpdb'),
                $c->get('translator')
            );
        },

        'migrator_redirects' => function (ContainerInterface $c) {
            return new RedirectMigrator(
                $c->get('wpdb'),
                $c->get('translator')
            );
        },

        'migrator_modules' => function (ContainerInterface $c) {
            return new ModulesMigrator(
                $c->get('wpdb'),
                $c->get('translator')
            );
        },

        'migrator_language_redirects' => function (ContainerInterface $c) {
            return new LanguageRedirectMigrator(
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
