<?php
/**
*
* Board Notices Manager
*
* @version 1.0.0
* @copyright (c) 2015 Fred Quointeau
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace fq\boardnotices\rules;

class not_logged_in implements rule
{
	private $user;
	
	public function __construct(\phpbb\user $user) {
		$this->user = $user;
	}
	
	public function isTrue($conditions) {
		$valid = false;
		$valid = ($this->user->data['user_id'] == ANONYMOUS);
		return $valid;
	}
	
	public function getTemplateVars()
	{
		return array();
	}
}
