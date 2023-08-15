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


// Créer les tables lors de l'activation du plugin
function custom_carrousel_create_table()
{
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // Table pour les carrousels
    $sql_carrousel = "CREATE TABLE {$wpdb->prefix}custom_carrousels (
        carrousel_id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        UNIQUE KEY carrousel_id (carrousel_id)
    ) $charset_collate;";

    // Table pour les slides
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

// Fonction pour créer un shortcode permettant d'afficher le carrousel
function custom_carrousel_shortcode($atts)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'custom_carrousel_slides';

 // Récupérer l'ID du carrousel depuis les attributs du shortcode
 $atts = shortcode_atts( array('id' => 0), $atts, 'custom_carrousel' );
 $carrousel_id = intval($atts['id']);

 if (!$carrousel_id) return 'ID de carrousel non spécifié ou invalide.';

 // Récupérer toutes les entrées de la table custom_carrousel_slides filtrées par l'ID du carrousel
 $items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE carrousel_id = %d", $carrousel_id));

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

// Partie ajoutée: fonction pour créer un carrousel
function custom_carrousel_create()
{
    global $wpdb;
    $carrousel_table = $wpdb->prefix . 'custom_carrousels';

    if (isset($_POST['create_carrousel'])) {
        $carrousel_name = sanitize_text_field($_POST['carrousel_name']);
        $wpdb->insert($carrousel_table, array('name' => $carrousel_name), array('%s'));
        add_action('admin_notices', function() {
            echo '<div class="notice notice-success"><p>Carrousel créé avec succès!</p></div>';
        });
            }
}

// Fonction pour afficher le contenu de la page d'administration du carrousel
function custom_link_carrousel_page()
{
    global $wpdb;
    $carrousel_table_name = $wpdb->prefix . 'custom_carrousels';
    $slides_table_name = $wpdb->prefix . 'custom_carrousel_slides';
    $carrousel_id = null;

    // Traiter le formulaire du nom du carrousel si les données sont envoyées
    if (isset($_POST['submit_carrousel_name'])) {
        $carrousel_name = sanitize_text_field($_POST['carrousel_name']);
    
        // Insérer le nom dans la base de données
        $wpdb->insert(
            $carrousel_table_name,
            array('name' => $carrousel_name),
            array('%s')
        );
    
        $carrousel_id = $wpdb->insert_id; // Récupère l'ID du carrousel nouvellement inséré
    
        echo '<div class="notice notice-success"><p>Carrousel créé avec succès! Voici votre shortcode: </p>';
        echo '<code>[custom_carrousel id="' . $carrousel_id . '"]</code></div>'; // Affiche le shortcode
    }
    

    // Traiter le formulaire du slide si les données sont envoyées
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

    if ($carrousel_id) {
        // Si le nom du carrousel est défini, afficher le formulaire du slide
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
        // Sinon, afficher le formulaire du nom du carrousel
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

// Afficher la liste des slides pour chaque carrousel
function custom_carrousel_list_page() {
    global $wpdb;
    $slides_table_name = $wpdb->prefix . 'custom_carrousel_slides';
    $carrousel_table_name = $wpdb->prefix . 'custom_carrousels';

    // Traitement de la suppression
    if (isset($_GET['delete_slide']) && isset($_GET['slide_id'])) {
        $slide_id = intval($_GET['slide_id']);
        $wpdb->delete($slides_table_name, array('id' => $slide_id));
        echo '<div class="notice notice-success"><p>Slide supprimé avec succès!</p></div>';
    }

    // Récupérer tous les carrousels
    $carrousels = $wpdb->get_results("SELECT * FROM $carrousel_table_name");

    foreach ($carrousels as $carrousel) {
        echo '<h2>Slides pour le carrousel: ' . esc_html($carrousel->name) . '</h2>';
        // Récupérer les slides pour ce carrousel
        $slides = $wpdb->get_results($wpdb->prepare("SELECT * FROM $slides_table_name WHERE carrousel_id = %d", $carrousel->carrousel_id));

        echo '<table class="wp-list-table widefat fixed striped table-view-list">';
        echo '<thead>
                <tr>
                    <th>ID</th>
                    <th>Image URL</th>
                    <th>Titre</th>
                    <th>Description</th>
                    <th>URL du lien</th>
                    <th>Actions</th>
                </tr>
              </thead>
              <tbody>';
        
        foreach ($slides as $slide) {
            echo '<tr>
                    <td>' . esc_html($slide->id) . '</td>
                    <td><img src="' . esc_url($slide->image_url) . '" width="50"></td>
                    <td>' . esc_html($slide->title) . '</td>
                    <td>' . esc_html($slide->description) . '</td>
                    <td>' . esc_url($slide->link_url) . '</td>
                    <td>
                        <a href="?page=custom_carrousel_list_page&delete_slide=true&slide_id=' . intval($slide->id) . '" onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer cette slide?\')">Supprimer</a>
                    </td>
                  </tr>';
        }

        echo '</tbody></table><br>';
    }
}

// Ajouter une page dans la zone d'administration pour afficher les slides
function custom_carrousel_admin_menu() {
    add_menu_page(
        'Gérer les Carrousels', 
        'Gérer les Carrousels', 
        'manage_options', 
        'custom_carrousel_list_page', 
        'custom_carrousel_list_page', 
        'dashicons-slides'
    );
}

add_action('admin_menu', 'custom_carrousel_admin_menu');
