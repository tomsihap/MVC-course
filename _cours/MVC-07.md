
# Gestion de données: MCD et relations (pratique)

> Rappel : avant de passer à la mise en place des models, il faut absolument que votre MCD soit dessiné même de façon sommaire afin de comprendre quelles relations vont avec quelles autres models.

> Note : Nous partirons du principe que les models pour chaque table sont créés, qu'il existe des getters et setters pour chaque champ ainsi que des attributs pour chaque champs.

## 1. Implémenter une relation 1-1 ou 1-N

Une relation 1-1 ou 1-N veut dire que, depuis User par exemple (la partie "1" du schéma "1-1" et "1-N", la partie qui "possède" une autre ressource), il existe une table où un champ "id_user" existe. En effet : il existe bien "id_user" dans la table Adresse et "id_user" dans la table Article !

Il nous faut donc des accesseurs depuis le model User qui nous permettent d'accéder aux données de ces deux tables (d'où l'intérêt de dessiner le MCD ! On sait tout de suite quelles méthodes il faudra implémenter).

La différence néanmoins entre 1-1 et 1-N est que dans le cas de 1-1, on ne va chercher qu'une seule ressource (une adresse). Dans le cas 1-N, on va chercher plusieurs ressources (des articles). Ce qui nous amène à nommer nos méthodes ainsi : `adresse()` (au singulier) et `articles()` (au pluriel).

###  Code
```php
// User.php

class User extends Db {
	// ...
	
	public function adresse() {
	}

	public function articles() {
	}
}
```

### Explications
L'idée est de pouvoir faire des choses comme :
```php
$user = User::findOne(1); // On récupère le user #1
$user->adresse(); // On récupère l'adresse de l'user sous forme d'objet
$user->articles(); // On récupère un tableau d'articles appartenant à l'user, sous forme d'objets

// On affiche la liste des articles de l'user
foreach($user->articles() as $article) {
	echo $article->title();
	echo "<br>";
}
```
L'avantage de récupérer ainsi les données, toujours sous forme d'objets, est que grâce à cela je peux utiliser toutes les méthodes de la classe `Adresse` sur l'objet `$user->adresse()`, et toutes les méthodes de la classe `Article` sur les éléments du tableau `$user->articles()` !

Exemple : `$user->adresse()` étant bien un objet de la classe `Adresse`, je peux faire :
```php
$user = User::findOne(1);
$user->adresse()->setCity('Nancy');
$user->adresse()->save(); 
```

Ou en raccourci (parce que mon setter me retourne $this... donc l'objet lui même) :
```php
$user = User::findOne(1);
$user->adresse()->setCity('Nancy')->save();
```

### Code

On va donc remplir nos méthodes : pour cela, on va devoir appeler dans Adresse et Articles des méthodes qui nous permettent de faire une recherche non pas par rapport à leur id (`Adresse::findOne()`), mais par rapport à l'id user.

Puisque nous sommes dans la classe User (c'est User qui va chercher l'adresse et les articles), on passe bien sûr l'ID de l'user cherchant ces données aux méthodes de Adresse et de Article, d'où le `$this->id()` en paramètres :

```php
// User.php

class User extends Db {
	// ...
	
	public function adresse() {
		return Adresse:findByUser($this->id());
	}

	public function articles() {
		return Article::findByUser($this->id());
	}
}
```

> On créée bien sûr les méthodes correspondantes. Attention : vérifiez bien dans quel fichier vous travaillez ! On a modifié `User.php` ci-dessus, on travaille sur `Adresse.php` puis `Article.php` ci-dess**o**us!

```php
// Adresse .php

class Adresse extends Db {
	// ...
	
	public static function findByUser($id_user) {
		// J'utilise la fonction find qui est déjà dans Adresse, et qui utilise Db::dbFind
		// dont la syntaxe est expliquée dans la documentation de Db.php.
		return self::find([
			['id_user', '=', $id_user]
		]);
	}
}
```

```php
// Article.php

class Article extends Db {
	// ...

	public static function findByUser($id_user) {
		// J'utilise la fonction find qui est déjà dans Adresse, et qui utilise Db::dbFind
		// dont la syntaxe est expliquée dans la documentation de Db.php.
		return self::find([
			['id_user', '=', $id_user]
		]);
	}
}
```

### Utilisation

Et voilà, la classe User a dorénavant accès à l'objet `Adresse` et aux objets `Article` ! Pour tester, vous pouvez vous inspirer du code présent dans le point **Explications** ci-dessus.

## 2. Implémenter une relation N-N


Une relation N-N veut dire que, depuis Article et vers Category par exemple, il existe une table de jointure qui lie les deux tables.

Il nous faut donc des accesseurs depuis le model Article **et** depuis le model Category, qui nous permettent d'accéder respectivement à : 
- aux catégories depuis un article (pouvoir faire `$article->categories();`)
- aux articles depuis une catégorie (pouvoir faire `$category->articles();`)

Ici, comme il y a un "N" des deux côtés (plusieurs articles pour une catégorie, plusieurs catégories pour un article), les méthodes seront nommées au pluriel dans chaque model.

###  Code
```php
// Article.php

class Article extends Db {
	// ...
	
	public function categories() {
		return ArticleCategory::findCategoriesByArticle($this->id());
	}
}
```
```php
// Category.php

class Category extends Db {
	// ...
	
	public function articles() {
		return ArticleCategory::findArticlesByCategory($this->id());
	}
}
```
> **Note** : on voit cette fois, à la différence du cas 1-1 et 1-N qui appelait `Adresse:findByUser()` et `Category::findByUser()` que l'on appelle une méthode définie dans la table de jointure, et pas dans la table d'arrivée elle même : `ArticleCategory::findByCategory()`.

Et bien sûr, comme toutes les tables, la table de jointure a son Model (avec ses getters/setters, constructeur et méthodes CRUD comme les autres).

On y ajoute les deux méthodes `findByCategory()` et `findByArticle()`. Ces deux méthodes sont un peu plus complexes à mettre en place. Par exemple, pour `findArticlesByCategory($id_category)` l'autre fonctionne de façon identique à l'inverse) :

