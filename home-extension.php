<?php

/**
 * Plugin Name: Custom Design Pattern
 * Description: Une extension pour créer des liens personnalisés et des carrousels personallisés avec des design patterns.
 * Version: 1.0
 * Author: Tamara VIOLEAU
 */

require_once plugin_dir_path(__FILE__) . 'custom-link.php';
require_once plugin_dir_path(__FILE__) . 'custom-carrousel.php';

// Section pour le menu d'administration du plugin
// Enregistrement du menu d'administration et des sous-menus
function custom_link_admin_menu()
{
    // Menu principal
    add_menu_page(
        'Design Pattern W3C',  // Titre de la page
        'Design Pattern W3C',  // Titre du menu
        'manage_options',      // Capacité
        'design_pattern_w3c',  // Slug de la page
        ''                     // Pas de fonction de rappel pour la page principale
    );

    // Sous-page pour les liens
    add_submenu_page(
        'design_pattern_w3c',       // Slug du menu parent
        'Liens',                    // Titre de la page
        'Liens',       // Titre du menu (remplace le nom de la sous-page automatique)
        'manage_options',           // Capacité
        'design_pattern_w3c',       // Slug de la sous-page (doit être le même que le slug de la page parente pour qu'elle soit la page par défaut)
        'custom_link_links_page'    // Fonction pour afficher le contenu de la sous-page "Liens"
    );

    // Sous-page pour le carrousel
    add_submenu_page(
        'design_pattern_w3c',   // Slug du menu parent
        'Carrousel',            // Titre de la page
        'Carrousel',            // Titre du menu
        'manage_options',       // Capacité
        'custom_carrousel',     // Slug de la sous-page
        'custom_link_carrousel_page' // Fonction pour afficher le contenu de la sous-page "Carrousel"
    );


    
}
add_action('admin_menu', 'custom_link_admin_menu');
