# Custom Design Pattern

L'extension WordPress "Custom Design Pattern" est créée par Tamara VIOLEAU. 
Elle s'appuie sur les design pattern présent sur le site W3C.
Elle permet de gérer des liens personnalisés avec des designs différents.

## Fonctionnalités

1. **Styles CSS et Scripts JavaScript**: L'extension enregistre ses propres styles CSS et scripts JavaScript pour permettre la personnalisation des liens.

2. **Tableau de Liens Personnalisés**: Une table dans la base de données WordPress est créée pour stocker les liens personnalisés avec leur URL, texte visible, et pattern de design.

3. **Shortcode pour Afficher les Liens**: L'extension fournit un shortcode `[custom_link]` qui peut être utilisé dans les posts ou les pages pour afficher les liens personnalisés. Le shortcode utilise l'id du lien créé afin de pouvoir avoir un shortcode unique pour chacun. 

4. **Menu d'Administration**: L'extension ajoute un menu d'administration où vous pouvez gérer vos liens et d'autres fonctionnalités comme un carrousel.

### Sous-menus
- **Liens**: Page où vous pouvez ajouter, visualiser et supprimer les liens personnalisés.
- **Carrousel**: Une sous-page pour gérer un carrousel.

## Installation

1. Téléchargez et installez l'extension via le tableau de bord WordPress.
2. Activez l'extension.

## Utilisation

### Ajouter un Lien Personnalisé

1. **Accéder à la Page de Gestion des Liens**: Dans le tableau de bord WordPress, naviguez vers le menu "Design Pattern W3C" > "Liens".
2. **Remplir le Formulaire**: Utilisez le formulaire pour ajouter un nouveau lien personnalisé. Vous devez fournir un intitulé, une URL et un texte visible pour le lien.
3. **Utiliser le Shortcode Généré**: Une fois le lien ajouté, un shortcode sera généré. Copiez ce shortcode et collez-le dans vos articles ou pages pour afficher le lien personnalisé.

### Gérer les Liens Existant

Vous pouvez visualiser, modifier ou supprimer les liens existants depuis la page de gestion des liens dans le menu d'administration.

### Utiliser le Carrousel

1. **Accéder à la Page de Gestion du Carrousel**: Dans le tableau de bord WordPress, naviguez vers le menu "Design Pattern W3C" > "Carrousel".
2. **Gérer le Carrousel**: Utilisez cette page pour ajouter, organiser et gérer le contenu du carrousel.

(Note: La section sur le carrousel peut nécessiter des détails supplémentaires en fonction de la fonctionnalité réelle de cette partie de l'extension, qui n'est pas détaillée dans le code fourni.)

## Support et Contribution
Pour toute question ou contribution, n'hésitez pas à contacter Tamara VIOLEAU.
