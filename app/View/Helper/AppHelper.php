<?php

    App::uses('Helper', 'View');
    class AppHelper extends Helper {

        /**
         * formats checkboxes in the advanced search box
         * @author David
         * @param array $data
         * @param string $type - type of data (genre|certificate)
         * @return string
         */
        public function checkboxFormatter($data, $type) {
            $checkboxes = '';
            $count = 0;
            $dataMapper = array('genre' =>
                                    array('tableName' => 'Genre',
                                          'field' => 'genre',
                                          'id' => 'genre_id'),
                                'certificate' =>
                                    array('tableName' => 'Certificate',
                                          'field' => 'certificate',
                                          'id' => 'certificate_id'));

            foreach($data as $d) {
                if(!($count % 2)) {
                    if($count) {
                        $checkboxes .= '</li>';
                    }
                    $checkboxes .= '<li class="span12">';
                    $offset = false;
                } else {
                    $offset = 'offset6 ';
                }

                $label = preg_replace('/[^a-z]/i', '',
                                      strtolower($d[$dataMapper[$type]['tableName']]
                                                   [$dataMapper[$type]['field']])).
                         '_'.$type;

                $checkboxes .=
                     '<span class="span6 '.$offset.'" >'.
                     '  <label class="checkbox inline nav_text">'.
                     '    <input type="checkbox">'.
                     $d[$dataMapper[$type]['tableName']][$dataMapper[$type]['field']].
                     ($type == 'genre' ? '&nbsp;(<%= '.$label.' %>)' : '').
                     '  </label>'.
                     '</span>';
                $count++;
            }

            return $checkboxes;

        }
        //end genreCheckboxFormatter

    }

?>