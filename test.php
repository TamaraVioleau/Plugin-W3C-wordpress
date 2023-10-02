function custom_link_carrousel_page()
{
echo '<h1>Gestionnaire de carrousels personnalisés</h1>';
echo '<p>Permet de créer facilement des <strong>carrousels personnalisés</strong> pour votre site. <br> Un <strong>carrousel</strong> est un diaporama offrant une présentation dynamique de plusieurs éléments. <br> Les <strong>slides</strong> sont les pages de ce diaporama contenant les informations.</p>';

$active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'create_carrousel';

echo '<div class="wrap">';
  echo '<h2 class="nav-tab-wrapper">';
    echo '<a href="?page=custom_carrousel&tab=create_carrousel" class="nav-tab ' . ($active_tab == 'create_carrousel' ? 'nav-tab-active' : '') . '">Créer un carrousel</a>';
    echo '<a href="?page=custom_carrousel&tab=choose_carrousel" class="nav-tab ' . ($active_tab == 'choose_carrousel' ? 'nav-tab-active' : '') . '">Choisir le carrousel</a>';
    echo '</h2>';

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

  // Traiter le formulaire du nom du carrousel
  if (isset($_POST['submit_carrousel_name']) && check_admin_referer('create_carrousel_action', 'create_carrousel_nonce')) {
  $carrousel_name = sanitize_text_field($_POST['carrousel_name']);

  $wpdb->insert(
  $carrousel_table_name,
  array('name' => $carrousel_name),
  array('%s')
  );

  $carrousel_id = $wpdb->insert_id;

  echo '<div class="notice notice-success">
    <p>Carrousel créé avec succès ! Voici votre shortcode: </p>';
    echo '<code>[custom_carrousel id="' . $carrousel_id . '"]</code>
  </div>'; // Affiche le shortcode
  $selected_carrousel_name = $carrousel_name; // Utiliser le nom du carrousel directement après sa création.
  }

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

  echo '<div class="notice notice-success">
    <p>Slide ajouté avec succès!</p>
  </div>';
  }
  }
  //Si on clique sur onglet choisir le carrousel
  elseif ($active_tab == 'choose_carrousel') {
  // Afficher le menu déroulant pour choisir un carrousel existant
  display_select_carrousel_dropdown($carrousel_id, $all_carrousels);

  if ($carrousel_id && !isset($_POST['modify_carrousel']) && !isset($_POST['delete_carrousel'])) {
  display_form_add_slide($carrousel_id);
  }

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

  echo '<div class="notice notice-success">
    <p>Slide ajouté avec succès!</p>
  </div>';
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

  echo '<div class="notice notice-success">
    <p>Slide <strong>"' . esc_html($title) . '"</strong> mis à jour avec succès!</p>
  </div>';
  }

  // Traiter la suppression du carrousel
  if (isset($_POST['delete_carrousel']) && isset($_POST['selected_carrousel'])) {
  $selected_carrousel = intval($_POST['selected_carrousel']);
  $carrousel = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}custom_carrousels WHERE carrousel_id = $selected_carrousel");
  deleteCarrousel($selected_carrousel, $carrousel_table_name);

  if ($carrousel && isset($carrousel->name)) {
  echo '<div class="notice notice-success">
    <p>Carrousel <strong>' . esc_html($carrousel->name) . '</strong> supprimé avec succès.</p>
  </div>';
  } else {
  echo '<div class="notice notice-error">
    <p>Erreur lors de la récupération du nom du carrousel !</p>
  </div>';
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
    echo '<form method="post" action="" class="slide-form">';
      foreach ($slides as $slide) {

      echo '<div class="item">';

        echo '<div class="slide-header">';
          echo '<input type="checkbox" name="selected_slides[]" value="' . intval($slide->id) . '">';
          echo '<h3>Slide ' . $slide_counter . '</h3>';
          echo '</div>';

        echo '<label for="title-' . $slide_counter . '" class="slide-data-label"><strong>Titre :</strong></label>';
        echo '<input id="title-' . $slide_counter . '" type="text" name="title" class="input_slides" value="' . esc_attr($slide->title) . '">';

        echo '<div class="more-details" style="display:none;">';

          echo '<label for="image_url-' . $slide_counter . '" class="slide-data-label"><strong>Image (URL) :</strong></label>';
          echo '<input id="image_url-' . $slide_counter . '" type="text" name="image_url" class="input_slides" value="' . esc_url($slide->image_url) . '">';

          echo '<label for="description-' . $slide_counter . '" class="slide-data-label"><strong>Description :</strong></label>';
          echo '<textarea id="description-' . $slide_counter . '" name="description" class="textarea_slides">' . esc_html($slide->description) . '</textarea>';

          echo '<label for="link_url-' . $slide_counter . '" class="slide-data-label"><strong>URL :</strong></label>';
          echo '<input id="link_url-' . $slide_counter . '" type="text" name="link_url" class="input_slides" value="' . esc_url($slide->link_url) . '">';

          echo '</div>';

        echo '<button type="button" onclick="toggleDetails(this)">Voir plus de détails</button>';
        echo '<input type="hidden" name="id" value="' . intval($slide->id) . '">';
        echo '<input type="hidden" name="carrousel_id" value="' . intval($carrousel_id) . '">';
        echo '<input type="submit" name="update_slide" value="Mettre à jour">';
        echo '</div>';

      $slide_counter++;
      }
      echo '<input type="submit" id="deleteButton" name="delete_selected_slides" value="Supprimer les slides sélectionnées" class="delete_selected_slides" disabled>';
      echo '</form>';
    echo '</div>';
  }
  }

  //Suppression multiple des slides au sein d'un carrousel
  if (isset($_POST['delete_selected_slides']) && isset($_POST['selected_slides'])) {
  $selected_slide_ids = $_POST['selected_slides'];
  $deleted_count = 0;

  foreach ($selected_slide_ids as $slide_id) {
  deleteSlide(intval($slide_id));
  $deleted_count++;
  }

  if ($deleted_count > 0) {
  echo '<div class="notice notice-success">
    <p>' . $deleted_count . ' slide(s) supprimée(s) avec succès.</p>
  </div>';
  } else {
  echo '<div class="notice notice-error">
    <p>Aucune slide n\'a été supprimée.</p>
  </div>';
  }
  }
  }