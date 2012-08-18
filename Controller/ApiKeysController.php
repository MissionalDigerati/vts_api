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

App::uses('AppController', 'Controller');
/**
 * ApiKeys Controller Manage the API keys
 *
 */
class ApiKeysController extends AppController {
	/**
	 * Define components you wish to use on all controllers
	 *
	 * @var array
	 */
	public $components = array('Session');
	/**
	 * Define helpers you will be using
	 *
	 * @var array
	 */
	public $helpers = array('TwitterBootstrap', 'Session', 'Html');
	
	public $layout = 'manage';
	
	/**
	 * A list of all the current API keys
	 *
	 * @return void
	 * @access public
	 * @author Johnathan Pulos
	 */
	public function index() {
		$this->set('apiKeys', $this->ApiKey->find('all'));
	}
	
	/**
	 * Add a new API key
	 *
	 * @return void
	 * @access public
	 * @author Johnathan Pulos
	 */
	public function add() {
		$this->ApiKey->create();
		if(!empty($this->request->data)) {
			if ($this->ApiKey->save($this->request->data, true, $this->ApiKey->attrAccessible)) {
				$this->Session->setFlash(__('Your api key has been created.'), '_flash_msg', array('msgType' => 'info'));
				$this->redirect(array('controller'	=>	'api_keys', 'action'	=>	'index'));
			} else {
				$this->Session->setFlash(__('Unable to create your api key.'), '_flash_msg', array('msgType' => 'error'));
			}
		}
	}

}
