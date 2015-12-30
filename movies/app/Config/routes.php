<?php

    CakePlugin::routes();

    Router::connect('/',
                    array('controller' => 'pages',
                          'action' => 'index'));
    Router::connect('/user/',
                    array('controller' => 'user',
                          'action' => 'index'));
    Router::connect('/user/login/',
                    array('controller' => 'user',
                          'action' => 'login'));
    Router::connect('/user/logout/',
                    array('controller' => 'user',
                          'action' => 'logout'));
    Router::connect('/user/favourite/',
                    array('controller' => 'user',
                          'action' => 'favourite'));
    Router::connect('/user/watched/',
                    array('controller' => 'user',
                          'action' => 'watched'));
    Router::connect('/movies/summary',
                    array('controller' => 'movies',
                          'action' => 'summary'));
    Router::connect('/movies/csv',
                    array('controller' => 'movies',
                          'action' => 'csv'));
    Router::connect('/movies/autocomplete/',
                    array('controller' => 'movies',
                          'action' => 'autocomplete'));
    Router::connect('/movies/lucky/',
                    array('controller' => 'movies',
                          'action' => 'lucky'));
    Router::connect('/movies/:movieID/',
                    array('controller' => 'movies',
                          'action' => 'movie',
                          'movieID', '[0-9]+'));
    Router::connect('/movie/delete/:movieID/',
                array('controller' => 'movies',
                      'action' => 'delete',
                      'movieID', '[0-9]+'));
    Router::connect('/movie/add/',
                array('controller' => 'movies',
                      'action' => 'add'));
    Router::connect('/movies',
                    array('controller' => 'movies',
                          'action' => 'movies'));
    Router::connect('/*',
                    array('controller' => 'pages',
                          'action' => 'index'));


