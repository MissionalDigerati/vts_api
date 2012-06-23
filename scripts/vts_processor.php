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
 * This script triggers the processing of clips and master_recordings.  It is triggered by a CRON or a Background Process.  It should be triggered
 * in the app directory containing this VTS API Service.
 * 
 *
 * @author Johnathan Pulos
 */
/**
 * The service being processed
 * CLIP - If an resource_id is provided,  it will process that clip.  If not,  it will take the next clip that needs processing. (default)
 * MASTER_RECORDING - If an resource_id is provided,  it will process that master recording.  If not,  it will take the next master recording that needs processing.
 *
 * @var string
 * @author Johnathan Pulos
 */
$service = (isset($argv[1])) ? strtoupper($argv[1]): 'CLIP';
if(!in_array($service, array('CLIP', 'MASTER_RECORDING'))) {
	echo "We do not serve: " . $service . "\r\n";
	echo 'FAIL';
	exit;
}
echo "Running service: " . $service . "\r\n";

/**
 * The resources primary key
 *
 * @var string
 * @author Johnathan Pulos
 */
$resourceId = (isset($argv[2])) ? $argv[2]: null;
if($resourceId) {
	echo "For the Resource: " . $resourceId . "\r\n";
}

/**
 * Set the location of the app directory relative to this file
 *
 * @var string
 * @author Johnathan Pulos
 */
$appDirectory = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR;

/**
 * Set the location of the webroot directory
 * 
 * @var string
 * @author Johnathan Pulos
 */
$webrootDirectory = $appDirectory . 'webroot' . DIRECTORY_SEPARATOR;

/**
 * Set the location of the render engine files using the app directory
 * 
 * @var string
 * @author Johnathan Pulos
 */
$renderEngineDirectory = $appDirectory . 'VENDOR' . DIRECTORY_SEPARATOR . 'render_engine' . DIRECTORY_SEPARATOR;

/**
 * get both seconds and microseconds parts of the time
 *
 * @author Johnathan Pulos
 */
list($usec, $sec) = explode(' ', microtime());

/**
 * remove the period in $usec
 *
 * @author Johnathan Pulos
 */
$usec = preg_replace('[\.]','', $usec);

/**
 * A random filename using the time, and md5
 * 
 * @var string
 * @author Johnathan Pulos
 */
$randomFilename = substr(md5(date('ymd') . $usec . $sec), 0, 20);

/**
 * Require the functions
 *
 * @author Johnathan Pulos
 */
require_once  $appDirectory . 'scripts' . DIRECTORY_SEPARATOR . 'processor_functions.php';

/**
 * Require the render engine lib files
 *
 * @author Johnathan Pulos
 */
require_once  $renderEngineDirectory . 'lib' . DIRECTORY_SEPARATOR . 'rendering.php';

/**
 * Get the database settings from the CakePHP files
 *
 * @author Johnathan Pulos
 */
require_once  $appDirectory . 'Config' . DIRECTORY_SEPARATOR . 'database.php';
$dbSettings = new DATABASE_CONFIG();

/**
 * Setup the database host
 *
 * @var string
 * @author Johnathan Pulos
 */
$socket = (isset($dbSettings->default['unix_socket'])) ? $dbSettings->default['unix_socket'] : null;

/**
 * Connect to the database
 *
 * @author Johnathan Pulos
 */
$mysqli = new mysqli($dbSettings->default['host'], $dbSettings->default['login'], $dbSettings->default['password'], $dbSettings->default['database'], null, $socket);
if ($mysqli->connect_errno) {
   echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	echo "FAIL";
	exit;
}
echo "Using the database: " . $dbSettings->default['database'] . "\r\n";

/**
 * Lets get to work
 *
 * @author Johnathan Pulos
 */
