<?php

class MediaController extends AppController {

    public $uses = array('Movie', 'Media', 'User', 'MediaLoaned');

    /**
     * loaned media
     * @author David
     */
    public function loaned() {

        $this->request->onlyAllow(array('get', 'post', 'put'));

        if(!$this->Auth->user('user_id') || !$this->Auth->user('admin')) {
            return new CakeResponse(array('status' => 401));
        }

        if($this->request->is('get')) {
            return $this->_onloan(isset($this->request->params['summary']));
        } elseif($this->request->is('post') || $this->request->is('put')) {
            return $this->_loan();
        }

    }
    //end loaned

    /**
     * toggles the loaned status of the supplied media
     * @author David
     */
    private function _loan() {

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
    //end _loan

    /**
     * returns a list of loaned media
     * @author David
     */
    private function _onLoan($summary) {

        if(isset($this->request->query['p']) &&
           (int)$this->request->query['p'] >= 1) {
            $page = $this->request->query['p'];
        } else {
            $page = 1;
        }

        $resultsPerPage = 25;
        $conditions = array('fields' => array('MediaLoaned.id', 'MediaLoaned.loaned_to',
                                              'MediaLoaned.date_loaned', 'Movie.title',
                                              'Movie.movie_id'),
                            'conditions' => array('MediaLoaned.date_returned IS NULL'),
                            'joins' => array(array('table' => 'movie',
                                                   'alias' => 'Movie',
                                                   'conditions' => 'Movie.media_id = MediaLoaned.media_id')),
                            'order' => array('MediaLoaned.date_loaned DESC'),
                            'limit' => $resultsPerPage,
                            'page' => $page);

        $searchType = $summary ? 'count' : 'all';

        if(!($data = $this->MediaLoaned->find($searchType, $conditions))) {
            $statusCode = 204;
        } else {
            $statusCode = 200;

            if($searchType == 'count') {
                $data = array('totalPages' => ceil($data / $resultsPerPage),
                              'page' => $page);
            }
        }

        return new CakeResponse(array('status' => $statusCode,
                                      'body' => json_encode($data)));

    }
    //end onLoan

}
