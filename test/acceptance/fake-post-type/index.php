<?php # -*- coding: utf-8 -*-

/*
 * Plugin Name: Fake Post Type
 */

add_action('plugins_loaded', function () {
    load_plugin_textdomain('fake-post-type', false, basename(dirname(__FILE__)) . '/languages');
});

add_action('init', function () {
    register_post_type('fake', [
        'label' => esc_html__('Fake Post Type', 'fake-post-type'),
        'public' => true,
        'has_archive' => true,
        'rewrite' => [
            'slug' => esc_html_x('fake-post-type-slug', 'slug', 'fake-post-type'),
            'with_front' => false,
        ],
    ]);
    register_taxonomy('fakes', 'fake', [
        'label' => esc_html__('Fakes Taxonomy', 'fake-post-type'),
        'rewrite' => [
            'slug' => esc_html_x('fakes-taxonomy-slug', 'slug', 'fake-post-type'),
            'with_front' => true,
            'hierarchical' => false,
        ],
    ]);
});
