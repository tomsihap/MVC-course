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