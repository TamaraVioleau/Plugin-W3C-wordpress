<div class="wrap">
    <h2>Choisir le carrousel</h2>
    <form method="post" action="">
        <select name="selected_carrousel" id="carrouselSelect">

            <?php
            if (!$carrousel_id) {
                echo '<option value="" selected="selected" data-default="true">Choisir le carrousel</option>';
            } else {
                echo '<option value="" data-default="true">Choisir le carrousel</option>';
            }

            // Déclare la variable globale $wpdb pour accéder aux fonctionnalités de la base de données de WordPress
            global $wpdb;

            // Parcourir tous les carrousels disponibles
            foreach ($all_carrousels as $carrousel) {
                // Détermine si l'option actuelle doit être sélectionnée
                $selected = ($carrousel_id == $carrousel->carrousel_id) ? 'selected="selected"' : '';

                // Récupère l'ID du carrousel courant dans la boucle
                $current_carrousel_id = $carrousel->carrousel_id;

                try {
                    // Compte le nombre de slides associées à ce carrousel
                    $count_slides = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}custom_carrousel_slides WHERE carrousel_id = $current_carrousel_id");
                } catch (Exception $e) {
                    // Gérer les exceptions
                    echo 'Caught exception: ',  $e->getMessage(), "\n";
                }

                // Affiche l'option dans le menu déroulant
                echo '<option value="' . esc_attr($current_carrousel_id) . '" ' . $selected . ' data-count-slides="' . $count_slides . '">' . esc_html($carrousel->name) . '</option>';
            }

            ?>

        </select>
        <input type="submit" name="edit_carrousel" class="button" id="editCarrouselButton" value="Ajouter une slide">
        <input type="submit" name="modify_carrousel" class="button" id="modifyCarrouselButton" value="Modifier les slides">
        <input type="submit" name="delete_carrousel" class="button" id="deleteCarrouselButton" value="Supprimer le carrousel">
    </form>

    <div id="shortcodeContainer" style="display: none;">
        <input type="text" id="shortcodeDisplay" readonly>
        <button onclick="copyShortcode()">Copier le shortcode</button>
    </div>
</div>


<script type="text/javascript">
    document.getElementById('carrouselSelect').addEventListener('change', function() {
        let carrouselId = this.value;
        if (carrouselId) {
            let shortcode = '[custom_carrousel id="' + carrouselId + '"]';
            document.getElementById('shortcodeDisplay').value = shortcode;
            document.getElementById('shortcodeContainer').style.display = 'block';
        } else {
            document.getElementById('shortcodeContainer').style.display = 'none';
        }
    });

    function copyShortcode() {
        let copyText = document.getElementById("shortcodeDisplay");
        copyText.select();
        copyText.setSelectionRange(0, 99999); // Pour les appareils mobiles
        document.execCommand("copy");
        alert("Shortcode copié: " + copyText.value);
    }
</script>