<?php

    class Certificate extends AppModel {

        public $useTable = 'certificate';

        public $primaryKey = 'certificate_id';

        public $hasMany = 'Movie';

        public $order = "Certificate.order ASC";

    }

?>