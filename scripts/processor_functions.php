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
 * Various functions used by the processor
 * 
 * @author Johnathan Pulos
 */
/**
 * Strips off the first directory seperator if it exists
 *
 * @param string $path the path to check
 * @return string
 * @access public
 * @author Johnathan Pulos
 */
function stripFirstDS($path) {
	if(substr($path, 0, 1) == "/") {
		return substr($path, 1);
	} else{
		return $path;
	}
}
/**
 * Replaces the current directory separator with the SERVER DIRECTORY_SEPARATOR
 *
 * @param string $path the path to check
 * @return string
 * @access public
 * @author Johnathan Pulos
 */
function replaceDSWithServerDS($path) {
	return str_replace('/', DIRECTORY_SEPARATOR, $path);
}
/**
 * Looks for the next process to complete.  If there are no clips to process, it will change service to master_recordings
 *
 * @param object $mysqli the mysql object
 * @return string
 * @access public
 * @author Johnathan Pulos
 */
function nextServiceToProcess($mysqli) {
	$query = getCorrectSql($mysqli, 'clips', null);
	$result = $mysqli->query($query);
	$clipData = $result->fetch_assoc();
	if(!empty($clipData)) {
		return 'CLIP';
	} else{
		return 'MASTER_RECORDING';
	}
}
/**
 * Get the correct query statement for the processing
 *
 * @param object $mysqli The mysql object
 * @param string $resourceType the table name to use
 * @param integer $resourceId the id of the resource
 * @return string
 * @access public
 * @author Johnathan Pulos
 */
function getCorrectSql($mysqli, $resourceType, $resourceId = null) {
	if($resourceId) {
		/**
		 * Asking for a specific resource
		 *
		 * @author Johnathan Pulos
		 */
		return "SELECT * from " . $resourceType . " WHERE id = " . $resourceId;
	}else {
		/**
		 * pick the next in line
		 *
		 * @author Johnathan Pulos
		 */
		return "SELECT * from " . $resourceType . " WHERE status = 'PENDING' OR status = 'ERROR' ORDER BY created ASC LIMIT 1";
	}
}
?>