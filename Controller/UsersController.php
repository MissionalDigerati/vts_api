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
App::uses('AppController', 'Controller');
/**
 * Users Controller
 *
 * @property User $User
 */
class UsersController extends AppController {
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
	public $helpers = array('TwitterBootstrap', 'Session', 'Html', 'Time');
	
	/**
	 * Set the Controller wide layout
	 *
	 * @var string
	 */
	public $layout = 'manage';
	
	/**
	 * Define pagination settings
	 *
	 * @var array
	 */
	public $paginate = array('limit' => 25);
	
	/**
	 * Declare CakePHP's callback
	 *
	 * @return void
	 * @access public
	 * @author Johnathan Pulos
	 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('request_password_change', 'change_password');
	}
	
	/**
	 * Login to the website
	 *
	 * @return void
	 * @access public
	 * @author Johnathan Pulos
	 */
	public function login() {
		if ($this->request->is('post')) {
			if ($this->Auth->login()) {
			    $this->redirect($this->Auth->redirect());
			} else {
			  $this->Session->setFlash(__('Invalid username or password, or your account has not been activated yet. Please try again.'), '_flash_msg', array('msgType' => 'error'));
				$this->request->data['User']['password'] = "";
			}
    }
	}
	
	/**
	 * Log out of the website
	 *
	 * @return void
	 * @access public
	 * @author Johnathan Pulos
	 */
	public function logout() {
		$this->redirect($this->Auth->logout());
	}
	
	/**
	 * view method My Account /my-account
	 *
	 * @return void
	 */
	public function my_account() {
		$id = $this->Auth->user('id');
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		$this->set('user', $this->User->read(null, $id));
	}

	/**
	 * edit method /edit-account
	 *
	 * @return void
	 */
	public function edit_account() {
		$id = $this->Auth->user('id');
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if(!isset($this->request->data['User']['change_password'])) {
				/**
				 * They do not want to change their password, so unset fields and remove validation
				 *
				 * @author Johnathan Pulos
				 */
				$this->User->unbindValidation('remove', array('password', 'confirm_password'));
				unset($this->request->data['User']['password']);
				unset($this->request->data['User']['confirm_password']);
			}
			$this->request->data['User']['id'] = $id;
			if ($this->User->save($this->request->data, true, $this->User->attrAccessible)) {
				$this->Session->setFlash(__('Your account has been updated.'), '_flash_msg', array('msgType' => 'info'));
				$this->redirect(array('action' => 'my_account'));
			} else {
				$this->Session->setFlash(__('Unable to update your account. Please, try again.'), '_flash_msg', array('msgType' => 'error'));
			}
		} else {
			$this->request->data = $this->User->read(null, $id);
		}
		$this->request->data['User']['password'] = "";
		$this->request->data['User']['confirm_password'] = "";
	}
	
	/**
	 * Request to change your password
	 *
	 * @return void
	 * @access public
	 * @author Johnathan Pulos
	 */
	public function request_password_change() {
		if($this->request->is('post')) {
			$user = $this->User->findByEmail($this->request->data['User']['email']);
			if($user) {
				$this->User->id = $user['User']['id'];
				$activationHash = $this->User->getActivationHash();
				if($this->User->saveField('activation_hash', $activationHash)) {
					$this->User->sendChangePassword($user['User']['name'],  $activationHash, $user['User']['email']);
					$this->Session->setFlash(__('Instructions have been sent to your email.'), '_flash_msg', array('msgType' => 'info'));
				}else{
					$this->Session->setFlash(__('Unable to complete the request.'), '_flash_msg', array('msgType' => 'error'));
				}
			}else {
				$this->Session->setFlash(__('Unable to locate your account.'), '_flash_msg', array('msgType' => 'error'));
			}
			$this->redirect(array('action'		=>	'login'));
		}
	}
	
	/**
	 * Change Password
	 *
	 * @param string $activation the User.activation_hash
	 * @return void
	 * @access public
	 * @author Johnathan Pulos
	 */
	public function change_password($activation = null) {
		if(!$activation) {
			$this->Session->setFlash(__('Your access code does not exist.'), '_flash_msg', array('msgType' => 'error'));
			$this->redirect("/");
		}
		$user = $this->User->findByActivationHash($activation);
		if($this->request->is('post')) {
			/**
			 * We are only validating the password
			 *
			 * @author Johnathan Pulos
			 */
			$this->User->unbindValidation('keep', array('password', 'confirm_password'));
			$this->request->data['User']['id'] = $user['User']['id'];
			$this->request->data['User']['activation_hash'] = '';
			if ($this->User->save($this->request->data, true, array('password', 'activation_hash'))) {
				$this->Auth->login($user['User']);
				$this->Session->setFlash(__('Your account has been updated, and you have been logged in.'), '_flash_msg', array('msgType' => 'info'));
				$this->redirect('/');
			} else {
				$this->Session->setFlash(__('Unable to update your account. Please, try again.'), '_flash_msg', array('msgType' => 'error'));
			}
		}
		$this->request->data['User']['password'] = "";
		$this->request->data['User']['confirm_password'] = "";
	}

}