<?php
/**
 * BeBeats Theme Functions
 * 
 * @package BeBeats
 */

/**
 * Enqueue styles and scripts
 */
function bebeats_enqueue_styles() {
    // Enqueue Bootstrap CSS
    wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css', array(), '5.3.2');
    
    // Enqueue fonts CSS
    wp_enqueue_style('bebeats-fonts', get_template_directory_uri() . '/fonts/fonts.css', array(), '1.0');
    
    // Enqueue theme stylesheet (style.css avec en-têtes WordPress contient déjà tout le CSS)
    wp_enqueue_style('bebeats-style', get_stylesheet_uri(), array('bootstrap-css', 'bebeats-fonts'), '1.0');
    
    // Enqueue page-specific styles
    if (is_page('connexion') || is_page('inscription')) {
        wp_enqueue_style('bebeats-auth-style', get_template_directory_uri() . '/css/auth.css', array('bebeats-style'), '1.0');
    }
    
    if (is_page('event')) {
        wp_enqueue_style('bebeats-event-style', get_template_directory_uri() . '/css/pages/event.css', array('bebeats-style'), '1.0');
    }
    
    if (is_page('contribuer')) {
        wp_enqueue_style('bebeats-contribuer-style', get_template_directory_uri() . '/css/pages/contribuer.css', array('bebeats-style'), '1.0');
    }
    
    if (is_page('recherche')) {
        wp_enqueue_style('bebeats-recherche-style', get_template_directory_uri() . '/css/pages/recherche.css', array('bebeats-style'), '1.0');
    }
    
    if (is_page('reglages')) {
        wp_enqueue_style('bebeats-reglages-style', get_template_directory_uri() . '/css/pages/reglages.css', array('bebeats-style'), '1.0');
    }
    
    if (is_page('resultats') || is_search()) {
        wp_enqueue_style('bebeats-resultats-style', get_template_directory_uri() . '/css/pages/resultats.css', array('bebeats-style'), '1.0');
    }
}

function bebeats_enqueue_scripts() {
    // Enqueue Bootstrap JS
    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js', array(), '5.3.2', true);
    
    // Enqueue custom scripts
    wp_enqueue_script('bebeats-search', get_template_directory_uri() . '/js/search.js', array('jquery'), '1.0', true);
    
    // Passer l'URL d'accueil à JavaScript
    wp_localize_script('bebeats-search', 'bebeatsAjax', array(
        'homeUrl' => home_url('/')
    ));
    
    // Page-specific scripts
    if (is_page('contribuer')) {
        wp_enqueue_script('bebeats-contribuer', get_template_directory_uri() . '/js/contribuer.js', array('jquery'), '1.0', true);
    }
    
    if (is_page('reglages')) {
        wp_enqueue_script('bebeats-toggles', get_template_directory_uri() . '/js/toggles.js', array('jquery'), '1.0', true);
    }
}

// Hook into WordPress
add_action('wp_enqueue_scripts', 'bebeats_enqueue_styles');
add_action('wp_enqueue_scripts', 'bebeats_enqueue_scripts');

/**
 * Theme setup
 */
function bebeats_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
    
    // Register navigation menus if needed
    register_nav_menus(array(
        'primary' => __('Menu Principal', 'bebeats'),
    ));
}
add_action('after_setup_theme', 'bebeats_setup');

?>

