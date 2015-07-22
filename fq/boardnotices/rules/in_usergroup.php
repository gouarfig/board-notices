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

class in_usergroup implements rule
{
	private $user;
	
	public function __construct(\phpbb\user $user) {
		$this->user = $user;
	}
	
	public function isTrue($conditions) {
		$valid = false;
		return $valid;
	}
}
