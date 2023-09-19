<?php

// Create a new carrousel
function createCarrousel($name)
{
    global $wpdb, $carrousel_table_name;
    $wpdb->insert($carrousel_table_name, array('name' => $name), array('%s'));
    return $wpdb->insert_id;
}

// Create a new slide
function createSlide($carrouselId, $imageUrl, $title, $description, $linkUrl)
{
    global $wpdb, $slides_table_name;
    $wpdb->insert(
        $slides_table_name,
        array(
            'carrousel_id' => $carrouselId,
            'image_url' => $imageUrl,
            'title' => $title,
            'description' => $description,
            'link_url' => $linkUrl
        ),
        array('%d', '%s', '%s', '%s', '%s')
    );
}

// Update a carrousel
function updateCarrousel($id, $newName)
{
    global $wpdb, $carrousel_table_name;
    $wpdb->update($carrousel_table_name, array('name' => $newName), array('id' => $id), array('%s'), array('%d'));
}

// Update a slide
function updateSlide($id, $newImageUrl, $newTitle, $newDescription, $newLinkUrl)
{
    global $wpdb, $slides_table_name;
    $wpdb->update(
        $slides_table_name,
        array(
            'image_url' => $newImageUrl,
            'title' => $newTitle,
            'description' => $newDescription,
            'link_url' => $newLinkUrl
        ),
        array('id' => $id),
        array('%s', '%s', '%s', '%s'),
        array('%d')
    );
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
    $slides_table_name = $wpdb->prefix . 'custom_carrousel_slides';  // Initialisation ici
    $wpdb->delete($slides_table_name, array('id' => $id), array('%d'));
}
