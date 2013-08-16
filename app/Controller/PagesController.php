<?php

    class PagesController extends AppController {

        public $uses = array('Genre', 'Movie', 'Certificate');

        public function index() {

            $this->helpers[] = 'App';
            $this->set(
                array('summary' => $this->Movie->search('summary', false),
                      'genres' => $this->Genre->find('all'),
                      'certificates' => $this->Certificate->find('all',
                                                array('recursive' => false))));
        }
        //end index

    }
