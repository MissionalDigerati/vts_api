<?php
/**
 * This file is part of OBS Video Translator API.
 * 
 * OBS Video Translator API is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * OBS Video Translator API is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see 
 * <http://www.gnu.org/licenses/>.
 *
 * @author Johnathan Pulos <johnathan@missionaldigerati.org>
 * @copyright Copyright 2012 Missional Digerati
 * 
 */
/**
 * Set the Root Url
 * 
 * @var string
 * @author Johnathan Pulos
 */
$rootUrl = 'http://api.domain.com';
/**
 * Change absolute path to match where your this example folder is
 *
 * @author Johnathan Pulos
 */
$examplePath = '/Users/Technoguru/Sites/php/open_bible_stories/www/api_video_translator/example/';
/**
 * A class for handling the cURL requests
 *
 * @package default
 * @author Johnathan Pulos
 */
class curlUtility {
	
	/**
	 * Make a cURL Request
	 *
	 * @param string $url the url to request
	 * @param string $method the method to use POST or GET
	 * @param array $fields an array of fields to send
	 * @return string
	 * @access public
	 * @author Johnathan Pulos
	 */
	public function makeRequest($url, $method, $fields = array()) {
		$method = strtoupper($method);
		/**
		 * open connection
		 *
		 * @author Johnathan Pulos
		 */
		$ch = curl_init();
		if($method == 'GET') {
			$fieldsString = $this->urlify($fields);
			$url = $url . "?" . $fieldsString;
		}else {
			$fieldsString = $fields;
		}
		/**
		 * Setup cURL
		 *
		 * @author Johnathan Pulos
		 */
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		if($method == 'POST') {
			curl_setopt($ch, CURLOPT_POST,1);
			curl_setopt($ch,CURLOPT_POSTFIELDS,$fieldsString);
		}
		/**
		 * execute request
		 *
		 * @author Johnathan Pulos
		 */
		$result = curl_exec($ch) or die(curl_error($ch));
		/**
		 * close connection
		 *
		 * @author Johnathan Pulos
		 */
		curl_close($ch);
		return $result;
	}
	
	/**
	 * Takes an array of fields and makes a string from them for passing in cURL
	 *
	 * @param array $fields the fields to urlify
	 * @return string
	 * @access public
	 * @author Johnathan Pulos
	 */
	public function urlify($fields) {
		$fieldsString = '';
		foreach($fields as $key=>$value) { $fieldsString .= $key.'='.$value.'&'; }
		return rtrim($fieldsString,'&');
	}
}
/**
 * A class for interacting with the VTS API
 *
 * @package default
 * @author Johnathan Pulos
 */
class VTS {
	/**
	 * Object holding the curlUtility Intance
	 *
	 * @var object
	 * @access private
	 */
	private $curlUtility;
	/**
	 * The root url of the api
	 *
	 * @var string
	 * @access public
	 */
	public $rootUrl;
	/**
	 * The Number of Seconds to Sleep Between Polling
	 *
	 * @var integer
	 * @access public
	 */
	public $pollSleep = 20;
	/**
	 * The translation request token
	 *
	 * @var string
	 * @access private
	 */
	private $token;
	/**
	 * An error message thrown by the api
	 *
	 * @var string
	 * @access private
	 */
	private $errorMessage = '';
	
	/**
	 * Construct the class
	 *
	 * @param string $rootUrl the root url for the API
	 * @access public
	 * @author Johnathan Pulos
	 */
	public function __construct($rootUrl) {
		/**
		 * Create instance of curlUtility
		 * 
		 * @var object
		 * @author Johnathan Pulos
		 */
		$this->curlUtility = new curlUtility();
		$this->rootUrl = $rootUrl;
	}
	
	/**
	 * get the translation request token
	 *
	 * @return string
	 * @access public
	 * @author Johnathan Pulos
	 */
	public function getToken() {
		$response = $this->makeRequest('translation_requests.json', 'POST', array());
		$translation_request = $response['vts']['translation_requests'][0];
		$this->token = $translation_request['token'];
		return $this->token;
	}
	
