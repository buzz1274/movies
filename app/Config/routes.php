<?php

    CakePlugin::routes();

    Router::connect('/',
                    array('controller' => 'pages',
                          'action' => 'index'));
    Router::connect('/movies/summary',
                    array('controller' => 'movies',
                          'action' => 'summary'));
    Router::connect('/movies/:imdbID/',
                    array('controller' => 'movies',
                          'action' => 'movie'),
                    array('imdbID', 'tt[0-9]{7}'));
    Router::connect('/movies/',
                    array('controller' => 'movies',
                          'action' => 'movies'));

?>
