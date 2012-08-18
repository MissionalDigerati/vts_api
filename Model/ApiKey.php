<?php
/**
 * This file is part of Video Translator Service Website Example.
 * 
 * Video Translator Service Website Example is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * Video Translator Service Website Example is distributed in the hope that it will be useful,
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
 * ApiKey Model
 *
 */
class ApiKey extends AppModel {
/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'app_resource';
	
	/**
	 * The accessible attributes for mass assignment
	 *
	 * @var array
	 */
	public $attrAccessible	=	array('app_resource');

	/**
	 * hasMany associations
	 *
	 * @var array
	 */
		public $hasMany = array(	'TranslationRequest' => array(	'className' => 'TranslationRequest',
																															'dependent' => true
																														)
															);
	
	/**
	 * Define validations for model
	 *
	 * @var array
	 */
	public $validate = array(
												    'app_resource' => array(
												        'rule'    => 'notEmpty',
												        'message' => 'This field cannot be left blank.'
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
			/**
			 * Create the hash key
			 *
			 * @author Johnathan Pulos
			 */
			$hashKey = "vts" . $this->id . $this->createToken(25);
			$this->query('UPDATE api_keys SET hash_key = "' . $hashKey . '" WHERE id = '.$this->id);
			return true;
		}	

}
