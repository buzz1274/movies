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
    Router::connect('/user/favourite/:movieID',
                    array('controller' => 'user',
                          'action' => 'favourite',
                          'movieID', '[0-9]{1,}'));
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
    Router::connect('/movies/get/:movieID/',
                    array('controller' => 'movies',
                          'action' => 'file',
                          'movieID', '[0-9]{1,}'));
    Router::connect('/movies/:movieID/',
                    array('controller' => 'movies',
                          'action' => 'movie',
                          'movieID', '[0-9]{1,}'));
    Router::connect('/movies/',
                    array('controller' => 'movies',
                          'action' => 'movies'));

?>
