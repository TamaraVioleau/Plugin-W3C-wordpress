<?php
// Inclure les fichiers CSS/JS
function custom_carrousel_enqueue_assets()
{
    // Pour l'admin
    if (is_admin()) {
        wp_enqueue_style('custom-carrousel-admin-styles', plugin_dir_url(__FILE__) . 'css/style-admin.css');
    }
    // Pour le front-end
    else {
        wp_enqueue_style('custom-carrousel-styles', plugin_dir_url(__FILE__) . 'css/styles.css');
        wp_enqueue_script('custom-carrousel-scripts', plugin_dir_url(__FILE__) . 'js/script-carrousel.js', array(), '1.0', true);
    }
}
add_action('admin_enqueue_scripts', 'custom_carrousel_enqueue_assets');
add_action('wp_enqueue_scripts', 'custom_carrousel_enqueue_assets');


// Création des tables lors de l'activation du plugin
function custom_carrousel_create_table()
{
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // Structure SQL pour créer la table des carrousels
    $sql_carrousel = "CREATE TABLE {$wpdb->prefix}custom_carrousels (
        carrousel_id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        UNIQUE KEY carrousel_id (carrousel_id)
    ) $charset_collate;";

    // Structure SQL pour créer la table des slides
    $sql_slides = "CREATE TABLE {$wpdb->prefix}custom_carrousel_slides (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        carrousel_id mediumint(9) NOT NULL,
        image_url varchar(255) NOT NULL,
        title varchar(255) NOT NULL,
        description text NOT NULL,
        link_url varchar(255) NOT NULL,
        UNIQUE KEY id (id),
        FOREIGN KEY (carrousel_id) REFERENCES {$wpdb->prefix}custom_carrousels(carrousel_id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_carrousel);
    dbDelta($sql_slides);
}

register_activation_hook(plugin_dir_path(__FILE__) . 'home-extension.php', 'custom_carrousel_create_table');

