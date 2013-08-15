<?php

    class UserController extends AppController {

        public function index() {
            return new CakeResponse(
                        array('body' => json_encode(array('name' => 'David',
                                                          'authenticated' => false))));
        }
        //end index

        /**
         * authenticates a user
         * @author David
         * @return mixed
         */
        public function login() {

            $User = $this->request->input('json_decode');

            if($User && isset($User->username) &&
               isset($User->password) &&
                ($User = $this->User->login($User->username,
                                            AuthComponent::password($User->password))) &&
               $User && $this->Auth->login($User)) {
                $status = 200;
                $body = array('name' => 'David',
                              'authenticated' => true);
            } else {
                $status = 403;
                $body = array('error_type' => 'invalid_credentials',
                              'error_message' => 'Invalid username/password');
            }

            return new CakeResponse(array('status' => $status,
                                          'body' => json_encode($body)));

        }
        //end login

        /**
         * unauthenticates user
         * @author David
         * @return mixed
         */
        public function logout() {

            if($this->Auth->user()) {
                $this->Auth->logout();
            }

            return new CakeResponse(array('status' => 200,
                                          'body' => json_encode(array('name' => null,
                                                                      'authenticated' => false))));

        }
        //end login

    }
