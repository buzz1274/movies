<?php

    class MoviesController extends AppController {

        public $paginate = false;

        public $limit = 20;

        public $page = 1;

        /**
         * returns all movies that match the supplied search critera
         * @
         */
        public function index() {

            $this->paginate = array(
                        'limit' => $this->limit,
                        'paramType' => 'querystring',
                        'page' => $this->_page(),
                        'order' => array('title' => 'asc'));

            $movies = $this->paginate('Movie');

            return new CakeResponse(array('body' => json_encode($movies)));

        }

        /**
         * provides summary details for the current resultset
         * @author David <david@sulaco.co.uk>
         */
        public function summary() {

            $this->_page();
            $totalMovies = $this->Movie->find('count');
            $startOffset = (($this->page - 1) * $this->limit) + 1;
            $totalPages = ceil($totalMovies / $this->limit);
            $endOffset = ($startOffset - 1) + $this->limit;

            if($endOffset > $totalMovies) {

                $endOffset = $totalMovies;

            }

            return new CakeResponse(array('body' =>
                            json_encode(array('totalMovies' =>
                                                $this->Movie->find('count'),
                                              'totalPages' =>
                                                $totalPages,
                                              'limit' =>
                                                $this->limit,
                                              'page' =>
                                                $this->page,
                                              'startOffset' =>
                                                $startOffset,
                                              'endffset' =>
                                                $endOffset))));

        }
        //end summary

        /**
         * determines the current page of the results
         * @author David <david@sulaco.co.uk>
         */
        private function _page() {

            if(isset($_GET['page']) && (int)$_GET['page'] > 0) {

                $this->page = $_GET['page'];

            }

            return $this->page;

        }
        //end _page

    }

?>
