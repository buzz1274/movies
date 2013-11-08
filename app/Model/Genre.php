<?php

    class Genre extends AppModel {

        public $useTable = 'genre';

        public $primaryKey = 'genre_id';

        public $order = "Genre.genre ASC";

    }