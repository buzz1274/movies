<?php

    class MediaStorage extends AppModel {

        public $useTable = 'media_storage';

        public $primaryKey = 'media_storage_id';

        public $hasMany = 'media';

        public $order = "media_storage.storage ASC";

    }