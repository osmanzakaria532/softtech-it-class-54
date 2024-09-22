<?php

/**
 * Comet 2016 functions and definitions
 *
 * Anyone can use the theme but he/she will have to maintain the rules of GPL 2 license.
 * Here you will get all the functions of Comet 2016
 */

// theme setup functions
add_action('after_setup_theme', 'theme_support_functions');
function theme_support_functions()
{

    // Text Domain
    load_theme_textdomain('comet', get_template_directory() . '/language');

    // Theme Support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');

    // Post Formate
    add_theme_support('post-formats', array(
        'gallery',
        'video',
        'audio',
        'quote'
    ));
}

// Add google fonts
function get_comet_fonts()
{

    $fonts = array();
    $fonts[] = 'Montserrat:400,700';
    $fonts[] = 'Raleway:300,400,500';
    $fonts[] = 'Halant:300,400';


    $comet_fonts = add_query_arg(array(
        'family' => urlencode(implode('|', $fonts)),
        'subset' => 'latin'
    ), 'https://fonts.googleapis.com/css');

    return $comet_fonts;
}


// Including the stylesheet
add_action('wp_enqueue_scripts', 'comet_stylesheet');
function comet_stylesheet()
{

    wp_enqueue_style('bundle', get_template_directory_uri() . '/css/bundle.css', array(), '', false);
    wp_enqueue_style('buildingStyle', get_template_directory_uri() . '/css/style.css', array(), '', false);
    wp_enqueue_style('fonts', get_comet_fonts());

    wp_enqueue_style('customStyle', get_stylesheet_uri());
}

// JavaScripts connection
add_action('wp_enqueue_scripts', 'conditional_scripts');
function conditional_scripts()
{

    wp_enqueue_script('html5shim', 'http://html5shim.googlecode.com/svn/trunk/html5.js');
    wp_script_add_data('html5shim', 'conditional', 'lt IE 9');

    wp_enqueue_script('respond', 'https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js');
    wp_script_add_data('respond', 'conditional', 'lt IE 9');
}

// jQuery depended javascript
add_action('wp_enqueue_scripts', 'jquery_depended_scripts');
function jquery_depended_scripts()
{

    // wp_enqueue_script('jq', get_template_directory_uri().'/js/jquery.js'); // atake bad diye direct wordpress no-config jquery ar upor depend kore dewa

    wp_enqueue_script('bundle', get_template_directory_uri() . '/js/bundle.js', array('jquery'), '', true);
    wp_enqueue_script('google-map', 'https://maps.googleapis.com/maps/api/js?v=3.exp', array('jquery'), '', true);
    wp_enqueue_script('main', get_template_directory_uri() . '/js/main.js', array('jquery', 'bundle'), '', true);
}

// Gallery Shortcodes
if (file_exists(dirname(__FILE__) . '/gallery.php')) {
    require_once(dirname(__FILE__) . '/gallery.php');
}