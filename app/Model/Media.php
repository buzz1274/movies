<?php

    class Media extends AppModel {

        public $useTable = 'media';

        public $primaryKey = 'media_id';

        public $order = "Media.media_id ASC";

        public $belongsTo = array('Storage' =>
                                       array('className' => 'MediaStorage',
                                             'foreignKey' => 'media_storage_id'),
                                  'Region' =>
                                       array('className' => 'MediaRegion',
                                             'foreignKey' => 'media_region_id'),
                                  'MediaFormat');


    }

?>