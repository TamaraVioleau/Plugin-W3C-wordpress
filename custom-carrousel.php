<?php
// Inclure les fichiers CSS/JS
function custom_carrousel_enqueue_assets()
{
    // Pour l'admin
    if (is_admin()) {
        wp_enqueue_style('custom-carrousel-admin-styles', esc_url(plugin_dir_url(__FILE__)) . 'css/style-admin.css');
        wp_enqueue_script('custom-carrousel-admin-scripts', esc_url(plugin_dir_url(__FILE__)) . 'js/script-carrousel-admin.js', array('jquery'), '1.0', true);
        wp_enqueue_script('custom-tab-admin-scripts', esc_url(plugin_dir_url(__FILE__)) . 'js/script-tab.js', array('jquery'), '1.0', true);
    }

    // Pour le front-end
    else {
        wp_enqueue_style('custom-carrousel-styles', esc_url(plugin_dir_url(__FILE__)) . 'css/styles.css');
        wp_enqueue_script('custom-carrousel-scripts', esc_url(plugin_dir_url(__FILE__)) . 'js/script-carrousel.js', array(), '1.0', true);
    }
}
add_action('admin_enqueue_scripts', 'custom_carrousel_enqueue_assets');
add_action('wp_enqueue_scripts', 'custom_carrousel_enqueue_assets');

// Fonctions pour insérer le bouton de la médiathèque wordpress
function wp_gear_manager_admin_scripts()
{
    wp_enqueue_media(); // Cela inclut tout ce qui est nécessaire pour la médiathèque moderne.
    wp_enqueue_script('form_add_slide');
}

add_action('admin_enqueue_scripts', 'wp_gear_manager_admin_scripts');


function wp_gear_manager_admin_styles()
{
    wp_enqueue_style('thickbox');
}

add_action('admin_print_scripts', 'wp_gear_manager_admin_scripts');
add_action('admin_print_styles', 'wp_gear_manager_admin_styles');

//Inclure le fichier CRUD dans le fichier principal
require 'crud_functions_carrousel.php';

function insert_into_table($table_name, $data, $format)
{
    global $wpdb;
    $wpdb->insert($table_name, $data, $format);
    return $wpdb->insert_id;
}

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

