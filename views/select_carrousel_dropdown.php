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

            foreach ($all_carrousels as $carrousel) {
                $selected = ($carrousel_id == $carrousel->carrousel_id) ? 'selected="selected"' : '';
                echo '<option value="' . esc_attr($carrousel->carrousel_id) . '" ' . $selected . '>' . esc_html($carrousel->name) . '</option>';
            }
            ?>

        </select>
        <input type="submit" name="edit_carrousel" class="button" id="editCarrouselButton" value="Ajouter une slide">
        <input type="submit" name="modify_carrousel" class="button" id="modifyCarrouselButton" value="Modifier les slides">
        <input type="submit" name="delete_carrousel" class="button" id="deleteCarrouselButton" value="Supprimer les slides">
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