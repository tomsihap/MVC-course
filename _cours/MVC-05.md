# Réalisation d'un MVC : mise en pratique (controllers)

Maintenant que nous avons finalisé l'aspect "données" du projet, c'est à dire tout ce qui permet d'enregistrer des données
Pour cet exemple, nous allons suivre pas à pas chacune des étapes pour créer une ressource Article.

### 1. Créer la table Article

Nous allons créer une table Article en base de données définie comme suit :

```
Article
--------
id              int PK AI
title           varchar(150)
short_content   varchar(250)    nullable
content         text
id_author       int             nullable
created_at      datetime        default: CURRENT_TIMESTAMP
updated_at      datetime        default: CURRENT_TIMESTAMP
```

### 2. Créer le model Article

Nous allons créer le fichier `models/Article.php` (au singulier, A en majuscule) et le préparer comme cela :

```php

/**
 * Nous allons utiliser des méthodes issues de Db, nous disons que Article
 * est une classe enfant de la classe Db
 */
class Article extends Db {
```

#### Attributs et constantes
> Pour chaque champ de la table, nous créons un attribut. En effet, nous allons générer un objet Article qui sera en fait une "copie dynamique" de son homologue enregistré dans la base de données ! Il a donc besoin des même attributs.

>On les met en "protected": cela nous évitera d'accéder aux propriétés avec $article->title, mais plutôt en passant par un Getter, lequel nous retournera bien le "title" mais avec des modifications ou validations si besoin est.

>Private ferait la même chose, mais bloquerait aussi l'accès aux classes enfants. Au cas où nous aurions des enfants de cette classe, nous restons sur Protected.
```php
    /**
     * Attributs
     */
    protected $id;
    protected $title;
    protected $short_content;
    protected $content;
    protected $id_author;
    protected $created_at;
    protected $updated_at;


    /**
     * Constantes
     * Nous pouvons aussi définir des constantes. Ici, il s'agit du nom de la table. Ainsi, s'il venait à changer, nous n'aurons plus qu'à le changer à cet endroit.
     */
    const TABLE_NAME = "Article";
```

#### Méthodes magiques
> Les méthodes magiques sont des méthodes qui s'activent à un moment précis de l'instanciation d'un objet issu de cette classe. __construct se lance en fait lorsque nous demandons "new Article() par exemple.

> On va pouvoir créer des articles grâce à ce constructeur. En paramètres, nous allons entrer tous les champs obligatoires (ici : $title et $content).

> Nous ajoutons aussi un paramètre $id qui sera null par défaut : il ne sera rempli que si nous créons un objet depuis des données de la base de données. S'il n'est pas rempli, c'est que nous venons de le créer de toutes pièces (un nouvel article par exemple).

```php
    /**
     * Méthodes magiques
     */
    public function __construct($title, $content, $id = null, $short_content = null, $content = null, $id_author = null, $created_at = null, $updated_at = null) {

        /**
         * Pour chaque argument, on utilise les Setters pour attribuer la valeur à l'objet.
         * Pour appeler une méthode non statique de la classe DANS la classe, on utilise $this.
         */
        $this->setTitle($title);
        $this->setDescription($description);
        $this->setId($id);
    }
```

#### Getters

> On va définir la liste des getters. C'est ces méthodes qui nous permettent d'accéder aux données avec si besoin est des modifications entre la donnée brute de la BDD et ce que l'on veut récupérer.

