<?php

    ##gangster boxset asin - B00007LZ6M

    class Movie extends AppModel {

        public $name = 'movie';

        public $useTable = 'movie';

        public $primaryKey = 'movie_id';

        public $belongsTo = 'Certificate';

        public $hasAndBelongsToMany = array(
            'Genre' =>
                array('className'             => 'Genre',
                      'joinTable'             => 'movie_genre',
                      'foreignKey'            => 'movie_id',
                      'associationForeignKey' => 'genre_id',
                      'unique'                => true,
                      'conditions'            => '',
                      'fields'                => '',
                      'order'                 => '',
                      'limit'                 => '',
                      'offset'                => '',
                      'finderQuery'           => '',
                      'deleteQuery'           => '',
                      'insertQuery'           => ''),
            'Keyword' =>
                array('className'             => 'Keyword',
                      'joinTable'             => 'movie_keyword',
                      'foreignKey'            => 'movie_id',
                      'associationForeignKey' => 'keyword_id',
                      'unique'                => true,
                      'conditions'            => '',
                      'fields'                => '',
                      'order'                 => '',
                      'limit'                 => '',
                      'offset'                => '',
                      'finderQuery'           => '',
                      'deleteQuery'           => '',
                      'insertQuery'           => ''),
            'Director' =>
                array('className'             => 'Person',
                      'joinTable'             => 'movie_role',
                      'foreignKey'            => 'movie_id',
                      'associationForeignKey' => 'person_id',
                      'unique'                => true,
                      'conditions'            => array('role_id=1'),
                      'fields'                => '',
                      'order'                 => '',
                      'limit'                 => '',
                      'offset'                => '',
                      'finderQuery'           => '',
                      'deleteQuery'           => '',
                      'insertQuery'           => ''),
            'Actor' =>
                array('className'             => 'Person',
                      'joinTable'             => 'movie_role',
                      'foreignKey'            => 'movie_id',
                      'associationForeignKey' => 'person_id',
                      'unique'                => true,
                      'conditions'            => array('role_id=2'),
                      'fields'                => '',
                      'order'                 => '',
                      'limit'                 => '',
                      'offset'                => '',
                      'finderQuery'           => '',
                      'deleteQuery'           => '',
                      'insertQuery'           => ''));

        /*@var array - default search parameters*/
        private $_search = array('page' => 1,
                                 'limit' => 20,
                                 'gID' => false,
                                 'personID' => false,
                                 'keywordID' => false,
                                 'search' => '',
                                 'sort' => 'title',
                                 'sortDirection' => 'asc',
                                 'hd' => false,
                                 'cid' => false);

        /**
         * @author David <david@sulaco.co.uk>
         * @param array
         * @param array $searchParams
         * @return mixed
         */
        public function search($searchType, $searchParams) {

            $this->_parseSearchParameters($searchParams);
            $results = array();

            if($searchType == 'search' &&
               is_array($results = $this->_search($searchType))) {

                $results = $this->_afterFind($results);

            } elseif($searchType == 'summary' &&
                     is_array($results = $this->_search($searchType))) {

                $results = array_pop($results);
                $totalPages = ceil($results['total_movies'] /
                                   $this->_search['limit']);

                $results = array_merge(
                              $results,
                              array('totalMovies' => $results['total_movies'],
                                    'totalPages' => $totalPages,
                                     'sd' => ($results['total_movies'] -
                                              $results['hd']),
                                     'not_watched' => ($results['total_movies'] -
                                                       $results['watched']),
                                     'page' => $this->_search['page']));

            }

            return is_array($results) ? $results : array();

        }
        //end search

        public function afterFind($results, $primary = false) {

            //error_log(json_encode($results));
/*
            if(isset($clean[$key]['Movie']['path'])) {
                $clean[$key]['Movie']['path'] =
                    'Y:\\'.str_replace('/', "\\", $clean[$key]['Movie']);
            }
*/
            return $results;

        }
        //end afterFind

        /**
         * cleans and formats result set after pulling from the database
         * @author David <david@sulaco.co.uk>
         * @param array $results
         * @return array $results
         */
        private function _afterFind($results) {

            foreach ($results as $key => $val) {

                $clean[$key]['Movie'] = array_pop($val);

                if(isset($clean[$key]['Movie']['date_added'])) {
                    $clean[$key]['Movie']['date_added'] =
                        date('M y', strtotime($clean[$key]['Movie']['date_added']));
                }

                if(isset($clean[$key]['Movie']['runtime'])) {
                    $hours = floor($clean[$key]['Movie']['runtime'] / 60);
                    $minutes = $clean[$key]['Movie']['runtime'] % 60;
                    $clean[$key]['Movie']['runtime'] = '';

                    if($hours > 1) {
                        $clean[$key]['Movie']['runtime'] = $hours.'hrs';
                    } elseif($hours == 1) {
                        $clean[$key]['Movie']['runtime'] = $hours.'hr';
                    }

                    if($minutes) {
                        $clean[$key]['Movie']['runtime'] .= ' '.$minutes.'mins';
                    }

                }

                if(isset($clean[$key]['Movie']['filesize'])) {
                    $clean[$key]['Movie']['filesize'] =
                        number_format($clean[$key]['Movie']['filesize'] /
                                      (1000 * 1000 * 1000), 2);
                }

            }

            return $clean;

        }
        //end _afterFind

        /**
         * retrieve movies that match the supplied search criteria
         * @author David <david@sulaco.co.uk>
         * @param $resultType - summary|search
         */
        private function _search($resultType) {

            //error_log(json_encode($this->_search));

            if($resultType == 'summary') {
                $limitQuery = false;
                $orderQuery = false;

                $selectQuery =
                    'SELECT COUNT(results.movie_id) AS total_movies, '.
                    '       SUM(CASE '.
                    '             WHEN results.watched = True THEN 1 '.
                    '             ELSE 0 '.
                    '           END) AS watched, '.
                    '       SUM(CASE '.
                    '             WHEN results.hd = True THEN 1 '.
                    '             ELSE 0 '.
                    '           END) AS hd, '.
                    '       MIN(results.imdb_rating) AS min_imdb_rating, '.
                    '       MAX(results.imdb_rating) AS max_imdb_rating, '.
                    '       MIN(results.runtime) AS min_runtime, '.
                    '       MAX(results.runtime) AS max_runtime, '.
                    '       MIN(results.release_year) AS min_release_year, '.
                    '       MAX(results.release_year) AS max_release_year';

                $genres = $this->Genre->Find('all', array('recursive' => 0));
                if(is_array($genres)) {
                    foreach($genres as $genre) {
                        $label = 'genre_'.$genre['Genre']['genre_id'];
                        $selectQuery .=
                                ', SUM(CASE '.
                                "        WHEN '".$genre['Genre']['genre_id']."' = ".
                                "               ANY(results.movie_genre_ids) THEN 1 ".
                                '        ELSE 0 '.
                                '      END) AS "'.$label.'" ';
                    }
                }

                $certificates = $this->Certificate->Find('all', array('recursive' => 0));
                if(is_array($certificates)) {
                    foreach($certificates as $certificate) {
                        $label = 'certificate_'.$certificate['Certificate']['certificate_id'];
                        $selectQuery .=
                                ', SUM(CASE '.
                                "        WHEN '".$certificate['Certificate']['certificate_id']."' = ".
                                "               results.certificate_id THEN 1 ".
                                '        ELSE 0 '.
                                '      END) AS "'.$label.'" ';
                    }
                }

            } else {
                $selectQuery = 'SELECT * ';
                $limitQuery = 'LIMIT 20 OFFSET '.
                              (($this->_search['page'] - 1) *
                                $this->_search['limit']);
                $orderQuery = 'ORDER BY '.$this->_search['sort'].' '.
                                          $this->_search['sortDirection'];
            }

            if(isset($this->_search['personID']) && $this->_search['personID']) {
                $personQuery =
                    "AND person.person_id = '".$this->_search['personID']."'";
            } else {
                $personQuery = false;
            }

            if(isset($this->_search['gid']) && $this->_search['gid']) {
                $genreQuery = '';
                foreach($this->_search['gid'] as $genreID) {
                    if((int)$genreID > 0) {
                        $genreQuery .=
                            "AND '".$genreID."' = ANY(genre.movie_genre_ids)";
                    }
                }
            } else {
                $genreQuery = false;
            }

            if(isset($this->_search['cid']) && $this->_search['cid']) {
                $certificateQuery =
                    "AND Movie.certificate_id = ANY(ARRAY[".implode(',', $this->_search['cid'])."])";
            } else {
                $certificateQuery = false;
            }

            if(isset($this->_search['genreID']) && $this->_search['genreID']) {
                $genreQuery = '';
                foreach($this->_search['genreID'] as $genreID) {
                    if((int)$genreID > 0) {
                        $genreQuery .=
                            "AND '".$genreID."' = ANY(genre.movie_genre_ids)";
                    }
                }
            } else {
                $genreQuery = false;
            }

            if(isset($this->_search['keywordID']) && $this->_search['keywordID']) {
                $keywordQuery =
                    "AND keyword.keyword_id = '".$this->_search['keywordID']."'";
            } else {
                $keywordQuery = false;
            }

            if(isset($this->_search['search']) && $this->_search['search']) {
                $searchQuery =
                    "AND ((Movie.title ILIKE '%".$this->_search['search']."%') OR ".
                    "     (Movie.synopsis ILIKE '%".$this->_search['search']."%') OR ".
                    "     (person.person_name ILIKE '%".$this->_search['search']."%') OR ".
                    "     (keyword.keyword ILIKE '%".$this->_search['search']."%'))";

            } else {
                $searchQuery = false;
            }

            if(isset($this->_search['hd']) &&
               $this->_search['hd'] !== false) {
                $HDQuery =
                    "AND movie.hd = '".(int)$this->_search['hd']."'";
            } else {
                $HDQuery = false;
            }

            if(isset($this->_search['watched']) &&
               $this->_search['watched'] !== false) {
                $watchedQuery =
                    "AND movie.watched = '".(int)$this->_search['watched']."'";
            } else {
                $watchedQuery = false;
            }

            $query = $selectQuery.' '.
                     'FROM   (SELECT    DISTINCT Movie.movie_id, Movie.watched, '.
                     '                  Movie.imdb_id, Movie.title, '.
                     '                  certificate.Certificate, '.
                     '                  Movie.hd, genre.movie_genres, '.
                     '                  genre.movie_genre_ids, Movie.date_added, '.
                     '                  Movie.imdb_rating, Movie.runtime, '.
                     '                  Movie.release_year, Movie.certificate_id, '.
                     '                  certificate.order '.
                     '        FROM      public.movie AS Movie '.
                     '        LEFT JOIN certificate ON '.
                     '                  (Movie.certificate_id = certificate.certificate_id) '.
                     ($personQuery || $searchQuery ?
                     '        LEFT JOIN (SELECT person.person_id, person.person_name, '.
                     '                          movie_role.movie_id '.
                     '                   FROM movie_role '.
                     '                   JOIN person ON movie_role.person_id = person.person_id '.
                     '                  ) AS person ON (person.movie_id = Movie.movie_id) ' : false).' '.
                     ($keywordQuery || $searchQuery ?
                     '        LEFT JOIN (SELECT keyword.keyword_id, keyword.keyword, '.
                     '                          movie_keyword.movie_id '.
                     '                   FROM movie_keyword '.
                     '                   JOIN keyword ON movie_keyword.keyword_id = keyword.keyword_id '.
                     '                  ) AS keyword ON (keyword.movie_id = Movie.movie_id) ' : false).' '.
                     '        LEFT JOIN (SELECT movie_genre.movie_id, '.
                     '                          ARRAY_AGG(genre.genre_id) AS movie_genre_ids, '.
                     '                          ARRAY_AGG(genre.genre) AS movie_genres '.
                     '                   FROM movie_genre '.
                     '                   JOIN genre ON movie_genre.genre_id = genre.genre_id '.
                     '                   GROUP BY movie_genre.movie_id '.
                     '                  ) AS genre ON (genre.movie_id = Movie.movie_id) '.
                     '        WHERE True '.
                     $genreQuery.' '.
                     $certificateQuery.' '.
                     $personQuery.' '.
                     $keywordQuery.' '.
                     $searchQuery.' '.
                     $watchedQuery.' '.
                     $HDQuery.' '.
                     $orderQuery.' '.
                     $limitQuery.' '.
                     '      ) AS results';

            //error_log($query);

            if(is_array($results = $this->query($query)) && $resultType == 'summary') {
                $results = array_pop($results);
            }

            return $results;

        }
        //end _search

        /**
         * parses the supplied search parameters and populates
         * $this->_search array
         * @author David <david@sulaco.co.uk>
         * @param $searchParams - array - see $this->search for permitted
         *                                key/values.
         */
        private function _parseSearchParameters($searchParams) {

            if(isset($searchParams['p']) &&
               (int)$searchParams['p'] > 0) {
                $this->_search['page'] = $searchParams['p'];
            }

            if(isset($searchParams['search']) &&
               !empty($searchParams['search'])) {
                $this->_search['search'] = $searchParams['search'];
            }

            if(isset($searchParams['pid']) &&
               (int)$searchParams['pid'] > 0) {
                $this->_search['personID'] = $searchParams['pid'];
            }

            if(isset($searchParams['kid']) &&
               (int)$searchParams['kid'] > 0) {
                $this->_search['keywordID'] = $searchParams['kid'];
            }

            if(isset($searchParams['s']) &&
               in_array($searchParams['s'],
                        array('title', 'release_year',
                              'imdb_rating', 'hd',
                              'runtime', 'filesize',
                              'date_added', 'cert'))) {

                $this->_search['sort'] = $searchParams['s'];

                if($this->_search['sort'] == 'cert') {
                    $this->_search['sort'] = 'certificate.order';
                }

                if(isset($searchParams['asc']) &&
                   ($searchParams['asc'] == 1 ||
                    $searchParams['asc'] == 0)) {

                    if($searchParams['asc'] == 1) {
                        $this->_search['sortDirection'] = 'asc';
                    } else {
                        $this->_search['sortDirection'] = 'desc';
                    }

                }

            }

            foreach(array('cid', 'gid') as $key) {
                if(isset($searchParams[$key])) {
                    $searchParams[$key] = preg_replace('/[^0-9,]/', '',
                                                       $searchParams[$key]);
                    if($searchParams[$key] &&
                       is_array($searchParams[$key] = explode(',', $searchParams[$key]))) {
                        $this->_search[$key] = $searchParams[$key];
                    }
                }
            }

            foreach(array('hd', 'watched') as $key) {
                if(isset($searchParams[$key]) &&
                   $searchParams[$key] != 'all' &&
                   ((int)$searchParams[$key] === 0 ||
                    (int)$searchParams[$key] === 1)) {
                    $this->_search[$key] = $searchParams[$key];
                }
            }

            $this->_cleanParameters();

        }
        //end _parseSearchParameters

        /**
         * cleans search parameters prior to use in query
         * $this->_search array
         * @author David <david@sulaco.co.uk>
         */
        private function _cleanParameters() {
            //fo
            //Sanitize::escape($string, $connection)

        }
        //end _cleanParameters

    }
    //end Movie

?>