<script>
jQuery(document).ready(function($) {
    $('#upload_image_button').on('click', function(e) {
        e.preventDefault();

        // La médiathèque WP
        var image_frame;
        if(image_frame){
            image_frame.open();
            return;
        }
        image_frame = wp.media.frames.file_frame = wp.media({
            title: 'Sélectionnez ou téléchargez une image',
            button: {
                text: 'Sélectionnez une image'
            },
            multiple: false
        });

        image_frame.on('select', function() {
            attachment = image_frame.state().get('selection').first().toJSON();
            $('#image_url').val(attachment.url);
        });

        image_frame.open();
    });
});
</script>


<div class="wrap">
    <h2>Ajouter un élément de carrousel</h2>
    <form method="post" action="">
        <?php wp_nonce_field('add_slide_action', 'add_slide_nonce'); ?>
        <input type="hidden" name="carrousel_id" value="<?php echo $carrousel_id; ?>">
        <table class="form-table">
            <tr>
                <th scope="row"><label for="image_url">URL de l'image</label></th>
                <td>
                    <input type="text" name="image_url" id="image_url" class="regular-text">
                    <input id="upload_image_button" type="button" value="Choisir depuis la médiathèque">
                </td>
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
