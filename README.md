# Custom Design Pattern

L'extension WordPress "Custom Design Pattern" est créée par Tamara VIOLEAU.
Elle s'appuie sur les design pattern présent sur le site W3C.
Elle permet de gérer des liens et des carrousels personnalisés en reprenant les design pattern de W3C.

## Fonctionnalités

1. **Tableau de Liens et de Carrousels Personnalisés**: Des tables dans la base de données WordPress sont créées pour stocker les liens personnalisés avec leur URL, texte visible, et pattern de design ainsi que les carrousels avec ses différentes slides.

2. **Shortcode pour afficher les Liens et les Carrousels**: L'extension fournit un shortcode `[custom_link id="X"]` (liens personnalisés) ou `[custom_carrousel id="X"]` qui peuvent être utilisé dans les posts ou les pages pour afficher éléments personnaliés. Le shortcode utilise l'id du lien créé afin de pouvoir avoir un shortcode unique pour chacun.

3. **Menu d'Administration**: L'extension ajoute un menu d'administration depuis lequel vous pouvez gérer vos liens et vos carrousels.

### Sous-menus
- **Liens**: Page où vous pouvez ajouter, visualiser et supprimer les liens personnalisés.
- **Carrousel**: Une sous-page pour gérer vos carrousel personnalisés et ses différentes slides.

## Installation

1. Téléchargez et installez l'extension via le tableau de bord WordPress.
2. Activez l'extension.

## Utilisation

### Utiliser le Lien Personnalisé

1. **Accéder à la Page de Gestion des Liens**: Dans le tableau de bord WordPress, naviguez vers le menu "Design Pattern W3C" > "Liens".
2. **Remplir le Formulaire**: Utilisez le formulaire pour ajouter un nouveau lien personnalisé. Vous devez fournir un intitulé, une URL et un texte visible pour le lien.
3. **Utiliser le Shortcode Généré**: Une fois le lien ajouté, un shortcode sera généré. Copiez ce shortcode et collez-le dans vos articles ou pages pour afficher le lien personnalisé.

### Utiliser le Carrousel Personnalisé

1. **Accéder à la Page de Gestion du Carrousel**: Dans le tableau de bord WordPress, naviguez vers le menu "Design Pattern W3C" > "Carrousel".
2. **Gérer le Carrousel**: Utilisez cette page pour ajouter, organiser et gérer le contenu du carrousel.
3. **Utiliser le Shortcode Généré**: Une fois le carrousel et ses slides ajoutés, un shortcode sera généré. Copiez ce shortcode et collez-le dans vos articles ou pages pour afficher le carrousel personnalisé.

## Support et Contribution
Pour toute question ou contribution, n'hésitez pas à contacter Tamara VIOLEAU.
