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
App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
	/**
	 * Define components you wish to use on all controllers
	 *
	 * @var array
	 */
	public $components = array('DebugKit.Toolbar');
	/**
	 * The current translation request id
	 *
	 * @var array
	 */
		public $currentTranslationRequestId;
	/**
	 * The current token supplied to this controller
	 *
	 * @var string
	 */
		public $currentToken;
	
	/**
	 * CakePHP callback beforeFilter
	 *
	 * @return void
	 * @access public
	 * @author Johnathan Pulos
	 */
	public function beforeFilter() {
		/**
		 * Make sure they are using the correct HTTP methods
		 *
		 * @author Johnathan Pulos
		 */
		switch ($this->request['action']) {
			case 'index':
			case 'view':
				if (!$this->request->is('get')) {
					throw new MethodNotAllowedException();
					exit;
				}
			break;
			case 'add':
				if (!$this->request->is('post')) {
					throw new MethodNotAllowedException();
					exit;
				}
			break;
			case 'edit':
				/**
				 * Due to cake's inability to handle PUT vars, and PHP's arcaic, stupid lack of sufficient support for PUT requests,
				 * we have to use a old hack.  We will use _method to determin PUT requests, and send a POST request.
				 *
				 * @author Johnathan Pulos
				 */
				if ($this->request->is('get') || $this->request->is('delete')) {
					throw new MethodNotAllowedException();
					exit;
				}
			break;
			case 'delete':
				/**
				 * Due to cake's inability to handle PUT vars, and PHP's arcaic, stupid lack of sufficient support for PUT requests,
				 * we have to use a old hack.  We will use _method to determin PUT requests, and send a POST request.
				 *
				 * @author Johnathan Pulos
				 */
				if ($this->request->is('get') || $this->request->is('put')) {
					throw new MethodNotAllowedException();
					exit;
				}
			break;
		}
	}
	
	/**
	 * Checks if the client has a valid translation_request token
	 *
	 * @return void
	 * @access public
	 * @author Johnathan Pulos
	 */
	public function mustHaveValidToken() {
		$this->currentToken = $this->cleanedToken($this->getParam('translation_request_token'));
		if(empty($this->currentToken)) {
			throw new Exception(__('Your token is missing.'), 401);
		}
		$this->loadModel('TranslationRequest');
		$currentTranslationRequest = $this->TranslationRequest->findByToken($this->currentToken);
		if (empty($currentTranslationRequest)) {
			throw new NotFoundException(__('Invalid translation request.'));
		}
		/**
		 * I have to force Cake to use this Translation Request, if not then the isExpired function gets the wrong Translation Request
		 *
		 * @author Johnathan Pulos
		 */
		$this->TranslationRequest->id = $currentTranslationRequest['TranslationRequest']['id'];
		if (!$this->TranslationRequest->exists()) {
			throw new NotFoundException(__('Invalid translation request.'));
		}
		if ($this->TranslationRequest->isExpired()) {
			throw new Exception(__('Your token has expired.'), 401);
		}
		$this->currentTranslationRequestId = $currentTranslationRequest['TranslationRequest']['id'];
	}
	
	/**
	 * Returns the value of the parameter based on the HTTP protocol
	 *
	 * @param string $key the key of the parameter
	 * @return string
	 * @access public
	 * @author Johnathan Pulos
	 */
	public function getParam($key) {
		if($this->request->is('get')) {
			return (isset($this->request->query[$key])) ? $this->request->query[$key] : '';
		} else{
			return (isset($this->request->data[$key])) ? $this->request->data[$key] : '';
		}
	}
	
	/**
	 * Cleans the token so it is only alphanumeric
	 *
	 * @param string $token the translation request token
	 * @return string
	 * @access private
	 * @author Johnathan Pulos
	 */
	private function cleanedToken($token) {
		return ereg_replace("[^A-Za-z0-9]", "", $token);
	}

}
