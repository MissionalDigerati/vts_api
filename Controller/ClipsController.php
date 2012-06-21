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
 * Call the CakePHP callback beforeFilter
 *
 * @return void
 * @access public
 * @author Johnathan Pulos
 */
	public function beforeFilter() {
		$this->mustHaveValidToken();
		parent::beforeFilter();
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$clips = array();
		$ready_for_processing = 'YES';
		$this->Clip->recursive = 0;
		$current_clips = $this->Clip->find('all', array('conditions' => array('Clip.translation_request_id' => $this->currentTranslationRequestId)));
		/**
		 * Remove the Clip key
		 *
		 * @author Johnathan Pulos
		 */
		foreach ($current_clips as $clip) {
			array_push($clips, $clip['Clip']);
			if($clip['Clip']['status'] != 'COMPLETE') {
				$ready_for_processing = 'NO';
			}
		}
		$this->set('clips', $clips);
		$this->set('ready_for_processing', $ready_for_processing);
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
		$this->Clip->id = $id;
		if (!$this->Clip->exists()) {
			throw new NotFoundException(__('Invalid clip'));
		}
		if ($this->Clip->delete()) {
			$this->set('message', __('Your clip has been deleted.'));
			$this->set('status', __('success'));
		}else {
			throw new BadRequestException(__('Malformed request.'));
		}
	}
	
}
