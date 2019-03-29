<?php
/**
 * Plugin Name: Hello CLI
 */
if (!defined('WP_CLI') || !WP_CLI) {
    return;
}

$hello = function ($args, $assoc_args) {
    WP_CLI::success($args[0] . ' ' . $assoc_args['word']);
};

add_action(
    'plugins_loaded',
    function () use ($hello) {
        WP_CLI::add_command('hello', $hello);
    },
    1
);
