<?php

/**
 * Nous allons utiliser des méthodes issues de Db, nous disons que Article
 * est une classe enfant de la classe Db
 */
class User extends Db {

    /**
     * Attributs
     */
    protected $id;
    protected $firstname;
    protected $surname;
    protected $created_at;

    /**
     * Constantes
     * Nous pouvons aussi définir des constantes. Ici, il s'agit du nom de la table. Ainsi, s'il venait à changer, nous n'aurons plus qu'à le changer à cet endroit.
     */
    const TABLE_NAME = "User";

    /**
     * Méthodes magiques
     */
    public function __construct($firstname, $surname, $id = null, $created_at = null) {

        /**
         * Pour chaque argument, on utilise les Setters pour attribuer la valeur à l'objet.
         * Pour appeler une méthode non statique de la classe DANS la classe, on utilise $this.
         */
        $this->setFirstname($firstname);
        $this->setSurname($surname);
        $this->setId($id);
        $this->setCreatedAt($created_at);
    }

    /**
     * Getters
     */

    public function id() {
        return $this->id;
    }
    public function firstname() {
        return $this->firstname;
    }
    public function surname() {
        return $this->surname;
    }
    public function createdAt() {
        return $this->created_at;
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
     * Setters
     */

    public function setId($id = null) {
        return $this->id = $id;
    }

    public function setFirstname($firstname = null) {
        return $this->firstname = $firstname;
    }

    public function setSurname($surname = null) {
        return $this->surname = $surname;
    }

    public function setCreatedAt($created_at = null) {
        return $this->created_at = $created_at;
    }



     /**
     * CRUD Methods
     */
    public function save() {

        $data = [
            "firstname"  => $this->firstname(),
            "surname"   => $this->surname()
        ];

        if ($this->id > 0) return $this->update();

        $nouvelId = Db::dbCreate(self::TABLE_NAME, $data);

        $this->setId($nouvelId);

        return $this;
    }

    public function update() {

        if ($this->id > 0) {

            $data = [
                "firstname"  => $this->firstname(),
                "surname"   => $this->surname()
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

                $objectsList[] = new User($d['firstname'], $d['surname'], intval($d['id']), $d['created_at']);
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
                $objectsList[] = new User($d['firstname'], $d['surname'], intval($d['id']), $d['created_at']);

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

        if (count($element) > 0) $element = $element[0];
        else return;

        if ($object) {
            $article = new User($element['firstname'], $element['surname'], intval($element['id']), $element['created_at']);
            return $article;
        }

        return $element;
        
    }

    public function articles() {

        $articles = Article::findByAuthor($this->id());
        return $articles;
    }

}