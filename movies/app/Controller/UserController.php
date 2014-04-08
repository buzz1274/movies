<?php

    class UserController extends AppController {

        public $uses = array('User', 'UserMovieFavourite', 'UserMovieWatched',
                             'UserMovieDownloaded', 'Movie');

        /**
         * returns authentication details if a user is
         * currently authenticated
         * @return CakeResponse
         */
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

            if(!$User || !isset($User->username) || !isset($User->password)) {
                $status = 400;
                $body = array();
            } else {
                if(($User = $this->User->login($User->username,
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

            return new CakeResponse(
                array('status' => 200,
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

            if(isset($Movie->movie_id) &&
                (int)$Movie->movie_id &&
                isset($Movie->favourite)) {

                $data = array('movie_id' => $Movie->movie_id,
                              'user_id' => $this->Auth->user('user_id'));

                if($Movie->favourite &&
                   $this->UserMovieFavourite->save($data)) {
                    $status = 200;

                } elseif(!$Movie->favourite &&
                          $this->UserMovieFavourite->deleteAll($data)) {
                    $status = 200;
                }

            }

            return new CakeResponse(array('status' => $status,
                                          'body' => json_encode(array('success'))));

        }
        //end favourite

        /**
         * add/removes a movie from the currently logged in users
         * watched list
         * @author David
         * @return mixed
         */
        public function watched() {

            $status = 400;
            $body = array();
            $Movie = $this->request->input('json_decode');


            if(isset($Movie->movie_id) && (int)$Movie->movie_id > 1) {

                if(isset($Movie->watched_id) &&
                   $this->UserMovieWatched->deleteAll(array('id' => $Movie->watched_id,
                                                            'user_id' => $this->Auth->user('user_id')))) {
                    $status = 200;
                    $body = array('response' => 'success');
                } else {
                    $date = date('Y-m-d H:i:s', strtotime('now'));
                    $data = array('movie_id' => $Movie->movie_id,
                                  'user_id' => $this->Auth->user('user_id'),
                                  'date_watched' => $date);

                    if(($watched = $this->UserMovieWatched->save($data))) {
                        $status = 200;
                        $body = array('id' => $watched['UserMovieWatched']['id'],
                                      'date_watched' => date('jS F Y H:i:s', strtotime($date)));
                    }
                }
            }

            return new CakeResponse(array('status' => $status,
                                          'body' => json_encode($body)));

        }
        //end watched

        /**
         * returns a list of movies the user has flagged for
         * download/auto downloaded
         * @author David
         * @return mixed
         */
        public function downloaded() {

            $status = 400;
            $Params = $this->request->input('json_decode');

            if(!$Params || (is_array($Params) && !isset($Params->movie_id))) {
                $conditions = array('conditions' => array('user_id' => $this->Auth->user('user_id')),
                                    'recursive' => 1,
                                    'order' => 'date_downloaded DESC',
                                    'limit' => '20',
                                    'page' => $this->request->query('p'));

                $data = $this->UserMovieDownloaded->find('all', $conditions);

                if(!$data) {
                    $status = 204;
                } else {
                    $status = 200;
                }

            } elseif(isset($Params->movie_id) &&
                     ($this->request->is('post') || $this->request->is('put'))) {

                $movieDetails = $this->Movie->find('all',
                                  array('conditions' => array('movie_id' => $Params->movie_id),
                                        'recursive' => -1));

                if(is_array($movieDetails)) {
                    $movieDetails = array_pop($movieDetails);

                    if(isset($Params->movie_id) && isset($Params->download_id)) {

                        $downloadDetails =
                            $this->UserMovieDownloaded->find('all',
                                array('conditions' => array('id' => $Params->download_id,
                                                            'user_id' => $this->Auth->user('user_id')),
                                      'recursive' => 1));

                        if(!is_array($downloadDetails)) {
                            $status = 400;
                        } elseif(($data = $this->UserMovieDownloaded->save(array('id' => $Params->download_id,
                                                                                 'status' => 'cancelled')))) {
                            $status = 200;
                        } else {
                            $status = 500;
                        }
                    } else {
                        $data = array('user_id' => $this->Auth->user('user_id'),
                                      'movie_id' => $movieDetails['Movie']['movie_id'],
                                      'date_downloaded' => date('Y-m-d H:i:s', strtotime('now')),
                                      'status' => 'queued',
                                      'filesize' => $movieDetails['Movie']['filesize']);

                        if(($data = $this->UserMovieDownloaded->save($data))) {
                            $status = 201;
                        } else {
                            $status = 500;
                        }
                    }
                }
            }

            return new CakeResponse(array('status' => $status,
                                          'body' => json_encode($data)));

        }
        //end downloaded

    }
