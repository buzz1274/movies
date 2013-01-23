<?php

    App::uses('Helper', 'View');
    class AppHelper extends Helper {

        /**
         * formats checkboxes for the advanced search box
         * @author David <david@sulaco.co.uk>
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

                if($d[$dataMapper[$type]['tableName']][$dataMapper[$type]['field']] == 'Sci-Fi') {
                    $d[$dataMapper[$type]['tableName']][$dataMapper[$type]['field']] = 'Sci&#8209;Fi';
                }

                $value = $d[$dataMapper[$type]['tableName']][$dataMapper[$type]['id']];
                $label = $type.'_'.$value;

                $checkboxes .=
                     '<span class="span6 '.$offset.'" >'.
                     '  <label class="checkbox inline nav_text">'.
                     '    <input type="checkbox" name="'.$type.'[]" value="'.$value.'">'.
                     $d[$dataMapper[$type]['tableName']][$dataMapper[$type]['field']].
                     "&nbsp;(<%= ".$label." != 0 && ".$label." != null ? ".$label." : '&#8209;' %>)".
                     '  </label>'.
                     '</span>';
                $count++;

            }

            return $checkboxes;

        }
        //end genreCheckboxFormatter

    }

?>