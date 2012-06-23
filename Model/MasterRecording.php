<?php
App::uses('AppModel', 'Model');
/**
 * MasterRecording Model
 *
 * @property TranslationRequest $TranslationRequest
 */
class MasterRecording extends AppModel {
	/**
	 * Display field
	 *
	 * @var string
	 */
	public $displayField = 'title';
	/**
	 * Accessible attributes for mass assignment
	 *
	 * @var array
	 */
	public $attrAccessible = array('title', 'language', 'final_filename');
	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
		'title' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Please supply a valid title for your master recording.'
			),
		),
		'language' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Please supply a valid language for your master recording.'
			),
		),
		'final_filename' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Please supply a valid final filename for your master recording.'
			),
		),
	);

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'TranslationRequest' => array(
			'className' => 'TranslationRequest',
			'foreignKey' => 'translation_request_id'
		)
	);
	
	/**
	 * Generates a valid response if there are errors on the validation of model
	 *
	 * @return string
	 * @access public
	 * @author Johnathan Pulos
	 */
	public function getValidationErrorResponse() {
		if(empty($this->validationErrors)) {
			return __('Unable to save your master recording.');
		}else {
			$response = '';
			foreach ($this->validationErrors as $attr => $errorArray) {
				foreach ($errorArray as $error) {
					$response = $response . $error . ' ';
				}
			}
			return trim($response);
		}
	}
	
	/**
	 * Call the CakePHP afterSave callback
	 *
	 * @return boolean
	 * @access public
	 * @author Johnathan Pulos
	 */
		public function afterSave() {
			$this->query('UPDATE master_recordings SET status = "PENDING" WHERE id = '.$this->id);
			$useCron = Configure::read('VTS.useCron');
			if($useCron == false) {
				/**
				 * Set the location of the app directory relative to this file
				 *
				 * @var string
				 * @author Johnathan Pulos
				 */
				$appDirectory = dirname(dirname(__FILE__)) . DS;
				/**
				 * Trigger the background process for merging audio and video for the clip.  Pipe the response to tmp/logs/processor.log
				 *
				 * @author Johnathan Pulos
				 */
				exec("cd " . $appDirectory . " && php trigger_bg_process.php MASTER_RECORDING ".$this->id." > tmp" . DS . "logs" . DS . "processor.log 2>&1 & echo $!");
			}
			return true;
		}
}
