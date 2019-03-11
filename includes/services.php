<?php
/**
 * Declares project configuration.
 *
 * @package MultilingualPress2to3
 */

use Dhii\Wp\I18n\FormatTranslator;
use Inpsyde\MultilingualPress2to3\MainHandler;
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

        /* The main handler */
		'handler_main'                  => function ( ContainerInterface $c ) {
			return new MainHandler( $c );
		},

		/*
		 * List of handlers to run
		 */
		'handlers'                => function ( ContainerInterface $c ) {
			return [
			];
		},

        'translator'              => function ( ContainerInterface $c ) {
		    return new FormatTranslator( $c->get('text_domain') );
        }
	];
};
