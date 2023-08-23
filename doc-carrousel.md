# Gestionnaire de Carrousels Personnalisés pour WordPress

Ce code fournit un moyen pour les administrateurs de WordPress de créer, modifier et supprimer des carrousels personnalisés. Ces carrousels peuvent être utilisés pour afficher des diapositives avec des images, des titres, des descriptions et des liens.

## Table des matières
- [Gestionnaire de Carrousels Personnalisés pour WordPress](#gestionnaire-de-carrousels-personnalisés-pour-wordpress)
  - [Table des matières](#table-des-matières)
  - [Utilisation](#utilisation)
  - [Détails Techniques](#détails-techniques)
    - [1. Initialisation du Shortcode](#1-initialisation-du-shortcode)
    - [2. Fonction custom\_carrousel\_create\_table() :](#2-fonction-custom_carrousel_create_table-)
    - [3. Fonction custom\_carrousel\_shortcode($atts) :](#3-fonction-custom_carrousel_shortcodeatts-)
    - [4. Fonction custom\_link\_carrousel\_page()](#4-fonction-custom_link_carrousel_page)
      - [Création d'un nouveau carrousel](#création-dun-nouveau-carrousel)
      - [Ajout de diapositives à un carrousel](#ajout-de-diapositives-à-un-carrousel)
      - [Modification des diapositives](#modification-des-diapositives)
      - [Suppression d'un carrousel](#suppression-dun-carrousel)
    - [5. Interface Administrateur](#5-interface-administrateur)
    - [6. Gestion des Données](#6-gestion-des-données)
    - [7. Sécurité](#7-sécurité)


## Utilisation

1. Installez et activez le plugin.
2. Une fois activé, deux tables seront automatiquement créées pour stocker les données des carrousels et des diapositives.
3. Pour intégrer un carrousel dans un article ou une page, utilisez le shortcode `[custom_carrousel id="XX"]`, où `XX` est l'ID du carrousel que vous souhaitez afficher.
4. Les styles et scripts nécessaires sont automatiquement inclus, que vous soyez dans l'interface d'administration ou sur le site lui-même.

> **À noter :**

- Assurez-vous d'avoir sauvegardé votre base de données avant d'activer ou de désactiver ce plugin pour éviter toute perte de données.
- Ce plugin utilise le **wpdb** global de WordPress pour interagir avec la base de données.
- Pour une personnalisation ou une extension des fonctionnalités, veuillez consulter un développeur.

## Détails Techniques

### 1. Initialisation du Shortcode

Le code commence par enregistrer un nouveau shortcode `custom_carrousel` qui, lorsqu'il est utilisé dans un article ou une page, déclenchera la fonction `custom_carrousel_shortcode`.

```php
add_shortcode('custom_carrousel', 'custom_carrousel_shortcode');    
```

### 2. Fonction custom_carrousel_create_table() :

Crée deux tables dans la base de données lors de l'activation du plugin :

- **custom_carrousels** : Pour stocker les informations générales sur chaque carrousel.
- **custom_carrousel_slides** : Pour stocker les détails de chaque diapositive associée à un carrousel.

### 3. Fonction custom_carrousel_shortcode($atts) :

Génère le code HTML du carrousel basé sur l'ID fourni via le shortcode. Si aucun ID valide n'est fourni, un message d'erreur sera retourné.

Exemple d'utilisation du shortcode : '[custom_carrousel id="1"]'.

### 4. Fonction custom_link_carrousel_page()

Cette fonction est la principale interface administrateur pour gérer l'affichage et le traitement des formulaires pour les carrousels et les diapositives.

Elle commence par afficher des informations générales sur ce qu'est un carrousel et comment il fonctionne.
Elle initialise ensuite quelques variables et configurations, telles que les noms des tables de la base de données utilisées pour stocker les carrousels et leurs diapositives.

- **Initialisation des variables** : Prépare les noms des tables et vérifie les entrées POST pour déterminer l'action en cours.

- **Création d'un nouveau carrousel** : Si un nom de carrousel est soumis, un nouveau carrousel est créé dans la base de données et son shortcode est affiché.
- **Suppression d'un carrousel** : Si l'option de suppression est choisie, le carrousel sélectionné et toutes ses diapositives associées sont supprimés.

- **Ajout d'une diapositive** : Si un formulaire de diapositive est soumis, une nouvelle diapositive est ajoutée à la base de données pour le carrousel sélectionné.
- **Modification des diapositives** : Si l'option de modification est choisie, un formulaire s'affiche pour chaque diapositive du carrousel choisi, permettant de les modifier.
- **Mise à jour d'une diapositive** : Si les détails d'une diapositive sont soumis pour mise à jour, la diapositive est mise à jour dans la base de données.

#### Création d'un nouveau carrousel
Les utilisateurs peuvent créer un nouveau carrousel en lui donnant un nom. Une fois le carrousel créé, un shortcode unique est généré pour intégrer le carrousel dans les publications ou les pages.

#### Ajout de diapositives à un carrousel
Une fois qu'un carrousel est créé, des diapositives peuvent être ajoutées. Chaque diapositive nécessite :
- **URL de l'image**
- **Titre**
- **Description**
- **URL du lien**

#### Modification des diapositives
Les diapositives d'un carrousel spécifique peuvent être modifiées. Les utilisateurs peuvent mettre à jour l'URL de l'image, le titre, la description et l'URL du lien de chaque diapositive.

#### Suppression d'un carrousel
Les carrousels peuvent être supprimés. La suppression d'un carrousel supprimera également toutes ses diapositives associées.

> **Note :** Les informations soumises via les formulaires sont assainies avant d'être insérées dans la base de données pour des raisons de sécurité.

### 5. Interface Administrateur
L'interface administrateur comprend plusieurs formulaires qui permettent à l'utilisateur d'effectuer diverses actions:

- **Créer un nouveau carrousel**: l'utilisateur peut saisir le nom d'un nouveau carrousel et le créer.
- **Ajouter des diapositives**: une fois un carrousel créé ou sélectionné, l'utilisateur peut lui ajouter des diapositives. Chaque diapositive a un champ pour une URL d'image, un titre, une description et une URL de lien.
- **Modifier les diapositives existantes**: Si l'utilisateur sélectionne l'option "Modifier", il peut mettre à jour les informations des diapositives existantes.
- **Supprimer un carrousel**: cela supprimera le carrousel et toutes ses diapositives.

### 6. Gestion des Données

La plupart des opérations liées aux données sont effectuées en utilisant l'objet global `$wpdb`, qui est un moyen d'interagir avec la base de données de WordPress.

Les fonctions principales utilisées sont:
- `$wpdb->insert()` : pour insérer de nouvelles entrées
- `$wpdb->update()` : pour mettre à jour les entrées existantes
- `$wpdb->delete()` : pour supprimer des entrées

### 7. Sécurité

Toutes les entrées de l'utilisateur sont sanitizées pour éviter les injections SQL et les autres types d'attaques. Les fonctions de WordPress comme `sanitize_text_field` et `intval` sont utilisées pour nettoyer les données avant qu'elles ne soient traitées ou stockées.