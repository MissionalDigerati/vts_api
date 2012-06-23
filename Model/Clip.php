<?php
App::uses('AppModel', 'Model');
/**
 * Clip Model
 *
 * @property TranslationRequest $TranslationRequest
 */
class Clip extends AppModel {
	
	/**
	 * Accessible attributes for mass assignment
	 *
	 * @var array
	 */
	public $attrAccessible = array('video_file_location', 'audio_file_location', 'audio_file');

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
	 * Call the CakePHP afterSave callback
	 *
	 * @return boolean
	 * @access public
	 * @author Johnathan Pulos
	 */
		public function afterSave() {
			$this->query('UPDATE clips SET status = "PENDING" WHERE id = '.$this->id);
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
				exec("cd " . $appDirectory . " && php trigger_bg_process.php CLIP ".$this->id." > tmp" . DS . "logs" . DS . "processor.log 2>&1 & echo $!");
			}
			return true;
		}
		
	/**
	 * Iterates over all clips for a specific translation request, and verifies it is ready for a master recording
	 *
	 * @param string $translation_request_id Clip.translation_request_id
	 * @return boolean
	 * @access public
	 * @author Johnathan Pulos
	 */
	public function readyForMasterRecording($translation_request_id) {
		$ready = true;
		$clips = $this->find('all', array('conditions' => array('Clip.translation_request_id' => $translation_request_id)));
		if(count($clips) == 0) {
			$ready = false;
		}else {
			foreach ($clips as $clip) {
				if($clip['Clip']['status'] != 'COMPLETE') {
					$ready = false;
				}
			}
		}
		return $ready;
	}
}
