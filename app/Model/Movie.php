<?php

    class Movie extends AppModel {

        public $name = 'movie';

        public $useTable = 'movie';

        public $primaryKey = 'movie_id';

        public $belongsTo = array('Certificate' =>
                                        array('className' => 'Certificate',
                                              'foreignKey' => 'certificate_id'),
                                   'Media');

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
                      'order'                 => 'order',
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
                      'order'                 => 'order',
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
                      'order'                 => 'order',
                      'limit'                 => '',
                      'offset'                => '',
                      'finderQuery'           => '',
                      'deleteQuery'           => '',
                      'insertQuery'           => ''));

        /*@var array - default search parameters*/
        private $_search = array('page' => 1,
                                 'limit' => 25,
                                 'gid' => false,
                                 'personID' => false,
                                 'keywordID' => false,
                                 'search' => '',
                                 'sort' => 'title',
                                 'sortDirection' => 'asc',
                                 'hd' => false,
                                 'imdb_rating' => false,
                                 'runtime' => false,
                                 'release_year' => false,
                                 'cid' => false);

        /**
         * @author David
         * @param array
         * @param array $searchParams
         * @return mixed
         */
        public function search($searchType, $searchParams) {

            $this->_parseSearchParameters($searchParams);
            $results = array();

            if($searchType == 'search' &&
               is_array($results = $this->_search($searchType))) {

                while(list($key, $val) = each($results)) {
                    $movie[$key]['Movie'] = array_pop($val);
                    $clean[$key] = array_pop($this->afterFind($movie));
                }

                $results = $clean;
                unset($movie);
                unset($clean);

            } elseif($searchType == 'summary' &&
                     is_array($results = $this->_search($searchType))) {

                $results = array_pop($results);
                $totalPages = ceil($results['total_movies'] /
                                   $this->_search['limit']);

                $results = array_merge(
                              $results,
                              array('totalMovies' => $results['total_movies'],
                                    'totalPages' => $totalPages,
                                    'not_favourites' => ($results['total_movies'] -
                                                         $results['favourites']),
                                    'sd' => ($results['total_movies'] -
                                             $results['hd']),
                                    'not_watched' => ($results['total_movies'] -
                                                      $results['watched']),
                                    'page' => $this->_search['page']));

            }

            return is_array($results) ? $results : array();

        }
        //end search

        /**
         * cleans results after pulling from the database will be
         * called after any of the framework find methods
         * @author David
         * @param array $results
         * @param boolean $primary
         * @return array $results
         */
        public function afterFind($results, $primary = false) {

             foreach ($results as $key => $val) {

                if(isset($results[$key]['Movie']['path'])) {
                    $results[$key]['Movie']['path'] =
                        'V:\\'.str_replace('/', "\\", $results[$key]['Movie']['path']);
                }

                if(isset($results[$key]['Movie']['date_added'])) {
                    $results[$key]['Movie']['date_added'] =
                        date('M y', strtotime($results[$key]['Movie']['date_added']));
                }

                foreach(array('Director', 'Actor') as $role) {
                    if(isset($results[$key][$role]) &&
                       is_array($results[$key][$role])) {

                        foreach($results[$key][$role] as $keyRole => $person) {
                            if(file_exists(IMAGE_SAVE_PATH.'/cast/'.
                                           $person['person_imdb_id'].'.jpg')) {
                                $results[$key][$role][$keyRole]['cast_image'] = true;
                            } else {
                                $results[$key][$role][$keyRole]['cast_image'] = false;
                            }

                            $results[$key][$role][$keyRole]['movie_count'] =
                                $this->MovieRole->find('count',
                                    array('fields' => 'COUNT(DISTINCT movie_id) as count',
                                          'conditions' => array('person_id' => $person['person_id'])));
                        }
                    }
                }

                if(isset($results[$key]['Genre']) &&
                   is_array($results[$key]['Genre'])) {

                    foreach($results[$key]['Genre'] as $keyGenre => $genre) {

                        $results[$key]['Genre'][$keyGenre]['movie_count'] =
                            $this->MovieGenre->find('count',
                                array('fields' => 'COUNT(DISTINCT movie_id) as count',
                                      'conditions' => array('genre_id' => $genre['genre_id'])));
                    }
                }

                if(isset($results[$key]['Keyword']) &&
                   is_array($results[$key]['Keyword'])) {

                    foreach($results[$key]['Keyword'] as $k => $keyword) {

                        $results[$key]['Keyword'][$k]['movie_count'] =
                            $this->MovieKeyword->find('count',
                                array('fields' => 'COUNT(DISTINCT movie_id) as count',
                                      'conditions' => array('keyword_id' => $keyword['keyword_id'])));

                    }
                }

                if(isset($results[$key]['Movie']['runtime'])) {
                    $hours = floor($results[$key]['Movie']['runtime'] / 60);
                    $minutes = $results[$key]['Movie']['runtime'] % 60;
                    $results[$key]['Movie']['runtime'] = '';

                    if($hours > 1) {
                        $results[$key]['Movie']['runtime'] = $hours.'hrs';
                    } elseif($hours == 1) {
                        $results[$key]['Movie']['runtime'] = $hours.'hr';
                    }

                    if($minutes) {
                        $results[$key]['Movie']['runtime'] .= ' '.$minutes.'mins';
                    }

                }

                if(isset($results[$key]['Movie']['filesize'])) {
                    $results[$key]['Movie']['filesize'] =
                        number_format($results[$key]['Movie']['filesize'] /
                                      (1000 * 1000 * 1000), 2);
                }

            }

            return $results;

        }
        //end afterFind

        /**
         * retrieve movies that match the supplied search criteria
         * @author David
         * @param $resultType - summary|search
         * @return mixed
         */
        private function _search($resultType) {

            error_log(json_encode($this->_search));

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
                    '       0 AS favourites, '.
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
                if(!$this->_search['limit'] ||
                   !(int)$this->_search['limit']) {
                    $limitQuery = false;
                } else {
                    $limitQuery = 'LIMIT '.$this->_search['limit'].' OFFSET '.
                                  (($this->_search['page'] - 1) *
                                    $this->_search['limit']);
                }
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

            if(isset($this->_search['keywordID']) && $this->_search['keywordID']) {
                $keywordQuery =
                    "AND keyword.keyword_id = '".$this->_search['keywordID']."'";
            } else {
                $keywordQuery = false;
            }

            if(isset($this->_search['search']) && $this->_search['search']) {
                if($this->_search['search_type'] == 'all') {
                    $searchQuery =
                        "AND ((Movie.title ILIKE '%".$this->_search['search']."%') OR ".
                        "     (Movie.synopsis ILIKE '%".$this->_search['search']."%') OR ".
                        "     (person.person_name ILIKE '%".$this->_search['search']."%') OR ".
                        "     (Movie.imdb_id = '".$this->_search['search']."') OR ".
                        "     (keyword.keyword ILIKE '%".$this->_search['search']."%'))";
                } elseif($this->_search['search_type'] == 'keyword' &&
                         !$this->_search['keywordID']) {
                    $searchQuery =
                        "AND keyword.keyword ILIKE '%".$this->_search['search']."%' ";
                } elseif($this->_search['search_type'] == 'cast' &&
                         !$this->_search['personID']) {
                    $searchQuery =
                        "AND person.person_name ILIKE '%".$this->_search['search']."%' ";
                } elseif($this->_search['search_type'] == 'title') {
                    $searchQuery =
                        "AND Movie.title ILIKE '%".$this->_search['search']."%' ";
                } else {
                    $searchQuery = false;
                }
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

            foreach(array('imdb_rating', 'release_year', 'runtime') as $field) {
                ${$field.'Query'} = false;
                if(isset($this->_search[$field]) &&
                   is_array($this->_search[$field])) {
                    ${$field.'Query'} = 'AND Movie.'.$field.' BETWEEN
                                        '.$this->_search[$field]['min'].' AND '.
                                        $this->_search[$field]['max'];

                }
            }


            if($this->_search['lucky']) {
                $randQuery = 'random() AS rand,';
                $orderQuery = 'ORDER BY rand ';
                $limitQuery = 'LIMIT 1';
            } else {
                $randQuery = false;
            }

            error_log($this->_search['userID']);

            $query = $selectQuery.' '.
                     'FROM   (SELECT    DISTINCT Movie.movie_id, Movie.watched, '.
                     '                  Movie.imdb_id, Movie.title, '.$randQuery.' '.
                     '                  certificate.Certificate, '.
                     '                  Movie.hd, genre.movie_genres, '.
                     '                  genre.movie_genre_ids, Movie.date_added, '.
                     '                  Movie.imdb_rating, Movie.runtime, '.
                     '                  Movie.release_year, Movie.certificate_id, '.
                     '                  certificate.order, Movie.filesize, '.
                     '                  COALESCE(user_movie_favourite.user_id, 0) AS favourite '.
                     '        FROM      public.movie AS Movie '.
                     '        LEFT JOIN certificate ON '.
                     '                  (Movie.certificate_id = certificate.certificate_id) '.
                     '        LEFT JOIN user_movie_favourite ON '.
                     '                  (    Movie.movie_id = user_movie_favourite.movie_id '.
                     '                   AND user_movie_favourite.user_id = '.$this->_search['userID'].') '.
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
                     '        WHERE Movie.deleted = False '.
                     $genreQuery.' '.
                     $certificateQuery.' '.
                     $personQuery.' '.
                     $keywordQuery.' '.
                     $searchQuery.' '.
                     $watchedQuery.' '.
                     $HDQuery.' '.
                     $imdb_ratingQuery.' '.
                     $release_yearQuery.' '.
                     $runtimeQuery.' '.
                     $orderQuery.' '.
                     $limitQuery.' '.
                     '      ) AS results';

            if(is_array($results = $this->query($query)) &&
               $resultType == 'summary') {
                $results = array_pop($results);
            }

            return $results;

        }
        //end _search

        /**
         * parses the supplied search parameters and populates
         * $this->_search array
         * @author David
         * @param $searchParams - array - see $this->search for permitted
         *                                key/values.
         */
        private function _parseSearchParameters($searchParams) {

            $this->_search['userID'] = $searchParams['userID'];

            if(!$this->_search['userID']) {
                $this->_search['userID'] = 0;
            }

            if(isset($searchParams['limit']) &&
               ($searchParams['limit'] === false ||
                (int)$searchParams['limit'] > 0)) {
                $this->_search['limit'] = $searchParams['limit'];
            }

            if(isset($searchParams['lucky']) && $searchParams['lucky']) {
                $this->_search['lucky'] = true;
            } else {
                $this->_search['lucky'] = false;
            }

            if(isset($searchParams['p']) &&
               (int)$searchParams['p'] > 0) {
                $this->_search['page'] = $searchParams['p'];
            }

            if(isset($searchParams['search']) &&
               !empty($searchParams['search'])) {
                $this->_search['search'] = urldecode($searchParams['search']);
            }

            if(!isset($searchParams['search_type']) || empty($searchParams['search_type'])) {
                $this->_search['search_type'] = 'all';
            } else {
                $this->_search['search_type'] = $searchParams['search_type'];
            }

            if(isset($searchParams['pid']) &&
               (int)$searchParams['pid'] > 0) {
                $this->_search['personID'] = $searchParams['pid'];
            }

            if(isset($searchParams['kid']) &&
               (int)$searchParams['kid'] > 0) {
                $this->_search['keywordID'] = $searchParams['kid'];
            }

            foreach(array('imdb_rating', 'runtime',
                          'release_year') as $field) {

                if(isset($searchParams[$field])) {
                    $searchParams[$field] =
                        explode(',', $searchParams[$field]);

                    if(isset($searchParams[$field][0]) &&
                       (int)$searchParams[$field][0] &&
                       isset($searchParams[$field][1]) &&
                       (int)$searchParams[$field][1] &&
                       (int)$searchParams[$field][0] <=
                       (int)$searchParams[$field][1]) {
                        $this->_search[$field] =
                                array('min' => $searchParams[$field][0],
                                      'max' => $searchParams[$field][1]);

                    }

                }
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

            $this->_search = $this->_cleanParameters($this->_search);

        }
        //end _parseSearchParameters

        /**
         * cleans search parameters prior to use in query
         *
         * @author David
         * @param mixed $search - data to clean
         * @return mixed $search
         */
        private function _cleanParameters($search) {

            while(list($key, $val) = each($search)) {
                if($val || $val === 0) {
                    if(is_array($val)) {
                        $search[$key] = $this->_cleanParameters($val);
                    } else {
                        $search[$key] = Sanitize::escape(urldecode($val),
                                                         'default');
                    }
                }
            }

            return $search;

        }
        //end _cleanParameters

    }
    //end Movie
