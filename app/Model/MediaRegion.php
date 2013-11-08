<?php

    class MediaRegion extends AppModel {

        public $useTable = 'media_region';

        public $primaryKey = 'media_region_id';

        public $hasMany = 'media';

        public $order = "media_region.region ASC";

    }