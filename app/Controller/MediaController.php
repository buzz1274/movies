<?php

class MediaController extends AppController {

    public $uses = array('Movie', 'Media', 'User', 'MediaLoaned');

    /**
     * marks a DVD as loaned
     * @author David
     */
    public function loaned() {

        if(!$this->Auth->user('user_id') || !$this->Auth->user('admin')) {
            return new CakeResponse(array('status' => 401));
        }

        $loaned = $this->request->input('json_decode', true);

        if(!$loaned || !isset($loaned['media_id']) ||
           (int)$loaned['media_id'] < 1 ||
            ((!isset($loaned['loaned_to']) ||
              empty($loaned['loaned_to'])) &&
             (!isset($loaned['media_loaned_id']) ||
              (int)$loaned['media_loaned_id'] < 1))) {

            if(!isset($loaned['media_loaned_id']) ||
               (int)$loaned['media_loaned_id'] < 1) {
                return new CakeResponse(array('status' => 400,
                                              'body' => json_encode(array('message' =>
                                                             'Please enter loaned to'))));

            } else {
                return new CakeResponse(array('status' => 400));
            }
        }

        if(!isset($loaned['media_loaned_id']) || empty($loaned['media_loaned_id'])) {

            $alreadyLoaned = $this->MediaLoaned->find('first',
                                        array('conditions' => array('media_id' => $loaned['media_id'],
                                                                    'MediaLoaned.date_returned IS NULL')));

            if($alreadyLoaned) {
                return new CakeResponse(array('status' => 409,
                                              'body' => json_encode(array('message' =>
                                                                      'Movie already on loan'))));
            }

        }

        if(isset($loaned['media_loaned_id'])) {
            $media_loaned = $this->MediaLoaned->find('first',
                array('recursive' => 1,
                      'conditions' => array('id' => $loaned['media_loaned_id'])));

            if(!$media_loaned ||
               $media_loaned['MediaLoaned']['date_returned'] !== null) {
                return new CakeResponse(array('status' => 400));
            }

        }

        if(isset($loaned['media_loaned_id'])) {
            $data = array('id' => $loaned['media_loaned_id'],
                          'date_returned' => date('Y-m-d'));
        } else {
            $data = array('media_id' => $loaned['media_id'],
                          'loaned_to' => $loaned['loaned_to'],
                          'date_loaned' => date('Y-m-d'));
        }

        if(!($loaned = $this->MediaLoaned->save($data))) {
            return new CakeResponse(array('status' => 500));
        } else {
            if(isset($loaned['MediaLoaned']['date_loaned'])) {
                $loaned['MediaLoaned']['date_loaned'] =
                    date('jS F Y', strtotime($loaned['MediaLoaned']['date_loaned']));
            }

            return new CakeResponse(array('status' => 200,
                                          'body' => json_encode($loaned)));
        }

    }
    //end loaned

}
