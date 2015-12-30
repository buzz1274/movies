<?php

    class Movie extends AppModel {

        public $uses = array('AuthComponent');

        public $name = 'movie';

        public $useTable = 'movie';

        public $primaryKey = 'movie_id';

        public $errors = false;

        public $belongsTo = array('Certificate' =>
                                        array('className' => 'Certificate',
                                              'foreignKey' => 'certificate_id'),
                                  'Provider' =>
                                        array('className' => 'Provider',
                                              'foreignKey' => 'provider_id'));

        public $hasMany = array(
            'Watched' =>
                array('className'  => 'UserMovieWatched',
                      'joinTable' => 'user_movie_watched',
                      'foreignKey' => 'movie_id',
                      'order' => 'date_watched ASC NULLS FIRST'),
            'Favourite' =>
                array('className' => 'UserMovieFavourite',
                      'joinTable' => 'user_movie_favourite',
                      'foreignKey' => 'movie_id'));

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

        public function __construct($id = false, $table = null, $ds = null) {

            if(!($userID = AuthComponent::User('user_id'))) {
                $userID = 0;
            }

            $this->hasMany['Watched']['conditions'] = array('user_id='.$userID);
            $this->hasMany['Favourite']['conditions'] = array('user_id='.$userID);

            parent::__construct($id, $table, $ds);

        }

        /**
         * queue a movie to be scraped
         * @author David
         * @param string
         * @param string
         * @param string
         * @return mixed
         */
        public function add($movie) {
            if(!$this->_isValidMovie($movie)) {
                return false;
            }

            $provider = $this->Provider->find('first', array('conditions' =>
                                array('provider_shortcode' => $movie->provider)));

            if(!$this->save(array('imdb_id' => (string)$movie->imdb_id,
                                  'title' => '',
                                  'hd' => (boolean)$movie->hd,
                                  'provider_id' => $provider['Provider']['provider_id']))) {
                return false;
            } else {
                return true;
            }
        }
        //end add

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
                    $movie = $this->afterFind($movie);
                    $clean[$key] = array_pop($movie);
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
         * @author David
         * @param string $search
         * @param string $searchType - [cast|keyword|all]title]
         * @return mixed
         */
        public function autocomplete($search, $searchType) {

            $autocomplete = false;

            if($searchType == 'all' || $searchType == 'title') {
                $results = $this->find('all',
                              array('fields' => array('movie_id AS idn',
                                                      "'title' AS search_type",
                                                      'title AS keyword'),
                                    'recursive' => -1,
                                    'conditions' => array('title ILIKE' => '%'.$search.'%'),
                                    'limit' => '10'));

                foreach($results as $result) {
                    $autocomplete['dropdown'][] = $result[0]['keyword'];
                    $autocomplete['results'][] = array('keyword' => $result[0]['keyword'],
                                                       'search_type' => $result[0]['search_type']);
                }
            }

            if($searchType == 'all' || $searchType == 'cast') {
                $results = $this->Actor->find('all',
                              array('fields' => array('person_id AS idn',
                                                      "'cast' AS search_type",
                                                      'person_name AS keyword'),
                                                      'recursive' => -1,
                                    'conditions' => array('person_name ILIKE' => '%'.$search.'%'),
                                    'limit' => '10'));

                foreach($results as $result) {
                    $autocomplete['dropdown'][] = $result[0]['keyword'];
                    $autocomplete['results'][] = array('keyword' => $result[0]['keyword'],
                                                       'search_type' => $result[0]['search_type']);
                }
            }

            if($searchType == 'all' || $searchType == 'keyword') {
                $results = $this->Keyword->find('all',
                              array('fields' => array('keyword_id AS idn',
                                                      "'keyword' AS search_type",
                                                      'keyword AS keyword'),
                                    'recursive' => -1,
                                    'conditions' => array('keyword ILIKE' => '%'.$search.'%'),
                                    'limit' => '10'));

                foreach($results as $result) {
                    $autocomplete['dropdown'][] = $result[0]['keyword'];
                    $autocomplete['results'][] = array('keyword' => $result[0]['keyword'],
                                                       'search_type' => $result[0]['search_type']);
                }
            }

            return $autocomplete;

        }
        //end autocomplete

        /**
         * cleans results after pulling from the database will be
         * called after any of the framework find methods
         * @author David
         * @param array $results
         * @param boolean $primary
         * @return array $results
         */
        public function afterFind($results, $primary = false) {

            foreach($results as $key => $val) {

                if(isset($results[$key]['Movie']['date_added'])) {
                    $results[$key]['Movie']['date_added'] =
                        date('M y', strtotime($results[$key]['Movie']['date_added']));
                }

                if((isset($results[$key]['Actor']) &&
                    is_array($results[$key]['Actor'])) ||
                   (isset($results[$key]['Director']) &&
                    is_array($results[$key]['Director']))) {

                    $castCount = $this->_castCount($results[$key]['Movie']['movie_id']);

                }

                foreach(array('Director', 'Actor') as $role) {
                    if(isset($results[$key][$role]) &&
                       is_array($results[$key][$role])) {

                        foreach($results[$key][$role] as $keyRole => $person) {
                            $results[$key][$role][$keyRole]['cast_image'] = true;
                            $results[$key][$role][$keyRole]['movie_count'] =
                                                    $castCount[$person['person_id']];
                        }
                    }
                }

                if(isset($results[$key]['Genre']) &&
                   is_array($results[$key]['Genre'])) {

                    $genreCount = $this->_genreCount($results[$key]['Movie']['movie_id']);

                    foreach($results[$key]['Genre'] as $keyGenre => $genre) {
                        $results[$key]['Genre'][$keyGenre]['movie_count'] =
                                                     $genreCount[$genre['genre_id']];
                    }
                }

                if(isset($results[$key]['Keyword']) &&
                   is_array($results[$key]['Keyword'])) {

                    $keywordCount = $this->_keywordCount($results[$key]['Movie']['movie_id']);

                    foreach($results[$key]['Keyword'] as $k => $keyword) {
                        $results[$key]['Keyword'][$k]['movie_count'] =
                                               $keywordCount[$keyword['keyword_id']];
                    }
                }

                if(isset($results[$key]['Watched']) &&
                   is_array($results[$key]['Watched'])) {

                    foreach($results[$key]['Watched'] as $k => $Watched) {
                        if($Watched['date_watched']) {
                            $results[$key]['Watched'][$k]['date_watched'] =
                                    date('jS F Y H:i:s', strtotime($Watched['date_watched']));
                        }

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

            $db = $this->getDataSource();
            $queryParameters = array();

            foreach(array('query', 'randQuery', 'genreQuery', 'certificateQuery', 'personQuery',
                          'searchQuery', 'watchedQuery', 'HDQuery', 'favouritesQuery',
                          'imdb_ratingQuery', 'release_yearQuery', 'runtimeQuery',
                          'orderQuery', 'limitQuery', 'keywordQuery', 'idQuery') as $queryType) {
                $$queryType = false;
            }

            if($resultType == 'summary') {
                $selectQuery =
                    'SELECT COUNT(results.movie_id) AS total_movies, '.
                    '       SUM(CASE '.
                    '             WHEN results.hd = True THEN 1 '.
                    '             ELSE 0 '.
                    '           END) AS hd, '.
                    '       SUM(results.watched) AS watched, '.
                    '       SUM(results.favourite) AS favourites, '.
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
                                "        WHEN :".$label." = ".
                                "               ANY(results.movie_genre_ids) THEN 1 ".
                                '        ELSE 0 '.
                                '      END) AS "'.$label.'" ';
                        $queryParameters[$label] = $genre['Genre']['genre_id'];
                    }
                }

                $certificates = $this->Certificate->Find('all', array('recursive' => 0));
                if(is_array($certificates)) {
                    foreach($certificates as $certificate) {
                        $label = 'certificate_'.$certificate['Certificate']['certificate_id'];
                        $selectQuery .=
                                ', SUM(CASE '.
                                "        WHEN :".$label." = results.certificate_id THEN 1 ".
                                '        ELSE 0 '.
                                '      END) AS "'.$label.'" ';
                        $queryParameters[$label] = $certificate['Certificate']['certificate_id'];
                    }
                }

            } else {
                $selectQuery = 'SELECT * ';
                if(!$this->_search['lucky'] && !$this->_search['movieID'] &&
                   $this->_search['limit'] && (int)$this->_search['limit'] >= 1) {
                    $limitQuery = 'LIMIT :limit OFFSET :offset ';

                    $queryParameters['limit'] = $this->_search['limit'];
                    $queryParameters['offset'] = (($this->_search['page'] - 1) *
                                                   $this->_search['limit']);
                }

                $orderQuery = 'ORDER BY '.$this->_search['sort'].' '.
                                          $this->_search['sortDirection'].' ';
            }

            if(isset($this->_search['movieID']) && $this->_search['movieID']) {
                $idQuery = 'AND Movie.movie_id = :movieID ';
                $queryParameters['movieID'] = $this->_search['movieID'];
            } else {
                if(isset($this->_search['personID']) && $this->_search['personID']) {
                    $personQuery = "AND person.person_id = :personID ";
                    $queryParameters['personID'] =  $this->_search['personID'];
                }

                if(isset($this->_search['gid']) && $this->_search['gid']) {
                    $genreQuery = '';
                    foreach($this->_search['gid'] as $genreID) {
                        if((int)$genreID > 0) {
                            $label = 'searchGenre_'.$genreID;
                            $genreQuery .= "AND :".$label." = ANY(genre.movie_genre_ids)";
                            $queryParameters[$label] =  $genreID;
                        }
                    }
                }

                if(isset($this->_search['cid']) && $this->_search['cid']) {
                    $certificateQuery =
                        "AND Movie.certificate_id::VARCHAR = ANY(regexp_split_to_array(:cid, ','))";
                    $queryParameters['cid'] = implode(',', $this->_search['cid']);
                }

                if(isset($this->_search['keywordID']) && $this->_search['keywordID']) {
                    $keywordQuery = "AND keyword.keyword_id = :keywordID ";
                    $queryParameters['keywordID'] = $this->_search['keywordID'];
                }

                if(isset($this->_search['search']) && $this->_search['search']) {
                    if($this->_search['search_type'] == 'all') {
                        $searchQuery =
                            "AND ((Movie.title ILIKE :search) OR ".
                            "     (Movie.synopsis ILIKE :search) OR ".
                            "     (person.person_name ILIKE :search) OR ".
                            "     (Movie.imdb_id = :imdb_search) OR ".
                            "     (keyword.keyword ILIKE :search))";

                        $queryParameters['search'] = '%'.$this->_search['search'].'%';
                        $queryParameters['imdb_search'] = $this->_search['search'];

                    } elseif($this->_search['search_type'] == 'keyword' &&
                             !$this->_search['keywordID']) {
                        $searchQuery =
                            "AND keyword.keyword ILIKE :search ";
                        $queryParameters['search'] = '%'.$this->_search['search'].'%';

                    } elseif($this->_search['search_type'] == 'cast' &&
                             !$this->_search['personID']) {
                        $searchQuery =
                            "AND person.person_name ILIKE :search ";
                        $queryParameters['search'] = '%'.$this->_search['search'].'%';

                    } elseif($this->_search['search_type'] == 'title') {
                        $searchQuery =
                            "AND Movie.title ILIKE :search ";
                        $queryParameters['search'] = '%'.$this->_search['search'].'%';
                    }
                }

                if(isset($this->_search['hd']) &&
                   $this->_search['hd'] !== false) {
                    $HDQuery =
                        "AND Movie.hd = :hd";
                    $queryParameters['hd'] = (int)$this->_search['hd'];
                }

                if(isset($this->_search['watched']) &&
                   $this->_search['watched'] !== false) {
                    if($this->_search['watched'] == 1) {
                        $watchedQuery = "AND user_movie_watched.user_id IS NOT NULL";
                    } else {
                        $watchedQuery = "AND user_movie_watched.user_id IS NULL";
                    }
                }

                if(isset($this->_search['favourites']) &&
                    $this->_search['favourites'] !== false) {
                    if($this->_search['favourites'] == 1) {
                        $favouritesQuery = "AND user_movie_favourite.user_id IS NOT NULL";
                    } else {
                        $favouritesQuery = "AND user_movie_favourite.user_id IS NULL";
                    }
                }

                foreach(array('imdb_rating', 'release_year', 'runtime') as $field) {
                    if(isset($this->_search[$field]) &&
                       is_array($this->_search[$field])) {
                        ${$field.'Query'} = 'AND Movie.'.$field.' BETWEEN
                                            :'.$field.'_min AND :'.$field.'_max';
                        $queryParameters[$field.'_min'] = $this->_search[$field]['min'];
                        $queryParameters[$field.'_max'] = $this->_search[$field]['max'];

                    }
                }

                if($this->_search['lucky']) {
                    $randQuery = 'random() AS rand,';
                    $orderQuery = 'ORDER BY rand ';
                    $limitQuery = 'LIMIT 1';
                }

            }

            $query = $selectQuery.' '.
                     'FROM   (SELECT    DISTINCT Movie.movie_id, '.
                     '                  Movie.imdb_id, Movie.title, '.$randQuery.' '.
                     '                  certificate.Certificate, '.
                     '                  Movie.hd, genre.movie_genres, '.
                     '                  genre.movie_genre_ids, Movie.date_added, '.
                     '                  Movie.imdb_rating, Movie.runtime, '.
                     '                  Movie.release_year, Movie.certificate_id, '.
                     '                  certificate.order, '.
                     '                  CASE '.
                     '                    WHEN user_movie_favourite.user_id IS NULL THEN 0 '.
                     '                    ELSE 1 '.
                     '                  END AS favourite, '.
                     '                  CASE '.
                     '                    WHEN user_movie_watched.user_id IS NULL THEN 0 '.
                     '                    ELSE 1 '.
                     '                  END AS watched '.
                     '        FROM      public.movie AS Movie '.
                     '        LEFT JOIN certificate ON '.
                     '                  (Movie.certificate_id = certificate.certificate_id) '.
                     '        LEFT JOIN user_movie_favourite ON '.
                     '                  (    Movie.movie_id = user_movie_favourite.movie_id '.
                     '                   AND user_movie_favourite.user_id = :userID) '.
                     '        LEFT JOIN user_movie_watched ON '.
                     '                  (    Movie.movie_id = user_movie_watched.movie_id '.
                     '                   AND user_movie_watched.user_id = :userID) '.
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
                     "          AND Movie.title != '' ".
                     ($idQuery ? $idQuery : $genreQuery.' '.
                                            $certificateQuery.' '.
                                            $personQuery.' '.
                                            $keywordQuery.' '.
                                            $searchQuery.' '.
                                            $watchedQuery.' '.
                                            $HDQuery.' '.
                                            $favouritesQuery.' '.
                                            $imdb_ratingQuery.' '.
                                            $release_yearQuery.' '.
                                            $runtimeQuery.' '.
                                            $orderQuery.' '.
                                            $limitQuery.' ').
                      '      ) AS results';

            $queryParameters['userID'] = $this->_search['userID'];

            if(is_array($results = $db->fetchAll($query, $queryParameters)) &&
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

            if(!isset($searchParams['userID']) ||
               (int)$searchParams['userID'] < 1) {
                $this->_search['userID'] = 0;
            } else {
                $this->_search['userID'] = $searchParams['userID'];
            }

            if(!isset($searchParams['id']) ||
                (int)$searchParams['id'] < 1) {
                $this->_search['movieID'] = false;
            } else {
                $this->_search['movieID'] = $searchParams['id'];
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
                              'watched', 'date_added', 'cert'))) {

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

            foreach(array('hd', 'watched', 'favourites') as $key) {
                if(isset($searchParams[$key]) &&
                   $searchParams[$key] != 'all' &&
                   ((int)$searchParams[$key] === 0 ||
                    (int)$searchParams[$key] === 1)) {
                    $this->_search[$key] = $searchParams[$key];
                }
            }

        }
        //end _parseSearchParameters

        /**
         * retrieves a movie count for all cast attached to
         * the supplied movieID
         * @author David
         * @param $movieID
         * @return mixed
         */
        private function _castCount($movieID) {

            $results = $this->MovieRole->find('all',
                            array('fields' => array('person_id',
                                                    'COUNT(DISTINCT mr.movie_id)'),
                                  'joins' => array(array('table' => 'movie_role',
                                                         'alias' => 'mr',
                                                         'conditions' => 'mr.person_id = MovieRole.person_id')),
                                  'conditions' => array('MovieRole.movie_id' => $movieID),
                                  'group' => 'MovieRole.person_id'));

            if(!is_array($results) || empty($results)) {
                $castCount = false;
            } else {
                foreach($results as $result) {
                    $castCount[$result['MovieRole']['person_id']] = $result[0]['count'];
                }
            }

            return $castCount;

        }
        //end _castCount

        /**
         * retrieves a movie count for all genres attached to
         * the supplied movieID
         * @author David
         * @param $movieID
         * @return mixed
         */
        private function _genreCount($movieID) {

            $results = $this->MovieGenre->find('all',
                array('fields' => array('genre_id',
                                        'COUNT(DISTINCT mg.movie_id)'),
                    'joins' => array(array('table' => 'movie_genre',
                                           'alias' => 'mg',
                                           'conditions' => 'mg.genre_id = MovieGenre.genre_id')),
                    'conditions' => array('MovieGenre.movie_id' => $movieID),
                    'group' => 'MovieGenre.genre_id'));

            if(!is_array($results) || empty($results)) {
                $genreCount = false;
            } else {
                foreach($results as $result) {
                    $genreCount[$result['MovieGenre']['genre_id']] = $result[0]['count'];
                }
            }

            return $genreCount;

        }
        //end _genreCount

        /**
         * retrieves a movie count for all keywords attached to
         * the supplied movieID
         * @author David
         * @param $movieID
         * @return mixed
         */
        private function _keywordCount($movieID) {

            $results = $this->MovieKeyword->find('all',
                array('fields' => array('keyword_id',
                                        'COUNT(DISTINCT mk.movie_id)'),
                      'joins' => array(array('table' => 'movie_keyword',
                                             'alias' => 'mk',
                                             'conditions' => 'mk.keyword_id = MovieKeyword.keyword_id')),
                      'conditions' => array('MovieKeyword.movie_id' => $movieID),
                      'group' => 'MovieKeyword.keyword_id'));

            if(!is_array($results) || empty($results)) {
                $keywordCount = false;
            } else {
                foreach($results as $result) {
                    $keywordCount[$result['MovieKeyword']['keyword_id']] = $result[0]['count'];
                }
            }

            return $keywordCount;

        }
        //end _keywordCount

        /**
         * validates that movie data supplied for queuing
         * is valid
         * @param array $movie
         * @return mixed
         */
        private function _isValidMovie($movie) {
            $this->errors = false;

            if(!is_object($movie) ||
               !isset($movie->imdb_id) || !$movie->imdb_id) {
                $this->errors['imdb_id'] = 'Please enter an imdb id';
            } elseif($this->find('first', array('conditions' =>
                         array('imdb_id' => $movie->imdb_id)))) {
                $this->errors['imdb_id'] = 'imdb id already in database';
            }

            if(!is_object($movie) ||
                !isset($movie->hd) ||
                !in_array($movie->hd, array('yes', 'no'))) {
                $this->errors['hd'] = 'Please select HD';
            }

            if(!is_object($movie) ||
                !isset($movie->provider) ||
                !$this->Provider->find('first', array('conditions' =>
                    array('provider_shortcode' => $movie->provider)))) {

                $this->errors['provider'] = 'Please select a valid provider';

            }

            return !is_array($this->errors);

        }
        //end validateAdd

    }
    //end Movie
