<?php
// Inclure les fichiers CSS/JS
function custom_carrousel_enqueue_assets()
{
    // Pour l'admin
    if (is_admin()) {
        wp_enqueue_style('custom-carrousel-admin-styles', esc_url(plugin_dir_url(__FILE__)) . 'css/style-admin.css');
        wp_enqueue_script('custom-carrousel-admin-scripts', esc_url(plugin_dir_url(__FILE__)) . 'js/script-carrousel-admin.js', array('jquery'), '1.0', true);
    }

    // Pour le front-end
    else {
        wp_enqueue_style('custom-carrousel-styles', esc_url(plugin_dir_url(__FILE__)) . 'css/styles.css');
        wp_enqueue_script('custom-carrousel-scripts', esc_url(plugin_dir_url(__FILE__)) . 'js/script-carrousel.js', array(), '1.0', true);
    }
}
add_action('admin_enqueue_scripts', 'custom_carrousel_enqueue_assets');
add_action('wp_enqueue_scripts', 'custom_carrousel_enqueue_assets');


// CREATION DES TABLES LORS DE L'ACTIVATION DU PLUGIN
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


/**
 * GENERE HTML DU CARROUSEL PERSONNALISE DEPUIS LA BDD
 * Cette fonction est destinée à être utilisée comme un shortcode dans WordPress.
 * Elle récupère les diapositives associées à un ID de carrousel spécifique
 * et retourne le code HTML correspondant pour afficher le carrousel.
 */
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

/***GESTIONNAIRE DE CARROUSELS PERSONNALISES POUR L'ADMIN WP ***/
/*Fonction gérant le formulaire de création du carrousel */
function display_form_create_carrousel()
{
    include(plugin_dir_path(__FILE__) . 'views/form_create_carrousel.php');
}

/*Fonction gérant le formulaire d'ajout de slide */
function display_form_add_slide($carrousel_id)
{
    extract(array('carrousel_id' => $carrousel_id));
    include(plugin_dir_path(__FILE__) . 'views/form_add_slide.php');
}


/** Cette fonction sert à gérer des carrousels personnalisés dans WordPress.
 * Elle permet de créer, modifier et supprimer des carrousels personnalisés.
 * Elle permet également de créer, modifier et supprimer des diapositives de carrousel personnalisées.**/
