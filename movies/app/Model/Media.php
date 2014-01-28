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

        public $hasMany = array('Loaned' =>
                                   array('className' => 'MediaLoaned',
                                         'joinTable' => 'media_loaned',
                                         'foreignKey' => 'media_id',
                                         'conditions' => array('Loaned.date_returned IS NULL'),
                                         'order' => 'date_loaned DESC'));

    }