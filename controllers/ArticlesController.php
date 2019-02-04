<?php

class ArticlesController {


    public function index() {

        $articles = Article::findAll();

        view('articles.index', compact('articles'));
    }
}