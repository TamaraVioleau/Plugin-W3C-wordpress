<?php

/**
 * Plugin Name: Custom Link Design Pattern
 * Description: Une extension pour créer des liens personnalisés avec des design patterns.
 * Version: 1.0
 * Author: Tamara VIOLEAU
 */

// Enregistrer les styles CSS
function custom_link_enqueue_styles()
{
    wp_enqueue_style('custom-link-styles', plugin_dir_url(__FILE__) . 'css/styles.css');
}
add_action('wp_enqueue_scripts', 'custom_link_enqueue_styles');

function custom_link_admin_enqueue_scripts()
{
    wp_enqueue_style('custom-link-admin-styles', plugin_dir_url(__FILE__) . 'css/style-admin.css');
}
add_action('admin_enqueue_scripts', 'custom_link_admin_enqueue_scripts');


function custom_link_enqueue_scripts()
{
    wp_enqueue_script('custom-link-scripts', plugin_dir_url(__FILE__) . 'js/scripts.js', array(), '1.0', true);
}
add_action('wp_enqueue_scripts', 'custom_link_enqueue_scripts');


// Créer la table lors de l'activation de l'extension
function custom_link_create_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'custom_links';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
         id mediumint(9) NOT NULL AUTO_INCREMENT,
         url varchar(255) NOT NULL,
         text varchar(255) NOT NULL,
         design_pattern varchar(255) NOT NULL,
         UNIQUE KEY id (id)
     ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'custom_link_create_table');

// Fonction de shortcode pour afficher le lien
function custom_link_shortcode($atts)
{
    global $wpdb;
    $atts = shortcode_atts(array('id' => 0), $atts);
    $table_name = $wpdb->prefix . 'custom_links';

    // Rechercher le lien dans la base de données
    $link = $wpdb->get_row($wpdb->prepare("SELECT url, text, design_pattern FROM $table_name WHERE id = %d", $atts['id']));

    if (!$link) return '';

    // Retourner le HTML du lien
    return "<span tabindex=\"0\" role=\"link\" onclick=\"goToLink(event, '{$link->url}')\" onkeydown=\"goToLink(event, '{$link->url}')\" class=\"{$link->design_pattern}\">" . stripslashes($link->text) . "</span>";
}
add_shortcode('custom_link', 'custom_link_shortcode');