echo "OK.  Let's get to work: \r\n";
switch ($service) {
	case 'CLIP':
		if($resourceId) {
			/**
			 * Asking for a specific resource
			 *
			 * @author Johnathan Pulos
			 */
			$query = "SELECT * from clips WHERE id = " . $resourceId;
		}else {
			/**
			 * pick the next in line
			 *
			 * @author Johnathan Pulos
			 */
			$query = "SELECT * from clips WHERE status = 'PENDING' OR status = 'ERROR' ORDER BY created ASC LIMIT 1";
		}
		$result = $mysqli->query($query);
		$clipData = $result->fetch_assoc();
		if(empty($clipData)) {
			echo "No resources available.\r\n";
			echo "FAIL";
			exit;
		}
		if(!$resourceId) {
			$resourceId = $clipData['id'];
		}
		if(!in_array(strtoupper($clipData['status']), array('PENDING', 'ERROR'))) {
			echo "The resource has already been processed.\r\n";
			echo "FAIL";
			exit;
		}
		$randomFilename = $resourceId . "_" . $randomFilename . '.mp4';
		$mysqli->query("UPDATE clips SET status = 'PROCESSING' WHERE id = " . $resourceId);
		
		/**
		 * Determine if the path starts with a seperator, and remove it
		 *
		 * @author Johnathan Pulos
		 */
		$audioFilePath =  $webrootDirectory . replaceDSWithServerDS(stripFirstDS($clipData['audio_file_location']));
		echo "Audio File: " . $audioFilePath . "\r\n";
		$masterFilePath = $webrootDirectory . replaceDSWithServerDS(stripFirstDS($clipData['video_file_location']));
		echo "Master File: " . $masterFilePath . "\r\n";
		$completedDirectory = $webrootDirectory . replaceDSWithServerDS('files/clips/completed/');
		echo "Final File: ". $completedDirectory . $randomFilename."\r\n";
		$clip = new ClipBuilder($masterFilePath, $audioFilePath, $randomFilename);
		$clip->final_file_directory = $completedDirectory;
		$finalFile = $clip->process();
		if(file_exists($finalFile)) {
			$mysqli->query("UPDATE clips SET status = 'COMPLETE', completed_file_location = '/files/clips/completed/" . $randomFilename . "', completed = NOW() WHERE id = " . $resourceId);
			echo "The resource has been processed.\r\n";
			echo "PASS";
			exit;
		}else {
			$mysqli->query("UPDATE clips SET status = 'ERROR' WHERE id = " . $resourceId);
			echo "The resource had an error.\r\n";
			echo "FAIL";
			exit;
		}
	break;
	case 'MASTER_RECORDING':
		if($resourceId) {
			/**
			 * Asking for a specific resource
			 *
			 * @author Johnathan Pulos
			 */
			$query = "SELECT * from master_recordings WHERE id = " . $resourceId;
		}else {
			/**
			 * pick the next in line
			 *
			 * @author Johnathan Pulos
			 */
			$query = "SELECT * from master_recordings WHERE status = 'PENDING' OR status = 'ERROR' ORDER BY created ASC LIMIT 1";
		}
		$result = $mysqli->query($query);
		$masterRecordingData = $result->fetch_assoc();
		if(empty($masterRecordingData)) {
			echo "No resources available.\r\n";
			echo "FAIL";
			exit;
		}
		if(!$resourceId) {
			$resourceId = $masterRecordingData['id'];
		}
		if(!in_array(strtoupper($masterRecordingData['status']), array('PENDING', 'ERROR'))) {
			echo "The resource has already been processed.\r\n";
			echo "FAIL";
			exit;
		}
		$mysqli->query("UPDATE master_recordings SET status = 'PROCESSING' WHERE id = " . $resourceId);
		$completedDirectory = $webrootDirectory . replaceDSWithServerDS('files/master_recordings/' . $masterRecordingData['final_filename'] . '/');
		/**
		 * Make a directory using the final_filename,  all videos will be stored here
		 *
		 * @author Johnathan Pulos
		 */
		if(!file_exists($completedDirectory)) {
			mkdir($completedDirectory, 0777);
		}
		echo "All files stored in: " . $completedDirectory . "\r\n";
		$videoBuilder = new VideoBuilder();
		$videoBuilder->final_file_directory = $completedDirectory;
		/**
		 * Iterate over all clips from earliest to latest
		 *
		 * @author Johnathan Pulos
		 */
		$clipQuery = $mysqli->query("SELECT completed_file_location FROM clips WHERE translation_request_id = " . $masterRecordingData['translation_request_id'] . " ORDER BY created ASC");
		while ($clip = $clipQuery->fetch_assoc()) {
			$clipCompletedPath = $webrootDirectory . replaceDSWithServerDS(stripFirstDS($clip['completed_file_location']));
			$videoBuilder->add_clip($clipCompletedPath);
			echo "Added clip: " . $clipCompletedPath . "\r\n";
		}
		/**
		 * Process the video by combining the two clips
		 *
		 * @author Johnathan Pulos
		 */
		$finalFile = $videoBuilder->process($masterRecordingData['final_filename']);
		if(file_exists($completedDirectory . $masterRecordingData['final_filename'] . '.mp4')) {
			$mysqli->query("UPDATE master_recordings SET status = 'COMPLETE', completed = NOW() WHERE id = " . $resourceId);
			echo "The resource has been processed.\r\n";
			echo "PASS";
			exit;
		}else {
			$mysqli->query("UPDATE master_recordings SET status = 'ERROR' WHERE id = " . $resourceId);
			echo "The resource had an error.\r\n";
			echo "FAIL";
			exit;
		}
	break;
}
?>