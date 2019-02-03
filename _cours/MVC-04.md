# Réalisation d'un MVC : mise en pratique

> La structure de notre projet est enfin réalisée. Si besoin, vous retrouverez ce modèle de projet fonctionnel dans le dossier `_base_project` à utiliser de suite.

## Comment commencer à travailler ?

Parmi tous ces fichiers à gérer (router, controllers, models, views, dépendances...), le travail peut paraître impressionnant à premier abord. Nous allons voir comment travailler de façon méthodique afin de profiter d'un des avantages du MVC : pouvoir travailler vite et efficacement grâce à un découpage logique !
> Principes de développement: [Keep It Simple, Stupid!](https://fr.wikipedia.org/wiki/Principe_KISS), [Don't Repeat Yourself](https://en.wikipedia.org/wiki/Don%27t_repeat_yourself), [SOLID](https://fr.wikipedia.org/wiki/SOLID_(informatique)), [You Ain't Gonna Need It](https://fr.wikipedia.org/wiki/YAGNI), [Extreme Programming](https://fr.wikipedia.org/wiki/Extreme_programming).

1. Créer les **tables** dans sa base de données.
2. Créer les **Model**, un par ressource.
3. Créer des **routes**: pensez qu'à chaque ressource, on peut avoir besoin en général d'au moins: "browse", "read", "edit", "add", "delete" (BREAD). Faites des routes en fonction !
4. Créer les **controllers**, un par ressource et une méthode par route nécessaire.
5. Créer les **views** pour chaque méthode de chaque ressource nécessitant une vue.
6. Répéter 1 à 5 pour chaque nouvelle ressource !


## Exemple d'une ressource : "Article"

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
    /**
     * Attributs
     * Pour chaque champ de la table, nous créons un attribut. En effet, nous allons générer un objet Article qui sera en fait une "copie dynamique" de son homologue enregistré dans la base de données ! Il a donc besoin des même attributs.
     * 
     * On les met en "protected": cela nous évitera d'accéder aux propriétés avec $article->title, mais plutôt en passant par un Getter, lequel nous retournera bien le "title" mais avec des modifications ou validations si besoin est.
     * 
     * Private ferait la même chose, mais bloquerait aussi l'accès aux classes enfants. Au cas où nous aurions des enfants de cette classe, nous restons sur Protected.
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


    /**
     * Méthodes magiques
     * Les méthodes magiques sont des méthodes qui s'activent à un moment précis de l'instanciation d'un objet issu de cette classe. __construct se lance en fait lorsque nous demandons "new Article() par exemple.
     * 
     * On va pouvoir créer des articles grâce à ce constructeur. En paramètres, nous allons entrer tous les champs obligatoires (ici : $title et $content).
     * 
     * Nous ajoutons aussi un paramètre $id qui sera null par défaut : il ne sera rempli que si nous créons un objet depuis des données de la base de données. S'il n'est pas rempli, c'est que nous venons de le créer de toutes pièces (un nouvel article par exemple).
     */
    public function __construct($title, $content, $id = null) {

        /**
         * Pour chaque argument, on utilise les Setters pour attribuer la valeur à l'objet.
         * Pour appeler une méthode non statique de la classe DANS la classe, on utilise $this.
         */
        $this->setTitle($title);
        $this->setDescription($description);
        $this->setId($id);
    }

    /**
     * Getters
     * On va définir la liste des getters. C'est ces méthodes qui nous permettent d'accéder aux données avec si besoin est des modifications entre la donnée brute de la BDD et ce que l'on veut récupérer.
     * 
     * Pour chaque champ que l'on a droit de lire, on crée un getter (même nom que le champ, en camelCase par convention).
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




    /**
     * Setters
     * On va définir la liste des setters. C'est ces méthodes qui nous permettent d'enregistrer les données avec si besoin est des modifications entre la donnée brute de la BDD et ce que l'on veut enregistrer.
     * 
     * Pour chaque champ que l'on a droit d'enregistrer, on crée un setter (même nom que le champ, en camelCase par convention, avec "set" devant).
     * 
     * Par exemple, les champs "id" et "created_at" ne peuvent pas être modifiés : on ne vas pas créer de getters pour eux (ils sont automatiques côté MySQL).
     * 
     * C'est aussi ici où l'on doit faire les validations à l'enregistrement !! On va faire l'exemple pour setTitle.
     * 
     * Pour gérer une erreur, on utilisera dorénavant throw new Exception('message');
     * 
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

    /**
     * Methods
     * C'est ici où nous allons créer les méthodes pour le CRUD : create, read, update, delete.
     */
    public function save() {
        $data = [
            "title"         => $this->title(),
            "description"   => $this->description()
        ];
        if ($this->id > 0) {
            $data["id"] = $this->id();
            $this->dbUpdate(self::TABLE_NAME, $data);
            return $this;
        }
        $this->id = $this->dbCreate(self::TABLE_NAME, $data);
        return $this;
    }
    public function delete() {
        $data = [
            'id' => $this->id(),
        ];
        
        $this->dbDelete(self::TABLE_NAME, $data);
        return;
    }
    public static function findAll() {
        return Db::dbFind(self::TABLE_NAME);
    }
    public static function find(array $request) {
        return Db::dbFind(self::TABLE_NAME, $request);
    }
    public static function findOne(int $id) {
        $element = Db::dbFind(self::TABLE_NAME, [
            ['id', '=', $id]
        ]);
        $element = $element[0];
        $cat = new Category($element['title'], $element['description'], $element['id']);
        return $cat;
    }
}
```