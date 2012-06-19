<?php
App::uses('AppModel', 'Model');
/**
 * Clip Model
 *
 * @property TranslationRequest $TranslationRequest
 */
class Clip extends AppModel {

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'TranslationRequest' => array(
			'className' => 'TranslationRequest',
			'foreignKey' => 'translation_request_id'
		)
	);
}