// Shortcode pour afficher un carrousel spécifique
function custom_carrousel_shortcode($atts)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'custom_carrousel_slides';

    $atts = shortcode_atts(array('id' => 0), $atts, 'custom_carrousel');
    $carrousel_id = intval($atts['id']);

    if (empty($carrousel_id)) return 'ID de carrousel non spécifié ou invalide.';


    // Récupérer toutes les entrées de la table custom_carrousel_slides filtrées par l'ID du carrousel
    $items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE carrousel_id = %d", $carrousel_id));

    if (!$items) return 'Aucun élément de carrousel trouvé.';

    $carousel_tabs = '';
    $carousel_items = '';
    $count = 1;

    foreach ($items as $item) {
        $selected = $count === 1 ? 'aria-selected="true"' : 'aria-selected="false"';
        $active = $count === 1 ? 'active' : '';
        $carousel_tabs .= '<button id="carousel-tab-' . $carrousel_id . '.' . $count . '" type="button" ... aria-controls="carousel-item-' . $carrousel_id . '.' . $count . '" ...';

        $carousel_items .= '<div class="carousel-item ' . $active . '" id="carousel-item-' . $carrousel_id . '.' . $count . '" ...';

        $count++;

        $carousel_tabs .= '<button id="carousel-tab-' . $count . '" type="button" role="tab" tabindex="-1" aria-label="Slide ' . $count . '" ' . $selected . ' aria-controls="carousel-item-' . $count . '">
            <svg width="34" height="34" version="1.1" xmlns="http://www.w3.org/2000/svg">
              <circle class="border" cx="16" cy="15" r="10"></circle>
              <circle class="tab-background" cx="16" cy="15" r="8"></circle>
              <circle class="tab" cx="16" cy="15" r="6"></circle>
            </svg>
          </button>';

        $carousel_items .= '<div class="carousel-item ' . $active . '" id="carousel-item-' . $count . '" role="tabpanel" aria-roledescription="slide" aria-label="' . $count . ' of ' . count($items) . '">
        <div class="carousel-image">
        <a href="' . esc_url($item->link_url) . '" id="carousel-image-' . $item->id . '">
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

// Enregistrer le shortcode pour utilisation dans les contenus
add_shortcode('custom_carrousel', 'custom_carrousel_shortcode');

// Fonction pour créer un carrousel dans la base de données et pour afficher son shortcode dans la page admin
function custom_link_carrousel_page()
{
    global $wpdb;
    $carrousel_table_name = $wpdb->prefix . 'custom_carrousels';
    $slides_table_name = $wpdb->prefix . 'custom_carrousel_slides';

    // Initialisez $carrousel_id avec la valeur du carrousel sélectionné, si disponible
    $carrousel_id = isset($_POST['selected_carrousel']) ? intval($_POST['selected_carrousel']) : null;

    // Récupérer tous les carrousels existants pour la liste déroulante
    $all_carrousels = $wpdb->get_results("SELECT * FROM $carrousel_table_name");


    // Traiter le formulaire du nom du carrousel
    if (isset($_POST['submit_carrousel_name'])) {
        $carrousel_name = sanitize_text_field($_POST['carrousel_name']);

        $wpdb->insert(
            $carrousel_table_name,
            array('name' => $carrousel_name),
            array('%s')
        );

        $carrousel_id = $wpdb->insert_id;

        echo '<div class="notice notice-success"><p>Carrousel créé avec succès ! Voici votre shortcode: </p>';
        echo '<code>[custom_carrousel id="' . $carrousel_id . '"]</code></div>'; // Affiche le shortcode
    }

    // Traiter le formulaire du slide
    if (isset($_POST['submit_slide'])) {
        $image_url = sanitize_text_field($_POST['image_url']);
        $title = sanitize_text_field($_POST['title']);
        $description = sanitize_text_field($_POST['description']);
        $link_url = sanitize_text_field($_POST['link_url']);
        $carrousel_id = intval($_POST['carrousel_id']);

        // Insérer les données du slide dans la base de données
        $wpdb->insert(
            $slides_table_name,
            array(
                'carrousel_id' => $carrousel_id,
                'image_url' => $image_url,
                'title' => $title,
                'description' => $description,
                'link_url' => $link_url
            ),
            array('%d', '%s', '%s', '%s', '%s')
        );

        echo '<div class="notice notice-success"><p>Slide ajouté avec succès!</p></div>';
    }

    // Traiter la suppression du carrousel
    if (isset($_POST['delete_carrousel']) && isset($_POST['selected_carrousel'])) {
        $selected_carrousel = intval($_POST['selected_carrousel']);
        $wpdb->delete($carrousel_table_name, array('carrousel_id' => $selected_carrousel), array('%d'));
        echo '<div class="notice notice-success"><p>Carrousel supprimé avec succès!</p></div>';
    }

    // Si l'utilisateur clique sur "Ajouter", la variable $carrousel_id est mise à jour
    if (isset($_POST['edit_carrousel']) && isset($_POST['selected_carrousel'])) {
        $carrousel_id = intval($_POST['selected_carrousel']);
    }

    // Formulaire pour sélectionner un carrousel existant
    echo '<h2>Choisir le carrousel</h2>
    <form method="post" action="">
        <select name="selected_carrousel">';

    // Si aucun carrousel n'est sélectionné, affichez l'option "Choisir le carrousel" comme étant la valeur par défaut.
    if (!$carrousel_id) {
        echo '<option value="" selected="selected">Choisir le carrousel</option>';
    } else {
        echo '<option value="">Choisir le carrousel</option>';
    }

    foreach ($all_carrousels as $carrousel) {
        $selected = ($carrousel_id == $carrousel->carrousel_id) ? 'selected="selected"' : '';
        echo '<option value="' . esc_attr($carrousel->carrousel_id) . '" ' . $selected . '>' . esc_html($carrousel->name) . '</option>';
    }

    echo '</select>
    <input type="submit" name="edit_carrousel" class="button" value="Ajouter">
    <input type="submit" name="delete_carrousel" class="button" value="Supprimer">
    </form>';

    echo '<div class="wrap">';

    // Afficher le formulaire approprié (slide ou carrousel) en fonction du contexte       
    // Si le nom du carrousel est défini, afficher le formulaire du slide
    if ($carrousel_id) {
?>
        <div class="wrap">
            <h2>Ajouter un élément de carrousel</h2>
            <form method="post" action="">
                <input type="hidden" name="carrousel_id" value="<?php echo $carrousel_id; ?>">
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="image_url">URL de l'image</label></th>
                        <td><input type="text" name="image_url" id="image_url" class="regular-text"></td>
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
                    <input type="submit" name="submit_slide" id="submit_slide" class="button button-primary" value="Ajouter">
                </p>
            </form>
        </div>
    <?php
    } else {
    ?>
        <div class="wrap">
            <h2>Créer un nouveau carrousel</h2>
            <form method="post" action="">
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="carrousel_name">Nom du carrousel</label></th>
                        <td><input type="text" name="carrousel_name" id="carrousel_name" class="regular-text" required></td>
                    </tr>
                </table>
                <p class="submit">
                    <input type="submit" name="submit_carrousel_name" id="submit_carrousel_name" class="button button-primary" value="Créer">
                </p>
            </form>
        </div>
<?php
    }
}
