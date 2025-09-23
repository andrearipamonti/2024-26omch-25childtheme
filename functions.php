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
}
add_action( 'wp_enqueue_scripts', 'main_styles' );

if( class_exists('acf') ) {
    function register_acf_blocks() {
        register_block_type( __DIR__ . '/blocks/testimonial' );
        register_block_type( __DIR__ . '/blocks/personal-card' );
        register_block_type( __DIR__ . '/blocks/progress-bar' );
    }
    add_action('init', 'register_acf_blocks');
}