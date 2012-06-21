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
App::uses('ExceptionRenderer', 'Error');
/**
 * A custom exception render
 *
 * @package default
 * @author Johnathan Pulos
 */
class AppExceptionRenderer extends ExceptionRenderer {
  /**
   * The resource is invalid,  so send the correct missing resource data
   *
   * @param Exception $error 
   * @return void
   * @access public
   * @author Johnathan Pulos
   */  
	public function error400($error) {
		$ext = $this->controller->request['ext'];
		$statusCode = $error->getCode();
		$this->controller->set('details', $error->getMessage());
		$this->controller->response->statusCode($statusCode);
		$this->controller->render('/Errors/'.$ext.'/error'.$statusCode, $ext.'/default');
		$this->controller->response->send();
	}
	
	/**
   * Internal Server Error
   *
   * @param Exception $error 
   * @return void
   * @access public
   * @author Johnathan Pulos
   */  
	public function error500($error) {
		$ext = $this->controller->request['ext'];
		$statusCode = $error->getCode();
		$this->controller->set('details', $error->getMessage());
		$this->controller->response->statusCode($statusCode);
		$this->controller->render('/Errors/'.$ext.'/error'.$statusCode);
		$this->controller->response->send();
	}

}
?>