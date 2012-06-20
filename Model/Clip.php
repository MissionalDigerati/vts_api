<?php
App::uses('AppModel', 'Model');
/**
 * Clip Model
 *
 * @property TranslationRequest $TranslationRequest
 */
class Clip extends AppModel {

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
 * Setup the CakePHP behaviors for this model.  It uses the Uploader Plugin
 * 
 * @link http://milesj.me/code/cakephp/uploader
 * @var string
 */	
	public $actsAs = array( 
		'Uploader.FileValidation' => array(
				'audio_file' => array(
						'extension' => array(
								'value' => array('mp3'),
								'error' => 'Only mp3 files are allowed!'
							)
					)
		),
		'Uploader.Attachment' => array(
				'audio_file' => array(
						'name'				=> 'formatFileName',// Name of the function to use to format filenames
						'uploadDir'			=> '/files/clips/',			// See UploaderComponent::$uploadDir
						'dbColumn'			=> 'audio_file_location',	// The database column name to save the path to
						'importFrom'		=> '',			// Path or URL to import file
						'defaultPath'		=> '',			// Default file path if no upload present
						'maxNameLength'	=> 30,			// Max file name length
						'overwrite'			=> true,		// Overwrite file with same name if it exists
						'stopSave'			=> true,		// Stop the model save() if upload fails
						'allowEmpty'		=> false,		// Allow an empty file upload to continue
						'transforms'		=> array(),		// What transformations to do on images: scale, resize, etc
						's3'						=> array(),		// Array of Amazon S3 settings
						'metaColumns'		=> array(		// Mapping of meta data to database fields
							'ext' 				=> '',
							'type' 				=> '',
							'size' 				=> '',
							'group' 			=> '',
							'width' 			=> '',
							'height' 			=> '',
							'filesize' 		=> ''
						)
					)
			)
	);
	
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
