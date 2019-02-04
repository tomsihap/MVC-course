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
require 'config/database.php';
require 'config/helpers.php';
require 'config/Db.php';
require 'config/routes.php';


/**
 * On créée deux articles
 */
$titre  = 'Un nouvel article';
$contenu = 'Lorem ipsum, dolor sit amet consectetur adipisicing elit. Sit quidem, earum, expedita ex cum, voluptatum assumenda maiores delectus id eius provident cumque porro cupiditate quo necessitatibus eos aspernatur unde eaque.';

$article = new Article($titre, $contenu);
$article->save();

$titre  = 'Un autre article';
$contenu = 'Lorem ipsum, dolor sit amet consectetur adipisicing elit. Sit quidem, earum, expedita ex cum, voluptatum assumenda maiores delectus id eius provident cumque porro cupiditate quo necessitatibus eos aspernatur unde eaque.';

$article = new Article($titre, $contenu);
$article->save();

/**
 * Tous les articles en objects
 */
echo "<b>Tous les articles (objects) : </b>";
$articles = Article::findAll();
var_dump($articles);

/**
 * Tous les articles en array
 */
echo "<hr>";
echo "<b>Tous les articles (arrays) : </b>";
$articlesArray = Article::findAll(false);
var_dump($articlesArray);

/**
 * Article "Un autre article" (title LIKE "autre")
 */
echo '<hr>';
echo "<b>Article 'un autre article' : </b>";
$autreArticle = Article::find([
    ['title', 'like', '%autre%']
]);
var_dump($autreArticle);


/**
 * Article # 1
 */
echo '<hr>';
echo "<b>Article id = 1 : </b>";
$premierArticle = Article::findOne(1);
var_dump($premierArticle);