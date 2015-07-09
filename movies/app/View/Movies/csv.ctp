<?php

    set_time_limit(0);
    $csv = '"ID","Title","Certificate","Rating","HD","Runtime",'.
           '"Release Year","Genres"'."\n";

    foreach($data as $movie) {
        $genres = explode(',', preg_replace('/\{|\}/', '',
                                            $movie['Movie']['movie_genres']));
        sort($genres, SORT_REGULAR);
        $csv .= '"'.$movie['Movie']['movie_id'].'",'.
                '"'.$movie['Movie']['title'].'",'.
                '"'.$movie['Movie']['certificate'].'",'.
                '"'.$movie['Movie']['imdb_rating'].'",'.
                '"'.($movie['Movie']['hd'] == 1 ? 'Yes' : 'No').'",'.
                '"'.$movie['Movie']['runtime'].'",'.
                '"'.$movie['Movie']['release_year'].'",'.
                '"'.implode(',', $genres).'"'.
                "\n";
    }

    echo($csv);
    die();

 ?>