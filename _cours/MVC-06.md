
# Gestion de données: MCD et relations (théorie)

Le MVC étant enfin prêt, il s'agit maintenant de reproduire les étapes depuis le routeur jusqu'à la vue pour réaliser votre projet.

Nous allons voir quelques méthodes à implémenter dans les classes qui peuvent être plus complexes : les différentes cardinalités d'un MCD (1-1, 1-N, N-N).

## 1. Rappels sur les cardinalités

> Note : pour les cardinalités suivantes, on va supposer que :
> - Un utilisateur n'a qu'une seule adresse
> - Une adresse n'appartient qu'à un seul utilisateur
> - Un utilisateur peut être auteur de plusieurs articles
> - Un article ne peut appartenir qu'à un seul auteur
> - Un article peut avoir plusieurs catégories
> - Une catégorie peut avoir plusieurs articles

> Ce travail d'inventaire des relations possibles entre mes données est **extrêmement** important : il faut le faire avant même de commencer à coder. Il nous permet de savoir à l'avance les features qui seront disponibles, possibles, celles qui sont actuellement impossibles et qui demanderaient des tables supplémentaires, etc.

### 1-1
- 1 élément de A possède 1 seul élément de B.
- 1 élément de B possède 1 seul élément de A.
![Cardinalité 1-1](https://i.imgur.com/GzSNMXg.png)
![Cardinalité 1-1 bis](https://i.imgur.com/qgK84bX.png)

Une cardinalité 1-1 est une relation entre deux tables qui pourraient être fusionnées en une seule. C'est à dire qu'on pourrait clairement mettre les données de la table Address dans la table User, puisque un utilisateur n'a qu'une seule adresse et une adresse n'appartient qu'à un utilisateur.
On a deux exemples en image: on peut aussi bien dire qu'une adresse appartient à un utilisateur (en mettant `id_user` dans Address), ou bien dire qu'un utilisateur possède une adresse (en mettant `id_address` dans la table User). 

> **Où placer l'id ?** Le choix de l'une ou l'autre table revient à vous et à l'aspect métier de votre projet : quel sens de lecture vous semble le plus cohérent ?

### 1-N
- 1 élément de A possède plusieurs élements de B.
- 1 élément de B n'appartient qu'à 1 seul élément de A.

![Cardinalité 1-N](https://i.imgur.com/zVmzYxW.png)

Une cardinalité 1-N est une relation d'appartenance d'une table avec une autre. Ici, un article appartient à un utilisateur (on a un `id_user` du côté de la table possédée, Article), et un utilisateur possède plusieurs articles.

> **Où placer l'id ?** Cette fois, la clé étrangère est la clé primaire de la table qui possède : ici, les utilisateurs possèdent. On place donc leur clé primaire (`id_user`) en tant que clé étrangère dans la table qui est possédée (les articles sont possédés par des utilisateurs, c'est dans Article que l'on met `id_user`).

### N-N
- Un élément de A possède plusieurs éléments de B
- Un élément de B possède plusieurs éléments de A![Relation N-N](https://i.imgur.com/QI8sIe0.png)

Une cardinalité N-N est un peu plus complexe : ici, on a un article qui peut avoir plusieurs catégories, ainsi que l'inverse: une catégorie peut avoir plusieurs articles.

- Si nous avions qu'une seule des deux phrases, *un article peut avoir plusieurs catégories*, on n'aurait juste à mettre un `id_article` dans la table Categorie (l'id du possesseur va dans la table du possédé, une relation 1-N) : on répond à la phrase, puisque les id d'articles sont bien dans la table Catégorie (les catégories peuvent avoir un id_article, on a bien "un article peut avoir plusieurs catégories".
- Or, on a également l'autre phrase qui est vraie : *une catégorie peut avoir plusieurs articles*. Si on mettait une autre relation 1-N avec un `id_category` dans la table Article (le possédé, article, prend l'id du possesseur, catégorie), on on répondrait aussi à la phrase : puisque les id de catégories sont bien dans la table Article (les articles peuvent avoir un id_category), on a bien *une catégorie peut avoir plusieurs articles*.
- ![Lexomil](https://i.imgur.com/B71Ksv1.jpg?1)

- Le problème, c'est qu'en faisant ainsi, les 2 phrases ne pourront pas être vraies en même temps ! On ne peut pas dire qu'un article appartient à plusieurs catégories, et qu'une catégorie appartient à plusieurs articles à la fois.

- La phrase correcte serait plutôt : "plusieurs articles peuvent avoir plusieurs catégories, plusieurs catégories peuvent avoir plusieurs articles".

- La solution est  créer une table de jointure (`article_category`) :

|id_article|id_category  |
|--|--|
| 1 | 1 |
| 1 | 2 |
| 1 | 3 |
| 2| 1 |
| 3 | 2 |
| 3| 4 |
> L'article 1 a les catégories 1, 2, 3, l'article 2 a la catégorie 1, l'article 3 a les catégories 2 et 4
> La catégorie 1 a les articles 1 et 2, la catégorie 2 a les articles 1 et 3, etc.

Comme on le voit, cette table de jointure possède deux clés **qui seront forcément primaires** et qui correspondent aux ID d'autres tables : on a donc : 
- une relation 1-N entre Article et article_category,
- une relation 1-N entre Category et article_category

Ce qui nous donne le schéma ci-dessus !

## 2. Vue d'ensemble

![MVC : vue d'ensemble](https://i.imgur.com/iiHoK0l.png)
