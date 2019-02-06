<?php

class ArticleCategory extends Db {

    protected $id_article;
    protected $id_category;

    const TABLE_NAME = 'article_category';

    public function __construct($id_article, $id_category) {

        $this->id_article = $id_article;
        $this->id_category = $id_category;
    }

    public function save() {
        $data = [
            "id_category"   => $this->id_category,
            "id_article"    => $this->id_article
        ];

        $nouvelId = Db::dbCreate(self::TABLE_NAME, $data);

        return $this;
    }

    public static function articlesCountByCategory() {

        $bdd = Db::getDb();

        $req = "SELECT category.title, count(*)
                FROM `article_category`
                INNER JOIN category ON category.id = article_category.id_category
                GROUP BY id_category";

        $res = $bdd->query($req);

        $data = $res->fetchAll(PDO::FETCH_ASSOC);

        var_dump($data);

    }


}