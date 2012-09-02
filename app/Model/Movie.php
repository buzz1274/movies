<?php

    class Movie extends AppModel {

        public $name = 'movie';

        public $useTable = 'movie';

        public function afterFind($results, $primary = false) {

            foreach ($results as $key => $val) {

                if(isset($val['Movie']['date_added'])) {

                    $results[$key]['Movie']['date_added'] =
                        date('jS M Y', strtotime($val['Movie']['date_added']));

                }

                if(isset($val['Movie']['runtime'])) {

                    $results[$key]['Movie']['runtime'] =
                        floor($val['Movie']['runtime'] / 60).'hrs '.
                        ($val['Movie']['runtime'] % 60).'mins';

                }

                if(isset($val['Movie']['filesize'])) {

                    $results[$key]['Movie']['filesize'] =
                        number_format($val['Movie']['filesize'] / (1000 * 1000 * 1000), 2);

                }

            }

            return $results;

        }
        //end afterFind

    }

?>