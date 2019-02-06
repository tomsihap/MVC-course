<?php

class Category extends Db {

    private $id;
    private $title;
    private $description;

    const TABLE_NAME = 'category';

    public function __construct($title, $description, $id = null) {

        $this->setTitle($title);
        $this->setDescription($description);
        $this->setId($id);
    }

    public function id() {
        return $this->id;
    }
    public function title() {
        return $this->title;
    }
    public function description() {
        return $this->description;
    }

    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }
    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }
    public function setId($id) {
        $this->id = $id;
        return $this;
    }


    public function save() {
        $data = [
            "id"            => $this->id(),
            "title"         => $this->title(),
            "description"   => $this->description(),
        ];

        $nouvelId = Db::dbCreate(self::TABLE_NAME, $data);

        $this->id = $nouvelId;

        return $this;
    }

    public function addArticle(Article $article) {

        $ac = new ArticleCategory($article->id(), $this->id());
        $ac->save();
    }

}