function custom_link_carrousel_page()
{
    echo '<h1>Gestionnaire de carrousels personnalisés</h1>';
    echo '<p>Permet de créer facilement des <strong>carrousels personnalisés</strong> pour votre site. <br> Un <strong>carrousel</strong> est un diaporama offrant une présentation dynamique de plusieurs éléments. <br> Les <strong>slides</strong> sont les pages de ce diaporama contenant les informations.</p>';

    global $wpdb;
    $selected_carrousel_name = '';
    $carrousel_table_name = $wpdb->prefix . 'custom_carrousels';
    $slides_table_name = $wpdb->prefix . 'custom_carrousel_slides';

    // Initialisez $carrousel_id avec la valeur du carrousel sélectionné, si disponible
    $carrousel_id = isset($_POST['selected_carrousel']) ? intval($_POST['selected_carrousel']) : null;

    // Récupérer tous les carrousels existants pour la liste déroulante
    $all_carrousels = $wpdb->get_results("SELECT * FROM $carrousel_table_name");

    // Traiter le formulaire du nom du carrousel
    if (isset($_POST['submit_carrousel_name']) && check_admin_referer('create_carrousel_action', 'create_carrousel_nonce')) {
        $carrousel_name = sanitize_text_field($_POST['carrousel_name']);

        $wpdb->insert(
            $carrousel_table_name,
            array('name' => $carrousel_name),
            array('%s')
        );

        $carrousel_id = $wpdb->insert_id;

        echo '<div class="notice notice-success"><p>Carrousel créé avec succès ! Voici votre shortcode: </p>';
        echo '<code>[custom_carrousel id="' . $carrousel_id . '"]</code></div>'; // Affiche le shortcode
        $selected_carrousel_name = $carrousel_name;  // Utiliser le nom du carrousel directement après sa création.

    }

    // Afficher le formulaire approprié (slide ou carrousel) en fonction du contexte       
    // Si le nom du carrousel est défini, afficher le formulaire du slide
    if ($carrousel_id && !isset($_POST['modify_carrousel']) && !isset($_POST['delete_carrousel'])) {
        display_form_add_slide($carrousel_id);
    } elseif (!isset($_POST['edit_carrousel']) && !isset($_POST['modify_carrousel'])) {
        display_form_create_carrousel();
    }

    // Traiter le formulaire du slide
    if (isset($_POST['submit_slide']) && check_admin_referer('add_slide_action', 'add_slide_nonce')) {
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

    // Formulaire pour sélectionner un carrousel existant
    echo '<h2>Choisir le carrousel</h2>
        <form method="post" action="">
            <select name="selected_carrousel" id="carrouselSelect">';

    // Si aucun carrousel n'est sélectionné, affiche l'option "Choisir le carrousel" comme étant la valeur par défaut.
    if (!$carrousel_id) {
        echo '<option value="" selected="selected" data-default="true">Choisir le carrousel</option>';
    } else {
        echo '<option value="" data-default="true">Choisir le carrousel</option>';
    }

    // Parcourir tous les carrousels disponibles et les afficher comme options dans le menu déroulant.
    foreach ($all_carrousels as $carrousel) {
        $selected = ($carrousel_id == $carrousel->carrousel_id) ? 'selected="selected"' : '';
        echo '<option value="' . esc_attr($carrousel->carrousel_id) . '" ' . $selected . '>' . esc_html($carrousel->name) . '</option>';
    }

    // Si un carrousel est sélectionné, récupérez ses données de la base de données.
    if ($carrousel_id) {
        $carrousel_data = $wpdb->get_row("SELECT * FROM $carrousel_table_name WHERE carrousel_id = $carrousel_id");
        // Si les données pour le carrousel sélectionné sont récupérées avec succès, stockez son nom pour une utilisation ultérieure.
        if ($carrousel_data) {
            $selected_carrousel_name = $carrousel_data->name;
        }
    }

    echo '</select>
        <input type="submit" name="edit_carrousel" class="button" id="editCarrouselButton" value="Ajouter">
        <input type="submit" name="modify_carrousel" class="button" id="modifyCarrouselButton" value="Modifier">
        <input type="submit" name="delete_carrousel" class="button" id="deleteCarrouselButton" value="Supprimer">
        </form>';

    // Traiter la suppression du carrousel
    if (isset($_POST['delete_carrousel']) && isset($_POST['selected_carrousel'])) {
        $selected_carrousel = intval($_POST['selected_carrousel']);

        // Récupérer le carrousel avant de le supprimer pour avoir son nom
        $carrousel = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}custom_carrousels WHERE carrousel_id = $selected_carrousel");

        $wpdb->delete($carrousel_table_name, array('carrousel_id' => $selected_carrousel), array('%d'));

        if ($carrousel && isset($carrousel->name)) {
            echo '<div class="notice notice-success"><p>Carrousel <strong>' . esc_html($carrousel->name) . '</strong> supprimé avec succès.</p></div>';
        } else {
            echo '<div class="notice notice-error"><p>Erreur lors de la récupération du nom du carrousel !</p></div>';
        }
    }

    // Si l'utilisateur clique sur "Ajouter", la variable $carrousel_id est mise à jour
    if (isset($_POST['edit_carrousel']) && isset($_POST['selected_carrousel'])) {
        $carrousel_id = intval($_POST['selected_carrousel']);
    }

    $slide_counter = 1;

    // Si l'utilisateur clique sur "Modifier", la liste des éléments présents dans le carrousel s'affiche
    if (isset($_POST['modify_carrousel']) && isset($_POST['selected_carrousel'])) {
        $carrousel_id = intval($_POST['selected_carrousel']);
        
        $slides = $wpdb->get_results($wpdb->prepare("SELECT * FROM $slides_table_name WHERE carrousel_id = %d", $carrousel_id));

        echo '<h3>Modification des slides du carrousel : ' . esc_html($selected_carrousel_name) . '</h3>';
        echo '<div class="slides-grid">';

        foreach ($slides as $slide) {
            echo '<form method="post" action="">';
            echo '<div class="item">';
            echo '<h3>Slide ' . $slide_counter . '</h3>';

            echo '<p class="slide-data-label"><strong>Titre :</strong></p>';
            echo '<input type="text" name="title" value="' . esc_attr($slide->title) . '">';

            echo '<p class="slide-data-label"><strong>Image (URL) :</strong></p>';
            echo '<input type="text" name="image_url" value="' . esc_url($slide->image_url) . '">';

            echo '<p class="slide-data-label"><strong>Description :</strong></p>';
            echo '<textarea name="description">' . esc_html($slide->description) . '</textarea>';

            echo '<p class="slide-data-label"><strong>URL :</strong></p>';
            echo '<input type="text" name="link_url" value="' . esc_url($slide->link_url) . '">';

            echo '<input type="hidden" name="id" value="' . intval($slide->id) . '">';
            echo '<input type="hidden" name="carrousel_id" value="' . intval($carrousel_id) . '">';

            echo '<p class="slide-data-label"><strong>Action :</strong></p>';
            echo '<input type="submit" name="update_slide" value="Mettre à jour">'; // Bouton pour enregistrer les modifications
            echo '</div>'; // Fermeture du div "item"
            echo '</form>';
            $slide_counter++;
        }
        echo '</div>'; // Fermeture du div "slides-grid"   
    }

    // Code pour traiter le formulaire de mise à jour du slide
    if (isset($_POST['update_slide']) && isset($_POST['id'])) {
        $id = intval($_POST['id']);
        $image_url = sanitize_text_field($_POST['image_url']);
        $title = sanitize_text_field($_POST['title']);
        $description = sanitize_text_field($_POST['description']);
        $link_url = sanitize_text_field($_POST['link_url']);

        // Mettre à jour les données du slide dans la base de données
        $wpdb->update(
            $slides_table_name,
            array(
                'image_url' => $image_url,
                'title' => $title,
                'description' => $description,
                'link_url' => $link_url
            ),
            array('id' => $id),
            array('%s', '%s', '%s', '%s'),
            array('%d')
        );

        echo '<div class="notice notice-success"><p>Slide mis à jour avec succès!</p></div>';
    }
}