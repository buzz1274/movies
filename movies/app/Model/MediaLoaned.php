<?php

    class MediaLoaned extends AppModel {

        public $useTable = 'media_loaned';


        public function afterFind($results, $primary = false) {

            foreach($results as $key => $val) {

                if(isset($results[$key]['MediaLoaned']['date_loaned']) &&
                   $results[$key]['MediaLoaned']['date_loaned']) {

                    $results[$key]['MediaLoaned']['date_loaned'] =
                        date('jS F Y', strtotime($results[$key]['MediaLoaned']['date_loaned']));

                }

            }

            return $results;

        }


    }