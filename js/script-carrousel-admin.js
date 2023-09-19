//FONCTION BOUTON MENU DEROULANT
jQuery(document).ready(function ($) {
  // Fonction pour gérer l'activation/désactivation des boutons
  function toggleButtons(carrouselSelected) {
    if (carrouselSelected) {
      $(
        "#editCarrouselButton, #modifyCarrouselButton, #deleteCarrouselButton"
      ).prop("disabled", false);
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
    currentlyOpenDetails.nextElementSibling.textContent = "Voir plus de détails";
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

