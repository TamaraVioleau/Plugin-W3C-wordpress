<div class="wrap" id="wrap_createform">
    <h2>Créer un nouveau carrousel</h2>
    <form method="post" action="">
        <?php wp_nonce_field('create_carrousel_action', 'create_carrousel_nonce'); ?>
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