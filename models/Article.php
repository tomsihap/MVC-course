<?php

/**
 * Nous allons utiliser des méthodes issues de Db, nous disons que Article
 * est une classe enfant de la classe Db
 */
class Article extends Db {

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

    /**
     * Méthodes magiques
     */
    public function __construct($title, $content, $id = null, $short_content = null, $id_author = null, $created_at = null, $updated_at = null) {

        /**
         * Pour chaque argument, on utilise les Setters pour attribuer la valeur à l'objet.
         * Pour appeler une méthode non statique de la classe DANS la classe, on utilise $this.
         */
        $this->setTitle($title);
        $this->setContent($content);
        $this->setId($id);
        $this->setShortContent($short_content);
        $this->setIdAuthor($id_author);
        $this->setCreatedAt($created_at);
        $this->setUpdatedAt($updated_at);
    }

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

     /**
     * Setters
     */

    public function setId(int $id) {
        return $this->id = $id;
    }

    public function setTitle(string $title) {

        if (strlen($title) == 0) {
            throw new Exception('Le titre ne peut pas être vide.');
        }

        if (strlen($title) > 150) {
            throw new Exception('Le titre ne peut pas être supérieur à 150 caractères.');
        }

        return $this->title = $title;
    }

    public function setShortContent(string $short_content = null) {
        return $this->short_content = $short_content;
    }
    public function setContent(string $content) {
        return $this->content = $content;
    }
    public function setIdAuthor(int $id_author = null) {
        return $this->id_author = $id_author;
    }

    public function setCreatedAt(string $created_at) {
        return $this->created_at = $created_at;
    }

    public function setUpdatedAt(string $updated_at) {
        return $this->updated_at = $updated_at;
    }

     /**
     * CRUD Methods
     */
    public function save() {

        $data = [
            "title"         => $this->title(),
            "content"   => $this->content()
        ];

        if ($this->id > 0) return $this->update();

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

      public static function findAll($objects = true) {


        $data = Db::dbFind(self::TABLE_NAME);
        
        if ($objects) {
            $objectsList = [];

            foreach ($data as $d) {

                $objectsList[] = new Article($d['title'], $d['content'], $d['id'], $d['short_content'], $d['content'], $d['id_author'], $d['created_at'], $d['updated_at']);
            }

            return $objectsList;
        }

        return $data;
    }

    public static function find(array $request, $objects = true) {
        $data = Db::dbFind(self::TABLE_NAME, $request);

        if ($objects) {
            $objectsList = [];

            foreach ($data as $d) {
                $objectsList[] = new Article($d['title'], $d['content'], $d['id'], $d['short_content'], $d['content'], $d['id_author'], $d['created_at'], $d['updated_at']);

            }
            return $objectsList;
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