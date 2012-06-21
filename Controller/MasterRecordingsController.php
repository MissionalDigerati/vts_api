<?php
App::uses('AppController', 'Controller');
/**
 * MasterRecordings Controller
 *
 * @property MasterRecording $MasterRecording
 * @property RequestHandlerComponent $RequestHandler
 */
class MasterRecordingsController extends AppController {

/**
 * Call the CakePHP callback beforeFilter
 *
 * @return void
 * @access public
 * @author Johnathan Pulos
 */
	public function beforeFilter() {
		$this->mustHaveValidToken();
		if($this->currentTranslationRequestId != '') {
			$this->loadModel('Clip');
			if($this->Clip->readyForMasterRecording($this->currentTranslationRequestId) === false) {
				throw new Exception(__('To access master recordings,  the translation request must have at least 1 clip, and all clips need a status of complete.'), 401);
			}
		}else{
			throw new Exception(__('Your translation request token is missing.'), 401);
		}
		parent::beforeFilter();
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {
		// $this->MasterRecording->recursive = 0;
		// $this->set('masterRecordings', $this->paginate());
	}

/**
 * view method
 *
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->MasterRecording->id = $id;
		if (!$this->MasterRecording->exists()) {
			throw new NotFoundException(__('The master recording does not exist.'));
		}
		$this->set('master_recording', $this->MasterRecording->read(null, $id));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		$this->MasterRecording->create();
		if ($this->MasterRecording->save($this->request->data)) {
			$id = $this->MasterRecording->getLastInsertID();
			$this->set('message', __('Your master recording request has been submitted.'));
			$this->set('status', __('success'));
			$this->set('master_recording', $this->MasterRecording->read(null, $id));
		}else {
			throw new BadRequestException($this->MasterRecording->getValidationErrorResponse());
		}
	}

/**
 * edit method
 *
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->MasterRecording->id = $id;
		if (!$this->MasterRecording->exists()) {
			throw new NotFoundException(__('Invalid master recording'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->MasterRecording->save($this->request->data)) {
				$this->flash(__('The master recording has been saved.'), array('action' => 'index'));
			} else {
			}
		} else {
			$this->request->data = $this->MasterRecording->read(null, $id);
		}
		$translationRequests = $this->MasterRecording->TranslationRequest->find('list');
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
		$this->MasterRecording->id = $id;
		if (!$this->MasterRecording->exists()) {
			throw new NotFoundException(__('Invalid master recording'));
		}
		if ($this->MasterRecording->delete()) {
			$this->flash(__('Master recording deleted'), array('action' => 'index'));
		}
		$this->flash(__('Master recording was not deleted'), array('action' => 'index'));
		$this->redirect(array('action' => 'index'));
	}
}
