<?php

    class Certificate extends AppModel {

        public $useTable = 'certificate';

        public $primaryKey = 'certificate_id';

        public $order = "Certificate.order ASC";

    }