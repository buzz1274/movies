<?php

    class MoviesController extends AppController {

        public $uses = array('Movie', 'UserMovieDownloaded');

        /**
         * retrieves movie details for the movie matching movieID
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

                $this->UserMovieDownloaded->save(
                        array('movie_id' => $this->request->params['movieID'],
                              'user_id' => $this->Auth->user('user_id'),
                              'filesize' => $movie['Movie']['filesize'],
                              'date_downloaded' => date('Y-m-d H:i:s', strtotime('now'))));

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

            set_time_limit(0);
            $data = $this->Movie->search('search',
                                         array_merge(array('limit' => false,
                                                           'userID' => $this->Auth->user('user_id')),
                                                     $this->request->query));

            if(!$data) {
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
         * returns json string with movies, actors and cast matching
         * the supplied search string
         * @author David
         */
        public function autocomplete() {

            return new CakeResponse(array('body' =>
                        json_encode($this->Movie->autocomplete($this->request->query['search']))));

        }
        //end autocomplete

        /**
         * returns a random movieID that matches the supplied search
         * criteria
         * @author David
         */
        public function lucky() {
            $data = $this->Movie->search('search',
                         array_merge($this->request->query,
                                     array('limit' => 1, 'lucky' => true,
                                           'userID' => $this->Auth->user('user_id'))));

            if($data) {
                $data = json_encode(array('movieID' => $data[0]['Movie']['movie_id']));
            }

            return new CakeResponse(array('body' => $data));

        }
        //end lucky

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
