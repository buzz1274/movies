<?php

    class UserController extends AppController {

        public $uses = array('Genre', 'Movie', 'Certificate');

        public function index() {
            return new CakeResponse(
                        array('body' => json_encode(array('name' => 'David',
                                                          'authenticated' => false))));
        }
        //end index

        public function login() {
            return new CakeResponse(
                        array('body' => json_encode(array('name' => 'David',
                                                          'authenticated' => true))));
        }
        //end login

        public function logout() {
            return new CakeResponse(
                        array('body' => json_encode(array('name' => false,
                                                          'authenticated' => false))));
        }
        //end login



    }
