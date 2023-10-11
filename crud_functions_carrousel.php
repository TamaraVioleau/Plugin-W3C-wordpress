<?php

// Create a new carrousel with the shortcode
function createCarrousel($wpdb, $carrousel_table_name)
{
    $carrousel_id = null;  // Initialise à null

    if (isset($_POST['submit_carrousel_name']) && check_admin_referer('create_carrousel_action', 'create_carrousel_nonce')) {
        $carrousel_name = sanitize_text_field($_POST['carrousel_name']);

        // Insérer les données du carrousel dans la base de données
        $wpdb->insert(
            $carrousel_table_name,
            array('name' => $carrousel_name),
            array('%s')
        );

        $carrousel_id = $wpdb->insert_id;

        echo '<div class="notice notice-success"><p>Carrousel créé avec succès ! Voici votre shortcode: </p>';
        echo '<code>[custom_carrousel id="' . $carrousel_id . '"]</code></div>';
    }
    return $carrousel_id;  // Retourne l'ID du nouveau carrousel

}

// Create a new slide
function createSlide($carrousel_id, $wpdb, $slides_table_name)
{
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

        echo '<div class="notice notice-success"><p>Slide "' . esc_html($title) . '" ajouté avec succès!</p></div>';
    }
}


// Update a carrousel
function updateCarrousel()
{
}

// Update slides
function updateMultipleSlides($postData, $wpdb, $slides_table_name)
{
    // Vérification de la présence des clés nécessaires dans $postData
    if (isset($postData['update_multiple_slides'])) {
        $ids = $postData['id'];
        $titles = $postData['titles'];
        $image_urls = $postData['image_urls'];
        $descriptions = $postData['descriptions'];
        $link_urls = $postData['link_urls'];

        // Parcours de chaque slide pour la mise à jour
        foreach ($ids as $index => $id) {
            // Sanitisation et conversion des données
            $id = intval($id);
            $title = sanitize_text_field($titles[$index]);
            $image_url = sanitize_text_field($image_urls[$index]);
            $description = sanitize_text_field($descriptions[$index]);
            $link_url = sanitize_text_field($link_urls[$index]);

            // Mise à jour de la slide dans la base de données
            $wpdb->update(
                $slides_table_name,
                array(
                    'title' => $title,
                    'image_url' => $image_url,
                    'description' => $description,
                    'link_url' => $link_url
                ),
                array('id' => $id),
                array('%s', '%s', '%s', '%s'),  // types de données
                array('%d')  // type de données pour l'ID
            );
        }

        echo '<div class="notice notice-success"><p>Slides mises à jour avec succès!</p></div>';
    }
}


// Delete a carrousel
function deleteCarrousel($id)
{
    global $wpdb;
    $carrousel_table_name = $wpdb->prefix . 'custom_carrousels';
    $slides_table_name = $wpdb->prefix . 'custom_carrousel_slides';

    // Supprimer les slides associées au carrousel
    $wpdb->delete($slides_table_name, array('carrousel_id' => $id), array('%d'));

    // Supprimer le carrousel lui-même
    $wpdb->delete($carrousel_table_name, array('carrousel_id' => $id), array('%d'));
}

// Delete a slide
function deleteSlide($id)
{
    global $wpdb;
    $slides_table_name = $wpdb->prefix . 'custom_carrousel_slides';
    $wpdb->delete($slides_table_name, array('id' => $id), array('%d'));
}
