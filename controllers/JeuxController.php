<?php

class JeuxController {

    public function index() {

        $list = Jeu::findAll();
        var_dump($list);

    }

    public function read($slug) {

        $jeu = Jeu::findBySlug($slug);
        var_dump($jeu);
    }

    public function indexApi() {

        $list = Jeu::findAll(false);

        $json = json_encode($list);

        header('Content-Type: application/json');
        echo $json;

    }

    public function searchApi($req) {

        $list = Jeu::find([
            [Jeu::PREFIX.'nom', 'like', '%'.$req.'%']
        ], false);

        $json = json_encode($list);

        header('Content-Type: application/json');
        echo $json;

    }

    public function searchByCategoryApi($req) {

        Jeu::findByCategory($req);

        $json = json_encode($list);

        header('Content-Type: application/json');
        echo $json;
    }

}