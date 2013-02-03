<?php

    class MoviesController extends AppController {

        /**
         * retrieves movie details for the movie matching imdb_id
         * @author David
         */
        public function movie() {

            $movie = $this->Movie->find('first',
                          array('recursive' => 2,
                                'conditions' =>
                                    array('movie_id' =>
                                            $this->request->params['movieID'])));

            return new CakeResponse(array('body' => json_encode($movie)));

        }
        //end movie

        /**
         * updates the watched status
         * @author David
         */
        public function watched() {

            $Movie = $this->request->input('json_decode');

            if(isset($Movie->Movie->movie_id) &&
               (int)$Movie->Movie->movie_id &&
               isset($Movie->Movie->watched) &&
               $this->Movie->save(
                        array('movie_id' => $Movie->Movie->movie_id,
                              'watched' => (boolean)$Movie->Movie->watched))) {

                $response = 'success';

            } else {

                $response = 'failure';
                $this->header('HTTP/1.1 400 Bad Request');

            }

            return new cakeresponse(array('body' =>
                            json_encode(array('name' => $response))));

        }
        //end watched

        /**
         * returns a list of titles matching supplied title
         * @author David <david@sulaco.co.uk>
         */
        public function title() {

        }
        //end title

        /**
         * returns a csv formatted list of movies
         * @author David <david@sulaco.co.uk>
         */
        public function csv() {

            $data = $this->Movie->search('search',
                                         array_merge(array('limit' => false),
                                                           $this->request->query));

            header("Content-type:application/vnd.ms-excel");
            header("Content-disposition:attachment;filename=movies.csv");
            $this->set(compact('data'));

        }
        //end csv

        /**
         * returns all movies that match the supplied search critera
         * @author David <david@sulaco.co.uk>
         */
        public function movies() {

            return new CakeResponse(
                array('body' => json_encode($this->Movie->search('search',
                                                                 $this->request->query))));

        }
        //end movies

        /**
         * provides summary details for the current resultset
         * @author David <david@sulaco.co.uk>
         */
        public function summary() {

            return new CakeResponse(
                array('body' => json_encode($this->Movie->search('summary',
                                                                 $this->request->query))));

        }
        //end summary
    }

?>
