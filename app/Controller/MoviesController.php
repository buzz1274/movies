<?php

    class MoviesController extends AppController {

        public $paginate = false;

        public $search = array('page' => 1,
                               'limit' => 20,
                               'genreID' => false,
                               'personID' => false,
                               'keywordID' => false,
                               'search' => '',
                               'sort' => 'title',
                               'sortDirection' => 'asc',
                               'genreID' => false);

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

            } else {

                $response = 'failure';
                $this->header('HTTP/1.1 400 Bad Request');

            }

            return new cakeresponse(array('body' =>
                            json_encode(array('name' => $response))));

        }
        //end watched

        /**
         * returns all movies that match the supplied search critera
         * @author David
         */
        public function movies() {

            $this->_parseQuery();
            $this->paginate = array(
                        'fields' => array('DISTINCT Movie.movie_id', '*',
                                          'Certificate.*'),
                        'limit' => $this->search['limit'],
                        'callbacks' => true,
                        'joins' => array(),
                        'conditions' => array(),
                        'page' => $this->search['page'],
                        'recursive' => 0,
                        'order' => array($this->search['sort'] => $this->search['sortDirection']));

            /*
            if($this->watched !== null) {
                $this->paginate['conditions'][] = array('Movie.watched' =>
                                                                $this->watched);
            }
            */

            if($this->search['personID'] || $this->search['search']) {
                $this->paginate['joins'][] =
                $this->Movie->personSearch($this->search['personID']);
            }

            if($this->search['keywordID'] || $this->search['search']) {
                $this->paginate['joins'][] =
                $this->Movie->keywordSearch($this->search['keywordID']);
            }

            if($this->search['genreID'] || $this->search['search']) {
                $this->paginate['joins'][] =
                $this->Movie->genreSearch($this->search['genreID']);
            }

            /*
            if($this->search['search']) {
                $this->paginate['conditions'][] =
                array('OR' => array(array('Movie.title ILIKE' =>
                                          '%'.$this->search['search'].'%'),
                                    array('person.person_name ILIKE' =>
                                          '%'.$this->search['search'].'%'),
                                    array('keyword.keyword ILIKE' =>
                                          '%'.$this->search['search'].'%')));
            }
            */

            $movies = $this->paginate('Movie');

            return new CakeResponse(array('body' => json_encode($movies)));

        }
        //end movies

        /**
         * provides summary details for the current resultset
         * @author David <david@sulaco.co.uk>
         */
        public function summary() {

            $this->_parseQuery();

            return new CakeResponse(
                        array('body' => json_encode($this->Movie->summary($this->search))));

        }
        //end summary

        /**
         * ensures request vars contain valid data
         * @author David <david@sulaco.co.uk>
         */
        private function _parseQuery() {

            if(isset($this->request->query['p']) &&
               (int)$this->request->query['p'] > 0) {
                $this->search['page'] = $this->request->query['p'];
            }

            if(isset($this->request->query['search']) &&
               !empty($this->request->query['search'])) {
                $this->search['search'] = $this->request->query['search'];
            }

            if(isset($this->request->query['gid']) &&
               (int)$this->request->query['gid'] > 0) {
                $this->search['genreID'] = $this->request->query['gid'];
            }

            if(isset($this->request->query['pid']) &&
               (int)$this->request->query['pid'] > 0) {
                $this->search['personID'] = $this->request->query['pid'];
            }

            if(isset($this->request->query['kid']) &&
               (int)$this->request->query['kid'] > 0) {
                $this->search['keywordID'] = $this->request->query['kid'];
            }

            if(isset($this->request->query['s']) &&
               in_array($this->request->query['s'],
                        array('title', 'release_year',
                              'imdb_rating', 'hd',
                              'runtime', 'filesize',
                              'date_added', 'cert'))) {

                $this->search['sort'] = $this->request->query['s'];

                if($this->search['sort'] == 'cert') {
                    $this->search['sort'] = 'Certificate.order';
                }

                if(isset($this->request->query['asc']) &&
                   ($this->request->query['asc'] == 1 ||
                    $this->request->query['asc'] == 0)) {

                    if($this->request->query['asc'] == 1) {

                        $this->search['sortDirection'] = 'asc';

                    } else {

                        $this->search['sortDirection'] = 'desc';

                    }

                }

            }



            if(isset($_GET['watched']) && $_GET['watched'] != 'all') {

                $this->watched = (boolean)$_GET['watched'];

            }

        }
        //end _parseQuery

    }

?>
