<?php
/*
    echo "Ciao, mondo!";
    echo 'Eih, ciao!';
    echo 'Sono l\'articolo principale'; 
    echo 21;
    echo 20 + 20;

$sono_una_variabile = "Ciao, sono una variabile!";
$sonoUnaVariabile = "Ciao, sono una variabile!";
echo $sono_una_variabile;
echo $sonoUnaVariabile;

*/

function main_styles() {
    wp_enqueue_style( 
        'parent-style', 
        get_template_directory_uri() . '/style.css'
    );
    wp_enqueue_style( 
        'child-style', 
        get_stylesheet_directory_uri() . '/style.css', 
        array('parent-style')
    );
    wp_enqueue_style( 
        'custom-icons', 
        get_stylesheet_directory_uri() . '/assets/fonts/custom-icons/css/custom-icons.css', 
        array()
    );
    wp_register_style( 
        'slick', 
        get_stylesheet_directory_uri() . '/assets/packages/slick-1.8.1/slick/slick.css',
        array(), 
        '1.8.1',
    );
}
add_action( 'wp_enqueue_scripts', 'main_styles' );

function main_scripts() {

    wp_enqueue_script( 
        'main', // ID, NOME UNIVOCO, HANDLE
        get_stylesheet_directory_uri() . '/assets/scripts/main.js', 
        array('jquery'), // Dipendenze
        '1.0', // versione
        true // true = nel footer, false = nell'header
    );

    wp_register_script( 
        'slick', // ID, NOME UNIVOCO, HANDLE
        get_stylesheet_directory_uri() . '/assets/packages/slick-1.8.1/slick/slick.min.js', // Percorso
        array('jquery'), // Dipendenze
        '1.8.1', // versione
        true // true = nel footer, false = nell'header
    );
    wp_register_script( 
        'carousel', 
        get_stylesheet_directory_uri() . '/blocks/carousel/carousel.js',
        array('main', 'slick'), 
        '1.0', 
        true
    );
    wp_register_script( 
        'carousel-captions', 
        get_stylesheet_directory_uri() . '/blocks/carousel-captions/carousel-captions.js',
        array('main', 'slick'), 
        '1.0', 
        true
    );
    wp_enqueue_script( 
        'callout', 
        get_stylesheet_directory_uri() . '/blocks/callout/callout.js',
        array('main'), 
        '1.0', 
        true
    );
    wp_enqueue_script( 
        'faq', 
        get_stylesheet_directory_uri() . '/blocks/faq/faq.js',
        array('main'), 
        '1.0', 
        true
    );
    wp_enqueue_script( 
        'floating-support', 
        get_stylesheet_directory_uri() . '/blocks/floating-support/floating-support.js',
        array('main'), 
        '1.0', 
        true
    );
}
add_action( 'wp_enqueue_scripts', 'main_scripts' );

if( class_exists('acf') ) {
    function register_acf_blocks() {
        register_block_type( __DIR__ . '/blocks/callout' );
        register_block_type( __DIR__ . '/blocks/carousel' );
        register_block_type( __DIR__ . '/blocks/carousel-captions' );
        register_block_type( __DIR__ . '/blocks/faq' );
        register_block_type( __DIR__ . '/blocks/faq-light' );
        register_block_type( __DIR__ . '/blocks/floating-support' );
        register_block_type( __DIR__ . '/blocks/personal-card' );
        register_block_type( __DIR__ . '/blocks/progress-bar' );
        register_block_type( __DIR__ . '/blocks/testimonial' );
    }
    add_action('init', 'register_acf_blocks');
}