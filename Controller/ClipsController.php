<?php
App::uses('AppController', 'Controller');
/**
 * Clips Controller
 *
 * @property Clip $Clip
 */
class ClipsController extends AppController {
	
/**
 * Call the CakePHP callback beforeFilter
 *
 * @return void
 * @access public
 * @author Johnathan Pulos
 */
	public function beforeFilter() {
		$this->mustHaveValidToken();
		$this->Auth->allow();
		parent::beforeFilter();
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$clips = array();
		$this->Clip->recursive = 0;
		$current_clips = $this->Clip->find('all', array('conditions' => array('Clip.translation_request_id' => $this->currentTranslationRequestId), 'order' => array('Clip.order_by' => 'ASC')));
		/**
		 * Remove the Clip key
		 *
		 * @author Johnathan Pulos
		 */
		foreach ($current_clips as $clip) {
			array_push($clips, $clip['Clip']);
		}
		$ready_for_processing = ($this->Clip->readyForMasterRecording($this->currentTranslationRequestId)) ? __('YES') : __('NO');
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
			throw new NotFoundException(__('The clip does not exist.'));
		}
		$this->set('clip', $this->Clip->read(null, $id));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if((!isset($this->request->form['audio_file'])) || (empty($this->request->form['audio_file']))) {
			throw new BadRequestException(__('You are missing the audio file.'));
		}
		$this->Clip->create();
		/**
		 * files are in the form key, not data key.  So move it over so the Uploader is triggered
		 *
		 * @author Johnathan Pulos
		 */
		$this->request->data['audio_file'] = $this->request->form['audio_file'];
		if ($this->Clip->save($this->request->data, true, $this->Clip->attrAccessible)) {
			$id = $this->Clip->getLastInsertID();
			/**
			 * Unbind the validation so we can add the translation request id
			 *
			 * @author Johnathan Pulos
			 */
			$this->Clip->unbindValidation('remove', array('order_by'));
			$this->Clip->set('translation_request_id', $this->currentTranslationRequestId);
			$this->Clip->save();
			$this->set('message', __('Your clip has been submitted.'));
			$this->set('status', __('success'));
			$this->set('clip', $this->Clip->read(null, $id));
		} else {
			$errors = $this->Clip->invalidFields();
			if(!empty($errors)) {
				throw new BadRequestException($this->ppErrors($errors));
			}else {
				throw new BadRequestException(__('Unable to add your clip.'));
			}
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
			throw new NotFoundException(__('The clip does not exist.'));
		}
		/**
		 * files are in the form key, not data key.  So move it over so the Uploader is triggered
		 *
		 * @author Johnathan Pulos
		 */
		$this->request->data['audio_file'] = $this->request->form['audio_file'];
		if ($this->Clip->save($this->request->data, true, $this->Clip->attrAccessible)) {
			$this->set('message', __('Your clip has been modified.'));
			$this->set('status', __('success'));
			$this->set('clip', $this->Clip->read(null, $id));
		} else {
			throw new BadRequestException(__('Unable to update your clip.'));
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
			throw new NotFoundException(__('The clip does not exist.'));
		}
		if ($this->Clip->delete()) {
			$this->set('message', __('Your clip has been deleted.'));
			$this->set('status', __('success'));
		}else {
			throw new BadRequestException(__('There was a problem with your request.'));
		}
	}
	
}
