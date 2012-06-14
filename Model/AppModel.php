<?php
/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {
	
	/**
	* Create a random hash using md5 and the current time/date and shortens it to the length param
	*
	* @param integer $length length of the hash
	* @param string $additional any addition content you want to include in the string
	* @return string
	* @author Johnathan Pulos
	*/
	function createToken($length, $additional = '') {
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
		return substr(md5(date('ymd') . $usec . $sec . $additional), 0, $length);
	}
}
