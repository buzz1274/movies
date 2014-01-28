<?php

    class UserMovieDownloaded extends AppModel {

        public $useTable = 'user_movie_downloaded';

        public $belongsTo = array('Movie');

        /**
         * parse results and formats into output for display
         * @param mixed $results
         * @param bool $primary
         * @return array|mixed
         */
        public function afterFind($results, $primary = false) {

            if(!is_array($results) || !isset($results[0]['UserMovieDownloaded'])) {
                return $results;
            } else {

                foreach($results as $result) {
                    $downloaded[] =
                        array('download_id' => $result['UserMovieDownloaded']['id'],
                              'movie_id' => $result['Movie']['movie_id'],
                              'date_downloaded' => date('jS F Y H:i:s',
                                                        strtotime($result['UserMovieDownloaded']
                                                                         ['date_downloaded'])),
                              'title' => $result['Movie']['title'],
                              'status' => ucfirst($result['UserMovieDownloaded']['status']),
                              'filesize' => $result['UserMovieDownloaded']['filesize']);
                }

                return $downloaded;

            }

        }
        //end afterFind

    }
    //end UserMovieDownloaded