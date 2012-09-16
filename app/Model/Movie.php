<?php

    class Movie extends AppModel {

        public $name = 'movie';

        public $useTable = 'movie';

        public $primaryKey = 'movie_id';

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
         * cleans results after pulling from the database
         * @author David <david@sulaco.co.uk>
         */
        public function afterFind($results, $primary = false) {

            foreach ($results as $key => $val) {

                if(isset($val['Movie']['date_added'])) {

                    $results[$key]['Movie']['date_added'] =
                        date('jS M Y', strtotime($val['Movie']['date_added']));

                }

                if(isset($val['Movie']['runtime'])) {

                    $hours = floor($val['Movie']['runtime'] / 60);
                    $minutes = $val['Movie']['runtime'] % 60;

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
                        str_replace('/', "\\", $val['Movie']['path']);
                }

            }

            return $results;

        }
        //end afterFind

        /**
         * constructs a join that can be used to search for genreIDs
         * @author David <david@sulaco.co.uk>
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
         * @author David <david@sulaco.co.uk>
         * @param integer $personID - id of the genre to search for
         */
        public function personSearch($personID) {

            return array('table' => '(SELECT person.person_id, '.
                                    '        person.person_name, '.
                                    '        movie_role.movie_id '.
                                    ' FROM movie_role '.
                                    ' JOIN person ON movie_role.person_id = '.
                                    '                       person.person_id)',
                         'alias' => 'person',
                         'conditions' => array(
                                    'person.movie_id = Movie.movie_id',
                                    'person.person_id = "'.$personID.'"'));

        }
        //end personSearch

    }

?>