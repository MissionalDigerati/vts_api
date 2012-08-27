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
	public $components = array('DebugKit.Toolbar', 
															'RequestHandler',
															'Auth' => array('authenticate' => array('Form' => array('fields' => array('username' => 'email'))))
														);
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
	 * The current API Key supplied to this controller
	 *
	 * @var string
	 */
		public $currentApiKey;
	
	/**
	 * CakePHP callback beforeFilter
	 *
	 * @return void
	 * @access public
	 * @author Johnathan Pulos
	 */
	public function beforeFilter() {
		// Pass settings in
		$url = $this->request->here();
		/**
		 * Set up the correct response
		 *
		 * @author Johnathan Pulos
		 */
		if(isset($this->request->params['ext'])) {
			switch ($this->request->params['ext']) {
				case 'json':
					$this->RequestHandler->respondAs('json');
				break;
				case 'xml':
					$this->RequestHandler->respondAs('xml');
				break;
			}
		}
		/**
		 * if we are not on API keys, then validate the HTP Status Code
		 *
		 * @author Johnathan Pulos
		 */
		if($this->name != 'ApiKeys') {
			$this->validateHttpRequest();
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
			throw new Exception(__('Your translation request token is missing.'), 401);
		}
		$this->loadModel('TranslationRequest');
		$currentTranslationRequest = $this->TranslationRequest->findByToken($this->currentToken);
		if (empty($currentTranslationRequest)) {
			throw new NotFoundException(__('Invalid translation request token submitted.'));
		}
		/**
		 * I have to force Cake to use this Translation Request, if not then the isExpired function gets the wrong Translation Request
		 *
		 * @author Johnathan Pulos
		 */
		$this->TranslationRequest->id = $currentTranslationRequest['TranslationRequest']['id'];
		if (!$this->TranslationRequest->exists()) {
			throw new NotFoundException(__('Invalid translation request token submitted.'));
		}
		if ($this->TranslationRequest->isExpired()) {
			throw new Exception(__('Your translation request token has expired.'), 401);
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
	public function cleanedToken($token) {
		return ereg_replace("[^A-Za-z0-9]", "", $token);
	}
	
	/**
	 * iterates over cakePHP's invalidFields() errors, and returns a string of the errors.
	 *
	 * @param array $errors the errors received from invalidFields()
	 * @return string
	 * @access public
	 * @author Johnathan Pulos
	 */
	public function ppErrors($errors) {
		$errorMsg = '';
		foreach($errors as $key => $value) {
			foreach ($value as $individualError) {
				if(strpos($errorMsg, $individualError) === false) {
					$errorMsg .= $individualError . " ";
				}
			}
		}
		return trim($errorMsg);
	}
	
	/**
	 * Make sure they are using the correct HTTP methods.  Throws error if it fails
	 *
	 * @return void
	 * @access private
	 * @author Johnathan Pulos
	 */
	private function validateHttpRequest() {
		switch ($this->request['action']) {
			case 'index':
			case 'view':
				if (!$this->request->is('get')) {
					throw new MethodNotAllowedException($url.__(' requires a GET HTTP Method.'));
					exit;
				}
			break;
			case 'add':
				if (!$this->request->is('post')) {
					throw new MethodNotAllowedException($url.__(' requires a POST HTTP Method.'));
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
					throw new MethodNotAllowedException($url.__(' requires a PUT HTTP Method or a POST with a _method var set to PUT.'));
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
					throw new MethodNotAllowedException($url.__(' requires a DELETE HTTP Method or a POST with a _method var set to DELETE.'));
					exit;
				}
			break;
		}
	}

}
