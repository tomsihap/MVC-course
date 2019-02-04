<?php


$router = new Router;

$router->get('hello', function() {
    echo "hello world";
});

$router->get('/articles', 'ArticlesController@index');
