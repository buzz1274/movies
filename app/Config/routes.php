<?php

    CakePlugin::routes();
    require CAKE . 'Config' . DS . 'routes.php';

    Router::connect('/', array('controller' => 'pages',
                               'action' => 'index'));
    Router::connect('/movies/*', array('controller' => 'movies',
                                       'action' => 'movies'));

?>
