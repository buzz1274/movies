<?php

    class Certificate extends AppModel {

        public $useTable = 'certificate';

        public $primaryKey = 'certificate_id';

        public $hasMany = 'Movie';

    }

?>