<?php

/**
 * Exemple de la classe Article avec des méthodes toutes rédigées en SQL
 * 
 * La classe est évidemment résumée !
 */
class Article extends Db {

    // [...]

     /**
     * CRUD Methods
     */
    public function save() {

        $bdd = Db::getDb();

        $req = "INSERT INTO Article(title, content, id_author)
                VALUES ($this->title(), $this->content(), $this->idAuthor()";

        $res = $bdd->execute($req);

        $this->setId($bdd->lastInsertId());

        return $this;
    }

    public function update() {

        if ($this->id > 0) {

            $bdd = Db::getDb();

            $req = "UPDATE Article
                    SET (   title = $this->title(),
                            content = $this->content(),
                            id_author = $this->idAuthor(),
                            updated_at = CURRENT_TIMESTAMP
                        )";

            $res = $bdd->execute($req);

            return $this;
        }

        return;
    }

    public function delete() {
        $bdd = Db::getDb();

        $req = "DELETE FROM Article
                WHERE id = " . $this->id();

        $res = $bdd->execute($req);
        return;
    }

    public static function findAll($objects = true) {

        $bdd = Db::getDb();

        $req = "SELECT * FROM Articles";

        $res = $bdd->query($req);

        $data = $res->fetchAll(PDO::FETCH_ASSOC);
        
        if ($objects) {
            $objectsList = [];

            foreach ($data as $d) {

                $objectsList[] = new Article($d['title'], $d['content'], intval($d['id']), $d['short_content'], $d['content'], intval($d['id_author']), $d['created_at'], $d['updated_at']);
            }

            return $objectsList;
        }

        return $data;
    }

    public static function find(array $request, $objects = true) {
        // Impossible à faire, find recherche par champs/valeur, ce que Db nous permettait de faire.
        // Si on veut faire une requête sur un champ particulier, il faudra procéder comme findOne ou findByAuthor ci-dessous.
    }

    public static function findOne(int $id, $object = true) {

        $bdd = Db::getDb();
        $req = "SELECT * FROM Articles WHERE id = " . $id;
        $res = $bdd->query($req);
        $data = $res->fetch(PDO::FETCH_ASSOC);

        if (count($data) > 0) $data = $data[0];
        else return;

        if ($object) {
            $article = new Article($data['title'], $data['content'], $data['id']);
            return $article;
        }

        return $data;
        
    }

    public static function findByAuthor($id_author) {

        $bdd = Db::getDb();
        $req = "SELECT * FROM Articles WHERE id_author = " . $id_author;
        $res = $bdd->query($req);
        $data = $res->fetchAll(PDO::FETCH_ASSOC);
        
        if ($objects) {
            $objectsList = [];

            foreach ($data as $d) {

                $objectsList[] = new Article($d['title'], $d['content'], intval($d['id']), $d['short_content'], $d['content'], intval($d['id_author']), $d['created_at'], $d['updated_at']);
            }

            return $objectsList;
        }

        return $data;
    }

    public function categories() {

        return ArticleCategory::findByCategory($this->id());
    }

    public function addCategory(Category $category) {

        $ac = new ArticleCategory($this->id(), $category->id());
        $ac->save();

        return;
    }

} // Dernière accolade correspondant à la première ligne "class Article { ..."