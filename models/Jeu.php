<?php

class Jeu extends Db {

    private $id;
    private $nom;
    private $sortie_eu;
    private $lowerprice;
    private $slug;

    const PREFIX = "jeux_";
    const TABLE_NAME = "site_jeux";

    public function __construct(string $nom,
                                int $id = null,
                                string $sortie_eu = null,
                                float $lowerprice =null,
                                string $slug = null) {

        $this->setNom($nom);
        $this->setId($id);
        $this->setSortieEu($sortie_eu);
        $this->setLowerprice($lowerprice);
        $this->setSlug($slug);
    }

    /**
     * Getters
     */

    public function id() {
        return $this->id;
    }
    public function nom() {
        return $this->nom;
    }
    public function sortieEu() {
        return $this->sortie_eu;
    }
    public function lowerprice() {
        return $this->lowerprice;
    }
    public function slug() {
        return $this->slug;
    }

    /**
     * Setters
     */
    public function setId($id) {
        $this->id = $id;
        return $this;
    }
    public function setNom($nom) {
        $this->nom = $nom;
        return $this;
    }
    public function setSortieEu($sortie_eu) {
        $this->sortie_eu = $sortie_eu;
        return $this;
    }
    public function setLowerprice($lowerprice) {
        $this->lowerprice = $lowerprice;
        return $this;
    }
    public function setSlug($slug) {
        $this->slug = $slug;
        return $this;
    }

    /**
     * Methods
     */

    public static function find(array $request, $objects = true) {

        $data = Db::dbFind(self::TABLE_NAME, $request);
        // TODO: array map

        if ($objects) {

            $objectsList = [];

            foreach ($data as $d) {

                $object = new Jeu(
                            $d[self::PREFIX.'nom'],
                            $d[self::PREFIX.'id'],
                            $d[self::PREFIX.'sortie_eu'],
                            $d[self::PREFIX.'lowerprice'],
                            $d[self::PREFIX.'slug']);

            }

            return;
        }

        return $data;

    }

    public static function findBySlug($slug, $object = true) {

        $request = [
            [self::PREFIX.'slug', 'like', $slug]
        ];

        $element = Db::dbFind(self::TABLE_NAME, $request);

        if (count($element) > 0) $element = $element[0];
        else return;

        if ($object) {
            $jeu = new Jeu(
                            $element[self::PREFIX.'nom'],
                            $element[self::PREFIX.'id'],
                            $element[self::PREFIX.'sortie_eu'],
                            $element[self::PREFIX.'lowerprice'],
                            $element[self::PREFIX.'slug']);

            return $jeu;
        }

        return $element;

    }

    public static function findAll($objects = true) {

        $data = Db::dbFind(self::TABLE_NAME);
        
        if ($objects) {
            $objectsList = [];

            foreach ($data as $d) {

                $objectsList[] = new Jeu(
                            $d[self::PREFIX.'nom'],
                            $d[self::PREFIX.'id'],
                            $d[self::PREFIX.'sortie_eu'],
                            $d[self::PREFIX.'lowerprice'],
                            $d[self::PREFIX.'slug']);
            }

            return $objectsList;
        }

        return $data;

    }

    public static function findByCategory($search) {
        
        

        $req = "
        SELECT *
        FROM jeux
        INNER JOIN categories on categories.id = jeux.category_id
        WHERE categories.name = " . $search;
        
        $res = $bdd->query($req);
        $results = $res->fetchAll();
    }
}