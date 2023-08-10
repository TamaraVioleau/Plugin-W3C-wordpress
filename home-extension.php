<?php

/**
 * Plugin Name: Custom Link Design Pattern
 * Description: Une extension pour créer des liens personnalisés avec des design patterns.
 * Version: 1.0
 * Author: Tamara VIOLEAU
 */

// Enregistrer les styles CSS

function custom_link_enqueue_scripts() {
    wp_enqueue_script('custom-link-scripts', plugin_dir_url(__FILE__) . 'js/scripts.js', array(), '1.0', true);
}
add_action('wp_enqueue_scripts', 'custom_link_enqueue_scripts');

require_once plugin_dir_path(__FILE__) . 'custom-link.php';
require_once plugin_dir_path(__FILE__) . 'custom-carrousel.php';
?>
