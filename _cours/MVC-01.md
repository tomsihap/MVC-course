# Réalisation d'un MVC : Comment ça fonctionne ?

## Les grandes lignes
Avec un pattern MVC (*Model-View-Controller*), nous allons gérer nos projets en **ressources** : en général, une ressource correspond à une **table** dans la base de données.

Pour chaque ressource, nous allons créer 2 à 3 fichiers : 
- Le Controller, c'est la classe qui contiendra toutes les actions que l'on peut réaliser autour de cette ressource (consulter des données, modifier des données, faire des opérations sur ces données...)
- Le Model, c'est la classe qui va *représenter* la base de données: lorsque le controller aura besoin de faire des opérations qui nécessitent de parler à la BDD (lire les données, les modifier, les enregistrer, les supprimer...), il va demander au Model d'effectuer ces opérations.
- La View, c'est en fait le rendu visuel : par exemple, lorsque je vais sur la page du film "Rocky" dans mon projet Vidéoclub, le **Controller** va *demander* au **Model** les données correspondant au film. Une fois reçues, le controller va **envoyer** les données à la **View**, c'est à dire le rendu HTML que l'utilisateur va voir !
- > Parfois nous n'aurons pas de view, c'est le cas pour une API où le controller va renvoyer une donnée en JSON mais sans passer par un fichier  contenant du HTML, ou bien pour une méthode POST (enregistrer un nouveau film par exemple) qui redirigera plutôt vers la liste des films plutôt qu'une vue disant simplement "le film est enregistré".

L'intérêt d'une architecture en MVC est de séparer les ressources en petits modules qui fonctionnent tous de la même manière.

Pour orchestrer tout ce fonctionnement, nous nous servons d'un **router** : c'est un outil qui permet d'associer une *route* (l'URL !) demandée par l'utilisateur à un controller et une action en particulier.

## Le détail des étapes
Voilà comment le tout fonctionne :

1. L'**utilisateur** va sur la page https://www.example.com/movies/3
> Grâce à un fichier .htaccess (un fichier qui donne des instructions au serveur Apache, celui qui gère les requêtes HTTP!), toute URL tapée par l'utilisateur ou par l'action d'un formulaire est redirigée vers... https://www.example.com/index.php. En effet, dans index.php, on inclut tous les fichiers nécessaires au bon fonctionnement du MVC, et notamment le router ! C'est lui qui prend le relai et qui va comprendre qu'on veut aller sur `/movies/3`.
2. Le **router** va réagir à cette demande: "On me demande la route `/movies/3` ! Je transmet la requête au **controller** correspondant, c'est `MoviesController`. De plus, vu comme est composée la requête, je sais que l'utilisateur veut consulter un film : je demande la méthode `read()`. Et comme je sais qu'on veut le film #3, je transmet cette variable aussi ! Je demande donc précisément `read($id)`, avec `$id = 3`." 
> En effet, par convention on appellera nos controllers par le nom de la ressource, au pluriel. Eh oui : c'est le "contrôleur des films" (`MoviesController`) ! Et comme c'est une classe, avec une première lettre majuscule.

> Par convention encore, lorsque l'on consultera une donnée, on demandera la méthode `read()` : comme ça, quelle que soit la nouvelle ressource que j'ai à mettre dans mon projet, les méthodes seront toujours les même ! (On verra la liste des méthodes conventionnelles plus bas).
3. Le **controller** `MoviesController` reçoit la demande venant du router : on lui a demandé la méthode `read($id)` avec `$id = 3`. La méthode `read($id)` va demander au **model** `Movie` le film ayant l'id 3, éventuellement faire des petits traitements sur la donnée reçue (si je suis un utilisateur Allemand, je traduis les données automatiquement, si je suis logué comme un utilisateur Admin, je prévois d'afficher les boutons d'édition et de suppression du film...). Lorsque mes données sont prêtes, j'envoie le tout à une **view** qui sera affichée à l'utilisateur.
> En fait, le controller est juste une classe, qui contient des méthodes (l'équivalent des fonctions mais dans une classe). Son but est de recevoir les demandes dispatchées par le router, et faire les opérations nécessaires à ce que l'on voulait faire en saisissant quelque chose dans l'URL. 

> Par convention là aussi, nous appelerons nos models par le nom de la ressource au singulier : `Movie` est le modèle d'un film ! Et comme c'est une classe, avec une première lettre majuscule.
4. Le **Model** se voit demander par le controller le film ayant l'ID numéro 3. C'est son rôle que de se connecter à la base de données (avec PDO par exemple) et de renvoyer le film demandé.
> De même que le controller, le Model est juste une classe avec des méthodes. En fait, le controller a demandé au model `Movie` une méthode que l'on écrira par exemple `findOne($id)`. Cela aurait pu être `select($request)`, il y a des tas de façons d'implémenter un Model.
5. La donnée venant du Model envoyée au Controller, `MoviesController` peut maintenant la traiter s'il en a besoin, et enfin envoyer ces données à la **View** : c'est en fait simplement un template en HTML qui va accueillir des variables en PHP là où nécessaire (le titre de la page, le titre du film, les données du film...).
6. Enfin, l'utilisateur qui au départ a demandé la page https://www.example.com/movies/3... se voit afficher la page du film dont l'ID est 3 !

## Pourquoi faire tout cela plutôt qu'une série de pages par table, comme par exemple un CRUD par table ?
On pourrait tout à fait bâtir notre application comme on l'a fait pour les CRUD et faire quelque chose comme cela :
```
localhost/project/
    index.php
    movies/
        add.php
        save.php
        list.php
        show.php

    categories/
        add.php
        save.php
        list.php
        show.php

    actors/
        add.php
        save.php
        list.php
        show.php
```

Ce système fonctionnerait tout à fait ! Néanmoins, nous serions obligés de (1) répéter du code de partout, (2) un peu trop mélanger du HTML et du PHP, (3) éparpiller les grands axes de traitement de la donnée (réception d'une requête, traitements en base de données, réponse à la demande...) : si une modification s'opère au niveau de la base de données (changer le nom d'un champ par exemple), ce sont tous les fichiers impliqués qu'il faudrait modifier. En MVC, il n'y aurait que le Model de la table que l'on a modifié qu'il faudrait adapter. De plus, grâce aux classes et à l'héritage, on peut économiser beaucoup de code en le transmettant de classes parents à classes enfants !

Voyons maintenant comment mettre en place un projet MVC !