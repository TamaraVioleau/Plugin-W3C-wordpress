/* Fichier JS pour la page d'administration des carrousels
 */

///FONCTION BOUTON MENU DEROULANT
jQuery(document).ready(function ($) {
  // Fonction pour gérer l'activation/désactivation des boutons
  function toggleButtons(carrouselSelected) {
    var countSlides = $("#carrouselSelect option:selected").data(
      "count-slides"
    );

    if (carrouselSelected) {
      $("#editCarrouselButton, #deleteCarrouselButton").prop("disabled", false);

      // Activer ou désactiver le bouton "Modifier les slides" en fonction du nombre de slides
      if (countSlides > 0) {
        $("#modifyCarrouselButton").prop("disabled", false);
      } else {
        $("#modifyCarrouselButton").prop("disabled", true);
      }
    } else {
      $(
        "#editCarrouselButton, #modifyCarrouselButton, #deleteCarrouselButton"
      ).prop("disabled", true);
    }
  }

  // Gérer le changement dans le menu déroulant
  $("#carrouselSelect").change(function () {
    const carrouselSelected = $(this).val();
    toggleButtons(carrouselSelected);
  });
  // Gérer la soumission du formulaire
  $("form").submit(function (e) {
    const carrouselSelected = $("#carrouselSelect").val();
    toggleButtons(carrouselSelected);
  });
  // Activation initiale des boutons en fonction de la valeur sélectionnée
  const initialCarrouselSelected = $("#carrouselSelect").val();
  toggleButtons(initialCarrouselSelected);
});

//FONCTION AFFICHAGE DETAILS SLIDES
// Variable pour garder une trace de la slide actuellement ouverte
let currentlyOpenDetails = null;
function toggleDetails(buttonElement) {
  const detailsDiv = buttonElement.previousElementSibling;
  // Fermer la slide actuellement ouverte si elle existe
  if (currentlyOpenDetails && currentlyOpenDetails !== detailsDiv) {
    currentlyOpenDetails.style.display = "none";
    currentlyOpenDetails.nextElementSibling.textContent =
      "Voir plus de détails";
  }

  // Ouvrir ou fermer la nouvelle slide
  if (detailsDiv.style.display === "none") {
    detailsDiv.style.display = "block";
    buttonElement.textContent = "Voir moins de détails";
    currentlyOpenDetails = detailsDiv;
  } else {
    detailsDiv.style.display = "none";
    buttonElement.textContent = "Voir plus de détails";
    currentlyOpenDetails = null;
  }
}

jQuery(document).ready(function ($) {
  //BOUTON SUPPRESSSION DESACTIVE SI NON SELECTION DE CASE
  // Fonction pour gérer l'activation/désactivation du bouton de suppression des slides
  function toggleDeleteButton() {
    const selectedSlides = $("input[name='selected_slides[]']:checked").length;
    if (selectedSlides > 0) {
      $(".delete_selected_slides").prop("disabled", false);
    } else {
      $(".delete_selected_slides").prop("disabled", true);
    }
  }
  toggleDeleteButton();

  $("input[name='selected_slides[]']").change(function () {
    toggleDeleteButton();
  });

  //BOUTON CREER CARROUSEL
  // Fonction pour activer/désactiver le bouton "Créer"
  function toggleCreateButton() {
    const carrouselName = $("#carrousel_name").val().trim();
    if (carrouselName === "") {
      $("#submit_carrousel_name").prop("disabled", true);
    } else {
      $("#submit_carrousel_name").prop("disabled", false);
    }
  }

  // Appliquer la vérification initiale
  toggleCreateButton();

  // Écouter les événements de changement sur le champ du formulaire
  $("#carrousel_name").on("input", toggleCreateButton);
});
