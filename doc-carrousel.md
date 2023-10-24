# Gestionnaire de Carrousels Personnalisés pour WordPress

Ce code fournit un moyen pour les administrateurs de WordPress de créer, modifier et supprimer des carrousels personnalisés. Ces carrousels peuvent être utilisés pour afficher des slides avec des images, des titres, des descriptions et des liens.

## Table des matières

- [Gestionnaire de Carrousels Personnalisés pour WordPress](#gestionnaire-de-carrousels-personnalisés-pour-wordpress)
  - [Table des matières](#table-des-matières)
  - [Utilisation](#utilisation)
  - [Détails Techniques](#détails-techniques)
    - [1. Initialisation du Shortcode](#1-initialisation-du-shortcode)
    - [2. Fonction custom_carrousel_create_table()](#2-fonction-custom_carrousel_create_table)
    - [3. Fonction custom_carrousel_shortcode($atts)](#3-fonction-custom_carrousel_shortcodeatts)
    - [4. Fonction display_form_create_carrousel()](#4-fonction-display_form_create_carrousel)
    - [5. Fonction display_select_carrousel_dropdown($carrousel_id, $all_carrousels)](#5-fonction-display_select_carrousel_dropdowncarrousel_id-all_carrousels)
    - [6. Fonction display_form_add_slide($carrousel_id)](#6-fonction-display_form_add_slidecarrousel_id)
    - [7. Fonction custom_link_carrousel_page()](#7-fonction-custom_link_carrousel_page)
      - [Si on clique sur l'onglet "Créer un carrousel" `if ($active_tab == 'create_carrousel')`](#si-on-clique-sur-longlet-créer-un-carrousel-if-active_tab--create_carrousel)
      - [Si on clique sur l'onglet "Choisir le carrousel" `elseif ($active_tab == 'choose_carrousel')`](#si-on-clique-sur-longlet-choisir-le-carrousel-elseif-active_tab--choose_carrousel)
      - [Suppression et Modification multiples des slides](#suppression-et-modification-multiples-des-slides)
    - [8. Utilisation du fichier `crud_functions_carrousel.php`](#8-utilisation-du-fichier-crud_functions_carrouselphp)
    - [9. Gestion des Données](#9-gestion-des-données)
    - [10. Sécurité](#10-sécurité)
  - [Interface Administrateur](#interface-administrateur)

## Utilisation

1. Installez et activez le plugin.
2. Une fois activé, deux tables seront automatiquement créées pour stocker les données des carrousels et des slides.
3. Pour intégrer un carrousel dans un article ou une page, utilisez le shortcode `[custom_carrousel id="XX"]`, où `XX` est l'ID du carrousel que vous souhaitez afficher.
4. Les styles et scripts nécessaires sont automatiquement inclus, que vous soyez dans l'interface d'administration ou sur le site lui-même.

> **À noter :**
>
> - Assurez-vous d'avoir sauvegardé votre base de données avant d'activer ou de désactiver ce plugin pour éviter toute perte de données.
> - Ce plugin utilise le **wpdb** global de WordPress pour interagir avec la base de données.
> - Pour une personnalisation ou une extension des fonctionnalités, veuillez consulter un développeur.

## Détails Techniques

### 1. Initialisation du Shortcode

Le code commence par enregistrer un nouveau shortcode `custom_carrousel` qui, lorsqu'il est utilisé dans un article ou une page, déclenchera la fonction `custom_carrousel_shortcode`.

```php
add_shortcode('custom_carrousel', 'custom_carrousel_shortcode');
```

### 2. Fonction custom_carrousel_create_table()

Crée deux tables dans la base de données lors de l'activation du plugin :

- **custom_carrousels** : Pour stocker les informations générales sur chaque carrousel.
- **custom_carrousel_slides** : Pour stocker les détails de chaque slide associée à un carrousel.

### 3. Fonction custom_carrousel_shortcode($atts)

Génère le code HTML du carrousel basé sur l'ID fourni via le shortcode. Si aucun ID valide n'est fourni, un message d'erreur sera retourné.

Exemple d'utilisation du shortcode : '[custom_carrousel id="1"]'.

### 4. Fonction display_form_create_carrousel()

Cette fonction joue un rôle crucial dans l'interface d'administration de votre plugin de carrousel. Elle est appelée lorsque l'utilisateur souhaite créer un nouveau carrousel. La fonction génère dynamiquement un formulaire HTML, intégrant tous les champs nécessaires à la création d'un carrousel. Une fois le formulaire soumis, les données sont envoyées à une autre fonction responsable de la création effective du carrousel dans la base de données.

- **Rôle** : Générer et afficher le formulaire de création de carrousel.
- **Utilisation typique** : Appelée lorsque l'administrateur accède à la page de création de carrousel dans le tableau de bord WordPress.

### 5. Fonction display_select_carrousel_dropdown($carrousel_id, $all_carrousels)

Cette fonction génère une liste déroulante contenant tous les carrousels disponibles. Cela permet à l'utilisateur de choisir facilement parmi les carrousels existants pour effectuer des opérations comme l'édition ou la suppression. La liste déroulante est rendue en HTML et est souvent utilisée en combinaison avec d'autres formulaires ou boutons dans l'interface d'administration.

- **Rôle** : Générer une liste déroulante des carrousels disponibles.
- **Paramètres** :
  - `carrousel_id` : L'ID du carrousel actuellement sélectionné. Utilisé pour mettre en surbrillance le choix actuel dans la liste déroulante.
  - `all_carrousels` : Un tableau contenant les IDs et les noms de tous les carrousels disponibles.

### 6. Fonction display_form_add_slide($carrousel_id)

Cette fonction est similaire à `display_form_create_carrousel` mais est spécifiquement conçue pour ajouter de nouvelles slides à un carrousel existant. Elle génère un formulaire HTML avec des champs pour l'image, le titre, la description et d'autres attributs d'une slide. Une fois le formulaire soumis, les données sont traitées et une nouvelle slide est ajoutée au carrousel spécifié.

- **Rôle** : Générer et afficher le formulaire d'ajout de slide à un carrousel.
- **Paramètres** :
  - `carrousel_id` : L'ID du carrousel auquel la nouvelle slide sera ajoutée.
- **Utilisation typique** : Appelée lorsque l'administrateur souhaite ajouter une nouvelle slide à un carrousel existant depuis le tableau de bord WordPress.

### 7. Fonction custom_link_carrousel_page()

Cette fonction est la principale interface administrateur pour gérer l'affichage et le traitement des formulaires pour les carrousels et les slides.

Elle commence par afficher des informations générales sur ce qu'est un carrousel et comment il fonctionne.

Elle initialise ensuite quelques variables et configurations, telles que les noms des tables de la base de données utilisées pour stocker les carrousels et leurs slides.

- **Initialisation des variables** : Accède à l'objet global de WordPress pour les opérations de base de données `($wpdb)` et initialise diverses variables. Cela comprend la préparation des noms des tables pour les carrousels et les slides, la récupération de l'ID du carrousel sélectionné à partir des données POST, et la requête de tous les carrousels existants dans la base de données, triés par nom.

#### Si on clique sur l'onglet "Créer un carrousel" `if ($active_tab == 'create_carrousel')`

- **Création d'un nouveau carrousel** : Les utilisateurs peuvent créer un nouveau carrousel en lui donnant un nom. Une fois le carrousel créé, un shortcode unique est généré pour intégrer le carrousel dans les publications ou les pages.
  - La fonction `display_form_create_carrousel()` est appelée pour afficher un formulaire permettant de créer un nouveau carrousel.
  - Si un nom de carrousel est soumis, un nouveau carrousel est créé dans la base de données et son shortcode est affiché.
  - Si un ID de carrousel est disponible, le formulaire pour ajouter une slide à ce carrousel est affiché.

#### Si on clique sur l'onglet "Choisir le carrousel" `elseif ($active_tab == 'choose_carrousel')`

- **Affichage du menu déroulant des carrousels existants :** La fonction `display_select_carrousel_dropdown()` est appelée pour afficher un menu déroulant permettant de choisir un carrousel existant.

- **Mise à jour d'une slide** : Si un carrousel est sélectionné et que le bouton "Modifier" est cliqué, l'ID du carrousel est mis à jour.

- **Suppression d'un carrousel** : Si le bouton "Supprimer" est cliqué, le carrousel sélectionné est supprimé de la base de données. La suppression d'un carrousel supprimera également toutes ses slides associées.

- **Ajout d'une slide** : Une fois qu'un carrousel est créé, des slides peuvent être ajoutées. Chaque slide nécessite : **URL de l'image** / **Titre** / **Description** / **URL du lien**
  - Si un carrousel est sélectionné et qu'aucune autre action (comme la suppression ou la modification du carrousel) n'est en cours, le formulaire pour ajouter une nouvelle slide est affiché.

#### Suppression et Modification multiples des slides

Les slides d'un carrousel spécifique peuvent être modifiées et supprimées. Les utilisateurs peuvent mettre à jour l'URL de l'image, le titre, la description et l'URL du lien de chaque slide.

- **Suppression multiples des slides** : Si plusieurs slides sont sélectionnées et que le bouton "Supprimer les slides sélectionnées" est cliqué, ces slides sont supprimées.

- **Modification multiples des slides** : Si le bouton "Modifier les slides" est cliqué, un formulaire est affiché pour chaque slide existante, permettant de les modifier.

> **Note :** Les informations soumises via les formulaires sont assainies avant d'être insérées dans la base de données pour des raisons de sécurité.

### 8. Utilisation du fichier `crud_functions_carrousel.php`

Ce fichier contient les fonctions essentielles pour les opérations CRUD (Create, Read, Update, Delete) des carrousels et des slides. Il est lié au fichier `custom-carrousel.php` qui utilise ces fonctions pour interagir avec la base de données. Voici quelques fonctions clés de ce fichier :

- `createCarrousel($wpdb, $carrousel_table_name)`: Crée un nouveau carrousel dans la base de données.
- `deleteCarrousel($carrousel_id)`: Supprime un carrousel existant et toutes ses slides associées de la base de données.
- `createSlide($carrousel_id, $wpdb, $slides_table_name)`: Ajoute une nouvelle slide à un carrousel existant.
- `deleteSlide($slide_id)`: Suppression multiples de slides d'un carrousel existant.
- `updateMultipleSlides($post_data, $wpdb, $slides_table_name)`: Mise à jour des données de plusieurs slides en une seule opération.

Ces fonctions sont appelées dans le fichier `custom-carrousel.php` pour exécuter les actions correspondantes lors de l'interaction avec l'interface utilisateur de l'extension de carrousel.

### 9. Gestion des Données

La plupart des opérations liées aux données sont effectuées en utilisant l'objet global `$wpdb`, qui est un moyen d'interagir avec la base de données de WordPress.

Les fonctions principales utilisées sont:

- `$wpdb->insert()` : pour insérer de nouvelles entrées
- `$wpdb->update()` : pour mettre à jour les entrées existantes
- `$wpdb->delete()` : pour supprimer des entrées

### 10. Sécurité

Toutes les entrées de l'utilisateur sont sanitizées pour éviter les injections SQL et les autres types d'attaques. Les fonctions de WordPress comme `sanitize_text_field` et `intval` sont utilisées pour nettoyer les données avant qu'elles ne soient traitées ou stockées.

## Interface Administrateur

L'interface administrateur comprend plusieurs formulaires qui permettent à l'utilisateur d'effectuer diverses actions:

- **Créer un nouveau carrousel**: l'utilisateur peut saisir le nom d'un nouveau carrousel et le créer.
- **Ajouter des slides**: une fois un carrousel créé ou sélectionné, l'utilisateur peut lui ajouter des slides. Chaque slide a un champ pour une URL d'image, un titre, une description et une URL de lien.
- **Modifier les slides existantes**: Si l'utilisateur sélectionne l'option "Modifier", il peut mettre à jour les informations des slides existantes.
- **Supprimer un carrousel**: cela supprimera le carrousel et toutes ses slides.