//Enregistrement du menu d'administration:
function custom_link_admin_menu()
{
    add_menu_page(
        'Design Pattern W3C',  // Titre de la page
        'Design Pattern W3C',  // Titre du menu
        'manage_options',      // Capacité
        'design_pattern_w3c',  // Slug de la page
        ''                     // Pas de fonction de rappel pour la page principale
    );

    add_submenu_page(
        'design_pattern_w3c',       // Slug du menu parent
        'Liens',                    // Titre de la page
        'Liens',       // Titre du menu (remplace le nom de la sous-page automatique)
        'manage_options',           // Capacité
        'design_pattern_w3c',       // Slug de la sous-page (doit être le même que le slug de la page parente)
        'custom_link_links_page'    // Fonction pour afficher le contenu de la sous-page "Liens"
    );


    // Vous pouvez ajouter d'autres sous-pages ici, comme "Carrousel"
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

//Fonction pour afficher le contenu de la page d'administration:
function custom_link_links_page()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'custom_links';

    // Traiter le formulaire s'il a été soumis
    if (isset($_POST['submit'])) {
        $url = sanitize_text_field($_POST['url']);
        $text = stripslashes(sanitize_text_field($_POST['text']));
        $design_pattern = sanitize_text_field($_POST['design_pattern']);

        $wpdb->insert(
            $table_name,
            array('url' => $url, 'text' => $text, 'design_pattern' => $design_pattern),
            array('%s', '%s', '%s')
        );

        $inserted_id = $wpdb->insert_id; // Récupère l'ID de la ligne nouvellement insérée

        echo '<div class="notice notice-success"><p>Lien ajouté avec succès! Voici votre shortcode: </p>';
        echo '<code>[custom_link id="' . $inserted_id . '"]</code></div>'; // Affiche le shortcode
    }


    // Formulaire pour ajouter un lien
    echo '<div class="wrap">
    <h2>Ajouter un lien personnalisé</h2>
    <form method="post">
        <table class="form-table">
                        <tr>
                <th><label for="design_pattern">Intitulé du lien</label></th>
                <td><input type="text" name="design_pattern" id="design_pattern" class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="url">URL</label></th>
                <td><input type="text" name="url" id="url" class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="text">Texte visible</label></th>
                <td><input type="text" name="text" id="text" class="regular-text" required></td>
            </tr>

        </table>
        <input type="submit" name="submit" class="button button-primary" value="Ajouter le lien">
    </form>
</div>';


    // Liste des liens existants
    $links = $wpdb->get_results("SELECT id, text, design_pattern FROM $table_name");

    echo '<h2>Liste des liens personnalisés</h2>';
    echo '<div class="grid-table">';
    echo '<div class="grid-header">';
    echo '<div class="grid-cell">Intitulé</div><div class="grid-cell">Shortcode</div><div class="grid-cell">Action</div>';
    echo '</div>'; // Fin de l'en-tête

    foreach ($links as $link) {
        $shortcode = '[custom_link id="' . $link->id . '"]';
        echo '<div class="grid-row">';
        echo '<div class="grid-cell">' . esc_html(stripslashes($link->design_pattern)) . '</div>';
        echo '<div class="grid-cell">' . esc_html($shortcode) . ' <button class="copy-shortcode-btn" data-shortcode="' . esc_attr($shortcode) . '">Copier</button></div>';
        echo '<div class="grid-cell">';
        // Bouton de modification
        echo '<form method="post" action="" style="display: inline-block;">';
        echo '<input type="hidden" name="edit_link" value="' . esc_attr($link->id) . '">';
        echo '<input type="submit" value="Modifier" class="button">';
        echo '</form>';
        // Bouton de suppression
        echo '<form method="post" action="" style="display: inline-block;">';
        echo '<input type="hidden" name="delete_link" value="' . esc_attr($link->id) . '">';
        echo '<input type="submit" value="Supprimer">';
        echo '</form>';
        echo '</div>';
        echo '</div>'; // Fin de la ligne
    }

    echo '</div>'; // Fin de la table

    // Gérer la suppression
    if (isset($_POST['delete_link'])) {
        $id_to_delete = intval($_POST['delete_link']);
        $wpdb->delete($table_name, array('id' => $id_to_delete), array('%d'));
        echo '<div class="notice notice-success"><p>Lien supprimé avec succès!</p></div>';
    }

    //Gérer la modification
    if (isset($_POST['submit_update'])) {
        $id_to_update = intval($_POST['update_link']);
        $url = sanitize_text_field($_POST['url']);
        $text = stripslashes(sanitize_text_field($_POST['text']));
        $design_pattern = sanitize_text_field($_POST['design_pattern']);

        $wpdb->update(
            $table_name,
            array('url' => $url, 'text' => $text, 'design_pattern' => $design_pattern),
            array('id' => $id_to_update),
            array('%s', '%s', '%s'),
            array('%d')
        );

        echo '<div class="notice notice-success"><p>Lien mis à jour avec succès!</p></div>';
    }


    // Si un lien a été sélectionné pour modification, affichez le formulaire de modification
    if (isset($_POST['edit_link'])) {
        $id_to_edit = intval($_POST['edit_link']);
        $link_to_edit = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id_to_edit));

        echo '<div class="wrap">
        <h2>Modifier un lien personnalisé</h2>
        <form method="post">
            <input type="hidden" name="update_link" value="' . esc_attr($link_to_edit->id) . '">
            <table class="form-table">
                <tr>
                    <th><label for="design_pattern">Intitulé du lien</label></th>
                    <td><input type="text" name="design_pattern" id="design_pattern" value="' . esc_attr(stripslashes($link_to_edit->design_pattern)) . '" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="url">URL</label></th>
                    <td><input type="text" name="url" id="url" value="' . esc_attr($link_to_edit->url) . '" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="text">Texte visible</label></th>
                    <td><input type="text" name="text" id="text" value="' . esc_attr(stripslashes($link_to_edit->text)) . '" class="regular-text" required></td>
                </tr>
            </table>
            <input type="submit" name="submit_update" class="button button-primary" value="Mettre à jour le lien">
        </form>
    </div>';
    }
}

//Fonction pour copier le shortcode 
function custom_link_enqueue_copy_script()
{
    if (isset($_GET['page']) && $_GET['page'] === 'design_pattern_w3c') {
        echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                var copyButtons = document.querySelectorAll(".copy-shortcode-btn");
                copyButtons.forEach(function(btn) {
                    btn.addEventListener("click", function(e) {
                        var shortcode = e.target.getAttribute("data-shortcode");
                        var textarea = document.createElement("textarea");
                        textarea.value = shortcode;
                        document.body.appendChild(textarea);
                        textarea.select();
                        document.execCommand("copy");
                        document.body.removeChild(textarea);
                        alert("Shortcode copié dans le presse-papiers : " + shortcode);
                    });
                });
            });
        </script>';
    }
}
add_action('admin_footer', 'custom_link_enqueue_copy_script');