> Pour chaque champ que l'on a droit de lire, on crée un getter (même nom que le champ, en camelCase par convention).
```php
    /**
     * Getters
     */

    public function id() {
        return $this->id;
    }
    public function title() {
        return $this->title;
    }
    public function shortContent() {
        return $this->short_content;
    }
    public function content() {
        return $this->content;
    }
    public function idAuthor() {
        return $this->id_author;
    }
    public function createdAt() {
        return $this->created_at;
    }
    public function updatedAt() {
        return $this->updated_at;
    }

    /**
     * On peut bien sûr créer des getters pour des usages propres à l'application !
     * On va par exemple créer les getters suivants :
     * - Date de création formatée en français
     * - Date de mise à jour formatée en français
     * - Intervalle entre la date de mise à jour et la date de création, en français
     */

    public function createdAtFr() {
        $date = new DateTime($this->createdAt());
        $dateFr = $date->format('d/m/Y H:i:s');

        return $dateFr;
    }

    /**
     * Pour l'exercice, on rajoute une variable facultative $time qui permet de retourner ou non l'heure. A l'usage on aurait donc :
     * $article->updatedAtFr()          // Retourne "21/12/2018 15:14:12"
     * $article->updatedAtFr(false)     // Retourne "21/12/2018"
     */
    public function updatedAtFr($time = true) {
        $date = new DateTime($this->updatedAt());

        $format = ($time) ? 'd/m/Y H:i:s' : 'd/m/Y';
        $dateFr = $date->format($format);

        return $dateFr;
    }

    /**
     * Grâce à la documentation PHP, on peut savoir comment comparer deux objets DateTime.
     * La variable $intervalle est un objet DateInterval :
     * http://www.php.net/manual/en/class.dateinterval.php
     * 
     * Cette fonction retourne la différence en jours.
     */
    public function daysSinceCreation() {
        $createdAt = new DateTime($this->createdAt());
        $updatedAt = new DateTime($this->updatedAt());

        $intervalle = $createdAt->diff($updatedAt);

        return $intervalle->d;

    }
```

#### Setters
> On va définir la liste des setters. C'est ces méthodes qui nous permettent d'enregistrer les données avec si besoin est des modifications entre la donnée brute de la BDD et ce que l'on veut enregistrer.

> Pour chaque champ que l'on a droit d'enregistrer, on crée un setter (même nom que le champ, en camelCase par convention, avec "set" devant).

> Par exemple, les champs "id" et "created_at" ne peuvent pas être modifiés : on ne vas pas créer de getters pour eux (ils sont automatiques côté MySQL).

> C'est aussi ici où l'on doit faire les validations à l'enregistrement !! On va faire l'exemple pour setTitle.

> Pour gérer une erreur, on utilisera dorénavant throw new Exception('message');


```php
    /**
     * Setters
     */

    public function setId($id) {
        return $this->id = $id;
    }

    public function setTitle($title) {

        if (strlen($title) == 0) {
            throw new Exception('Le titre ne peut pas être vide.');
        }

        if (strlen($title) > 150) {
            throw new Exception('Le titre ne peut pas être supérieur à 150 caractères.');
        }

        return $this->title = $title;
    }

    public function setShortContent($short_content = null) {
        return $this->short_content = $short_content;
    }
    public function setContent($content) {
        return $this->content = $content;
    }
    public function setIdAuthor($id_author = null) {
        return $this->id_author = $id_author;
    }

    public function setUpdatedAt($updated_at) {
        return $this->updated_at = $updated_at;
    }
```

#### Autres méthodes : CRUD

> Nous avons créé les getters (récupérer la donnée filtrée si besoin) et les setters (enregistrer la donnée, filtrée si besoin). Il nous faut aussi des méthodes pour faire des opérations en BDD autour de notre objet.

> Nous avons importé la classe `Db` lors de la mise en place de notre projet : il s'agit d'une bibliothèque de méthodes nous permettant de facilement faire une connexion avec PDO sans se soucier de comment se passe la connexion (eh oui, c'est le but de la classe `Db` : elle s'occupe de tout et nous donne des interfaces faciles à utiliser !)

##### Un exemple: Db::dbCreate(string $table, array $data)

D'après la documentation de `Db` (on peut la retrouver au sein du fichier de la classe), pour utiliser dbCreate, il faut passer en premier paramètre un `string $table` (ça tombe bien, c'est la constante `TABLE_NAME` que l'on a défini plus haut) et en second paramètre un tableau de données au format `champ => valeur`.

