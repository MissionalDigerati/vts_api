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
	 * CakePHP callback beforeRender
	 *
	 * @return void
	 * @access public
	 * @author Johnathan Pulos
	 */
	public function beforeRender() {
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
				if (!$this->request->is('put')) {
					throw new MethodNotAllowedException();
					exit;
				}
			break;
			case 'delete':
				if (!$this->request->is('delete')) {
					throw new MethodNotAllowedException();
					exit;
				}
			break;
		}
	}
	/**
	 * Cleans the token so it is only alphanumeric
	 *
	 * @param string $token the translation request token
	 * @return string
	 * @access public
	 * @author Johnathan Pulos
	 */
	public function cleanedToken($token) {
		return ereg_replace("[^A-Za-z0-9]", "", $token);
	}

}
