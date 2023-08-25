
jQuery(document).ready(function($) {
    // Désactiver les boutons par défaut
    $('#editCarrouselButton, #modifyCarrouselButton, #deleteCarrouselButton').prop('disabled', true);

    // Écouter les changements du menu déroulant
    $('#carrouselSelect').on('change', function() {
        var selectedValue = $(this).val();

        if (selectedValue) {
            // Activer les boutons si une valeur est sélectionnée
            $('#editCarrouselButton, #modifyCarrouselButton, #deleteCarrouselButton').prop('disabled', false);
        } else {
            // Sinon, les désactiver
            $('#editCarrouselButton, #modifyCarrouselButton, #deleteCarrouselButton').prop('disabled', true);
        }
    });
});


