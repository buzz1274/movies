<?php

    class MoviesController extends AppController {

        public $paginate = false;

        public $limit = 10;

        public $page = 1;

        public $sort = 'true';

        public $sortDirection = 'asc';

        public $search = false;

        public $genreID = false;

        public $personID = false;

        /**
         * returns all movies that match the supplied search critera
         * @author David <david@sulaco.co.uk>
         */
        public function movies() {

            //error_log(json_encode($_GET));

            $this->_parseURLVars();
            $this->paginate = array(
                        'limit' => $this->limit,
                        'callbacks' => true,
                        'conditions' => array(),
                        'joins' => array(),
                        'page' => $this->page,
                        'recursive' => 0,
                        'order' => array($this->sort => $this->sortDirection));

            if($this->search) {

                $this->paginate['conditions'][] =
                    array('Movie.title ILIKE' => '%'.$this->search.'%');

            }

            if($this->genreID) {

                $this->paginate['joins'][] =
                    $this->Movie->genreSearch($this->genreID);

            }

            if($this->personID) {

                $this->paginate['joins'][] =
                    $this->Movie->personSearch($this->personID);

            }

            $movies = $this->paginate('Movie');

            return new CakeResponse(array('body' => json_encode($movies)));

        }

        public function movie() {

            $movie = $this->Movie->find('first',
                          array('recursive' => 2,
                                'conditions' =>
                                    array('imdb_id' => $this->request->params['imdbID'])));

            //error_log(json_encode($movie));

            return new CakeResponse(array('body' => json_encode($movie)));

        }
        //end movie

        /**
         * provides summary details for the current resultset
         * @author David <david@sulaco.co.uk>
         */
        public function summary() {

            $searchCriteria = array('recursive' => -1);
            $this->_parseURLVars();

            if($this->search) {

                $searchCriteria['conditions'] =
                    array('Movie.title ILIKE' => '%'.$this->search.'%');

            }

            if($this->genreID) {

                $searchCriteria['joins'][] =
                    $this->Movie->genreSearch($this->genreID);

            }

            if($this->personID) {

                $searchCriteria['joins'][] =
                    $this->Movie->personSearch($this->personID);

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
                                              'limit' =>
                                                $this->limit,
                                              'page' =>
                                                $this->page,
                                              'startOffset' =>
                                                $startOffset,
                                              'endOffset' =>
                                                $endOffset))));

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
               ($_GET['asc'] == 'true' ||
                $_GET['asc'] == 'false')) {

                if($_GET['asc'] == 'true') {

                    $this->sortDirection = 'asc';

                } else {

                    $this->sortDirection = 'desc';

                }

            }

        }
        //end _parseURLVars

    }

?>
