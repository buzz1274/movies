<?php

    CakePlugin::routes();

    Router::connect('/',
                    array('controller' => 'pages',
                          'action' => 'index'));
    Router::connect('/login',
                    array('controller' => 'pages',
                          'action' => 'login'));
    Router::connect('/movies/summary',
                    array('controller' => 'movies',
                          'action' => 'summary'));
    Router::connect('/movies/csv',
                    array('controller' => 'movies',
                          'action' => 'csv'));
    Router::connect('/movies/watched/:id/',
                    array('controller' => 'movies',
                          'action' => 'watched',
                          'movieID' => '[0-9]{1,}'));
    Router::connect('/movies/:movieID/',
                    array('controller' => 'movies',
                          'action' => 'movie',
                          'movieID', '[0-9]{1,}'));
    Router::connect('/movies/',
                    array('controller' => 'movies',
                          'action' => 'movies'));

?>
