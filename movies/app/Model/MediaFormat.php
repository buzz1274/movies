<?php

    class MediaFormat extends AppModel {

        public $useTable = 'media_format';

        public $primaryKey = 'media_format_id';

        public $hasMany = 'media';

        public $order = "media_format.media_format ASC";

    }