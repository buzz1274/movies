<?php

    App::uses('Controller', 'Controller');
    App::uses('Sanitize', 'Utility');

    class AppController extends Controller {

        public $components = array(
            'Session',
            'Auth',
        );

        public function beforeFilter() {
            $this->Auth->allow('index', 'movies', 'summary', 'login', 'logout', 'movie',
                               'autocomplete');
        }

    }
