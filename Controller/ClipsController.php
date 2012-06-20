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
		if((!isset($this->request->form['audio_file'])) || (empty($this->request->form['audio_file']))) {
			throw new BadRequestException(__('Missing attribute audio_file.'));
		}
		/**
		 * files are in the form key, not data key.  So move it over so the Uploader is triggered
		 *
		 * @author Johnathan Pulos
		 */
		$this->request->data['audio_file'] = $this->request->form['audio_file'];
		$this->request->data['translation_request_id'] = $this->currentTranslationRequestId;
		if ($this->Clip->save($this->request->data)) {
			$id = $this->Clip->getLastInsertID();
			$this->set('message', __('Your clip has been submitted.'));
			$this->set('status', __('success'));
			$this->set('clip', $this->Clip->read(null, $id));
		} else {
			throw new BadRequestException(__('Missing attribute audio_file.'));
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
		/**
		 * files are in the form key, not data key.  So move it over so the Uploader is triggered
		 *
		 * @author Johnathan Pulos
		 */
		$this->request->data['audio_file'] = $this->request->form['audio_file'];
		if ($this->Clip->save($this->request->data)) {
			$this->set('message', __('Your clip has been modified.'));
			$this->set('status', __('success'));
			$this->set('clip', $this->Clip->read(null, $id));
		} else {
			throw new BadRequestException(__('Missing attribute audio_file.'));
		}
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
		$currentTranslationRequest = $this->TranslationRequest->findByToken($this->cleanedToken($this->request['data']['translation_request_token']));
		if (empty($currentTranslationRequest)) {
			throw new NotFoundException(__('Invalid translation request.'));
		}
		/**
		 * I have to force Cake to use this Translation Request, if not then the isExpired function gets the wrong Translation Request
		 *
		 * @author Johnathan Pulos
		 */
		$this->TranslationRequest->id = $currentTranslationRequest['TranslationRequest']['id'];
		if (!$this->TranslationRequest->exists()) {
			throw new NotFoundException(__('Invalid translation request.'));
		}
		if ($this->TranslationRequest->isExpired()) {
			throw new Exception(__('Your token has expired.'), 401);
		}
		$this->currentTranslationRequestId = $currentTranslationRequest['TranslationRequest']['id'];
	}
}