On va donc :
1. Créer un tableau $data contenant la liste des champs de ma table que je souhaite enregistrer (bien penser évidemment à passer tous les champs NON NULL au minimum, sinon MySQL n'aimera pas !)
2. Si mon objet possède un ID c'est qu'il existe forcément en base de données, j'appelle plutôt la méthode update du Model (elle est définie juste en dessous, elle fait pareil mais avec dbUpdate plutôt que dbCreate)

3. J'appelle la méthode dbCreate de la classe Db : `Db::dbCreate` à laquelle je passe tous les arguments dont j'ai besoin. 

> Note : pour accéder à une constante de classe au sein de la classe, on peut utiliser `self::TABLE_NAME`.

4. D'après la documentation, je sais que l'opération `Db::dbCreate` retourne l'ID créé. Je le récupère et l'attribue à mon objet ($this->id = $nouvelId).

5. Enfin, je retourne l'objet lui même car il faut en général toujours retourner quelque chose d'une fonction, autant retourner quelque chose de cohérent.

> Pour les autres méthodes, je ne les décrit pas ici car elles suivent le même principe : on lit la documentation pour voir comment utiliser l'outil venant de la classe Db et on l'applique.

```php
    /**
     * CRUD Methods
     */
    public function save() {

        $data = [
            "title"         => $this->title(),
            "description"   => $this->description()
        ];

        if ($this->id > 0) : return $this->update();

        $nouvelId = Db::dbCreate(self::TABLE_NAME, $data);

        $this->setId($nouvelId);

        return $this;
    }

    public function update() {

        if ($this->id > 0) {

            $data = [
                "id"                => $this->id(),
                "title"             => $this->title(),
                "short_content"     => $this->shortcontent(),
                "content"           => $this->content(),
                "id_author"         => $this->idAuthor(),
                "updated_at"        => "CURRENT_TIMESTAMP"
            ];

            Db::dbUpdate(self::TABLE_NAME, $data);

            return $this;
        }

        return;
    }

    public function delete() {
        $data = [
            'id' => $this->id(),
        ];
        
        Db::dbDelete(self::TABLE_NAME, $data);
        return;
    }

```
> On peut aussi ajouter des méthodes de recherche qui nous facilitent la vie (findAll, find, findOne plutôt que de faire des SELECT c'est plus simple ! Mais il faut l'implémenter).

> Là aussi, on effectue tout cela en s'inspirant de la documentation de la classe Db.

> Nous précisons à chaque méthode un argument $objects = true qui nous permet de dire au Model de retourner des objets par défaut pour chaque entrée en base de données recherchée (ainsi on a directement accès aux méthodes sur la donnée), plutôt qu'un simple tableau de données brut.

> On pourrait avoir besoin de cet argument mis sur false ($objects = false) dans les cas où on a besoin de la donnée brute plutôt qu'un objet (certaines API par exemple).

```php
    public static function findAll($objects = true) {

        $data = Db::dbFind(self::TABLE_NAME);

        if ($objects) {
            $objectsList = [];

            foreach ($data as $d) {
                $objectsList[] = new Article($d['title'], $d['content'], $d['id'], $d['short_content'], $d['content'], $d['id_author'], $d['created_at'], $d['updated_at']);

                return $objectsList;
            }
        }

        return $data;
    }

    public static function find(array $request, $objects = true) {
        $data = Db::dbFind(self::TABLE_NAME, $request);

        if ($objects) {
            $objectsList = [];

            foreach ($data as $d) {
                $objectsList[] = new Article($d['title'], $d['content'], $d['id'], $d['short_content'], $d['content'], $d['id_author'], $d['created_at'], $d['updated_at']);

                return $objectsList;
            }
        }

        return $data;
    }

    public static function findOne(int $id, $object = true) {

        $request = [
            ['id', '=', $id]
        ];

        $element = Db::dbFind(self::TABLE_NAME, $request);
        $element = $element[0];

        if ($object) {
            $article = new Article($element['title'], $element['content'], $element['id']);
            return $article;
        }

        return $element;
        
    }

} // Dernière accolade correspondant à la première ligne "class Article { ..."
```

Voilà, le **Model** est prêt. Vous pouvez bien sûr rajouter d'autres méthodes qui vous semblent intéressantes sur la gestion des données, selon vos propres données vous aurez sans doute des cas particuliers à gérer (des dates, des intervalles, mais aussi des types spéciaux, l'upload de fichiers se fait également ici en partie...).