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

class language implements rule
{
	private $user;
	
	public function __construct(\phpbb\user $user) {
		$this->user = $user;
	}
	
	public function isTrue($conditions) {
		$valid = false;
		$languages = @unserialize($conditions);
		if ($languages === false)
		{
			// There's only one language
			$languages = array($conditions);
		}
		if (!empty($languages))
		{
			foreach ($languages as $language_id) {
				$valid = ($this->user->data['user_lang'] == $language_id);
				if (!$valid)
				{
					break;
				}
			}
		}
		return $valid;
	}
	
	public function getTemplateVars()
	{
		return array();
	}
}
