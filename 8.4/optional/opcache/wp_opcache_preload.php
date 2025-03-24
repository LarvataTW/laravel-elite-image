<?php
declare( strict_types=1 );

$wp_dir = '/var/www/public/';

$preload_patterns = [
    $wp_dir . "wp-includes/Text/Diff/Renderer.php",
    $wp_dir . "wp-includes/Text/Diff/Renderer/inline.php",
    $wp_dir . "wp-includes/SimplePie/**/*.php",
    $wp_dir . "wp-includes/SimplePie/*.php",
    $wp_dir . "wp-includes/Requests/**/*.php",
    $wp_dir . "wp-includes/Requests/*.php",
    $wp_dir . "wp-includes/**/class-*.php",
    $wp_dir . "wp-includes/class-*.php",
];

$exclusions = [
    $wp_dir . 'wp-includes/class-simplepie.php',
    $wp_dir . 'wp-includes/SimplePie/File.php',
    $wp_dir . 'wp-includes/SimplePie/Core.php',
    $wp_dir . 'wp-includes/class-wp-simplepie-file.php',
    $wp_dir . 'wp-includes/class-snoopy.php',
    $wp_dir . 'wp-includes/class-json.php',
];

foreach ( $preload_patterns as $pattern ) {
    $files = glob( $pattern );

    foreach ( $files as $file ) {
        if ( ! in_array( $file, $exclusions, true ) ) {
            opcache_compile_file( $file );
        }
    }
}