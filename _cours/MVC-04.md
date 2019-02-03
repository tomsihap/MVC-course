# Réalisation d'un MVC : mise en pratique

La structure de notre projet est enfin réalisée. Si besoin, vous retrouverez ce modèle de projet fonctionnel dans le dossier `_base_project`.


Un **routeur** va *écouter* ce qui est saisi dans l'URL d'une requête HTTP (en GET par l'URL, en POST via un formulaire par exemple) : selon ce qui est demandé par l'utilisateur ou le formulaire, le rôle du router est de rediriger cette demande vers la bonne action, dans le bon fichier.

> Par exemple, un utilisateur demande la route `/articles`. Pour le site `https://www.example.com`, l'URL serait `https://www.example.com/articles`.

## 2. Controller
Le rôle du 



un **controller** à cette URL : ̀`$router->get('/articles', 'ArticlesContro router est 


> Ici, lorsque l'utilisateur tappe `example.com/articles`, l'action qui sera effectuée est la méthode `index` qui est dans la classe `ArticlesController`.

Il suffit donc, pour réaliser une nouvelle *route*, de :

1. Créer la route dans le fichier du routeur
2. Créer l'action dans la classe correspondante, le *controller*.


