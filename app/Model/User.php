<?php

    class User extends AppModel {

        public $name = 'user';

        public $useTable = 'user';

        public function login($username, $password) {

            $user = $this->find('first', array(
                        'conditions' => array('username' => $username,
                                              'password' => $password),
                        'recursive' => -1));

            return $user;

        }
        //end login


    }