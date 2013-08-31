<?php

    class UserController extends AppController {

        public $uses = array('UserMovieFavourite');

        public function index() {

            if(($User = $this->Auth->user())) {
                $body = array('name' => $User['name'],
                              'admin' => $User['admin'],
                              'authenticated' => true);
            } else {
                $body = array('name' => false,
                              'admin' => false,
                              'authenticated' => false);
            }

            return new CakeResponse(array('body' => json_encode($body)));
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
               $User && isset($User['User']) && $this->Auth->login($User['User'])) {
                $status = 200;
                $body = array('name' => $User['User']['name'],
                              'admin' => $User['User']['admin'],
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
         * un-authenticates user
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
        //end logout

        /**
         * add/removes a movie from users favourite list
         * @author David
         * @return mixed
         */
        public function favourite() {

            $status = 400;
            $Movie = $this->request->input('json_decode');

            if(isset($Movie->Movie->movie_id) &&
                (int)$Movie->Movie->movie_id &&
                isset($Movie->Movie->favourite)) {

                $data = array('movie_id' => $Movie->Movie->movie_id,
                              'user_id' => 1);

                if($Movie->Movie->favourite &&
                   $this->UserMovieFavourite->save($data)) {
                    $status = 200;

                } elseif(!$Movie->Movie->favourite &&
                          $this->UserMovieFavourite->deleteAll($data)) {
                    $status = 200;
                }

            }

            return new CakeResponse(array('status' => 200,
                                          'body' => json_encode(array('ok'))));

        }
        //end favourite

        /**
         * add/removes a movie from users favourite list
         * @author David
         * @return mixed
         */
        public function watched() {

        }
        //end watched

    }
