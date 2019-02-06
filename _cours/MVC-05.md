# Réalisation d'un MVC : mise en pratique (controllers)

Maintenant que nous avons finalisé l'aspect "données" du projet, c'est à dire tout ce qui permet d'enregistrer des données, nous allons gérer la partie routeur et controller, c'est à dire écouter une URL et renvoyer vers la méthode correspondante dans le bon contrôleur.


## 1. Le fichier routes.php

C'est dans ce fichier où nous allons répertorier la liste des routes de notre application, la liste des URL disponibles.

> Si un utilisateur essaie une URL qui n'existe pas, il ne se passera rien. Vous pouvez chercher dans la documentation de Bramus/Router comment gérer ces exceptions qui sont des erreurs 404.

On va rédiger une première route de test en suivant la documentation du routeur :

```php

$router = new Router; // Grâce à l'alias dans app.php, on peut utiliser ce raccourci plutôt que \Bramus\Router\Router

// On enregistre une nouvelle route GET : on écoute "lorsque l'utilisateur demande https://www.example.com/hello"

// On associe à cela une fonction anonyme, c'est à dire une fonction qui va s'exécuter d'elle même (ça nous permet de faire des tests rapides)

$router->get('/hello', function() {
    echo "hello world";
});

// Indispensable à la fin de la liste des routes pour que les routes fonctionnent !
$router->run();
```
> Pour tester, allez sur votre application à l'adresse [/hello](#).

Comme ça ne serait pas pratique d'effectuer nos actions dans des fonctions anonymes (aller lire tous les articles, modifier un article...), on va utiliser un fichier dédié à réagir aux routes : le **controller**.

Créez le fichier /controllers/ArticlesController.php (pluriel, A en majuscule) :

```php
class ArticlesController {

}
```

On va en fait lier une route à une méthode du contrôleur, par exemple ajoutons dans `routes.php`: 

```php
$router->get('/articles', 'ArticlesController@index');
```
## 2. Le contrôleur

On vient de lier la route `/articles` à la méthode `index()` dans le contrôleur `ArticlesController`. Il faut donc créer cette méthode ! Elle contiendra un appel vers la méthode `findAll()` du Model que nous avons créé plus tôt, qui nous permet de retrouver tous les éléments de la table `Article`.

```php
class ArticlesController {

    public function index() {

        // On appelle le Model qui nous retrouvera tous les éléments
        $articles = Article::findAll();

        var_dump($articles); // Un var_dump pour tester que nos données arrivent bien
    }
}
```

C'est dans ce fichier où je peux faire des traitements complémentaires à `$articles` (gérer de la traduction, gérer les données à afficher selon le niveau d'autentification...) avant l'affichage à l'utilisateur.

Comme il serait fastidieux d'afficher du HTML à cet endroit là, on va plutôt inclure ici des fichiers contenant du HTML à afficher. Dans le fichier `helper.php`, il y a une fonction qui vous permet d'afficher une vue en lui transmettant des données, on va l'utiliser dans le contrôleur :

```php
public function index() {

        // On appelle le Model qui nous retrouvera tous les éléments
        $articles = Article::findAll();

        view('articles.index', compact('articles'));
    }
```

1. `view()` prend en premier paramètre le chemin virtuel vers la vue : en fait, il va chercher "public/views/articles/index.php".

> Il faut donc créer le dossier `articles` dans `public/views/`, et le fichier `index.php` dans `public/views/articles/` ! L'intérêt d'écrire `articles.index` plutôt que `articles/index.php` voire `public/views/articles/index.php` est simplement pour améliorer la lisibilité (moins de symboles inutiles à la compréhension) et abstraire le fait que `view()` va chercher les vues dans `/public/views`.

2. Le deuxième paramètre est "compact()" : on passe en string la liste des variables à passer à la vue ([voir la doc](http://php.net/manual/fr/function.compact.php)).

### 3. La vue

On va créer le fichier de vue qu'on demande dans le contrôleur : `/public/views/articles/index.php`, que l'on peut remplir pour commencer comme cela :

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    

    <ul>
    <?php foreach($articles as $a) : ?>
        <li><strong><?= $a->title(); ?></strong>: <?= $a->content();?></li>
    <?php endforeach;?>
    </ul>
</body>
</html>
```

S'il y a des articles en base de données (ce qui devrait être le cas si vous avez testé votre Model dans le cours précédent), ils devraient s'afficher en liste.