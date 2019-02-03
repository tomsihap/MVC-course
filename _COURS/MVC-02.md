# Réalisation d'un MVC : Composer, index.php et .htaccess

Dorénavant, comme on l'a vu, quelle que soit l'adresse saisie par l'utilisateur, il passera par index.php qui appelera le router. Il nous faut donc, *après avoir créé un dossier de travail bien sûr*, 3 éléments :
- Importer un router
- Un fichier index.php appelant le router
- Un fichier .htaccess qui redirigera chaque requête vers index.php

### Importer le router
Nous pourrions créer de toutes pièces notre router, néanmoins il existe des milliers de bibliothèques toutes prêtes en PHP, un router efficace, open-source et donc éprouvé existe donc déjà sans doute !

> Le gestionnaire de packages pour PHP s'appelle **Composer**. Il doit normalement déjà être installé sur votre système, si ça n'est pas déjà le cas vous pouvez trouver les instructions d'installation [sur getcomposer.org](https://getcomposer.org/doc/00-intro.md).

> Où trouver des packages intéressants pour mon projet ? Vous pouvez bien sûr trouver des milliers d'idées sur Github, ou regarder sur [Packagist](https://packagist.org/) qui est le répertoire des paquets installables avec Composer.

Nous allons utiliser le package `bramus/router` ([documentation sur Packagist.org](https://packagist.org/packages/bramus/router)). Pour installer une dépendance avec Composer, il faut se rendre avec un terminal dans le dossier de travail et saisir : `composer require *author*/*package* *version`. Pour notre cas, on va saisir dans un terminal :

`$ composer require bramus/router ~1.3`
> Attention ! Le `$` n'est pas à saisir ! Il s'agit juste d'une indication que nous sommes dans un terminal.

> `~ 1.3` veut dire : "si une version supérieure est disponible lors d'un `composer install` (installer les dépendances d'un projet) ou d'un `composer update` (mettre à jour les dépendances d'un projet), je ne veux que les versions comprises dans l'intervalle `>=1.3.0.0-dev <2.0.0.0-dev`.
> Plus d'informations sur le [Semantic Versioning](https://semver.org/) et les contraintes de [versions avec Composer](https://getcomposer.org/doc/articles/versions.md).

Composer vient de créer plusieurs fichiers et dossiers :

```
/vendor         # Dossier contenant les dépendances installées
composer.json   # Fichier indiquant la liste des dépendances du projet
composer.json.lock # Fichier technique indiquant l'état des dépendances actuellement installées
```

Il faut maintenant créer un fichier `index.php` et lui indiquer en tout premier lieu que nous allons inclure les dépendances grâce à l'autoloader de Composer :

```php
require __DIR__ . '/vendor/autoload.php';
```

D'après la documentation de `bramus/router`, on instancie le routeur avec `$router = new \Bramus\Router\Router();`. Testons si notre dépendance est bien détectée et installée !

```php
require __DIR__ . '/vendor/autoload.php';
$router = new \Bramus\Router\Router();
var_dump($router);
```

Si un `object(Bramus\Router\Router)` est retourné par le `var_dump()`, alors tout fonctionne.

Enfin, toujours d'après la documentation de la dépendance, pour que le routeur fonctionne, il faut rediriger toutes les requêtes vers index.php (en effet ! Il faut bien que l'URL demandée soit lue par notre routeur, et comme on instancie le routeur dans index.php, on envoie les requêtes vers ce fichier !).

Pour cela, on créée un fichier nommé `.htaccess` avec dedans : 

```htaccess
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . index.php [L]
```

Voilà, notre projet est presque prêt à démarrer. Il manque un dernier détail : le fichier `.gitignore`.

Comme nous avons déjà la liste des dépendances du projet dans le fichier `composer.json`, et qu'il suffit d'une simple commande (`$ composer install`) pour les installer... Je n'ai pas besoin des importer dans mon versionnage Git ! De plus, comme ce sont des dépendances extérieures, il ne faut pas que je touche à ces fichiers. Je n'ai donc vraiment aucun intérêt à les versionner (en plus, comme les dépendances peuvent être très nombreuses, cela prendrait un temps énorme inutilement à chaque git push/pull).

Pour éviter ce problème, on va créer à la racine du projet un fichier `.gitignore` contenant simplement :

```git
vendor/
```

Cela indiquera à Git de ne pas versionner les fichiers et dossiers contenus dans le dossier `vendor/`. Nous utiliserons encore ce fichier plus tard.

> Attention: Dorénavant, si vous travaillez avec un projet comportant un fichier `composer.json`, vous devez absolument installer les dépendances du projet pour pouvoir l'utiliser avec un `$ composer install` à la racine du projet.