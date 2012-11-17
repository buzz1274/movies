<?php

    class MoviesController extends AppController {

        public $paginate = false;

        public $limit = 20;

        public $page = 1;

        public $sort = 'true';

        public $sortDirection = 'asc';

        public $search = false;

        public $genreID = false;

        public $personID = false;

        public $keywordID = false;

        /**
         * retrieves movie details for the movie matching imdb_id
         * @author David
         */
        public function movie() {

            $movie = $this->Movie->find('first',
                          array('recursive' => 2,
                                'conditions' =>
                                    array('imdb_id' =>
                                            $this->request->params['imdbID'])));

            return new CakeResponse(array('body' => json_encode($movie)));

        }
        //end movie

        /**
         * returns all movies that match the supplied search critera
         * @author David
         */
        public function movies() {

            //error_log(json_encode($_GET));
            $this->_parseURLVars();
            $this->paginate = array(
                        'fields' => array('DISTINCT Movie.movie_id', '*',
                                          'Certificate.*'),
                        'limit' => $this->limit,
                        'callbacks' => true,
                        'joins' => array(),
                        'conditions' => array(),
                        'page' => $this->page,
                        'recursive' => 0,
                        'order' => array($this->sort => $this->sortDirection));

            if($this->search || $this->personID) {
                $this->paginate['joins'][] =
                    $this->Movie->personSearch($this->personID);
            }
            if($this->search || $this->keywordID) {
                $this->paginate['joins'][] =
                    $this->Movie->keywordSearch($this->keywordID);
            }
            if($this->genreID) {
                $this->paginate['joins'][] =
                    $this->Movie->genreSearch($this->genreID);
            }
            if($this->search) {
                $this->paginate['conditions'][] =
                    array('OR' => array(array('Movie.title ILIKE' =>
                                                 '%'.$this->search.'%'),
                                        array('person.person_name ILIKE' =>
                                                 '%'.$this->search.'%'),
                                        array('keyword.keyword ILIKE' =>
                                                 '%'.$this->search.'%')));
            }

            $movies = $this->paginate('Movie');

            return new CakeResponse(array('body' => json_encode($movies)));

        }
        //end movies

        /**
         * provides summary details for the current resultset
         * @author David <david@sulaco.co.uk>
         */
        public function summary() {

            $this->_parseURLVars();

            $searchCriteria =
                array('fields' => "COUNT(DISTINCT Movie.movie_id) ",
                      'recursive' => -1);
            if($this->search) {
                $searchCriteria['joins'] =
                    array($this->Movie->personSearch($this->personID),
                          $this->Movie->keywordSearch());
                $searchCriteria['conditions'] =
                    array('OR' => array(array('Movie.title ILIKE' =>
                                                 '%'.$this->search.'%'),
                                        array('person.person_name ILIKE' =>
                                                 '%'.$this->search.'%'),
                                        array('keyword.keyword ILIKE' =>
                                                 '%'.$this->search.'%')));
            } elseif($this->personID) {
                $searchCriteria['joins'][] =
                    $this->Movie->personSearch($this->personID);
            } elseif($this->keywordID) {
                $searchCriteria['joins'][] =
                    $this->Movie->keywordSearch($this->keywordID);
            }

            if($this->genreID) {
                $searchCriteria['joins'][] =
                    $this->Movie->genreSearch($this->genreID);
            }

            $totalMovies = $this->Movie->find('count', $searchCriteria);
            $startOffset = (($this->page - 1) * $this->limit) + 1;
            $totalPages = ceil($totalMovies / $this->limit);
            $endOffset = ($startOffset - 1) + $this->limit;

            if($totalMovies == 0) {
                $startOffset = 0;
            }

            if($endOffset > $totalMovies) {
                $endOffset = $totalMovies;
            }

            return new CakeResponse(array('body' =>
                            json_encode(array('totalMovies' =>
                                                 $totalMovies,
                                              'totalPages' =>
                                                $totalPages,
                                              'page' =>
                                                $this->page))));

        }
        //end summary

        /**
         * determines the current page of the results
         * @author David <david@sulaco.co.uk>
         */
        private function _parseURLVars() {

            if(isset($_GET['p']) && (int)$_GET['p'] > 0) {

                $this->page = $_GET['p'];

            }

            if(isset($_GET['search']) && !empty($_GET['search'])) {

                $this->search = $_GET['search'];

            }

            if(isset($_GET['gid']) && (int)$_GET['gid'] > 0) {

                $this->genreID = $_GET['gid'];

            }

            if(isset($_GET['pid']) && (int)$_GET['pid'] > 0) {

                $this->personID = $_GET['pid'];

            }

            if(isset($_GET['kid']) && (int)$_GET['kid'] > 0) {

                $this->keywordID = $_GET['kid'];

            }

            if(isset($_GET['s']) &&
               in_array($_GET['s'], array('title', 'release_year',
                                          'imdb_rating', 'hd',
                                          'runtime', 'filesize',
                                          'date_added', 'cert'))) {

                $this->sort = $_GET['s'];

                if($this->sort == 'cert') {

                    $this->sort = 'Certificate.order';

                }

            }

            if(isset($_GET['asc']) &&
               ($_GET['asc'] == 1 || $_GET['asc'] == 0)) {

                if($_GET['asc'] == 1) {

                    $this->sortDirection = 'asc';

                } else {

                    $this->sortDirection = 'desc';

                }

            }

        }
        //end _parseURLVars

    }

?>
