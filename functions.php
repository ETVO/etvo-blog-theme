<?php

define('SITE_URL', '');

function register_to_rest()
{
    register_rest_field(
        'post',
        'featured_image_data',
        array(
            'get_callback' => 'etvo_get_featured_image_data',
        )
    );

    register_rest_field(
        'post',
        'category',
        array(
            'get_callback' => 'etvo_get_category',
        )
    );

    register_rest_field(
        'post',
        'category',
        array(
            'get_callback' => 'etvo_get_category',
        )
    );
}
add_action('rest_api_init', 'register_to_rest');

function etvo_get_featured_image_data($post, $field_name, $request)
{

    if (empty($post['featured_media'])) {
        return;
    }

    $image_id = (int) $post['featured_media'];

    if (!$image = get_post($image_id)) {
        return;
    }

    return array(
        'src' => wp_get_attachment_url($image_id),
        'alt' => get_post_meta($image_id, '_wp_attachment_image_alt', true),
        'caption' => $image->post_excerpt
    );
}

function etvo_get_category($post, $field_name, $request)
{
    if (empty($post['id'])) {
        return;
    }

    $category = get_the_terms($post['id'], 'category');

    if (!$category) {
        return;
    }

    return $category;
}

// Set custom excerpt length
add_filter("excerpt_length", "custom_excerpt_len", 999);
function custom_excerpt_len($length)
{
    return 20;
}


add_action("after_setup_theme", "theme_setup");
function theme_setup()
{

    // Enable support for site logo
    add_theme_support(
        "custom-logo",
        apply_filters(
            "custom_logo_args",
            array(
                "flex-height" => true,
                "flex-width"  => true,
            )
        )
    );

    function new_excerpt_more($more)
    {
        return '...';
    }
    add_filter('excerpt_more', 'new_excerpt_more');

    // Enable support for Post Formats.
    add_theme_support('post-formats', array('video', 'gallery', 'audio', 'quote', 'link'));

    // Let WordPress handle Title Tag in all pages
    add_theme_support("title-tag");

    // Enable support for Post Thumbnails on posts and pages.
    add_theme_support('post-thumbnails');

    // Enable support for excerpt text on posts and pages.
    add_post_type_support('page', 'excerpt');

    // Switch default core markup to output valid HTML5.
    add_theme_support(
        'html5',
        array(
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'widgets',
        )
    );
}

// add_filter( 'yoast_seo_development_mode', '__return_true' );
add_filter( 'wpseo_schema_graph_pieces', 'etvo_remove_from_yoast_schema', 11, 2 );

function etvo_remove_from_yoast_schema( $pieces, $context ) {
    return \array_filter( $pieces, function( $piece ) {
        return ! $piece instanceof \Yoast\WP\SEO\Generators\Schema\Person;
    } );
}