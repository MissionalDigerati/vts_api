<?php
App::uses('AppController', 'Controller');
/**
 * Clips Controller
 *
 * @property Clip $Clip
 */
class ClipsController extends AppController {

/**
 * Define components you wish to use on all controllers
 *
 * @var array
 */
	public $components = array('RequestHandler');

/**
 * The current translation request id
 *
 * @var array
 */
	public $currentTranslationRequestId;
	
/**
 * Call the CakePHP callback beforeFilter
 *
 * @return void
 * @access public
 * @author Johnathan Pulos
 */
	public function beforeFilter() {
		if((!isset($this->request['data']['translation_request_token'])) || (empty($this->request['data']['translation_request_token']))) {
			throw new Exception(__('Your token is missing.'), 401);
		}
		$this->getTranslationRequest();
		parent::beforeFilter();
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Clip->recursive = 0;
		$this->set('clips', $this->paginate());
	}

/**
 * view method
 *
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->Clip->id = $id;
		if (!$this->Clip->exists()) {
			throw new NotFoundException(__('Invalid clip'));
		}
		$this->set('clip', $this->Clip->read(null, $id));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		$this->Clip->create();
		$this->request->data['translation_request_id'] = $this->currentTranslationRequestId;
		if ($this->Clip->save($this->request->data)) {
			$id = $this->Clip->getLastInsertID();
			$this->set('message', __('Your clip has been submitted.'));
			$this->set('status', __('success'));
			$this->set('clip', $this->Clip->read(null, $id));
		} else {
		}
	}

/**
 * edit method
 *
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->Clip->id = $id;
		if (!$this->Clip->exists()) {
			throw new NotFoundException(__('Invalid clip'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Clip->save($this->request->data)) {
				$this->flash(__('The clip has been saved.'), array('action' => 'index'));
			} else {
			}
		} else {
			$this->request->data = $this->Clip->read(null, $id);
		}
		$translationRequests = $this->Clip->TranslationRequest->find('list');
		$this->set(compact('translationRequests'));
	}

/**
 * delete method
 *
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Clip->id = $id;
		if (!$this->Clip->exists()) {
			throw new NotFoundException(__('Invalid clip'));
		}
		if ($this->Clip->delete()) {
			$this->flash(__('Clip deleted'), array('action' => 'index'));
		}
		$this->flash(__('Clip was not deleted'), array('action' => 'index'));
		$this->redirect(array('action' => 'index'));
	}
	
/**
 * Get the current translation request
 *
 * @return void
 * @access private
 * @author Johnathan Pulos
 */
	private function getTranslationRequest() {
		$this->loadModel('TranslationRequest');
		$currentTranslationRequest = $this->TranslationRequest->findByToken($this->request['data']['translation_request_token']);
		if ((empty($currentTranslationRequest)) || (!$this->TranslationRequest->exists($currentTranslationRequest['TranslationRequest']['id']))) {
			throw new NotFoundException(__('Invalid translation request.'));
		}
		if ($this->TranslationRequest->isExpired()) {
			throw new Exception(__('Your token has expired.'), 401);
		}
		$this->currentTranslationRequestId = $currentTranslationRequest['TranslationRequest']['id'];
	}
}
