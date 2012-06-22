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
		if(time() > strtotime($this->field('expires_at'))) {
			return true;
		}else {
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
			$token = "tr" . $this->createToken(25);
			$tomorrow = mktime(date("G"),date("i"),date("s"),date("m"),date("d")+1,date("Y"));
			$expires_at = date('Y-m-d G:i:s', $tomorrow);
			$this->query('UPDATE translation_requests SET token = "' . $token . '", expires_at = "' . $expires_at . '" WHERE id = '.$this->id);
			return true;
		}
}
