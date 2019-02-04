<?php


$router = new Router;

$router->get('hello', function() {
    echo "hello world";
});

$router->get('/articles', 'ArticlesController@index');

$router->get('/jeux', 'JeuxController@index');
$router->get('/jeux/([^/]+)', 'JeuxController@read');

$router->get('/api/jeux', 'JeuxController@indexApi');

$router->get('/api/jeux/search/(\w+)', 'JeuxController@searchApi');

$router->get('/api/jeux/searchByCategory/(\w+)', 'JeuxController@searchByCategoryApi');


$router->run();