/*Fonction gérant le menu déroulant de sélection d'un carrousel*/
function display_select_carrousel_dropdown($carrousel_id, $all_carrousels)
{
    extract(array(
        'carrousel_id' => $carrousel_id,
        'all_carrousels' => $all_carrousels
    ));
    include(plugin_dir_path(__FILE__) . 'views/select_carrousel_dropdown.php');
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

    $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'create_carrousel';

    echo '<div class="wrap">';
    echo '<div class="nav-tab-wrapper">';
    echo '<div role="tablist" aria-labelledby="tablist-1" class="manual">';

    echo '<a id="tab-create" href="?page=custom_carrousel&tab=create_carrousel" role="tab" aria-controls="tabpanel-create" aria-selected="' . ($active_tab == 'create_carrousel' ? 'true' : 'false') . '" class="nav-tab ' . ($active_tab == 'create_carrousel' ? 'nav-tab-active' : '') . '">Créer un carrousel</a>';
    echo '<a id="tab-choose" href="?page=custom_carrousel&tab=choose_carrousel" role="tab" aria-controls="tabpanel-choose" aria-selected="' . ($active_tab == 'choose_carrousel' ? 'true' : 'false') . '" class="nav-tab ' . ($active_tab == 'choose_carrousel' ? 'nav-tab-active' : '') . '">Modifier le carrousel</a>';
    echo '</div>';
    echo '</div>';

    global $wpdb;
    $selected_carrousel_name = '';
    $carrousel_table_name = $wpdb->prefix . 'custom_carrousels';
    $slides_table_name = $wpdb->prefix . 'custom_carrousel_slides';
    $carrousel_id = isset($_POST['selected_carrousel']) ? intval($_POST['selected_carrousel']) : null;
    $all_carrousels = $wpdb->get_results("SELECT * FROM $carrousel_table_name ORDER BY name ASC");

    //Si on clique sur onglet créer un carrousel
    if ($active_tab == 'create_carrousel') {
        // Afficher le formulaire de création de carrousel
        display_form_create_carrousel();

        // Traiter le du nom du carrousel vers la base de données et afficher le shortcode correspondant à l'ID du carrousel créé
        createCarrousel($wpdb, $carrousel_table_name);

        // Afficher le formulaire approprié (slide ou carrousel) en fonction du contexte       
        if ($carrousel_id && !isset($_POST['modify_carrousel']) && !isset($_POST['delete_carrousel'])) {
            display_form_add_slide($carrousel_id);
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
            echo '<div class="notice notice-success"><p>Slide "' . esc_html($title) . '" ajouté avec succès !</p></div>';
        }
    }
    //Si on clique sur onglet choisir le carrousel
    elseif ($active_tab == 'choose_carrousel') {
        // Afficher le menu déroulant pour choisir un carrousel existant
        display_select_carrousel_dropdown($carrousel_id, $all_carrousels);

        // Mettre à jour le nom du carrousel sélectionné
        if (isset($_POST['edit_carrousel']) && isset($_POST['selected_carrousel'])) {
            $carrousel_id = intval($_POST['selected_carrousel']);
        }

        /** SUPPRESSION D'UN CARROUSEL **/
        // Si je clique sur le bouton supprimer le carrousel, le carrousel est supprimé
        if (isset($_POST['delete_carrousel']) && isset($_POST['selected_carrousel'])) {
            $selected_carrousel = intval($_POST['selected_carrousel']);
            $carrousel = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}custom_carrousels WHERE carrousel_id = $selected_carrousel");
            deleteCarrousel($selected_carrousel);

            if ($carrousel && isset($carrousel->name)) {
                echo '<div class="notice notice-success"><p>Carrousel <strong>' . esc_html($carrousel->name) . '</strong> supprimé avec succès.</p></div>';
            } else {
                echo '<div class="notice notice-error"><p>Erreur lors de la récupération du nom du carrousel !</p></div>';
            }
        }

        /** AJOUT D'UNE NOUVELLE SLIDE **/
        //Affiche le formulaire de création d'une slide quand je clique sur le bouton "Ajouter une slide"
        if ($carrousel_id && !isset($_POST['modify_carrousel']) && !isset($_POST['delete_carrousel'])) {
            display_form_add_slide($carrousel_id);
        }

        // Gestion de la soumission du formulaire de création de slide (ajout d'une slide)
        createSlide($carrousel_id, $wpdb, $slides_table_name);

        /** MODIFICATION DES SLIDES **/
        //Modification d'une slide existante
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

            echo '<div class="notice notice-success"><p>Slide <strong>"' . esc_html($title) . '"</strong> mis à jour avec succès!</p></div>';
        }

        // Affichage et modification des slides existantes à l'aide d'un formulaire
        $slide_counter = 1;
        if (isset($_POST['modify_carrousel']) && isset($_POST['selected_carrousel'])) {
            $carrousel_id = intval($_POST['selected_carrousel']);

            $slides = $wpdb->get_results($wpdb->prepare("SELECT * FROM $slides_table_name WHERE carrousel_id = %d", $carrousel_id));

            echo '<h3>Modification des slides du carrousel : ' . esc_html($selected_carrousel_name) . '</h3>';
            echo '<div class="slides-grid">';
            echo '<form method="post" action="" class="slide-form">';
            foreach ($slides as $slide) {

                echo '<div class="item">';

                echo '<div class="slide-header">';
                echo '<input type="checkbox" name="selected_slides[]" value="' . intval($slide->id) . '">';
                echo '<h3>Slide ' . $slide_counter . '</h3>';
                echo '</div>';

                echo '<label for="title-' . $slide_counter . '" class="slide-data-label"><strong>Titre :</strong></label>';
                echo '<input id="title-' . $slide_counter . '" type="text" name="titles[]" class="input_slides" value="' . esc_attr($slide->title) . '">';

                echo '<div class="more-details" style="display:none;">';

                echo '<label for="image_url-' . $slide_counter . '" class="slide-data-label"><strong>Image (URL) :</strong></label>';
                echo '<input id="image_url-' . $slide_counter . '" type="text" name="image_urls[]" class="input_slides" value="' . esc_url($slide->image_url) . '">';

                echo '<label for="description-' . $slide_counter . '" class="slide-data-label"><strong>Description :</strong></label>';
                echo '<textarea id="description-' . $slide_counter . '" name="descriptions[]" class="textarea_slides">' . esc_html($slide->description) . '</textarea>';

                echo '<label for="link_url-' . $slide_counter . '" class="slide-data-label"><strong>URL :</strong></label>';
                echo '<input id="link_url-' . $slide_counter . '" type="text" name="link_urls[]" class="input_slides" value="' . esc_url($slide->link_url) . '">';

                echo '</div>';

                echo '<button type="button" onclick="toggleDetails(this)">Voir plus de détails</button>';
                echo '<input type="hidden" name="id[]" value="' . intval($slide->id) . '">';
                echo '<input type="hidden" name="carrousel_id" value="' . intval($carrousel_id) . '">';

                echo '</div>';

                $slide_counter++;
            }
            echo '<input type="submit" name="update_multiple_slides" value="Mettre à jour les slides">';
            echo '<input type="submit" id="deleteButton" name="delete_selected_slides" value="Supprimer les slides sélectionnées" class="delete_selected_slides" disabled>';
            echo '</form>';
            echo '</div>';
        }
    }

    //Suppression multiple des slides au sein d'un carrousel
    if (isset($_POST['delete_selected_slides']) && isset($_POST['selected_slides'])) {
        $selected_slide_id = $_POST['selected_slides'];
        $deleted_count = 0;

        foreach ($selected_slide_id as $slide_id) {
            deleteSlide(intval($slide_id));
            $deleted_count++;
        }

        if ($deleted_count > 0) {
            echo '<div class="notice notice-success"><p>' . $deleted_count . ' slide(s) supprimée(s) avec succès.</p></div>';
        } else {
            echo '<div class="notice notice-error"><p>Aucune slide n\'a été supprimée.</p></div>';
        }
    }

    if (isset($_POST['update_multiple_slides'])) {
        updateMultipleSlides($_POST, $wpdb, $slides_table_name);
    }
}
