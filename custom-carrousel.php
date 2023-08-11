<?php
// Les mêmes fonctions pour charger les fichiers CSS et JS restent inchangées

// Créer une table dans la base de données lors de l'activation du plugin
function custom_carrousel_create_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'custom_carrousel';
    $charset_collate = $wpdb->get_charset_collate();

    // Instruction SQL pour créer la table
    $sql = "CREATE TABLE $table_name (
         id mediumint(9) NOT NULL AUTO_INCREMENT,
         image_url varchar(255) NOT NULL,
         title varchar(255) NOT NULL,
         description text NOT NULL,
         link_url varchar(255) NOT NULL,
         UNIQUE KEY id (id)
     ) $charset_collate;";

    // Inclure le fichier upgrade.php et exécuter dbDelta pour créer la table
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

register_activation_hook(plugin_dir_path(__FILE__) . 'home-extension.php', 'custom_carrousel_create_table');

// Fonction pour créer un shortcode permettant d'afficher le carrousel
function custom_carrousel_shortcode($atts)
{
    global $wpdb;
    $atts = shortcode_atts(array('id' => 0), $atts);
    $table_name = $wpdb->prefix . 'custom_carrousel';

    // Requête pour récupérer les éléments du carrousel
    $items = $wpdb->get_results("SELECT image_url, title, description, link_url FROM $table_name ORDER BY id");

    // Générer le HTML du carrousel ici en fonction des éléments récupérés
    // Vous devrez ajuster ce HTML pour correspondre à la structure de votre carrousel
    $output = '<section id="myCarousel" class="carousel-tablist" aria-roledescription="carousel" aria-label="Highlighted television shows">';
    $output .= '<div class="carousel-inner">';
    foreach ($items as $item) {
        $output .= '<div class="carousel-item">';
        $output .= '<div class="carousel-image">';
        $output .= '<a href="' . esc_url($item->link_url) . '" id="carousel-image-1">';
        $output .= '<img src="' . esc_url($item->image_url) . '" alt="' . esc_attr($item->description) . '">';
        $output .= '</a></div>';
        $output .= '<div class="carousel-caption">';
        $output .= '<h3><a href="#">' . esc_html($item->title) . '</a></h3>';
        $output .= '</div></div>';
    }
    $output .= '</div></section>';
    $output .= '<div class="col-sm-1"></div>';

    return $output;
}
add_shortcode('custom_carrousel', 'custom_carrousel_shortcode');

// Fonction pour afficher le contenu de la page d'administration du carrousel
function custom_link_carrousel_page()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'custom_carrousel';

    // Traiter le formulaire si les données sont envoyées
    if (isset($_POST['submit'])) {
        $image_url = sanitize_text_field($_POST['image_url']);
        $title = sanitize_text_field($_POST['title']);
        $description = sanitize_text_field($_POST['description']);
        $link_url = sanitize_text_field($_POST['link_url']);

        // Insérer ou mettre à jour dans la base de données
        $wpdb->insert(
            $table_name,
            array(
                'image_url' => $image_url,
                'title' => $title,
                'description' => $description,
                'link_url' => $link_url,
            ),
            array('%s', '%s', '%s', '%s')
        );

        $inserted_id = $wpdb->insert_id; // Récupère l'ID de la ligne nouvellement insérée

        echo '<div class="notice notice-success"><p>Lien ajouté avec succès! Voici votre shortcode: </p>';
        echo '<code>[custom_carrousel id="' . $inserted_id . '"]</code></div>'; // Affiche le shortcode
    }

    // Afficher le formulaire
?>
    <div class="wrap">
        <h2>Ajouter un élément de carrousel</h2>
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="image_url">URL de l'image</label></th>
                    <td><input type="text" name="image_url" id="image_url" class="regular-text" required></td>
                </tr>
                <tr>
                    <th scope="row"><label for="title">Titre</label></th>
                    <td><input type="text" name="title" id="title" class="regular-text" required></td>
                </tr>
                <tr>
                    <th scope="row"><label for="description">Description</label></th>
                    <td><textarea name="description" id="description" class="regular-text" required></textarea></td>
                </tr>
                <tr>
                    <th scope="row"><label for="link_url">URL du lien</label></th>
                    <td><input type="text" name="link_url" id="link_url" class="regular-text" required></td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" name="submit" id="submit" class="button button-primary" value="Ajouter">
            </p>
        </form>
    </div>
<?php
}