- On est dans la table de jointure. On retrouve tous les éléments dont l'id_category est celui demandé afin d'avoir la liste des articles pour cette catégorie ;
- Ensuite, pour chacun des éléments récupérés (donc la liste couples id_article/id_category ayant l'id_category demandé... Donc en fait la liste des id_articles de la catégorie !), on va récupérer grâce à `Article::findOne($id)` l'objet article.
- On met tout cela dans un tableau $articles et on retourne ce tableau !

```php
// ArticleCategory.php

class ArticleCategory extends Db {
	// ...
	
	public function findArticlesByCategory($id_category) {
		// On retrouve la liste des couples id_article/id_category qui ont l'id de catégorie donné :
		$elements = self::find([
			['id_category', '=', $id_category]
		]);
		
		$articles = []; // Tableau qui contiendra les articles
		
		// Pour chaque élément trouvé dans la table de jointure, on retrouve l'article correspondant :
		foreach($elements as $el) {
			$articles[] = Article::findOne($el['id_article']);
		}
		
		return $articles;
	}
	
	// Idem pour l'inverse !
	public function findCategoriesByArticle($id_article) {
		$elements = self::find([
			['id_article', '=', $id_article]
		]);
		
		$categories = [];

		foreach($elements as $el) {
			$categories[] = Category::findOne($el['id_category']);
		}
		
		return $categories ;
	}
}
```




### Explications
L'idée est de pouvoir faire des choses comme :
```php
$article = Article::findOne(1);
$article->categories(); // On récupère la liste des catégories de l'article

// On peut par exemple afficher la liste des catégories :
foreach($article->categories() as $category) {
	echo $category->title();
	echo '<br>';
}

// Idem pour l'inverse : d'une catégorie, je veux ses articles.

$category = Category::findOne(1);

// J'affiche la liste des articles de la catégorie :
foreach($category->articles() as $article) {
	echo $article->title();
	echo "<br>";
}
```

> Comme pour le cas 1-1 et 1-N, on utilise bien des objets dans `$category->articles()` et `$articles->categories()`. On a donc accès à toutes les méthodes de ces objets là dans le foreach ! Par exemple faire une modification sur chaque article...

> **Comment ajouter un article à une catégorie ?** Pour cela, il faudra simplement créer un nouvel objet ArticleCategory avec dans en constructeur quelque chose comme : `__construct($id_article, $id_category){}`, puis l'enregistrer avec `->save()` comme n'importe quel autre objet !

## 3. Requêtes complexes: exécuter du SQL directement depuis le Model

### A. Rappels sur les requêtes effectuées
Avec les points 1 et 2, nous avons vu comment traiter les cas classiques de communication entre plusieurs tables (1-1/1-N/N-N) : concrètement, sous couvert de l'abstration offerte par la classe Db, on a simplement fait :

#### Récupérer l'adresse d'un utilisateur (1-1)
```sql
SELECT *
FROM Adresse
WHERE id_user = $idUser
````

#### Récupérer les articles d'un utilisateur (1-N)
```sql
SELECT *
FROM Article
WHERE id_user = $idUser
````

Et pour les cas N-N :
#### Récupérer les catégories d'un article (N-N)
```sql
SELECT *
FROM ArticleCategory
WHERE id_article = $idArticle

// Pour chacun des résultats retournés ci-dessus:
SELECT *
FROM Category
WHERE id_category = $idCategory
````

#### Récupérer les articles d'une catégorie (N-N)
```sql
SELECT *
FROM ArticleCategory
WHERE id_category = $idCategory

// Pour chacun des résultats retournés ci-dessus:
SELECT *
FROM Article
WHERE id_article = $idArticle
````

En réalité, les cas N-N sont gérés de façon sous-optimale : comme on utilise la classe Db qui ne nous permet que de faire des `SELECT` simples éventuellement agrémentés de `WHERE`, on n'a pas pu faire proprement les requêtes avec un `INNER JOIN` qui auraient plutôt du ressembler à :

#### Récupérer les catégories d'un article (N-N)
```sql
SELECT *
FROM ArticleCategory
INNER JOIN Category ON Category.id = ArticleCategory.id_category
WHERE ArticleCategory.id_article = $idArticle
````

#### Récupérer les articles d'une catégorie (N-N)
```sql
SELECT *
FROM ArticleCategory
INNER JOIN Article ON Article.id = ArticleCategory.id_article
WHERE ArticleCategory.id_category= $idCategory
````

Soit en 1 select, récupérer tous les résultats, plutôt que 1 select par article ou par catégorie ! Une optimisation vraiment conséquente.

### B. Comment effectuer des requêtes personnalisées

Pour cela, nous allons réutiliser `PDO` comme nous l'avons déjà fait en programmation fonctionnelle.  Il se trouve que la classe Db nous fournit déjà un pointeur de base de données avec PDO, on va pouvoir l'utiliser :

#### Exemple: requête pour récupérer le nombre d'articles par catégorie

> Où placer cette requête ? Est-il plus cohérent de la placer dans Article (depuis l'usine Article, est-ce que je peux avoir accès au nombre d'articles par catégorie ?) ou dans Category (depuis l'usine Category, est-ce que je peux avoir accès au nombre d'articles par catégorie ?)
> La réponse est oui pour les deux cas. Au lieu de placer la fonction dans l'une ou l'autre classe... Autant la placer dans la classe de jointure !

```php
class ArticleCategory extends Db {

	// ...

	// La méthode est statique afin d'être appelée depuis "l'usine"
	public static function articlesCountByCategory() {
	
		// J'utilise getDb de la classe Db qui me donne un pointeur PDO.
		$bdd = Db::getDb();

		// Définition de la requête
		$req = "SELECT category.title as title, count(*) as count
				FROM `article_category`
				INNER JOIN category ON  category.id  =  article_category.id_category
				GROUP BY id_category";

		// Execution de la requête
		$res = $bdd->query($req);

		// Récupération des résultats
		$data = $res->fetchAll(PDO::FETCH_ASSOC);

		// On retourne les résultats
		return $data;
	}
}
```
Utilisation :
```php

// Comme la méthode est statique, je peux l'appeler depuis "l'usine" à ArticleCategory :
$articlesCountByCategory = ArticleCategory::articlesCountByCategory();


// Un foreach pour retourner quelque chose comme : "Catégorie Sciences: 6 articles"
foreach($articlesCountByCategory as $artCount) {
	echo "Catégorie " . $artCount['title'] . ": " . $artCount['count'] . " articles";
}
```

> **Note** : Comment se fait-il que je puisse utiliser `title` et `count` dans `$artCount['title']` et `$artCount['count']` ? Car je les ai définis en aliases dans ma requête SQL ! En effet, dans le SELECT, il y a : `SELECT category.title as title, count(*) as count`.

### C. Conclusion

On peut évidemment placer n'importe quelle requête SQL dans n'importe quel Model. Il faut bien penser à instancier le pointeur de base de données avec `$bdd = Db::getDb();`.
En réalité, s'il n'y avait pas eu la classe `Db`, toutes les méthodes de CRUD (find, findOne, findBy, save, update, delete...) auraient toutes pu être rédigées en SQL de cette manière !

> Il n'y a aucune problème à faire des requêtes SQL à chaque méthode CRUD d'un Model si vous ne souhaitez pas utiliser l'abstraction offerte par la classe Db.  Pour exemple ,la classe Article avec des méthodes uniquement en SQL est dans le fichier `/models/ArticlesSQLVERSION.php`.