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