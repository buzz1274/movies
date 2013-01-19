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

        /**
         * retrieve movies that match the supplied search criteria
         * @author David
         */
        public function search($resultType, $searchParams) {

            if($resultType == 'summary') {
                $limitQuery = false;
                $selectQuery =
                    'SELECT COUNT(summary.movie_id) AS total_movies, '.
                    '       SUM(CASE '.
                    '             WHEN summary.watched = True THEN 1 '.
                    '             ELSE 0 '.
                    '           END) AS watched, '.
                    '       SUM(CASE '.
                    '             WHEN summary.hd = True THEN 1 '.
                    '             ELSE 0 '.
                    '           END) AS hd, '.
                    '       MIN(summary.imdb_rating) AS min_imdb_rating, '.
                    '       MAX(summary.imdb_rating) AS max_imdb_rating, '.
                    '       MIN(summary.runtime) AS min_runtime, '.
                    '       MAX(summary.runtime) AS max_runtime, '.
                    '       MIN(summary.release_year) AS min_release_year, '.
                    '       MAX(summary.release_year) AS max_release_year';

                if(is_array($Genres = $this->Genre->Find('all', array('recursive' => 0)))) {
                    foreach($Genres as $Genre) {
                        $label = preg_replace('/[^a-z]/i', '',
                                              strtolower($Genre['Genre']['genre']));
                        $selectQuery .=
                                ', SUM(CASE '.
                                "        WHEN '".$Genre['Genre']['genre']."' = ".
                                "               ANY(summary.movie_genres) THEN 1 ".
                                '        ELSE 0 '.
                                '      END) AS "'.$label.'_genre" ';
                    }

                }

            } else {
                $selectQuery =
                    'SELECT * ';
                $limitQuery = false;
            }

            if(isset($searchParams['personID']) && $searchParams['personID']) {
                $personQuery =
                    "AND person.person_id = '".$searchParams['personID']."'";
            } else {
                $personQuery = false;
            }

            if(isset($searchParams['genreID']) && $searchParams['genreID']) {
                $genreQuery =
                    "AND '".$searchParams['genreID']."' = ANY(genre.movie_genre_ids)";
            } else {
                $genreQuery = false;
            }

            if(isset($searchParams['keywordID']) && $searchParams['keywordID']) {
                $keywordQuery =
                    "AND keyword.keyword_id = '".$searchParams['keywordID']."'";
            } else {
                $keywordQuery = false;
            }

            if(isset($searchParams['search']) && $searchParams['search']) {
                $searchQuery =
                    "AND ((Movie.title ILIKE '%".$searchParams['search']."%') OR ".
                    "     (person.person_name ILIKE '%".$searchParams['search']."%') OR ".
                    "     (keyword.keyword ILIKE '%".$searchParams['search']."%'))";

            } else {
                $searchQuery = false;
            }

            $query = $selectQuery.' '.
                     'FROM   (SELECT    DISTINCT Movie.movie_id, Movie.watched, '.
                     '                  Movie.hd, genre.movie_genres, '.
                     '                  Movie.imdb_rating, Movie.runtime, '.
                     '                  Movie.release_year '.
                     '        FROM      public.movie AS Movie '.
                     '        LEFT JOIN (SELECT person.person_id, person.person_name, '.
                     '                          movie_role.movie_id '.
                     '                   FROM movie_role '.
                     '                   JOIN person ON movie_role.person_id = person.person_id '.
                     '                  ) AS person ON (person.movie_id = Movie.movie_id) '.
                     '        LEFT JOIN (SELECT keyword.keyword_id, keyword.keyword, '.
                     '                          movie_keyword.movie_id '.
                     '                   FROM movie_keyword '.
                     '                   JOIN keyword ON movie_keyword.keyword_id = keyword.keyword_id '.
                     '                  ) AS keyword ON (keyword.movie_id = Movie.movie_id) '.
                     '        LEFT JOIN (SELECT movie_genre.movie_id, '.
                     '                          ARRAY_AGG(genre.genre_id) AS movie_genre_ids, '.
                     '                          ARRAY_AGG(genre.genre) AS movie_genres '.
                     '                   FROM movie_genre '.
                     '                   JOIN genre ON movie_genre.genre_id = genre.genre_id '.
                     '                   GROUP BY movie_genre.movie_id '.
                     '                  ) AS genre ON (genre.movie_id = Movie.movie_id) '.
                     '        WHERE True '.
                     $genreQuery.' '.
                     $personQuery.' '.
                     $keywordQuery.' '.
                     $searchQuery.' '.
                     '      ) AS summary '.
                     $limitQuery;

            if(is_array($results = $this->query($query))) {
                $results = array_pop($results);
            }

            return $results;

        }
        //end search

        public function summary($searchParams) {

            if(!is_array($summary = $this->search('summary', $searchParams))) {
                return false;
            } else {
                $summary = array_pop($summary);
                $totalPages = ceil($summary['total_movies'] / $searchParams['limit']);

                /*
                $startOffset = (($searchParams['page'] - 1) * $searchParams['limit']) + 1;
                $endOffset = ($startOffset - 1) + $searchParams['limit'];
                */

                return array_merge($summary,
                                  array('totalMovies' => $summary['total_movies'],
                                        'totalPages' => $totalPages,
                                        'sd' => ($summary['total_movies'] -
                                                 $summary['hd']),
                                        'not_watched' => ($summary['total_movies'] -
                                                          $summary['watched']),
                                        'page' => $searchParams['page']));

            }

        }
        //end summary

        /**
         * cleans results after pulling from the database
         * @author David
         */
        public function afterFind($results, $primary = false) {

            foreach ($results as $key => $val) {

                if(isset($val['Movie']['date_added'])) {

                    $results[$key]['Movie']['date_added'] =
                        date('M y', strtotime($val['Movie']['date_added']));

                }

                if(isset($val['Movie']['runtime'])) {

                    $hours = floor($val['Movie']['runtime'] / 60);
                    $minutes = $val['Movie']['runtime'] % 60;
                    $results[$key]['Movie']['runtime'] = '';

                    if($hours > 1) {

                        $results[$key]['Movie']['runtime'] = $hours.'hrs';

                    } elseif($hours == 1) {

                        $results[$key]['Movie']['runtime'] = $hours.'hr';

                    }

                    if($minutes) {

                        $results[$key]['Movie']['runtime'] .=
                            ' '.$minutes.'mins';

                    }

                }

                if(isset($val['Movie']['filesize'])) {

                    $results[$key]['Movie']['filesize'] =
                        number_format($val['Movie']['filesize'] /
                                      (1000 * 1000 * 1000), 2);

                }

                if(isset($val['Movie']['path'])) {
                    $results[$key]['Movie']['path'] =
                        'Y:\\'.str_replace('/', "\\", $val['Movie']['path']);
                }

            }

            return $results;

        }
        //end afterFind

        /**
         * constructs a join that can be used to search for genreIDs
         * @author David
         * @param integer $genreID - id of the genre to search for
         */
        public function genreSearch($genreID) {

            return array('table' => '(SELECT genre.genre_id, genre.genre, '.
                                    '        movie_genre.movie_id '.
                                    ' FROM movie_genre '.
                                    ' JOIN genre ON movie_genre.genre_id = '.
                                    '                       genre.genre_id)',
                         'alias' => 'genre',
                         'conditions' => array(
                                    'genre.movie_id = Movie.movie_id',
                                    'genre.genre_id = "'.$genreID.'"'));

        }
        //end genreSearch

        /**
         * constructs a join that can be used to search for personIDs
         * @author David
         * @param integer $personID - id of the genre to search for
         */
        public function personSearch($personID) {

            $join = array('table' => '(SELECT person.person_id, '.
                          '                   person.person_name, '.
                          '                   movie_role.movie_id '.
                          '           FROM movie_role '.
                          '           JOIN person ON movie_role.person_id = '.
                          '                          person.person_id)',
                         'alias' => 'person',
                         'conditions' => array(
                                           'person.movie_id = Movie.movie_id'));

            if($personID) {
                $join['conditions'][] = 'person.person_id = "'.$personID.'"';
            }

            return $join;

        }
        //end personSearch

        /**
         * constructs a join that can be used to search for personIDs
         * @author David
         * @param integer $keywordID - id of the keyword to search for
         */
        public function keywordSearch($keywordID) {

            $join = array('table' => '(SELECT keyword.keyword_id, '.
                          '                   keyword.keyword, '.
                          '                   movie_keyword.movie_id '.
                          '           FROM movie_keyword '.
                          '           JOIN keyword ON movie_keyword.keyword_id = '.
                          '                           keyword.keyword_id)',
                         'alias' => 'keyword',
                         'conditions' => array(
                                           'keyword.movie_id = Movie.movie_id'));

            if($keywordID) {
                $join['conditions'][] = 'keyword.keyword_id = "'.$keywordID.'"';
            }

            return $join;

        }
        //end personSearch

    }
    //end Movie

?>