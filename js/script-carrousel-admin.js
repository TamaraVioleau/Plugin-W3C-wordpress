jQuery(document).ready(function($) {
    // Fonction pour gérer l'activation/désactivation des boutons
    function toggleButtons(carrouselSelected) {
        if(carrouselSelected) {
            $('#editCarrouselButton, #modifyCarrouselButton, #deleteCarrouselButton').prop('disabled', false);
        } else {
            $('#editCarrouselButton, #modifyCarrouselButton, #deleteCarrouselButton').prop('disabled', true);
        }
    }

    // Gérer le changement dans le menu déroulant
    $('#carrouselSelect').change(function() {
        const carrouselSelected = $(this).val();
        toggleButtons(carrouselSelected);
    });

    // Gérer la soumission du formulaire
    $('form').submit(function(e) {
        const carrouselSelected = $('#carrouselSelect').val();
        toggleButtons(carrouselSelected);
    });

    // Activation initiale des boutons en fonction de la valeur sélectionnée
    const initialCarrouselSelected = $('#carrouselSelect').val();
    toggleButtons(initialCarrouselSelected);
});
