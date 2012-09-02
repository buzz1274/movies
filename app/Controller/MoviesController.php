<?php

    class MoviesController extends AppController {

        public $paginate = false;

        public $limit = 10;

        public $page = 1;

        public $sort = 'title';

        public $sortDirection = 'asc';

        public $search = false;

        /**
         * returns all movies that match the supplied search critera
         * @
         */
        public function index() {

            $this->_parseURLVars();
            $this->paginate = array(
                        'limit' => $this->limit,
                        'callbacks' => true,
                        'page' => $this->page,
                        'order' => array($this->sort => $this->sortDirection));

            if($this->search) {

                $this->paginate['conditions'] =
                    array('Movie.title ILIKE' => '%'.$this->search.'%');

            }

            $movies = $this->paginate('Movie');

            return new CakeResponse(array('body' => json_encode($movies)));

        }

        /**
         * provides summary details for the current resultset
         * @author David <david@sulaco.co.uk>
         */
        public function summary() {

            $searchCriteria = array();
            $this->_parseURLVars();

            if($this->search) {

                $searchCriteria['conditions'] =
                    array('Movie.title ILIKE' => '%'.$this->search.'%');

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

            if(isset($_GET['page']) && (int)$_GET['page'] > 0) {

                $this->page = $_GET['page'];

            }

            if(isset($_GET['search']) && !empty($_GET['search'])) {

                $this->search = $_GET['search'];

            }

            if(isset($_GET['sort']) &&
               in_array($_GET['sort'], array('title', 'release_year',
                                             'imdb_rating', 'hd',
                                             'runtime', 'filesize',
                                             'date_added'))) {

                $this->sort = $_GET['sort'];

            }

            if(isset($_GET['sort_ascending']) &&
               ($_GET['sort_ascending'] == 'true' ||
                $_GET['sort_ascending'] == 'false')) {

                if($_GET['sort_ascending'] == 'true') {

                    $this->sortDirection = 'asc';

                } else {

                    $this->sortDirection = 'desc';

                }

            }

        }
        //end _parseURLVars

    }

?>
