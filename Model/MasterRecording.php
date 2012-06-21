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
	 * Call the CakePHP beforeSave callback
	 *
	 * @return boolean
	 * @access public
	 * @author Johnathan Pulos
	 */
		public function beforeSave() {
			if (!$this->id && !isset($this->data[$this->alias][$this->primaryKey])) {
				/**
				 * Put in any functionality for the add method
				 *
				 * @author Johnathan Pulos
				 */
			} else{
				/**
				 * Put in any functionality for the edit method
				 *
				 * @author Johnathan Pulos
				 */
			}
			$this->data[$this->alias]['status'] = 'PENDING';
			return true;
		}
}
