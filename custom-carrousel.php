<?php
function custom_carrousel_admin_enqueue_scripts()
{
    wp_enqueue_style('custom-carrousel-admin-styles', plugin_dir_url(__FILE__) . 'css/style-admin.css');
}
add_action('admin_enqueue_scripts', 'custom_carrousel_admin_enqueue_scripts');

// Chargement du fichier CSS pour le front-end du site
function custom_carrousel_enqueue_styles()
{
    wp_enqueue_style('custom-carrousel-styles', plugin_dir_url(__FILE__) . 'css/styles.css');
}
add_action('wp_enqueue_scripts', 'custom_carrousel_enqueue_styles');

// Chargement du fichier JavaScript pour le front-end du site
function custom_carrousel_enqueue_scripts()
{
    wp_enqueue_script('custom-carrousel-scripts', plugin_dir_url(__FILE__) . 'js/script-carrousel.js', array(), '1.0', true);
}
add_action('wp_enqueue_scripts', 'custom_carrousel_enqueue_scripts');


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
    $table_name = $wpdb->prefix . 'custom_carrousel';

    // Récupérer toutes les entrées de la table custom_carrousel
    $items = $wpdb->get_results("SELECT * FROM $table_name");

    if (!$items) return 'Aucun élément de carrousel trouvé.';

    $carousel_tabs = '';
    $carousel_items = '';
    $count = 1;

    foreach ($items as $item) {
        $selected = $count === 1 ? 'aria-selected="true"' : 'aria-selected="false"';
        $active = $count === 1 ? 'active' : '';

        $carousel_tabs .= '<button id="carousel-tab-' . $count . '" type="button" role="tab" tabindex="-1" aria-label="Slide ' . $count . '" ' . $selected . ' aria-controls="carousel-item-' . $count . '">
            <svg width="34" height="34" version="1.1" xmlns="http://www.w3.org/2000/svg">
              <circle class="border" cx="16" cy="15" r="10"></circle>
              <circle class="tab-background" cx="16" cy="15" r="8"></circle>
              <circle class="tab" cx="16" cy="15" r="6"></circle>
            </svg>
          </button>';

        $carousel_items .= '<div class="carousel-item ' . $active . '" id="carousel-item-' . $count . '" role="tabpanel" aria-roledescription="slide" aria-label="' . $count . ' of ' . count($items) . '">
        <div class="carousel-image">
          <a href="' . esc_url($item->link_url) . '" id="carousel-image-' . $count . '">
            <img src="' . esc_url($item->image_url) . '" alt="' . esc_attr($item->title) . '">
          </a>
        </div>
        <div class="carousel-caption">
          <h3>
            <a href="' . esc_url($item->link_url) . '"> ' . esc_html($item->title) . ' </a>
          </h3>
          <div>
            <p><span class="contrast">' . esc_html($item->description) . '</span></p>
          </div>
        </div></div>';

        $count++;
    }

    return '<section id="myCarousel" class="carousel-tablist" aria-roledescription="carousel" aria-label="Highlighted television shows">
        <div class="carousel-inner">
        <div class="controls">
        <button class="rotation" type="button">
        <svg width="42" height="34" version="1.1" xmlns="http://www.w3.org/2000/svg" class="svg-play">
          <rect class="background" x="2" y="2" rx="5" ry="5" width="38" height="24"></rect>
          <rect class="border" x="4" y="4" rx="5" ry="5" width="34" height="20"></rect>

          <polygon class="pause" points="17 8 17 20"></polygon>

          <polygon class="pause" points="24 8 24 20"></polygon>

          <polygon class="play" points="15 8 15 20 27 14"></polygon>
        </svg>
      </button>
        <div class="tab-wrapper">
        <div role="tablist" aria-label="Slides">' . $carousel_tabs . '</div></div></div>
        <div id="myCarousel-items" class="carousel-items playing" aria-live="off">' . $carousel_items . '</div></div>
      </section><div class="col-sm-1"></div>';
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
