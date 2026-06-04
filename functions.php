<?php
function artisraw_enqueue_assets() {
    // Load your design tokens
    wp_enqueue_style('artisraw-tokens', get_template_directory_uri() . '/css/tokens.css', array(), '1.0');
    // Load main theme styles
    wp_enqueue_style('artisraw-main', get_stylesheet_uri(), array('artisraw-tokens'), '1.0');
}
add_action('wp_enqueue_scripts', 'artisraw_enqueue_assets');