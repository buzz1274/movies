<?php

    class MoviesController extends AppController {

        /**
         * retrieves movie details for the movie matching imdb_id
         * @author David
         */
        public function movie() {

            $movie = $this->Movie->find('first',
                          array('recursive' => 2,
                                'conditions' =>
                                    array('movie_id' =>
                                            $this->request->params['movieID'])));

            return new CakeResponse(array('body' => json_encode($movie)));

        }
        //end movie

        /**
         * updates the watched status
         * @author David
         */
        public function watched() {

            $Movie = $this->request->input('json_decode');

            if(isset($Movie->Movie->movie_id) &&
               (int)$Movie->Movie->movie_id &&
               isset($Movie->Movie->watched) &&
               $this->Movie->save(
                        array('movie_id' => $Movie->Movie->movie_id,
                              'watched' => (boolean)$Movie->Movie->watched))) {

                $response = 'success';
                $statusCode = '200';

            } else {

                $statusCode = '400';
                $response = 'failure';

            }

            return new cakeresponse(array('statusCode' => $statusCode,
                                          'body' => json_encode(array('name' => $response))));

        }
        //end watched

        /**
         * returns the movie file
         * @author David
         */
        public function file() {

            $movie = $this->Movie->find('first',
                         array('recursive' => -1,
                               'conditions' => array('movie_id' =>
                                                        $this->request->params['movieID'])));

            if(!$movie) {
                header('HTTP/1.1 404 Not Found', true, 401);
                header('Location: /#file-error');
                die();
            } else {
                $filename = preg_replace('/.*\//is', '', $movie['Movie']['path']);
                $this->viewClass = 'Media';
                $params = array('id'        => $filename,
                                'name'      => preg_replace('/].*/', ']', $filename),
                                'download'  => true,
                                'extension' => preg_replace('/.*]\./', '', $filename),
                                'path'      => MEDIA_SERVER_PATH.'/');
                $this->set($params);
            }

        }
        //end file

        /**
         * returns a csv formatted list of movies
         * @author David
         */
        public function csv() {

            $data = $this->Movie->search('search',
                                         array_merge(array('limit' => false,
                                                           'userID' => $this->Auth->user('user_id')),
                                                     $this->request->query));

            if($data) {
                header('HTTP/1.1 404 Not Found', true, 401);
                header('Location: /#file-error');
                die();
            } else {
                header("Content-type:application/vnd.ms-excel");
                header("Content-disposition:attachment;filename=movies.csv");
                $this->set(compact('data'));
            }

        }
        //end csv

        /**
         * returns all movies that match the supplied search critera
         * @author David
         */
        public function movies() {

            $data = $this->Movie->search('search',
                                         array_merge(array('userID' => $this->Auth->user('user_id')),
                                                     $this->request->query));

            return new CakeResponse(array('body' => json_encode($data)));

        }
        //end movies

        /**
         * provides summary details for the current resultset
         * @author David
         */
        public function summary() {

            $data = $this->Movie->search('summary',
                array_merge(array('userID' => $this->Auth->user('user_id')),
                                  $this->request->query));

            return new CakeResponse(array('body' => json_encode($data)));

        }
        //end summary
    }
