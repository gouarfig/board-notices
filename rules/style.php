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

class style implements rule
{
	private $user;

	public function __construct(\phpbb\user $user)
	{
		$this->user = $user;
	}

	public function getDisplayName()
	{
		return "User style is";
	}

	public function getType()
	{
		return 'multiple choice';
	}

	public function getPossibleValues()
	{
		return null;
	}

	public function isTrue($conditions)
	{
		$valid = false;
		$styles = @unserialize($conditions);
		if ($styles === false)
		{
			// There's only one style
			$styles = array((int) $conditions);
		}
		if (!empty($styles))
		{
			foreach ($styles as $style_id)
			{
				$valid = ($this->user->data['user_style'] == $style_id);
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
