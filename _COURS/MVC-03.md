# Réalisation d'un MVC : La structure de fichiers, le routeur et autres dépendances

## Structure des fichiers

Pour réaliser une architecture en MVC, nous allons systématiquement découper notre dossier de travail comme suit :

```
config/
    app.php             # Variables de configuration de l'app
    helpers.php         # Des helpers si besoin
    routes.php          # Liste des routes
    database.php        # Identifiants de BDD
    Db.php              # Une librairie à importer plus tard

models/                 # Contiendra un model par ressource

controllers/            # Contiendra un controller par ressource

public/
    uploads/            # Les éventuels uploads d'utilisateurs
    assets/             # Ressources front
        img/
        js/
        css/
    views/              # Les templates de views (un sous-dossier par ressource)

index.php       # Porte d'entrée de l'application

vendor/         # On touche pas ! Il est généré par Composer
.gitignore      # Liste de ce que je ne veux pas versionner
composer.json   # La liste de mes dépendances
composer.lock   # On touche pas ! Etat des dépendances actuellement installées
```

Soyez vigilants sur cette organisation de dossier ! Vous pourrez bien sûr plus tard adapter cette organisation selon les besoins de votre projet, pour de l'optimisation...

## index.php

Une fois le découpage effectué, nous allons remplir notre index.php afin qu'il importe le contenu de chaque dossier dans le bon ordre :

```php
<?php
/**
 * Composer Autoload
 */
require __DIR__ . '/vendor/autoload.php';

/**
 * Autoload du MVC : cette fonction nous permet de charger nos classes dynamiquement au moment précis où on en a besoin, plutôt que de charger toutes nos classes d'un seul coup. On passe en argument "CLASSES_SOURCES" qui est une constante qui est définie dans app.php
 */
spl_autoload_register (function ($class) {
    $sources = array_map(function($s) use ($class) {
        return $s . '/' . $class . '.php';
    },
    CLASSES_SOURCES);
    
    foreach ($sources as $source) {
        if (file_exists($source)) {
            require_once $source;
        } 
    } 
});

/**
 * On récupère les fichiers de configuration dans le bon ordre
 */
require 'config/app.php';
require 'config/helpers.php';
require 'config/Db.php';
require 'config/routes.php';
```

## config/app.php

Nous devons aussi remplir le fichier `app.php` afin de faire fonctionner l'application sans problèmes :

```php

/**
 * Aliases : raccourcis pour les noms de classes
 */
class_alias('\Bramus\Router\Router', 'Router');

/**
 * Constantes : éléments de configuration propres au système
 */
const WEBSITE_TITLE = "Mon nouveau site en MVC";
const BASE_URL = "localhost/videoclub";

/**
 * Liste des dossiers source pour l'autoload des classes
 */
const CLASSES_SOURCES = [
    'controllers',
    'config',
    'models',
];
```

## config/database.php
Et enfin, nous allons remplir le fichier `database.php` qui renseignera nos identifiants de base de données :
```php
<?php
/**
 * Don't be silly ! Do not commit this file.
 */
const DB_HOST = 'localhost';
const DB_PORT = '3308';
const DB_NAME = 'videoclub';
const DB_USER = 'root';
const DB_PWD  = '';
```

> Attention : ce fichier contient toutes les informations pour se connecter à votre base de données ! Il est important d'ajouter ce fichier dans le `.gitignore` dans un projet réel afin de ne jamais publier ces informations au grand public, et de re-créer à la main ce fichier à chaque fois que vous clonez le projet.

> Pour tout de même garder une trace de ce que contient ce fichier, vous pouvez par exemple versionner un fichier `database.php.example` qui contient tous les champs avec des données d'exemple. Pour ce cours, nous pouvons laisser database.php tel qu'il est car nous sommes tous sur localhost avec des identifiants basiques (root/root).

## config/Db.php

Vous devez récupérer le contenu de ce fichier dans le dépôt Git de ce cours. En effet, `Db.php` est une bibliothèque de fonctions que je mettrai à jour constamment, il faut la considérer comme une dépendance externe et prendre la dernière version à jour sans la modifier.

## config/helpers.php

Idem, veuillez récupérer la version à jour dans le dépôt Git de ce cours.
Ce fichier contient des helpers d'URL utilisant la constante `BASE_URL` définie dans `app.php` nous permettant de ne pas avoir de problèmes de liens relatifs selon les configurations.

Il contient également une fonction très pratique permettant d'inclure un template de View et de lui transmettre des variables (`view()`).

## Conclusion 

Notre projet est enfin parfaitement configuré, nous allons pouvoir directement travailler avec un vrai MVC !