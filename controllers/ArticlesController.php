<?php

class ArticlesController {


    public function index() {

        $articles = Article::findAll();

        var_dump($articles);
    }
}