	/**
	 * Create a clip for processing.  It is recommended to add a sleep() before posting another.  cURL needs time to upload the previous file
	 *
	 * @param string $audioFilePath the path to the local file to upload
	 * @param string $videoFilePath the path of the master file on the server starting from webroot
	 * @return array
	 * @access public
	 * @author Johnathan Pulos
	 */
	public function createClip($audioFilePath, $videoFilePath) {
		$fields = array(	'translation_request_token' 	=> $this->token,
								'audio_file' 						=> '@'.$audioFilePath.";type=audio/mp3",
								'video_file_location' 			=> $videoFilePath
							);
		$response = $this->makeRequest('clips.json', 'POST', $fields);
		return $response;
	}
	/**
	 * Create the master recording after all clips have been processed
	 *
	 * @param string $title the title of the master recording
	 * @param string $lang the language of the master recording
	 * @param string $filename the final file name
	 * @return array
	 * @access public
	 * @author Johnathan Pulos
	 */
	public function createMasterRecording($title, $lang, $filename) {
		$fields = array(	'translation_request_token' 	=> $this->token,
								'title' 								=> $title,
								'language' 							=> $lang,
								'final_filename' 					=> $filename
							);
		if($this->pollClipProcessorStatus() === true) {
			/**
			 * Create the Master Recording
			 *
			 * @author Johnathan Pulos
			 */
			echo "We are ready for creating the master recording. \r\n";
			$response = $this->makeRequest('master_recordings.json', 'POST', $fields);
			$masterRecording = $response['vts']['master_recordings'][0];
			if($this->pollMasterRecordingProcessorStatus($masterRecording['id']) === true) {
				return $masterRecording;
			}
		}
	}
	
	/**
	 * Make a request using the curlUtility, and return an array of the response if no errors present
	 *
	 * @param string $path the path to request
	 * @param string $method the HTTP method to use
	 * @param string $fields an array of fields to pass
	 * @return array
	 * @access private
	 * @author Johnathan Pulos
	 */
	private function makeRequest($path, $method, $fields = array()) {
		$response = $this->curlUtility->makeRequest($this->rootUrl . $path, $method, $fields);
		echo $response . "\r\n";
		$decoded_response = json_decode($response, true);
		if($this->hasError($decoded_response)){
			throw new Exception("\r\nUnable to complete request:\r\n" . $this->errorMessage);
			exit;
		}else {
			return $decoded_response;
		}
	}
	
	/**
	 * Poll the server to see when clip processing is complete.  This uses a traversal concept.
	 *
	 * @return boolean
	 * @access private
	 * @author Johnathan Pulos
	 */
	private function pollClipProcessorStatus() {
		$response = $this->makeRequest('clips.json', 'GET', array('translation_request_token' 	=> $this->token));
		$ready_for_processing = $response['vts']['ready_for_processing'];
		if($ready_for_processing == 'YES') {
			return true;
		}else {
			sleep($this->pollSleep);
			return $this->pollClipProcessorStatus();
		}
	}
	
	/**
	 * Poll the master recording process, and check when it is complete
	 *
	 * @param integer $id MasterRecording.id
	 * @return boolean
	 * @access private
	 * @author Johnathan Pulos
	 */
	private function pollMasterRecordingProcessorStatus($id) {
		$response = $this->makeRequest('master_recordings/' . $id . '.json', 'GET', array('translation_request_token' 	=> $this->token));
		$masterRecordingStatus = $response['vts']['master_recordings'][0]['status'];
		if($masterRecordingStatus == 'COMPLETE') {
			return true;
		}else {
			sleep($this->pollSleep);
			return $this->pollMasterRecordingProcessorStatus($id);
		}
	}
	
	/**
	 * Check if there is an error.  If so, then display the error and kill the code
	 *
	 * @param array $resource The response resource
	 * @return boolean
	 * @access private
	 * @author Johnathan Pulos
	 */
	private function hasError($resource) {
		if($resource['vts']['status'] == 'error') {
			$this->errorMessage = $resource['vts']['message'] . "\r\n";
			if(isset($resource['vts']['details'])) {
				$this->errorMessage .= $resource['vts']['details'] . "\r\n";
			}
			return true;
		}else {
			return false;
		}
	}
}
$vts = new VTS($rootUrl);
/**
 * Get the translation request token
 *
 * @author Johnathan Pulos
 */
$token = $vts->getToken();
echo "We have the translation request token. \r\n";
echo "TOKEN: " . $token . "\r\n";
/**
 * Let us add the clips now
 *
 * @author Johnathan Pulos
 */
for ($i=1; $i <= 3; $i++) {
	$audioFilePath = $examplePath . "files/23_" . $i . ".mp3";
	/**
	 * This path starts from the webroot
	 *
	 * @author Johnathan Pulos
	 */
	$videoFilePath = "/files/master_files/example/the_compassionate_father_" . $i . ".mp4";
	$clipResponse = $vts->createClip($audioFilePath, $videoFilePath);
	$clip = $clipResponse['vts']['clips'][0];
	echo "Uploaded " . basename($audioFilePath) . " to server as " . basename($clip['audio_file_location']) . ". \r\n";
	sleep(20);
}
/**
 * Now lets create the master recording
 *
 * @author Johnathan Pulos
 */
echo "Now polling the server for clips to be processed,  then we will create the master recording. (This may take some time...) \r\n";
$masterRecording = $vts->createMasterRecording('The Compassionate Father', 'Portugese', 'pt_compassionate_father');
echo "You will find the final file at: " . $rootUrl . "files/master_recordings/pt_compassionate_father/pt_compassionate_father.mp4";
?>