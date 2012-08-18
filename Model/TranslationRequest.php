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

App::uses('AppModel', 'Model');
/**
 * TranslationRequest Model
 *
 */
class TranslationRequest extends AppModel {

	/**
	 * Accessible attributes for mass assignment
	 *
	 * @var array
	 */
	public $attrAccessible = array();

	/**
	 * hasMany associations
	 *
	 * @var array
	 */
	public $hasMany = array(
		'Clip' => array(
			'className' => 'Clip',
			'dependent'    => true
		),
		'MasterRecording' => array(
			'className' => 'MasterRecording',
			'dependent'    => true
		)
	);
			
	/**
	 * Check if the translation request token has expired
	 *
	 * @return boolean
	 * @access public
	 * @author Johnathan Pulos
	 */
	public function isExpired() {
		$expires = Configure::read('VTS.translationRequest.expires');
		if($expires === true) {
			return (time() > strtotime($this->field('expires_at'))) ? true : false;
		} else {
			return false;
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
			$expires = Configure::read('VTS.translationRequest.expires');
			$expiresIn = Configure::read('VTS.translationRequest.expiresIn');
			$token = "tr" . $this->createToken(25);
			if($expires === true) {
				$expirationDate = mktime(date("G"),date("i"),date("s"),date("m"),date("d")+$expiresIn,date("Y"));
				$expiresAt = date('Y-m-d G:i:s', $expirationDate);
			}else {
				$expiresAt = '';
			}
			$this->query('UPDATE translation_requests SET token = "' . $token . '", expires_at = "' . $expiresAt . '" WHERE id = '.$this->id);
			return true;
		}
}
