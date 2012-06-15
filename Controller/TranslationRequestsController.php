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
 * TranslationRequests Controller
 *
 * @property TranslationRequest $TranslationRequest
 */
class TranslationRequestsController extends AppController {

/**
 * Define components you wish to use on all controllers
 *
 * @var array
 */
	public $components = array('RequestHandler');
	
/**
 * index method
 *
 * @return void
 */
	public function index() {
		if (!$this->request->is('get')) {
			throw new MethodNotAllowedException();
		}
		$this->TranslationRequest->recursive = 0;
		$this->set('translationRequests', $this->paginate());
	}

/**
 * view method
 *
 * @param string $id the id for the resource
 * @return void
 */
	public function view($id = null) {
		if (!$this->request->is('get')) {
			throw new MethodNotAllowedException();
		}
		$this->TranslationRequest->id = $id;
		if (!$this->TranslationRequest->exists()) {
			throw new NotFoundException(__('Invalid translation request.'));
		}
		$this->set('translation_request', $this->TranslationRequest->read(null, $id));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->TranslationRequest->create();
		if ($this->TranslationRequest->save($this->request->data)) {
			$id = $this->TranslationRequest->getLastInsertID();
			$this->set('message', __('Your translation request has been created.'));
			$this->set('status', __('success'));
			$this->set('translation_request', $this->TranslationRequest->read(null, $id));
		} else {
			$this->set('message', __('Your translation request has been denied.'));
			$this->set('status', __('error'));
			$this->set('translation_request', array());
		}
	}

/**
 * edit method
 *
 * @param string $id the id for the resource
 * @return void
 */
	public function edit($id = null) {
		$this->TranslationRequest->id = $id;
		if (!$this->TranslationRequest->exists()) {
			throw new NotFoundException(__('Invalid translation request'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->TranslationRequest->save($this->request->data)) {
				$this->flash(__('The translation request has been saved.'), array('action' => 'index'));
			} else {
			}
		} else {
			$this->request->data = $this->TranslationRequest->read(null, $id);
		}
	}

/**
 * delete method
 *
 * @param string $id the id for the resource
 * @return void
 */
	public function delete($id = null) {
		if (!$this->request->is('delete')) {
			throw new MethodNotAllowedException();
		}
		$this->TranslationRequest->id = $id;
		if (!$this->TranslationRequest->exists()) {
			throw new NotFoundException(__('Invalid translation request.'));
		}
		if ($this->TranslationRequest->delete()) {
			$this->set('message', __('Your translation request has been deleted.'));
			$this->set('status', __('success'));
		}else {
			throw new InternalErrorException();
		}
	}
}
